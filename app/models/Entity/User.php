<?php

namespace Entity;

use Nette\Environment;

/**
 * Třída pro práci s entitou uživatele
 */
class User extends Base implements \Observer {

	private $subCategories;

	const DEFAULT_ROLE = "user";

	const ACTIVATED = 1;

	const BANNED = -1;

	const INACTIVATED = 0;

	/**
	 * Získá rating uživatele
	 * @return int
	 */
	public function getRating() {
		if ($this->offsetExists('rating')) {
			return $this->offsetGet('rating');
		}
		else {
			$result = $this->dataSource->related('Reference', 'rated_id')->aggregation('AVG(rating)');
			$this->offsetSet('rating', (float)$result);
			return $this->offsetGet('rating');
		}
	}

	/**
	 * Získá level uživatele
	 * @return int
	 */
	public function getLevel() {
		if ($this->offsetExists('level')) {
			return $this->offsetGet('level');
		}
		else {
			/*
			 * SELECTnem si kolik by bylo expů za projekt, z toho si vytáhneme 1% a
			 * vynásobíme to na kolik procent byl uživatel spokojen a vytáhneme si SUMU
			 * FROM:
			 * Derivovaná tabulka selectne projekty, které má uživatel dokončené a 4ty pak joinneme s referencema
			 */
			$row = $this->db->query(
				'SELECT SUM((Project.scale * 100)*(`Reference`.rating * 0.2)) as exp
	FROM
		(((SELECT Project_User.project_id as pid
		FROM Project_User
		WHERE Project_User.user_id = ? AND Project_User.status = ?) as projects)
		JOIN Project ON (projects.pid = Project.id))
	JOIN `Reference` ON (`Reference`.project_id = Project.id)'
				, $this->id, \Entity\Project::FINISHED)->fetch();
			$this->offsetSet('exp', $row->exp);
			$this->offsetSet('level', \Level::getLevelByExp($row->exp));
			return $this->offsetGet('level');
		}
	}

	/**
	 * Získá zkušenostní body Usera
	 * @return int
	 */
	public function getExp() {
		if ($this->offsetExists('exp')) {
			return $this->offsetGet('exp');
		}
		else {
			/*
			 * SELECTnem si kolik by bylo expů za projekt, z toho si vytáhneme 1% a
			 * vynásobíme to na kolik procent byl uživatel spokojen a vytáhneme si SUMU
			 * FROM:
			 * Derivovaná tabulka selectne projekty, které má uživatel dokončené a 4ty pak joinneme s referencema
			 */
			$row = $this->db->query(
				'SELECT SUM((Project.scale * 100)*(`Reference`.rating * 0.2)) as exp
	FROM
		(((SELECT Project_User.project_id as pid
		FROM Project_User
		WHERE Project_User.user_id = ? AND Project_User.status = ?) as projects)
		JOIN Project ON (projects.pid = Project.id))
	JOIN `Reference` ON (`Reference`.project_id = Project.id)'
				, $this->id, \Entity\Project::FINISHED)->fetch();
			$this->offsetSet('exp', $row->exp);
			$this->offsetSet('level', \Level::getLevelByExp($row->exp));
			return $this->offsetGet('exp');
		}
	}

	/**
	 * Vrátí město uživatele, nebo NULL, pokud žádné nemá
	 * @return \Entity\City|null
	 */
	public function getCity() {
		$cityId = $this->offsetGet('city');
		if (is_int($cityId)) {
			$activeRow = $this->dataSource->ref('City', 'city');
			return Environment::getContext()->cityMapper->makeInstanceFromId($activeRow->id, $activeRow);
		}
		return NULL;
	}

	/**
	 * Získá dokončené projekty uživatele
	 * @param bool $justNumber Zda chceme projekty pouze jako číslo, nebo ne
	 * @return \Entity\Project[]|int
	 */
	public function getFinishedProjects($justNumber = FALSE) {
		if ($justNumber) {
			return $this->dataSource->related('Project_User', 'user_id')
				 ->where('status = ?', \Entity\Project::FINISHED)
				 ->count();
		}
		$rel = $this->dataSource->related('Project_User', 'user_id')
			 ->where('status = ?', \Entity\Project::FINISHED);
		$projectMapper = Environment::getContext()->projectMapper;
		$projectIds = \Arrays::pickUp($rel, 'project_id');
		return $projectMapper->findByIds($projectIds);
	}

	/**
	 * Zjistí zda je uživatelův účet aktivní
	 * @param int $status Status v jakém se může uživatel nacházet viz konstanty
	 * @return bool
	 */
	public function is($status) {
		return (bool)($this->activated == $status);
	}

	/**
	 * Nastaví uživatele na jistý status
	 * @param int $status 0 - neaktivovaný, 1 - aktivní, -1 zabanován
	 * @return bool
	 */
	public function setStateTo($status) {
		return (bool)$this->db->table('User')->find($this->id)->update(array('activated' => intval($status)));
	}

	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getSchools() {
		return $this->db->table('Education')->where('user_id', $this->id);
	}

	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getLanguages() {
		return $this->db->table('Language_User')->where('user_id', $this->id);
	}

	/**
	 * Přidá jazyk člověku
	 * @param \Language   $lang    Instance jazyku
	 * @param int         $level   INT (škála levelu)
	 * @return int|bool ID vloženého záznamu, FALSE při chybě
	 */
	public function addLanguage(\Language $lang, $level) {
		$this->db->table('Language_User')->insert(
			array('language_id' => $lang->getId(), 'level' => $level, 'user_id' => $this->id)
		);
		$id = $this->db->lastInsertId();
		return ($id > 0) ? $id : FALSE;
	}

	/**
	 * Odebere jazyk uživateli
	 * @param \Language $lang
	 * @return bool TRUE pokud se operace povedla, FALSE pokud ne
	 */
	public function removeLanguage(\Language $lang) {
		$res = $this->db->table('Language_User')
			 ->where('user_id = ?', $this->id)
			 ->where('language_id = ?', $lang->id)
			 ->delete();
		return $res !== FALSE ? TRUE : FALSE;
	}

	/**
	 * Aktualizuje seznam uživatelů, ve kterých uživatelé jsou
	 * @param array $categories
	 * @return boolean Zda se akce povedla či ne
	 */
	public function updateCategories($categories) {
		$this->db->beginTransaction();
		$err = 0;
		$res = $this->db->table('User_SubCategory')->where('user_id = ?', $this->id)->delete();
		if ($res === FALSE) {
			$err++;
		}
		foreach ($categories as $category) {
			$res = (bool)$this->db->table('User_SubCategory')->insert(array(
																							 'user_id'        => $this->id,
																							 'subCategory_id' => $category
																						 ));
			if (!$res) {
				$err++;
			}
		}
		if ($err == 0) {
			$this->db->commit();
			return TRUE;
		}
		else {
			$this->db->rollBack();
			return FALSE;
		}
	}

	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getOldProject() {
		return $this->dataSource->related('OldProject')->where('user_id', $this->id);
	}

	/* =========================== PROJEKTY ====================================== */

	/**
	 * Pokud je uživatel přihlášen k projektu vrací true, jinak false
	 * @param int $projectId ID projektu
	 * @return bool
	 */
	public function isSignedToProject($projectId) {
		$result = $this->db->table("Project_User")
			 ->where("user_id", $this->id)
			 ->where("project_id", $projectId)
			 ->where("status", \Entity\Project::ATTENDED)->fetch();
		return (bool)$result;
	}

	/**
	 * Rozhodne, zda se uživatel může přihlásit k projektu
	 * @param int $projectId
	 * @return bool FALSE pokud se uživatel nemůže přihlásit, TRUE pokud ano.
	 */
	public function canSignToProject($projectId) {
		$result = $this->db->table("Project_User")->where(
			"user_id = ? AND project_id = ? AND status = ?", $this->id, $projectId, \Entity\Project::REVOKED
		)->fetch();
		return !$result ? TRUE : FALSE;
	}

	/**
	 * Odhlásí uživatele z projektu
	 * @param int $projectId
	 * @return bool (V případě že se odhlášení povedlo TRUE, jinak FALSE)
	 */
	public function signOutFromProject($projectId) {
		$result = $this->db->table("Project_User")->where(
			"user_id = ? AND project_id = ? ", $this->id, $projectId
		)->delete();
		return (bool)$result;
	}

	/**
	 * Přidá Usera do uchazečů k projektu
	 * @param \Entity\Project $project (Objekt projektu)
	 * @return bool (V případě že se přidání povedlo, vrátí true, jinak false.)
	 */
	public function signToProject(\Entity\Project $project) {
		$args = array("user_id" => $this->id, "project_id" => $project->getId(), "status" => "attended");
		$result = $this->db->table('Project_User')->insert($args);
		return (bool)$result;
	}

	/**
	 * Získá projekty které patří uživatelovi
	 * @param string $status
	 * @return \Entity\Project[]
	 */
	public function getMineProjects($status = NULL) {
		$rows = $this->db->table('Project')->where('owner', $this->id);
		if ($status !== NULL) {
			$rows = $rows->where('status', $status);
		}
		$ids = \Arrays::pickUp($rows, 'id');
		return Environment::getContext()->projectMapper->findByIds($ids, $rows);
	}

	/**
	 * Získá projekty, ve kterých se vyskytuje (je pozván, odmítnut)
	 * @param string $status
	 * @return \Entity\Project[]
	 */
	public function getRelatedProjects($status = NULL) {
		$rows = $this->db->table('Project_User')->where('user_id', $this->id);
		if ($status !== NULL) {
			$rows = $rows->where('status', $status);
		}
		$ids = \Arrays::pickUp($rows, 'project_id');
		return Environment::getContext()->projectMapper->findByIds($ids);
	}

	/* =========================== PROJEKTY KONEC ====================================== */

	/**
	 * Získá kategorie ve kterých je uživatel
	 * @return array
	 */
	public function getCategories() {
		if (empty($this->subCategories)) {
			// TODO :: vyřešit přes $this->dataSource, kvůli optimalizaci dotazů
			/*
			  $rel = $this->dataSource->related('User_SubCategory',"user_id")->execute();

			  $user = $this->db->table("User")->find($this->id);
			  foreach ($user as $user) {
			  foreach ($user->related("User_SubCategory") as $rel) {
			  }
			  }
			 */
			$this->subCategories = $this->db->query('SELECT SubCategory.id, SubCategory.name
                                                  FROM User_SubCategory
                                                  JOIN SubCategory ON (User_SubCategory.subCategory_id = SubCategory.id)
                                                  WHERE user_id = ?', $this->id)->fetchPairs('id', 'name');
		}
		return $this->subCategories;
	}

	/**
	 * Získá přehled posledních zprávy od každého uživatele
	 * @return \Nette\Database\Table\Selection
	 */
	public function getMessages() {

		$messages = $this->db->table('Message')->where(
			'date', $this->db->table('Message')->select('MAX(date)')->group('from_id')->where('to_id', $this->id)
		)->where('to_id', $this->id)
			 ->order('date DESC');

		$ids = \Arrays::pickUp($messages, 'from_id');
		$users = $this->db->table('User')->where('id', $ids);

		foreach ($messages as $message) {
			$message['from'] = $users[$message->from_id];
		}
		return $messages;
	}

	/**
	 * Získá všechny zprávy od jednoho uživatele
	 * @param int $id   ID uživatele
	 * @return \Nette\Database\Table\Selection
	 */
	public function getMessagesFrom($id) {
		$this->db->table('Message')->where('from_id', $id)->update(array('status' => \Message::READ));
		// Chceme zprávy od něho mě, a také odemě němu
		return $this->db->table('Message')->where(
			'(from_id = ? AND to_id = ?) OR (from_id = ? AND to_id = ?)', $id, $this->id, $this->id, $id
		)->order('date DESC');
	}

	/**
	 * Zjistí, zda uživatel má stejné heslo
	 * @param string $password Heslo oproti kterému se má zkusit aktuální
	 * @return bool
	 */
	public function isPasswordSame($password) {
		$result = $this->db->table('User')
			 ->where('id', $this->id)
			 ->where('pass', $password)
			 ->fetch();
		return (bool)$result;
	}

	/**
	 * Notifikuje uživatele zprávou \Message $message
	 * @param \Message $message
	 * @return void
	 */
	public function notify(\Message $message) {
		$insertion = array(
			"user_id" => $this->id,
			"message" => $message->getMessage(),
			"date"    => $message->getDate(),
			"status"  => \Message::UNREAD
		);
		Environment::getContext()->notificationMapper->insert($insertion);
	}

}
