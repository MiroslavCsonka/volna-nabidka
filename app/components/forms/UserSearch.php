<?php
namespace our_forms;

use Nette\Application\UI\Form,
	 Nette\ComponentModel\IContainer;

/**
 * Formulář na vyhledávání uživatelů
 */
class UserSearch extends \BaseForm {

	public function __construct(\Data $provider, IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		$this->addText("name", "Jméno uživatele:");
		$this->addMultiSelect("categories", "Kategorie:", $provider->getCategories());
		$this->addText("numProjects", "Počet dokončených projektů:")
			 ->addCondition(Form::FILLED)
			 ->addRule(Form::INTEGER, "Musí být zadána číselná hodnota");
		$this->addSubmit("submit", "Hledat");
		return $this;
	}
}
