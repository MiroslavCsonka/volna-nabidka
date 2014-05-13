<?php
namespace Mapper;

/**
 * Mapper který pracuje nad Entitou \Entity\Thread
 */
class Thread extends Base {

	/**
	 * Získá společné vlákno pro určité uživatele
	 * @param array $users Pole uživatelů, pro které chceme thread
	 * @return \Entity\Thread
	 * @throws \Exception
	 */
	public function getThreadByUsers(array $users) {
		$ids = array();
		foreach ($users as $user) {
			$ids [] = ($user instanceof \Entity\User) ? intval($user->id) : intval($user);
		}
		if (empty($ids)) throw new \Exception('Nebyl dodán seznam validních uživatelů');
		sort($ids, SORT_NUMERIC);
		$sth = $this->db->query('
			SELECT *
			FROM `Group` JOIN (
				SELECT `Group`.`thread_id` AS `threadId`, COUNT(`Group`.`user_id`) AS `usersPerThread`
				FROM `Group`
				WHERE `Group`.`user_id` IN (' . implode(',', $ids) . ')
				GROUP BY `Group`.`thread_id`
				HAVING usersPerThread = ' . count($ids) . '
			) as `threadsInCommon` ON (`Group`.`thread_id` = `threadsInCommon`.`threadId`)
			ORDER BY `Group`.`user_id`
		');
		$result = $sth->fetchAll();
		$threads = array();
		foreach ($result as $row) {
			$threads[$row->thread_id][] = $row->user_id;
		}
		foreach ($threads as $threadId => $users) {
			if ($ids === $users) { // Check if threads have same users
				$row = $this->db->table('Thread')->where('id', $threadId)->fetch();
				return $this->makeInstanceFromId($threadId, $row);
			}
		}
		$newThreadId = $this->createThreadWith($ids);
		$row = $this->db->table('Thread')->where('id', $newThreadId)->fetch();
		return $this->makeInstanceFromId($newThreadId, $row);
	}

	/**
	 * Vytvoří konverzaci s nějaký uživatelem
	 * @param array $users      Pole uživatelů mezi kterými se má vytvořit dané vlákno
	 * @return int|bool      Vráci ID Vlákna nebo false
	 */
	private function createThreadWith(array $users) {
		$status = $this->db->beginTransaction();
		if (!$status) return FALSE;

		$insertedRow = $this->db->table('Thread')->insert(array());
		if ($insertedRow instanceof \Nette\Database\Table\ActiveRow) {
			try {
				foreach ($users as $user) {
					$userId = ($user instanceof \Entity\User) ? intval($user->id) : intval($user);
					$this->db->table('Group')->insert(
						array('thread_id' => $insertedRow->id, 'user_id' => $userId));
				}
				$this->db->commit();
				return $insertedRow->id;
			} catch (\Exception $e) {
				$this->db->rollBack();
				return FALSE;
			}
		}
		$this->db->rollBack();
		return FALSE;
	}

	/**
	 * Získá uživatelova vlákna
	 * @param \Entity\User|int $user
	 * @return array
	 */
	public function getThreadsByUser($user) {
		$id = ($user instanceof \Entity\User) ? $user->id : intval($user);
		$threads = $this->db->table('Group')->where('user_id', $id);
		$out = array();
		foreach ($threads as $thread) {
			$out[$thread->thread_id] = $this->makeInstanceFromId($thread->thread_id, $thread);
		}
		return $out;
	}
}
