<?php

namespace our_forms;

use Nette\Application\UI\Form, Nette\ComponentModel\IContainer;

/**
 * Formulář na reference do cv
 */
class OldProjectReference extends \BaseForm {

	public function __construct(IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		$this->addText("forWho", "Zaměstnavatel")
			 ->addRule(Form::FILLED, "Zadejte prosím zaměstnavatele")
			 ->addRule(Form::MIN_LENGTH, "Zaměstnavatel musí mít alespoň %d znaků", 5);

		$this->addTextArea("description", "Popiště projekt/práci")->addRUle(
			Form::FILLED, "Popis projektů/práce ja povinný!"
		);

		$this->addSubmit("addEducationSubmit", "Přidat");
	}
}
