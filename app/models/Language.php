<?php

/**
 * Jednoduchý store na ID
 */
class Language extends \Nette\Object {

	/** @var int ID záznamu */
	private $id;

	/**
	 * @param int $id ID jazyku o kterém mluvíme
	 */
	public function __construct($id) {
		$this->id = $id;
	}

	/**
	 * ID jazyku
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

}
