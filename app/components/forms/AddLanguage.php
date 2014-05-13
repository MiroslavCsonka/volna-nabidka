<?php

namespace our_forms;

use Nette\Application\UI\Form,
	 Nette\ComponentModel\IContainer;

/**
 * Formulář na jazyk
 */
class AddLanguage extends \BaseForm {

	public function __construct(\Data $provider, IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		$this->addSelect("language", "Jazyk", $provider->getLanguages())->setPrompt("Vyberte jazyk")->addRule(
			Form::FILLED, "Vyplňte prosím jazyk"
		)->setAttribute("class", "chosen");

		$this->addSelect("level", "Úroveň", \Data::getLevels())->setPrompt("Vyberte obtížnost")->addRule(
			Form::FILLED, "Vyplňte prosím úroveň"
		)->setAttribute("class", "chosen");

		$this->addSubmit("addEducationSubmit", "Přidat")->setAttribute("class", "btn btn-success");
	}

}
