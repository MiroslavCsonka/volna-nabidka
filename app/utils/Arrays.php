<?php

/**
 * Třída, která obsahuje utility na práci s poli
 */
class Arrays {

	/**
	 * Získá všechno co je v prvním poli, ale není v druhém
	 * @param int[] $first  První pole ve kterém se bude hledat např array(1,2,3,4,5)
	 * @param int[] $second Druhé polé, podle kterého se bude hledat array(1,5)
	 * @return int[] array(2,3,4)
	 */
	public static function whatsNotIn($first, $second) {
		return array_diff($first, $second);
	}

	/**
	 * Z 2D pole vyzobe sloupec
	 * @param Traversable  $list
	 * @param string       $column   Název sloupce, ze kterého to chceme vyzobat
	 * @return array                   Vrací klasické pole
	 */
	public static function pickUp($list, $column) {
		$out = array();
		foreach ($list as $array) {
			$out[] = $array[$column];
		}
		return $out;
	}

	/**
	 * Odebere z pole duplicitní prvky (stejně jako array_unique($array))
	 * @param $array
	 * @return array
	 */
	public static function cleanDuplicity($array) {
		return array_unique($array);
	}
}
