<?php

/**
 * Helper na práci s výsledky z modelu
 */
class DataResult {

	private $mapper;

	private $result;

	private $columnName;

	public function __construct(\Mapper\Base $mapper, Nette\Database\Table\Selection $result, $columnPickup = "id") {
		$this->mapper = $mapper;
		$this->result = $result;
		$this->columnName = $columnPickup;
	}

	/**
	 * @return int Počet výsledků
	 */
	public function getCount() {
		return $this->result->count('id');
	}

	/**
	 * Vrátí Entity s limitem od offsetu
	 * @param int $limit
	 * @param int $offset
	 * @return \Entity\Project|\Entity\User
	 */
	public function getResults($limit, $offset = 0) {
		$results = $this->result->limit((int)$limit, (int)$offset);
		$ids = \Arrays::pickUp($results, $this->columnName);
		return $this->mapper->findByIds($ids, $results);
	}

}
