<?php

/**
 * Model zastřešující akce nad starými referencemi
 */
class OldReference {

	/** @var \Nette\Database\Connection */
	private $db;

	/**
	 * @param Nette\Database\Connection $db
	 */
	public function __construct(\Nette\Database\Connection $db) {
		$this->db = $db;
	}

	/**
	 * Odebere uživateli danou referenci
	 * @param int          $id    ID reference
	 * @param \Entity\User $owner Vlastník reference
	 * @return bool TRUE pokud se operace povedla, FALSE pokud ne.
	 */
	public function delete($id, \Entity\User $owner) {
		$res = (bool)$this->db->table('OldProject')
			 ->where('id = ?', $id)
			 ->where('user_id = ?', $owner->getId())
			 ->delete();
		return $res;
	}

	/**
	 * Vloží nový záznam o referenci
	 * @param array $args
	 * @return bool
	 */
	public function create($args) {
		$res = (bool)$this->db->table('OldProject')->insert($args);
		return $res;
	}

}
