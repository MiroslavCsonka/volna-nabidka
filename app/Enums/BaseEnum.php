<?php

class BaseEnum {

	/**
	 * Zjistí zda byla kostanta definována
	 * @param $constant   Jméno konstanty která má být skontrolována
	 * @return bool       Zda existuje či nikoliv
	 */
	protected final static function checkIfExists($constant) {
		$className = get_called_class();
		return (defined("$className::$constant")) ? TRUE : FALSE;
	}
}
