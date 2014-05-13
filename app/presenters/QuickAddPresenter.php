<?php

use Nette\Application\UI\Form;

/**
 * Presenter starající se o přidání projektu
 */
class QuickAddPresenter extends BasePresenter {

	/** @var \Mapper\Project */
	private $projectMapper;

	public function startup() {
		parent::startup();
		$this->projectMapper = $this->context->projectMapper;
	}

	public function renderDefault($category) {
		if ($category !== NULL) {
			$this['addProject']->setDefaults(array('categories' => $category));
		}
	}

	/**
	 * Formulář na přidání projektu
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentAddProject() {
		$form = new \our_forms\NewProject($this->context->data);
		$form->onSuccess[] = callback($this, "projectAddSuccess");
		return $form;
	}

	/**
	 * Zpracuje přidání formuláře na přidání projektu
	 * @param Nette\Application\UI\Form $form
	 */
	public function projectAddSuccess(Form $form) {
		$this->onlyForLoggedIn();
		$values = $form->getValues();
		$values['reward'] = Int::convert($values['reward']);
		$id = $this->projectMapper->create($values, $this->oUser);
		if (!is_bool($id)) {
			$this->fm(TRUE, "Projekt byl úspěšně přidán");
			$this->redirect("Project:detail", $id);
		}
		else {
			$this->bfm("Projekt se nepovedlo přidat!");
			$this->redirect("this");
		}
	}

}
