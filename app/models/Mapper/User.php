<?php

namespace Mapper;

/**
 * Mapper který pracuje nad Entitou \Entity\User
 */
class User extends Base {

	/**
	 * Získá \DataResult pro všechny Usery
	 * @return \DataResult
	 */
	public function getAll() {
		$users = $this->db->table('User');
		return new \DataResult($this, $users);
	}

	/** Získá uživatele na základě jména
	 * @param string $mail
	 * @return \Entity\User|null
	 */
	public function getByEmail($mail) {
		$row = $this->db->table("User")->where("mail LIKE ?", "$mail")->fetch();
		if ($row) {
			return $this->makeInstanceFromId($row->id, $row);
		}
		return NULL;
	}

	/** Získá uživatele na základě jména
	 * @param string $name
	 * @return \Entity\User|null
	 */
	public function getByName($name) {
		$row = $this->db->table("User")->where("name LIKE ?", "$name")->fetch();
		if ($row) {
			return $this->makeInstanceFromId($row->id, $row);
		}
		return NULL;
	}

	/**
	 * Získá všechny projekty na základě filtru
	 * @param array $params Asociativní pole parametrů
	 * @return \DataResult
	 */
	public function getByFilter(Array $params) {
		// Unset prázdných položek
		foreach ($params as $key => $value) {
			if (empty($value)) {
				unset($params[$key]);
			}
		}
		$result = $this->db->table('User');
		if (isset($params['categories'])) {
			$userSubcategory = $this->db->table('User_SubCategory')->where('subCategory_id', array_values($params['categories']));
			$userIds = \Arrays::pickUp($userSubcategory, 'user_id');
			$result->where('id', $userIds);
		}
		if (isset($params['name'])) {
			$result->where('name LIKE ?', "%" . $params['name'] . "%");
		}
		if (isset($params['numProjects'])) {
			$projectUser = $this->db
				 ->query("SELECT uid
                                       FROM
                                          (
                                          SELECT user_id AS uid, COUNT(STATUS) AS cnt
                                          FROM Project_User
                                          WHERE STATUS = 'finished'
                                          GROUP BY user_id
                                          ) AS derived
                                       WHERE cnt >= ?", $params['numProjects'])->fetchAll();
			$ids = array();
			foreach ($projectUser as $row) {
				$ids[] = $row->uid;
			}
			$result->where('id', $ids);
		}
		return new \DataResult($this, $result);
	}

	/** Zaregistruje nového uživatele, nastaví mu základní hodnoty
	 * @param $values
	 * @return \Entity\Project|\Entity\User
	 */
	public function register($values) {
		// TODO :: jestli se rozhodneme mít check přes email, tady mít 0
		$values->activated = 1;
		$values->role = \Entity\User::DEFAULT_ROLE;
		return $this->insert($values);
	}

	/**
	 * Získá všechny nicky uživatelů
	 * @param string $key Kousek jména
	 * @return array
	 */
	public function getNicks($key) {
		$out = array();
		$users = $this->db->table('User')->select('id,name');
		if ($key) {
			$users->where('name LIKE ?', "%$key%");
		}
		foreach ($users as $nick) {
			$out[$nick->id] = $nick->name;
		}
		return $out;
	}

	/**
	 * Získá pracovníka projektu
	 * @param \Entity\Project $project
	 * @return \Entity\User|null Vrací pracovníka projektu, nebo null, pokud neni nalezen.
	 */
	public function getWorkerOf(\Entity\Project $project) {
		$res = $this->db->table('Project_User')
			 ->where('project_id = ?', $project->getId())
			 ->where('status = ?', \Entity\Project::WORKING_ON)->fetch();
		$user = $this->find($res->user_id);
		if ($user instanceof \Entity\User) {
			return $user;
		}
		return NULL;
	}

}
