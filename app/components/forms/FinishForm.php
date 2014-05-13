<?php

namespace our_forms;

use Nette\Application\UI\Form,
	 Nette\ComponentModel\IContainer;

/**
 * Formulář na dokončení projektu
 */
class FinishProject extends \BaseForm {

	public function __construct(IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		// Pole hodnot musí být prázdné, aby se nám nevypisoval label
		$this->addRadioList("star1", "Hodnocení pracovníka: ", array(1 => "", 2 => "", 3 => "", 4 => "", 5 => ""))
			 ->setAttribute('class', 'star')
			 ->separatorPrototype->setName(NULL);
		$this->addTextArea("review", "Ohodnoťte pár slovy pracovníka: ")
			 ->setRequired("Toto je povinná položka")
			 ->addRule(Form::MIN_LENGTH, "Délka recenze musí mít alespoň %d znaků", 15)
			 ->addRule(Form::MAX_LENGTH, "Délka recenze musí být kratší než %d znaků", 250);
		$this->addSubmit('submit', 'Dokončit projekt!');
	}

}