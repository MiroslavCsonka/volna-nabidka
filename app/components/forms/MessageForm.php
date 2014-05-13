<?php

namespace our_forms;

use Nette\Application\UI\Form, Nette\ComponentModel\IContainer;

/**
 * Formulář na přidání zprývy
 */
class MessageForm extends \BaseForm {

	public function __construct(IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		$this->addText("user", "Pro: ")
			 ->addRule(Form::FILLED, "Vyplňtě jméno příjemce");

		$this->addTextArea("text", "Obsah: ")
			 ->setRequired("Vyplňte tělo zprávy")
			 ->addRule(Form::MAX_LENGTH, "Zpráva může mít maximálně %d znaků!", 1000);
		$this->addSubmit("submit", "Odeslat zprávu");

		$this->onValidate[] = function ($form) {
			$user = $form->presenter->context->userMapper->getByName($form->values->user);
			if ($user === NULL) $form->addError("Uživatel neexistuje");
		};
	}
}
