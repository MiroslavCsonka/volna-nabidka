<?php

namespace our_forms;

use Nette\Application\UI\Form,
	 Nette\ComponentModel\IContainer;

/**
 * Formulář na reset hesla
 */
class PasswordReset extends \BaseForm {

	public function __construct(IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		$this->addText("mail", "Email: ")
			 ->setRequired("Vyplňte email")
			 ->addRule(Form::EMAIL, "Zadejte validní email");
		$this->addSubmit("submit", "Obnovit heslo");
	}

}
