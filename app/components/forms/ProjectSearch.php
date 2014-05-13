<?php

namespace our_forms;

use Nette\ComponentModel\IContainer;

/**
 * Formulář na vyhledávání projektu
 */
class ProjectSearch extends \BaseForm {

	public function __construct(\Data $provider, IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		$this->addText("name", "Jméno projektu");
		$this->addHidden("min", 0);
		$this->addHidden("max");

		$this->addMultiSelect("categories", "Vyberte kategorie", $provider->getCategories());

		$this->addMultiSelect("cities", "Vyberte lokaci", $provider->getCities());

		$this->addRadioList("type", "Typ odměny:", \Data::projectRewardTypes())
			 ->separatorPrototype->setName(NULL);

		$this->addSubmit("submit", "Hledat!");
	}

}
