<?php

namespace our_forms;

use Nette\Application\UI\Form, Nette\ComponentModel\IContainer;

/**
 * Formulář na přidání komentáře pod projekt
 */
class AddProjectMessage extends \BaseForm {

	public function __construct(IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		$this->addTextArea("message", "Vaše zpráva: ")->setRequired("Toto je povinné pole!")->addRule(
			Form::MIN_LENGTH, "Zpráva musí mít alespoň %d znaků!", 10
		)->addRule(Form::MAX_LENGTH, "Zpráva může mít maximálně %d znaků!", 1000);

		$this->addSubmit("submitMessage", "Poslat zprávu!");
	}
}