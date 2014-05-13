<?php

/**
 * Statická třída na práci s ENUM položkami
 */
class GenderEnum extends BaseEnum {

	const __default = GenderEnum::DUNNO;

	const MALE = "male";

	const FEMALE = "female";

	const DUNNO = "dunno";

	private static $translates = array(
		self::MALE   => "Muž",
		self::FEMALE => "Žena",
		self::DUNNO  => "Jiné",
	);

	/**
	 * @param string $index
	 * @return array
	 */
	public static function getAll($index = "") {
		if ($index !== "") {
			self::checkIfExists($index);
			return self::$translates[$index];
		}
		else {
			return self::$translates;
		}
	}

}
