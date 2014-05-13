<?php

namespace Mapper;

use Nette\Database\Table\ActiveRow,
	 Nette\Database\Connection,
	 Nette\Database\Table\Selection;

/**
 * Předek všech \Mapperů předává jim abstraktní funkcionalitu kterou by všichni měli mít
 */
abstract class Base extends \Nette\Object {

	/** @var Connection */
	protected $db;

	/** @var string Jméno hlavní databázové tabulky nad kterou se provádí operace daného typu */
	protected $tableName;

	/** @var Selection Primární tabulka nad kterou jdou rovnou provádět operace */
	protected $table;

	/**
	 * Připravý mapper na práci nad entitami
	 * @param Connection $db
	 */
	public function __construct(Connection $db) {
		$this->db = $db;
		$this->tableName = ucfirst(\String::getPureClassName(get_called_class()));
		$this->table = $this->db->table($this->tableName);
	}

	/**
	 * Najde entitu podle primárního klíče
	 * @param int $id
	 * @return \Entity\Project|\Entity\User|null
	 */
	public function find($id) {
		$dataSource = $this->db->table($this->tableName)->find($id)->fetch();
		if ($dataSource instanceof ActiveRow) {
			return $this->makeInstanceFromId($id, $dataSource);
		}
		return NULL;
	}

	/**
	 * Získá entity na základě pole IDček
	 * @param array                           $ids
	 * @param \Nette\Database\Table\Selection $selection
	 * @return array
	 */
	public function findByIds($ids, Selection $selection = NULL) {
		if ($selection === NULL) {
			$selection = $this->db->table($this->tableName)->where('id', $ids);
		}
		$out = array();
		foreach ($selection as $row) {
			$out[$row->id] = $this->makeInstanceFromId($row->id, $row);
		}
		return $out;
	}

	/**
	 * Vytvoří entitu a získá rovnou její instanci
	 * @param string[string] $values
	 * @return \Entity\Base|null
	 */
	public function insert($values) {
		try {
			$dataSource = $this->table->insert($values);
			if ($dataSource instanceof ActiveRow) {
				return $this->makeInstanceFromId($dataSource->id, $dataSource);
			}
		} catch (\PDOException $e) {
		}
		return NULL;
	}

	/**
	 * Vytvoří entitu
	 * @param int           $id
	 * @param ActiveRow     $dataSource
	 * @return \Entity\Base
	 */
	public function makeInstanceFromId($id, ActiveRow $dataSource) {
		$className = "Entity\\$this->tableName";
		return new $className((int)$id, $this->db, $dataSource);
	}

	/**
	 * Zjistí počet záznamů pro danou entitu
	 * @return int
	 */
	public function getCount() {
		return $this->table->count();
	}

	/**
	 * Získá VŠECHNY potomky dané entity
	 * @return array
	 */
	public function getAll() {
		$return = array();
		foreach ($this->db->table($this->tableName) as $entity) {
			$return[$entity->id] = $this->makeInstanceFromId($entity->id, $entity);
		}
		return $return;
	}

}
