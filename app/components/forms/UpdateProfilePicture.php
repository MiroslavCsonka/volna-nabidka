<?php

namespace our_forms;

use Nette\Application\UI\Form, Nette\ComponentModel\IContainer;

/**
 * Formulář na profilový obrázek
 */
class UpdateProfilePicture extends \BaseForm {

	public function __construct(IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);

		$this->addUpload("picture")->addRule(
			Form::IMAGE, "Nevhodný formát pro profilový obrázek (můžete pouze JPEG, GIF nebo PNG)"
		);

		$this->addSubmit("submitUpdateProfilePicture", "Změnit obrázek");
	}
}
