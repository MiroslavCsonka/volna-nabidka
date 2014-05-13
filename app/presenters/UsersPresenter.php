<?php

/**
 * Slouží pro hromadné vykreslení stránky s uživateli
 */
class UsersPresenter extends BasePresenter {

	/** @var \Mapper\User */
	private $userMapper;

	public function startup() {
		parent::startup();
		$this->userMapper = $this->context->userMapper;
	}

	public function actionDefault() {
		if (isset($_SESSION['userSearch'])) {
			unset($_SESSION['userSearch']);
		}
	}

	public function renderDefault() {
		$dataResult = $this->userMapper->getAll();
		$this->setDataResult($dataResult);
	}

	public function renderSearch() {
		$this->setView('default');
		if (isset($_SESSION['userSearch'])) {
			$dataResult = $this->userMapper->getByFilter($_SESSION['userSearch']);
			$this->setDataResult($dataResult);
		}
		else {
			$dataResult = $this->userMapper->getAll();
			$this->setDataResult($dataResult);
		}
	}

	private function setDataResult(\DataResult $dataResult) {
		/** @var VisualPaginator */
		$paginator = $this['vP']->paginator;
		$paginator->itemsPerPage = 5;
		$paginator->itemCount = $dataResult->getCount();
		$this->template->users = $dataResult->getResults(
			$paginator->getLength(), $paginator->getOffset()
		);
	}

	public function createComponentSearchUsers() {
		$form = new \our_forms\UserSearch($this->context->data);
		if (isset($_SESSION['userSearch'])) {
			$form->setDefaults($_SESSION['userSearch']);
		}
		$form->onSuccess[] = callback($this, "search");
		return $form;
	}

	public function search(\Nette\Application\UI\Form $form) {
		$_SESSION['userSearch'] = $form->getValues(TRUE);
		$this->redirect('Users:search');
	}

	/**
	 * Komponenta na zobrazení stránkování
	 * @param $name  string Jméno komponenty
	 * @return VisualPaginator
	 */
	public function createComponentVP($name) {
		return new \VisualPaginator($this, $name);
	}

}

