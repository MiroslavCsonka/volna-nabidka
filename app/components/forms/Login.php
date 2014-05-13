<?php

namespace our_forms;

use Nette\ComponentModel\IContainer;

/**
 * Formulář na vzdělání
 */
class Login extends \BaseForm {

	public function __construct(IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);

		$this->addText("mail", "E-mail")->setRequired("");

		$this->addPassword("pass", "Heslo")->setRequired("");

		$this->addSubmit('loginSubmit', 'Přihlásit se');
	}

}
