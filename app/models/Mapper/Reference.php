<?php

namespace Mapper;

/**
 * Mapper který pracuje nad Entitou \Entity\Reference
 */
class Reference extends Base {

	/**
	 * Získá reference pro $usera obalené \DataResultem
	 * @param \Entity\User $user
	 * @return \DataResult
	 */
	public function getReferencesByUser(\Entity\User $user) {
		$result = $this->db->table('Reference')
			 ->where('rated_id = ?', $user->getId())
			 ->order('id DESC');
		return new \DataResult($this, $result, 'id');
	}

}
