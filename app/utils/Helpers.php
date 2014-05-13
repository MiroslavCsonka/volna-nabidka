<?php

/**
 * Třída obsahující helpery pro šablony
 */
class Helpers {

	/**
	 * "Brána" ke všem helperům (pokaždé se zavolá tato metoda a ta vrátí pouze výsledek dané metody)
	 * @param string $helper Název metody k zavolání
	 * @return \Nette\Callback|NULL
	 */
	public static function loader($helper) {
		if (method_exists(__CLASS__, $helper)) {
			return callback(__CLASS__, $helper);
		}
	}

	/**
	 * Danou hodnotu z enumu, přeloží na rozumný ekvivalent
	 * @param string $string   strojová verze stringu
	 * @return string          česká verze stringu
	 * @throws Exception
	 */
	public static function decodeEnum($string) {
		switch ($string) {
			case "perProject":
				return "za projekt";
			case "perHour":
				return "za hodinu";
			default:
				throw new Exception("Unsupported ENUM value '$string' register it at Helpers::decodeEnum please");
		}
	}

	/**
	 * Převede jakýkoliv formát na český styl datumu
	 * @param string $string  Vstupní datum
	 * @return string   1.1.2012
	 */
	public static function czechDate($string) {
		$seconds = strtotime($string);
		return date('j. n. Y', $seconds);
	}

	/**
	 * Převede jakýkoliv formát na český styl datumu se sekundami
	 * @param string $string  Vstupní datum
	 * @return string   1.1.2012 20:55
	 */
	public static function czechDateTime($string) {
		$seconds = strtotime($string);
		return date('j. n. Y H:i', $seconds);
	}

	/**
	 * Vyčístí z HTML nebezpečné znaky
	 * @param string $string
	 * @return string
	 * @see HTMLPurifier
	 */
	public static function purify($string) {
		return \Nette\Environment::getContext()->HTMLPurifier->purify($string);
	}

	/**
	 * Zjistí rozmezí mezi datumem a nyní
	 * @param string $date
	 * @return string    Počet dní
	 */
	public static function countRemaininig($date) {
		$datetime1 = new DateTime('now');
		$datetime2 = new DateTime($date);
		$interval = $datetime1->diff($datetime2);
		$inDays = $interval->format('%R%a days') + 1;
		return $inDays;
	}

	/**
	 * @param $int
	 * @return number int
	 */
	public static function abs($int) {
		return abs($int);
	}

	/**
	 * Zjistí cestu k obrázku (může ověřit práva cokoliv)
	 * @param int          $id          ID dané entity
	 * @param string       $entity      Entita která je hledaná
	 * @param string       $size        Velikost obrázku (profile, mini, thumbnail)
	 * @return string
	 */
	public static function picture($id, $entity, $size = "profile") {
		$notFound = "/images/image_not_found.jpg";
		switch ($entity) {
			case "user":
				if ($size == "profile") {
					$path = "/images/profile";
					$path .= "/$id.png";
					if (file_exists(WWW_DIR . $path)) {
						return $path;
					}
				}
				if ($size == "mini") {
					$path = "/images/mini";
					$path .= "/$id.png";
					if (file_exists(WWW_DIR . $path)) {
						return $path;
					}
				}
				if ($size == "thumbnail") {
					$path = "/images/thumbnail";
					$path .= "/$id.png";
					if (file_exists(WWW_DIR . $path)) {
						return $path;
					}
				}
			default:
				return $notFound;
		}
	}

	/**
	 * Zjistí jakou koncovku má podle počtu dní přidat
	 * @param string $days
	 * @return string   (den,dny,dní) podle číslovky $days
	 * @throws Exception
	 */
	public static function days($days) {
		if ($days == 1 || $days == 0) {
			return $days . " den";
		}
		if ($days >= 2 && $days < 5) {
			return $days . " dny";
		}
		if ($days >= 5) {
			return $days . " dní";
		}
		if ($days < 0) {
			throw new Exception("Nemůže být záporný počet dní");
		}
	}

	public static function timeAgoInWords($time) {
		if (!$time) {
			return FALSE;
		}
		elseif (is_numeric($time)) {
			$time = (int)$time;
		}
		elseif ($time instanceof DateTime) {
			$time = $time->format('U');
		}
		else {
			$time = strtotime($time);
		}

		$delta = time() - $time;

		if ($delta < 0) {
			$delta = round(abs($delta) / 60);
			if ($delta == 0) return 'za okamžik';
			if ($delta == 1) return 'za minutu';
			if ($delta < 45) return 'za ' . $delta . ' ' . self::plural($delta, 'minuta', 'minuty', 'minut');
			if ($delta < 90) return 'za hodinu';
			if ($delta < 1440) return 'za ' . round($delta / 60) . ' ' . self::plural(round($delta / 60), 'hodina', 'hodiny', 'hodin');
			if ($delta < 2880) return 'zítra';
			if ($delta < 43200) return 'za ' . round($delta / 1440) . ' ' . self::plural(round($delta / 1440), 'den', 'dny', 'dní');
			if ($delta < 86400) return 'za měsíc';
			if ($delta < 525960) return 'za ' . round($delta / 43200) . ' ' . self::plural(round($delta / 43200), 'měsíc', 'měsíce', 'měsíců');
			if ($delta < 1051920) return 'za rok';
			return 'za ' . round($delta / 525960) . ' ' . self::plural(round($delta / 525960), 'rok', 'roky', 'let');
		}

		$delta = round($delta / 60);
		if ($delta == 0) return 'před okamžikem';
		if ($delta == 1) return 'před minutou';
		if ($delta < 45) return "před $delta minutami";
		if ($delta < 90) return 'před hodinou';
		if ($delta < 1440) return 'před ' . round($delta / 60) . ' hodinami';
		if ($delta < 2880) return 'včera';
		if ($delta < 43200) return 'před ' . round($delta / 1440) . ' dny';
		if ($delta < 86400) return 'před měsícem';
		if ($delta < 525960) return 'před ' . round($delta / 43200) . ' měsíci';
		if ($delta < 1051920) return 'před rokem';
		return 'před ' . round($delta / 525960) . ' lety';
	}


	/**
	 * Plural: three forms, special cases for 1 and 2, 3, 4.
	 * (Slavic family: Slovak, Czech)
	 * @param  int
	 * @return mixed
	 */
	private static function plural($n) {
		$args = func_get_args();
		return $args[($n == 1) ? 1 : (($n >= 2 && $n <= 4) ? 2 : 3)];
	}
}
