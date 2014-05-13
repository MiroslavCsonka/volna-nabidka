<?php

namespace our_forms;

use Nette\Application\UI, Nette\ComponentModel\IContainer;

/**
 * Registrační formulář
 */
class Register extends \BaseForm {

	/**
	 * @param IContainer   $parent
	 * @param null         $name
	 */
	public function __construct(IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);

		$this->addText('mail', 'Email')
			 ->setRequired('Email musí být vyplněn')
			 ->addRule(UI\Form::EMAIL, 'Zadejte prosím validní email');

		$this->addText('name', 'Uživatelské jméno')
			 ->setRequired('Jméno musí být vyplněno')
			 ->addRule(UI\Form::REGEXP, 'Zadávejte pouze správné znaky (a-Z, A-Z, 0-9)', "/^[a-zA-Z0-9]+$/")
			 ->addRule(UI\Form::MIN_LENGTH, 'Minimální délka jména je %d znaků', 5);

		$this->addSelect("gender", "Pohlaví", \GenderEnum::getAll())
			 ->setPrompt("- Zvolte pohlaví -");

		$this->addPassword('pass', 'Heslo')
			 ->setRequired('Zadejte prosím heslo')
			 ->addRule(UI\Form::MIN_LENGTH, 'Heslo musí mít minimálně %d znaků', 5);

		$this->addPassword('passwordAgain', 'Heslo znovu')
			 ->setRequired('Zadejte prosím heslo pro kontrolu')
			 ->addRule(UI\Form::EQUAL, 'Hesla se musí shodovat', $this['pass']);

		$this->addSubmit('submitRegister', 'Dokončit registraci')
			 ->setAttribute('class', 'btn btn-success');
	}
}
