<?php

namespace Entity;

use Nette\Database\Table\ActiveRow,
	 Nette\Database\Connection,
	 Nette\Object;

/**
 * Předek všech \Entit předává jim abstraktní funkcionalitu kterou by všichni měli mít
 */
abstract class Base extends Object implements \ArrayAccess {

	/** @var int ID záznamu */
	protected $id;

	/** @var Connection Připojení k databázi */
	protected $db;

	/** @var $dataSource \Nette\Database\Table\ActiveRow Rádek z databáze podle kterého se dá určit takzvaný GroupedSelection (na provádění hromadných dotazů a ne cyklických) */
	protected $dataSource;

	/** @var string Jméno hlavní databázové tabulky dané entity */
	protected $tableName;

	/** @var array Assoc pole základních informací o dané entitě */
	protected $data;

	/**
	 * Nastaví informace do attributů
	 * @param  int                               $id
	 * @param \Nette\Database\Connection         $db
	 * @param \Nette\Database\Table\ActiveRow    $dataSource
	 */
	public function __construct($id, Connection $db, ActiveRow $dataSource) {
		$this->id = $id;
		$this->db = $db;
		$this->dataSource = $dataSource;
		$className = \String::getPureClassName(get_called_class());
		$this->tableName = ucfirst($className);
		$this->data = iterator_to_array($this->dataSource);
	}

	/**
	 * Zjistí ID dané entity
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Aktualizuje základní informace o entitě
	 * @param string[string] $values
	 * @return bool|int Počet ovlivněných řádků. FALSE on error
	 */
	public function update($values) {
		return $this->dataSource->update($values);
	}

	/**
	 * Odebere entitu na základě primárního klíče
	 * @param $id int Primární klíč
	 * @return int Počet ovlivněných řádku nebo FALSE při chybě
	 */
	public function delete($id = NULL) {
		if (is_null($id)) {
			return $this->dataSource->delete();
		}
		return $this->db->table($this->tableName)->find($id)->delete();
	}

	/**
	 * Zjistí zda je setnutý daný index
	 * @param string|int $offset
	 * @return bool
	 */
	public function offsetExists($offset) {
		return (bool)(array_key_exists($offset, $this->data));
	}

	/**
	 * Získá hodnotu pod daným indexem
	 * @param mixed $offset
	 * @throws \Exception
	 * @return mixed|null
	 */
	public function &offsetGet($offset) {
		if ($this->offsetExists($offset)) {
			return $this->data[$offset];
		}
		else {
			throw new \Exception("NOT FOUNT INDEX '$offset'");
		}
	}

	/**
	 * Nastaví položce danou hodnotu
	 * @param string|int $offset   Index
	 * @param mixed      $value    Hodnota
	 */
	public function offsetSet($offset, $value) {
		$this->data[$offset] = $value;
	}

	/**
	 * Provede unset dané hodnoty
	 * @param mixed $offset
	 */
	public function offsetUnset($offset) {
		unset($this->data[$offset]);
	}

	/**
	 * Slouží k používání $object->neExistujícíAttribut
	 * @param string $name
	 * @return mixed|null
	 */
	public function &__get($name) {
		if ($this->offsetExists($name)) {
			return $this->offsetGet($name);
		}
		return parent::__get($name);
	}

}
