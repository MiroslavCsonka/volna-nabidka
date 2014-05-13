<?php
/**
 * Library class na práci s čísly
 */
class Int {

	/**
	 * Převede jakýkoliv string na číslo (vyseká všechno co není číslo)
	 * @param $string
	 * @return int Výsledné číslo
	 */
	public static function convert($string) {
		$clean = preg_replace("/[^0-9]+/", "", $string);
		return ((int)$clean);
	}
}
