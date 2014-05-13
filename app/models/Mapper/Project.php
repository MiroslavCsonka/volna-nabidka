<?php

namespace Mapper;

use \Nette\Database\SqlLiteral;

/**
 * Mapper který pracuje nad Entitou \Entity\Project
 */
class Project extends Base {

	/**
	 * Získá \DataResult pro všechny projekty, které jsou aktivní
	 * @return \DataResult
	 */
	public function getAll() {
		$projects = $this->db->table("Project")
			 ->where("status = ?", \Entity\Project::LIVE)
			 ->where("deadline >= CURDATE()")
			 ->order('id DESC');
		return new \DataResult($this, $projects);
	}

	/**
	 * Vrátí ukončené projekty obalené DataResultem pro $usera
	 * @param \Entity\User $user
	 * @return \DataResult
	 */
	public function getFinishedByUser(\Entity\User $user) {
		$selection = $this->db->table('Project_User')
			 ->where('user_id = ?', $user->getId())
			 ->where('status = ?', \Entity\Project::FINISHED);
		$ids = \Arrays::pickUp($selection, 'project_id');
		$selection = $this->db->table('Project')
			 ->where('id', $ids)
			 ->order('id DESC');
		return new \DataResult($this, $selection, 'id');
	}

	/**
	 * Vrátí kolik je dostupných projektů k přihlášení
	 * @return int
	 */
	public function getNumOfAlive() {
		return $this->db->table("Project")
			 ->where("status = ?", \Entity\Project::LIVE)
			 ->where("deadline >= CURDATE()")
			 ->count();
	}

	/**
	 * Získá sumu (odměn) za daný typ odměny
	 * @param string $type
	 * @return string
	 */
	public function getSumOf($type = NULL) {
		$table = $this->db->table('Project');
		if ($type) {
			return $table->where("Project.pricing", $type)->aggregation("SUM(reward)");
		}
		return $table->aggregation("SUM(reward)");
	}

	/**
	 * Získá všechny projekty, které náleží dané kategorii
	 * @param int $id Id subkategorie
	 * @return \DataResult|null
	 */
	public function getByCategory($id) {
		$relation = $this->db->table('Project_SubCategory')->where('subcategory_id = ?', $id);
		$ids = array();
		foreach ($relation as $row) {
			$ids[] = $row->project_id;
		}
		$result = $this->db->table('Project')
                        ->where('id', $ids)
                        ->where('status = ?', \Entity\Project::LIVE)
                        ->where("deadline >= CURDATE()");
		if ($result->count('id') > 0) {
			return new \DataResult($this, $result, "id");
		}
		return NULL;
	}

	/**
	 * Získá všechny projekty na základě filtru
	 * @param array $params Asociativní pole parametrů
	 * @return \DataResult
	 */
	public function getByFilter($params) {
		// Unset prázdných položek
		foreach ($params as $key => $value) {
			if (empty($value)) {
				unset($params[$key]);
			}
		}
		$result = $this->db->table('Project')
			 ->where('status = ?', \Entity\Project::LIVE)
			 ->where("deadline >= CURDATE()");
		if (isset($params['categories'])) {
			$res = $this->db->table('Project_SubCategory')->where('subcategory_id', array_values($params['categories']));
			$ids = \Arrays::pickUp($res, "project_id");
			$result->where('id', $ids);
		}
		// Jméno projektu
		if (isset($params['name'])) {
			$result->where('name LIKE ?', "%" . $params['name'] . "%");
		}
		$allowed = array(\Entity\Project::REWARD_PER_PROJECT, \Entity\Project::REWARD_PER_HOUR);
		if (isset($params['type']) && (in_array($params['type'], $allowed))) {
			if (isset($params['min'])) {
				$result->where('reward >= ?', new SqlLiteral(\Int::convert($params['min'])));
			}
			if (isset($params['max'])) {
				$result->where('reward <= ?', new SqlLiteral(\Int::convert($params['max'])));
			}
			$result->where('pricing = ?', $params['type']);
		}
		if (isset($params['cities'])) {
			$result->where('location', $params['cities']);
		}
		$result->order('id DESC');
		return new \DataResult($this, $result);
	}

	/**
	 * Vytvoří nový projekt
	 * @param \Nette\ArrayHash $values Hodnoty které mají být projektu přiřazeny
	 * @param \Entity\User     $owner  Vlastník projektu
	 * @return int|bool Vrací FALSE v případě že se nepovedlo vložit nový projekt.
	 *                  Vrací int (idčko projektu) v případě že se povedlo projekt přidat
	 */
	public function create(\Nette\ArrayHash $values, \Entity\User $owner) {
		$deadline = \DateTime::createFromFormat('d-m-Y', $values->deadline);
		$insertionArray = array(
			\Enum::PROJECT_NAME_COL        => $values->name,
			\Enum::PROJECT_OWNER_COL       => $owner->getId(),
			\Enum::PROJECT_STATUS_COL      => \Entity\Project::LIVE,
			\Enum::PROJECT_DESCRIPTION_COL => $values->description,
			\Enum::PROJECT_DEADLINE_COL    => $deadline,
			\Enum::PROJECT_PRICING_COL     => $values->pricing,
			\Enum::PROJECT_REWARD_COL      => $values->reward,
			\Enum::PROJECT_SCALE_COL       => $values->scale,
			\Enum::PROJECT_LOCATION_COL    => $values->location
		);
		$err = 0;
		$this->db->beginTransaction();
		if (!$this->insert($insertionArray) instanceof \Entity\Project) {
			$err++;
		}
		$projectId = $this->db->lastInsertId();
		$categories = array();
		foreach ($values->categories as $category) {
			$categories[] = array(
				\Enum::SUBCAT_PROJECT_COL  => $projectId,
				\Enum::SUBCAT_CATEGORY_COL => $category
			);
		}
		$this->db->table('Project_SubCategory')->insert($categories) ? : $err++;
		if ($err == 0) {
			$this->db->commit();
			return $projectId;
		}
		else {
			$this->db->rollBack();
			return FALSE;
		}
	}

	/**
	 *
	 * @param string $type Zda chceme hodinovou mzdz, nebo za projekt
	 * @return array Asociativní pole kde min je minimální mzda a max maximální mzda
	 * @throws \Nette\ArgumentOutOfRangeException
	 */
	public function getWageRange($type = \Entity\Project::REWARD_PER_PROJECT) {

		if (!in_array($type, array(\Entity\Project::REWARD_PER_PROJECT, \Entity\Project::REWARD_PER_HOUR))) {
			throw new \Nette\ArgumentOutOfRangeException("Nebylo možné najít mzdu podle zadaného parametru $type");
		}
		$result = $this->db->query('SELECT MIN(Project.reward) as min, MAX(Project.reward) as max
                                    FROM Project
                                    WHERE Project.pricing = ? AND Project.status = ? AND Project.deadline >= CURDATE()', $type, \Entity\Project::LIVE)->fetch();
		$out = array(
			"min" => (int)$result->min,
			"max" => (int)$result->max
		);
		return $out;
	}

	/**
	 * Získá projekty, na které může $inviter odeslat pozvánku
	 * @param \Entity\User $inviter  Uživatel, který chce odeslat pozvánku
	 * @param \Entity\User $reciever Uživatel, který má příjmout pozvánku
	 * @return \Entity\Project[]
	 */
	public function getForInvitation($inviter, $reciever) {
		/* TODO: Dodělat aby se nesměla poslat pozvánka userovi, který už je na projekt přihlášený a který byl odmítnut */
		// Získáme idčka projektů, které již nesmíme invitnout
		$invitations = $this->db->table('Invitation')
			 ->where('inviter = ?', $inviter->id)
			 ->where('reciever = ?', $reciever->id);
		$ids = array();
		foreach ($invitations as $invitation) {
			$ids[] = $invitation->project_id;
		}
		$result = $this->db->table('Project')
			 ->where('owner = ?', $inviter->id)
			 ->where('status = ?', \Entity\Project::LIVE);
		// Pokud máme nějaké IDčko, které nesmíme použít, dáme ho do NOT INu
		if (count($ids) > 0) {
			$result->where('NOT id', $ids);
		}
		$projectIds = \Arrays::pickUp($result, 'id');
		return $this->findByIds($projectIds);
	}

}
