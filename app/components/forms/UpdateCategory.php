<?php

namespace our_forms;

use Nette\ComponentModel\IContainer;

/**
 * Formulář na kategorie
 */
class UpdateCategory extends \BaseForm {

	public function __construct(IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		$this->addMultiSelect("categories");
	}

	public function setItems($items) {
		$this['categories']->items = $items;
	}
}
