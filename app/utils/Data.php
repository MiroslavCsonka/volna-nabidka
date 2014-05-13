<?php

/**
 * Třída, která poskytuje data
 */
class Data {

	private static $rewardType = array(
		"perProject" => "Odměna za projekt", "perHour" => "Hodinová sazba"
	);

	private $db;

	public function __construct(\Nette\Database\Connection $db) {
		$this->db = $db;
	}

	/**
	 * Vrací 2d pole. 1. dimenze jsou categorie, 2. dimenze jsou subcategorie a jejich IDčko jako klíč.
	 * @return array 2d pole
	 */
	public function getCategories() {
		$toSelect = array();
		foreach ($this->db->table('Category') as $category) {
			foreach ($category->related('SubCategory') as $subCategory) {
				$toSelect[$category['name']][$subCategory['id']] = $subCategory['name'];
			}
		}
		return $toSelect;
	}

	/**
	 * Vrátí pole hlavních kategorií. Pokud je $firstEmpty TRUE. Tak první prvek bude: "- Vyberte -"
	 * @param bool $firstEmpty
	 * @return array
	 */
	public function getMainCategories($firstEmpty = FALSE) {
		$mainCategories = array();
		if ($firstEmpty) {
			$mainCategories["empty"] = "- Vyberte -";
		}
		foreach ($this->db->table('Category') as $category) {
			$mainCategories[$category->id] = $category->name;
		}
		return $mainCategories;
	}

	/**
	 * Získá informace o jazyku
	 * @return array
	 */
	public function getLanguages() {
		$out = array();
		$result = $this->db->table('Language')->fetchPairs("id", "name");
		foreach ($result as $id => $name) {
			$out[$id] = $name;
		}
		return $out;
	}

	/**
	 * Získá všechna města
	 * @return array
	 */
	public function getCities() {
		$out = array();
		$result = $this->db->table('City')->order('id')->fetchPairs('id', 'name');
		foreach ($result as $id => $name) {
			$out[$id] = $name;
		}
		return $out;
	}

	/**
	 * Získá informace o obtížnostních
	 * @return array
	 */
	public static function getLevels() {
		return array(
			0 => "",
			1 => "Začátečník",
			2 => "Středně pokročilý",
			3 => "Pokročilý",
			4 => "Rodilý mluvčí"
		);
	}

	/**
	 * Vrátí level, náležící číslu
	 * @param int $id
	 * @return string
	 * @throws Nette\UnexpectedValueException
	 */
	public static function getLevel($id) {
		switch ($id) {
			case 1:
				return "Začátečník";
			case 2:
				return "Středně pokročilý";
			case 3:
				return "Pokročilý";
			case 4:
				return "Rodilý mluvčí";
			default:
				throw new \Nette\UnexpectedValueException("Data::getLevel called with '$id' ");
		}
	}

	/**
	 * Vrátí pole typů odměn
	 * @param bool $toSelect
	 * @param bool $firstEmpty
	 * @return array
	 */
	public static function projectRewardTypes() {
		return self::$rewardType;
	}

	/**
	 * Vrátí pole měsíců
	 * @return array
	 */
	public static function getMonths() {
		return array(
			1 => "Leden", 2 => "Únor", 3 => "Březen", 4 => "Duben", 5 => "Květen", 6 => "Červen",
			7 => "Červenec", 8 => "Srpen", 9 => "Září", 10 => "Říjen", 11 => "Listopad", 12 => "Prosinec"
		);
	}

	/**
	 * Vrátí pole rozsahu projektu
	 * @return array
	 */
	public static function getScale() {
		return array(
			1 => "Malý",
			2 => "Střední",
			3 => "Velký",
		);
	}

}

