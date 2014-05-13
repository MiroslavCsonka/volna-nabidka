<?php

namespace Entity;

/**
 * Třída pro práci s entitou projektu
 */
class Project extends Base implements \MessageAble, \OwnAble, \Subject {

	protected $categories;

	/** @var \Observer[] */
	private $observers = array();

	const ATTENDED = 'attended';

	const REVOKED = 'revoked';

	const FINISHED = 'finished';

	const LIVE = 'live';

	const LOCKED = 'locked';

	const WORKING_ON = 'workingOn';

	const REWARD_PER_PROJECT = "perProject";

	const REWARD_PER_HOUR = "perHour";

	/**
	 * Získá uchazeče o projekt
	 * @param bool $justNumber Pouze číslo kolik jich je
	 * @return \Entity\User[]
	 */
	public function getAttendees($justNumber = FALSE) {
		if ($justNumber) {
			$rows = $this->dataSource->related("Project_User")->where("status = ?", self::ATTENDED)->count('id');
			return $rows;
		}
		$rels = $this->dataSource->related('Project_User')->where("status = ?", self::ATTENDED)->where(
			"project_id = ?", $this->id
		);
		$users = array();
		if ($rels instanceof \Nette\Database\Table\GroupedSelection) {
			foreach ($rels as $rel) {
				$source = $rel->user;
				$users[] = new \Entity\User($source->id, $this->db, $source);
			}
		}
		return $users;
	}

	/**
	 * Získá všechny kategorie, které náleží projektu
	 * @return array
	 */
	public function getCategories() {
		if (empty($this->categories)) {
			$rels = $this->dataSource->related("Project_SubCategory");
			if ($rels instanceof \Nette\Database\Table\GroupedSelection) {
				foreach ($rels as $rel) {
					$this->categories[$rel->category->id] = $rel->category->name;
				}
			}
			else {
				$this->categories = array();
			}
		}
		return $this->categories;
	}

	/**
	 * Vloží zprávu k projektu
	 * @param \Message        $message Obejtk zprávy
	 * @param \Entity\User    $user    Objekt Usera, odesílatel
	 * @return bool True pokud se úspěšně podařilo vložit zprávu, false pokud ne
	 * @see \MessageAble
	 */
	public function addMessage(\Message $message, $user) {
		$res = (bool)$this->db->table('Comment')->insert(
			array('from' => $user->getId(), 'project' => $this->id, 'value' => $message->getMessage(),
					'date' => $message->getDate())
		);
		return $res;
	}

	/**
	 * Získá message k projektu
	 * @return array
	 * @see \MessageAble
	 */
	public function getMessages() {
		$comments = $this->db->query("SELECT Comment.value, Comment.date, User.name, User.id AS u_id
                                    FROM Comment JOIN User ON (Comment.from = User.id)
                                    WHERE Comment.project = ?
                                    ORDER BY Comment.date", $this->id)->fetchAll();
		return $comments;
	}

	/**
	 * Získá pracovníka projektu, pokud existuje. Jako pracovník se považuje User, s kterým byl projekt dokončen
	 * @return \Entity\User|null
	 */
	public function getEmployee() {
		$row = $this->db->table('Project_User')
			 ->where('project_id = ?', $this->id)
			 ->where('status = ?', self::FINISHED)
			 ->fetch();
		$userMapper = \Nette\Environment::getContext()->userMapper;
		$employee = $userMapper->find($row->user_id);
		return $employee instanceof \Entity\User ? $employee : NULL;
	}

	/**
	 * Získá vlastníka projektu
	 * @return \Entity\User|null
	 */
	public function getOwner() {
		if ($this->offsetExists("owner")) {
			$user = $this->offsetGet("owner");
			if (is_int($user)) {
				$userMapper = new \Mapper\User($this->db);
				$userEntity = $userMapper->find($user);
				$this->offsetSet("owner", $userEntity);
			}
			return $this->offsetGet("owner");
		}
		return NULL;
	}

	/**
	 * Zjistí zda uživatel je vlastníkem projektu
	 * @param User|null $user
	 * @return bool
	 * @see \OwnAble
	 */
	public function isOwner($user) {
		if ($user instanceof \Entity\User) {
			if ($this->dataSource->owner == $user->getId()) {
				return TRUE;
			}
		}
		// Pokud nedostaneme usera, tak automaticky nemůže být vlastníkem projektu
		return FALSE;
	}

	/**
	 * Vyřadí účastníka z projektu
	 * @param int $userId
	 * @return bool TRUE pokud se vyřazení povede. FALSE pokud ne.
	 */
	public function revokeUser($userId) {
		$result = $this->db->table('Project_User')
			 ->where("project_id = ?", $this->id)
			 ->where("user_id = ?", $userId)
			 ->update(array(
							 "status" => Project::REVOKED,
						 ));
		return (bool)$result;
	}

	/**
	 * Zaktualizuje informace o projektu včetně jeho kategorií pokud jsou zadány
	 * @param array $values Asociativní pole parametrů na update
	 * @link http://forum.nette.org/cs/8844-nette-database-multi-insert#p68603 Jak má vypadat pole na multi insert
	 * @return bool TRUE pokud se update povede, FALSE pokud ne
	 */
	public function updateExtended(Array $values) {
		$deadline = \DateTime::createFromFormat('d-m-Y', $values['deadline']);
		$updateArray = array(
			\Enum::PROJECT_NAME_COL        => $values["name"],
			\Enum::PROJECT_PRICING_COL     => $values["pricing"],
			\Enum::PROJECT_REWARD_COL      => \Int::convert($values["reward"]),
			\Enum::PROJECT_DESCRIPTION_COL => $values["description"],
			\Enum::PROJECT_SCALE_COL       => $values["scale"],
			\Enum::PROJECT_DEADLINE_COL    => $deadline,
			\Enum::PROJECT_LOCATION_COL    => $values["location"]
		);
		$categories = array();
		foreach ($values['categories'] as $category) {
			$categories[] = array(
				\Enum::SUBCAT_PROJECT_COL  => $this->id,
				\Enum::SUBCAT_CATEGORY_COL => $category
			);
		}
		if (count($categories) > 0) {
			$err = 0;
			$tx = $this->db->beginTransaction();
			if (!$tx) {
				return FALSE;
			}
			$updateRes = $this->update($updateArray);
			if ($updateRes === FALSE) {
				$err++;
			}
			$deleteRes = $this->db->table('Project_SubCategory')->where('project_id = ?', $this->id)->delete();
			if ($deleteRes === FALSE) {
				$err++;
			}
			$insertRes = $this->db->table('Project_SubCategory')->insert($categories);
			if ($insertRes === FALSE) {
				$err++;
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
		return ($this->update($updateArray) === FALSE) ? FALSE : TRUE;
	}

	/**
	 * Uzamkne projekt s uživatelem $user, a odešle $userovi notifikaci $message
	 * @param \Entity\User $user    Uživatel se kterým chceme uzamknout projekt
	 * @param \Message     $message Notifikace pro uživatele
	 * @return bool
	 */
	public function lockWith(\Entity\User $user, \Message $message) {
		$tx = $this->db->beginTransaction();
		if (!$tx) {
			return FALSE;
		}
		$err = 0;
		// Uzamkneme projekt
		$res1 = (bool)$this->update(array('status' => self::LOCKED));
		if (!$res1) {
			$err++;
		}
		// Zahájíme práci s userem
		$res2 = (bool)$this->db->table('Project_User')
			 ->where('user_id = ?', $user->getId())
			 ->where('project_id = ?', $this->id)
			 ->update(array('status' => self::WORKING_ON));
		if (!$res2) {
			$err++;
		}
		// Notifikujeme usera o zahájení práce
		$this->notifyObservers($message);
		if ($err == 0) {
			$this->db->commit();
			return TRUE;
		}
		$this->db->rollBack();
		return FALSE;
	}

	/**
	 * Notifikuje observery zprávou a znevaliduje pole observerů
	 * @param \Message $message
	 * @return void
	 */
	public function notifyObservers(\Message $message) {
		foreach ($this->observers as $observer) {
			$observer->notify($message);
		}
		$this->unregisterObservers();
	}

	/**
	 * Přidá observera
	 * @param \Observer $observer
	 * @return void
	 */
	public function registerObserver(\Observer $observer) {
		$this->observers[] = $observer;
	}

	/**
	 * Odebere observery
	 * @return void
	 */
	public function unregisterObservers() {
		$this->observers = array();
	}

	/**
	 * Odešle $message
	 * @param \Entity\User $reciever User, který dostává pozvánku
	 * @param \Message     $message  Zpráva pro $recievera
	 * @return bool
	 */
	public function invite(\Entity\User $reciever, \Message $message) {
		$res = (bool)$this->db->table('Invitation')
			 ->insert(array(
							 "inviter"    => $this->offsetGet('owner'),
							 "reciever"   => $reciever->id,
							 "project_id" => $this->id
						 ));
		if ($res) {
			$this->notifyObservers($message);
		}
		return $res;
	}

	/**
	 * Dokončí projekt, vyplní referenci uživateli a pošle mu notifikaci
	 * @param \Nette\ArrayHash|array $values  Pole hodnot, které se vyplní jako reference (rating a review)
	 * @param \Entity\User          $worker  Uživatel, který na projektu pracoval jako zaměstnavatel
	 * @param \Message              $message Zpráva pro uživatele, že byl projekt dokončen
	 * @return boolean
	 */
	public function finish($values, \Entity\User $worker, \Message $message) {
		$tx = $this->db->beginTransaction();
		if (!$tx) {
			return FALSE;
		}
		$err = 0;
		$res1 = (bool)$this->update(array('status' => self::FINISHED));
		if (!$res1) {
			$err++;
		}
		$res2 = (bool)$this->db->table('Project_User')
			 ->where('project_id = ?', $this->id)
			 ->where('status = ?', self::WORKING_ON)
			 ->update(array('status' => self::FINISHED));
		if (!$res2) {
			$err++;
		}
		$referenceMapper = \Nette\Environment::getContext()->referenceMapper;
		$res3 = $referenceMapper->insert(array(
														"project_id" => $this->id,
														"owner_id"   => $this->offsetGet('owner'),
														"rated_id"   => $worker->id,
														"rating"     => \Int::convert($values['star1']),
														"comment"    => $values['review'])
		);
		if (!$res3 instanceof \Entity\Reference) {
			$err++;
		}
		if ($err == 0) {
			$this->notifyObservers($message);
			$this->db->commit();
			return TRUE;
		}
		$this->db->rollBack();
		return FALSE;
	}

	/* =========================== STATES ====================================== */

	/**
	 * Zjistí, zda je projekt zamčen nebo ne
	 * @return bool
	 */
	public function isLocked() {
		$status = $this->offsetGet('status');
		return $status == self::LOCKED ? TRUE : FALSE;
	}

	/**
	 * Zjistí, zda je Projekt dokončen
	 * @return bool
	 */
	public function isFinished() {
		$state = $this->offsetGet('status');
		return $state == self::FINISHED ? TRUE : FALSE;
	}

	/**
	 * Zjistí, zda je Projekt živý
	 * @return bool
	 */
	public function isAlive() {
		$state = $this->offsetGet('status');
		return $state == self::LIVE ? TRUE : FALSE;
	}

	/**
	 * Zjistí, zda je Projekt expirnutý
	 * @return bool
	 */
	public function isExpired() {
		$deadline = $this->offsetGet('deadline');
		/** @var $diff \DateInterval */
		$diff = $deadline->diff(new \DateTime());
		if ($diff->format('%r%d') > 0) {
			return TRUE;
		}
		return FALSE;
	}

}
