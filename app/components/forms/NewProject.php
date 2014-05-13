<?php

namespace our_forms;

use Nette\Application\UI\Form,
	 Nette\ComponentModel\IContainer;

/**
 * Formulář na nový projekt/úpravu projektu
 */
class NewProject extends \BaseForm {

	public function __construct(\Data $provider, IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);

		$this->addText("name", "Jméno projektu:")
			 ->addRule(Form::MIN_LENGTH, "Jméno projektu musí mít minimálně %d znaků", 5);
		$this->addTextArea("description", "Podrobnosti:");
		$this->addMultiSelect("categories", "Kategorie projektu:", $provider->getCategories())
			 ->setRequired("Vyplňte alespoň jednu kategorii projektu")
			 ->addRule(Form::COUNT, "Vyberte maximálně 5 kategorií", array(NULL, 5));

		$this->addSelect("location", "Místo projektu: ", $provider->getCities())
			 ->setPrompt("- Vyberte lokaci projetku -");

		$this->addSelect("scale", "Rozsah projektu:", \Data::getScale())
			 ->setPrompt("- Vyberte rozsah projektu -")
			 ->setRequired("Toto je povinná položka");

		$this->addGroup("Odměna");
		$this->addSelect("pricing", "Typ odměny:", \Data::projectRewardTypes())
			 ->setPrompt('- Vyberte typ odměny -')
			 ->setRequired("Vyplňte typ odměny");

		$this->addText("reward", "Odměna:")
			 ->addRule(Form::PATTERN, "Odměna musí být číslo", "^[0-9 ]+(kč)?$");
		$this->addText("deadline", "Datum deadlinu")
			 ->setRequired("Vyplňte datum deadlinu")
			 ->addRule(Form::PATTERN, "Zadejte validní datum (dd-mm-yyyy)", "^[0-9]{2}-[0-9]{2}-[0-9]{4}$");

		$this->addSubmit("submitAddProject");

		$this->onValidate[] = function (Form $form) {
			$datetime = \DateTime::createFromFormat('d-m-Y', $form->values->deadline);
			$diff = $datetime->diff(new \DateTime());
			if (!($datetime instanceof \DateTime)) {
				$form->addError("Datum zadáno ve špatném formátu");
			}
			// 5 - pět dní před dneškem, -3 - tři dny po dnešku
			if ($diff->format('%r%d') > 0) {
				$form->addError("Nemůžete zadat datum v minulosti");
			}
		};
	}

}
