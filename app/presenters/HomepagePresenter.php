<?php

/**
 * Zajištuje načtení titulní stránky se statistikami, a komponentou na rychlé přidání projektu
 */
class HomepagePresenter extends BasePresenter {

	public function renderDefault() {
		$this->template->numOfUsers = $this->context->userMapper->getCount();
		$this->template->numOfProjects = $this->context->projectMapper->getCount();
		$this->template->sumOfProjectRewards = $this->context->projectMapper->getSumOf("perProject");
		$this->template->availableProjects = $this->context->projectMapper->getNumOfAlive();
		/** @var $provider Data */
		$provider = $this->context->data;
		$this->template->categories = $provider->getCategories();
		$this->template->_form = $this['categories'];
	}

	public function createComponentCategories() {
		/** @var $provider Data */
		$provider = $this->context->data;
		$mainCategories = $provider->getMainCategories(TRUE);

		$form = new \BaseForm();
		$form->addSelect('staticCategories', 'Hlavní kategorie', $mainCategories);
		$form->addSelect('dynamicCategories', 'Podkategorie', array('- Vyberte -'));
		$form->addSubmit('submit', 'Vložit nabídku');

		$form->onSuccess[] = function (BaseForm $form) {
			$values = $form->getHttpData();
			$form->presenter->redirect("QuickAdd:default", $values['dynamicCategories']);
		};
		return $form;
	}

	/**
	 * Načte subkategorie pro danou kategorii
	 * @param int Id kategorie
	 * @return array Pole subkategorií
	 */
	public function handleSelectLoad($id) {
		$out = array();
		if ($id != 0) {
			$out = $this->context->database->table('SubCategory')->where('parent', $id)->fetchPairs('id', 'name');
		}
		$form = $this['categories'];
		$form['dynamicCategories']->setItems($out);
		$form['dynamicCategories']->setPrompt('- Vyberte -');
		$this->invalidateControl('subCategories');
	}

}