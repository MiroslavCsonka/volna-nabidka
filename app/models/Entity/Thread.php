<?php
namespace Entity;

/**
 * Třída pro práci s entitou threadu
 */
class Thread extends Base implements \MessageAble {

	public function getUsers() {
		$rows = $this->db->table('Group')
			 ->select('user_id')
			 ->where('thread_id', $this->id);
		$userIds = \Arrays::pickUp($rows, 'user_id');
		return \Nette\Environment::getContext()->userMapper->findByIds($userIds);
	}


	/**
	 * Přidá zprávu subjektu
	 * @param \Message              $message      Content of message
	 * @param User|int              $user         SENDER OF MESSAGE
	 * @return mixed
	 */
	public function addMessage(\Message $message, $user) {
		$userId = ($user instanceof User) ? $user->id : intval($user);
		$groupId = $this->db->table('Group')
			 ->where('thread_id', $this->id)
			 ->where('user_id', $userId)
			 ->fetch()
			 ->id;
		try {
			$this->db->beginTransaction();
			$activeRow = $this->db->table('Message')->insert(
				array('group_id' => $groupId, 'content' => $message->getMessage(), 'date' => $message->getDate())
			);
			$this->db->table('Group')->where('thread_id', $this->id)->update(
				array('last_id' => $activeRow->id)
			);
			$this->db->table('Group')->where('thread_id', $this->id)->where('user_id', $userId)->update(
				array('lastRead_id' => $activeRow->id)
			);
			return (bool)$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollBack();
			return FALSE;
		}
	}

	/**
	 * Získá všechny zprávy z vlákna
	 * @return \Nette\Database\Table\Selection
	 */
	public function getMessages() {
		$id = intval($this->id);
		return $this->db->query("
			SELECT *
			FROM `Group` JOIN `Message` ON (`Group`.`id` = `Message`.`group_id`)
					JOIN `User` ON (`User`.`id` = `Group`.`user_id`)
			WHERE `Group`.`thread_id` = $id
			ORDER BY `date` DESC
		")->fetchAll();
	}

	/**
	 * Zjistí zda uživatel je účastníkem vlákna
	 * @param $user
	 * @return bool
	 */
	public function isUserAtThread($user) {
		$id = ($user instanceof User) ? $user->id : intval($user);
		return (bool)$this->db->table('Group')->where('user_id', $id)->where('thread_id', $this->id)->count();
	}

	/**
	 * Získá poslední zprávu z vlákna
	 * @return \Nette\Database\Table\ActiveRow
	 */
	public function getLastMessage() {
		$threadId = intval($this->id);
		$result = $this->db->query("
			SELECT *
			FROM `Group` JOIN `Message` ON (`Group`.`last_id` = `Message`.`id`)
					JOIN `User` ON (`User`.`id` = `Group`.`user_id`)
			WHERE `Group`.`thread_id` = $threadId
			LIMIT 0,1
		");
		return $result->fetch();
	}

	/**
	 * Označí zpravy za přečtené pro uživatele
	 * @param $user
	 * @return bool
	 */
	public function markAsReadByUser($user) {
		$id = ($user instanceof \Entity\User) ? $user->id : intval($user);
		return (bool)$this->db->table('Group')->where('thread_id', $this->id)->where('user_id', $id)->update(
			array('lastRead_id' => new \Nette\Database\SqlLiteral('`last_id`'))
		);
	}
}

