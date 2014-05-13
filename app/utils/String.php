<?php
/**
 * Library class na práci s textem
 */
class String {

	/**
	 * Z cesty s namespace vyseká jméno třídy
	 * @param string $fullClassName   např. Nette\Utils\String
	 * @return string                 vrátí string
	 */
	public static function getPureClassName($fullClassName) {
		$exploded = explode("\\", $fullClassName);
		return strtolower(array_pop($exploded));
	}
}
