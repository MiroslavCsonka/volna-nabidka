<?php

/**
 * Model zastřešující práci nad 'vzděláním'
 */
class Education {

	/** @var \Nette\Database\Connection */
	private $db;

	public function __construct(\Nette\Database\Connection $db) {
		$this->db = $db;
	}

	/**
	 * Přidá záznam o studiu
	 * @param array $arguments
	 * @return bool
	 */
	public function add($arguments) {
		$res = (bool)$this->db->table('Education')->insert($arguments);
		return $res;
	}

	/**
	 * Odebere jistému vlastníku aktuální vzdělání
	 * @param int          $id    ID záznamu
	 * @param \Entity\User $owner Vlastník vzdělání
	 * @return bool
	 */
	public function delete($id, \Entity\User $owner) {
		$res = (bool)$this->db->table('Education')
			 ->where('id = ?', $id)
			 ->where('user_id = ?', $owner->getId())
			 ->delete();
		return $res;
	}

}
