<?php

/**
 * Wrapper nad zprávou
 */
class Message extends \Nette\Object {

	/** @var string Tělo zprávy */
	private $text;

	/** @var DateTime Datum vytvoření zprávy */
	private $date;

	/** @var string Status, který signalizuje, že zpráva není přečtena */
	const UNREAD = "unread";

	/** @var string Status, který signalizuje, že je zpráva přečtena */
	const READ = "read";

	/** @var string Status, který signalizuje, že je zpráva smazána */
	const DELETED = "deleted";

	/**
	 * @param string           $text    Tělo zprávy
	 * @param DateTime|string  $date    Datum zprávy
	 */
	public function __construct($text, $date) {
		$this->text = $text;
		$this->date = $date;
	}

	/**
	 * Získá obsah zprávy
	 * @return string
	 */
	public function getMessage() {
		return $this->text;
	}

	/**
	 * Datum kdy byla zpráva odeslána
	 * @return DateTime|string
	 */
	public function getDate() {
		return $this->date;
	}

}