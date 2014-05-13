<?php

/**
 * Třída, která provádí početní operace nad levely a expy
 */
class Level {

	/** @var int Číslo, kterým násobíme získáné expy, aby jsme se pohybovali v řádech stovek */
	const MULTIPLIER = 100;

	/**
	 * Zjistí, kolik je třeba zkušenostních bodů na level $level
	 * @param int $level Level
	 * @throws \Nette\ArgumentOutOfRangeException Pokud je $level negativní nebo roven 0
	 * @return int Kolik expů je potřeba na daný level
	 */
	public static function getExpByLevel($level) {
		/**
		 * Example:
		 * (a)n = ((n^2 + 3n - 4)/2) * self::MULTIPLIER
		 * a1 = 0
		 * a2 = 300
		 * a3 = 700
		 * a4 = 1200
		 * a5 = 1800
		 */
		if ($level <= 0) {
			throw new \Nette\ArgumentOutOfRangeException("Level cannot be negative or zero");
		}
		return ((pow($level, 2) + 3 * $level - 4) / 2) * self::MULTIPLIER;
	}

	/**
	 * Vrátí level na základě zkušenostních bodů
	 * @param int $exp Zkušenostní body
	 * @throws ErrorException Pokud návratová hodnota není kladné číslo
	 * @throws \Nette\ArgumentOutOfRangeException Pokud $exp je záporné číslo
	 * @return int
	 */
	public static function getLevelByExp($exp) {
		/*
		 * Example:
		 * Vstup: 2300 expů
		 * (n^2+3n-4)/2 * 100 = 2300
		 * Vykrátíme:
		 * 50(n^2+3n-4) = 2300 /:50
		 * n^2+3n-4 = 46 /-46
		 * n^2+3n-50 = 0 // Kvadratická rovnice
		 * Diskriminant = 9 - 4(-50) = 209
		 * root(Diskriminant) = 14,45
		 * n1,2 = (-3 +- 14,45)/2
		 * n1 = 11,5/2 = 5,75 // Vyhovuje Tuto hodnotu vrátíme
		 * n2 = -17.5/2 // Nevyhovuje (zápornej počet levelů)
		 */
		if ($exp < 0) {
			throw new \Nette\ArgumentOutOfRangeException("Unexpected value $exp");
		}
		$multiplier = self::MULTIPLIER / 2;
		$diff = $exp / $multiplier;
		$c = -4 - $diff;
		$discriminant = 9 - 4 * ($c);
		$rootOfDiscrimamnt = sqrt($discriminant);
		$n1 = (-3 + $rootOfDiscrimamnt) / 2;
		if ($n1 > 0) {
			return $n1;
		}
		throw new ErrorException("Level is not positive");
	}

}
