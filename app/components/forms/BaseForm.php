<?php
/**
 * Defaultní formulář na přehození formulářových error zpráv do flash message
 */
class BaseForm extends \Nette\Application\UI\Form {

	public function __construct(Nette\ComponentModel\IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		$this->onError[] = function (\Nette\Application\UI\Form $form) {
			foreach ($form->errors as $error) {
				$form->presenter->bfm($error);
			}
			$form->cleanErrors();
		};
	}
}
