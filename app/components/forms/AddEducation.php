<?php

namespace our_forms;

use Nette\Application\UI\Form, Nette\ComponentModel\IContainer;

/**
 * Formulář na vzdělání
 */
class AddEducation extends \BaseForm {

	public function __construct(IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		$this->addText("name", "Jméno školy")->addRule(Form::FILLED, "Vyplňte prosím název školy");

		$this->addText("focus", "Zaměření")->addRule(Form::FILLED, "Vyplňte prosím zaměření");

		$this->addText("end", "Rok skončení")->addRule(Form::INTEGER, "Zadejte rok pouze jako číslici");

		$this->addSubmit("addEducationSubmit", "Přidat")->setAttribute("class", "btn btn-success");
	}
}
