<?php

namespace Mapper;

/**
 * Mapper který pracuje nad Entitou \Entity\Notification
 */
class Notification extends Base {

	/**
	 * Získá \Entity\Notification pro daného \Entity\Usera
	 * @param \Entity\User $user
	 * @param bool         $justNumber Flag, který signalizuje, zda chceme notifikace pouze jako číslo
	 * @return \Entity\Notification[]|null Vrátí pole Entit Notifikací, nebo null, pokud uživatel žádné nemá
	 */
	public function getNotificationsByUser(\Entity\User $user, $justNumber = FALSE) {
		if ($justNumber) {
			return $this->db->table('Notification')
				 ->where('user_id = ?', $user->getId())
				 ->where('status = ?', \Message::UNREAD)->count('id');
		}
		$result = $this->db->table('Notification')
			 ->where('user_id = ?', $user->getId())
			 ->where('status != ?', \Message::DELETED)
			 ->order('date DESC');
		$out = array();
		foreach ($result as $notification) {
			$out[] = $this->makeInstanceFromId($notification->id, $notification);
		}
		;
		return $out;
	}

}
