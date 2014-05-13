<?php

namespace Entity;

/**
 * Třída pro práci s entitou notifikace
 */
class Notification extends Base implements \OwnAble {

	/**
	 * Označí notifikaci za přečtenou
	 * @return bool Zda se operace povedla či ne
	 */
	public function markRead() {
		$res = (bool)$this->update(array("status" => \Message::READ));
		return $res;
	}

	/**
	 * Označí notifikaci za nepřečtenou
	 * @return bool Zda se operace povedla či ne
	 */
	public function markUnread() {
		$res = (bool)$this->update(array("status" => \Message::UNREAD));
		return $res;
	}

	/**
	 * Označí notifikaci za smazanou
	 * @return bool Zda se operace povedla či ne
	 */
	public function markDeleted() {
		$res = (bool)$this->update(array("status" => \Message::DELETED));
		return $res;
	}

	/**
	 * Vrátí stav ve kterém se notofikace nachází: \Message::READ, \Message::UNREAD, \Message::DELETED
	 * @return string
	 */
	public function getState() {
		return $this->offsetGet('status');
	}

	/**
	 * Získá vlastníka notifikace
	 * @return \Entity\User|null
	 */
	public function getOwner() {
		if ($this->offsetExists("user_id")) {
			$user = $this->offsetGet("user_id");
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
	 * Zjistí zda uživatel je vlastníkem notifikace
	 * @param mixed $user
	 * @return bool
	 * @see \OwnAble
	 */
	public function isOwner($user) {
		if ($user instanceof \Entity\User) {
			if ($this->dataSource->user_id == $user->getId()) {
				return TRUE;
			}
			return FALSE;
		}
		// Pokud nedostaneme usera, tak automaticky nemůže být vlastníkem projektu
		return FALSE;
	}

}
