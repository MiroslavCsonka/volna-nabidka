<?php
use Nette\Application\UI\Form;

/**
 * Presenter zastřešující akce nad projekty
 */
class ProjectsPresenter extends BasePresenter {

	/** @var \Mapper\Project */
	private $projectMapper;

	private $projects;

	public function startup() {
		parent::startup();
		$this->projectMapper = $this->context->projectMapper;
	}

	public function actionDefault($category) {
		/* Pokud uživatel odejde z vyhledávacího renderu,
		 * unsetnem sessionu, kde držíme informace o hodnotách z hledacího formuláře
		 */
		if (isset($_SESSION['searchParams'])) {
			unset($_SESSION['searchParams']);
		}
	}

	public function renderDefault($category) {
		if ($category !== NULL) {
			$dataResult = $this->projectMapper->getByCategory($category);
		}
		else { // default zobrazení
			$dataResult = $this->projectMapper->getAll();
		}
		$this->setDataResult($dataResult);
	}

	public function renderSearch() {
		$this->setView('default');
		if (isset($_SESSION['searchParams'])) {
                        $clone = clone $_SESSION['searchParams'];
			$dataResult = $this->projectMapper->getByFilter($clone);
		}
		else { // default zobrazení
			$dataResult = $this->projectMapper->getAll();
		}
		$this->setDataResult($dataResult);
	}

	private function setDataResult($dataResult) {
		if ($dataResult instanceof \DataResult) {
			/** @var VisualPaginator */
			$visualPaginator = $this["vP"]->paginator;
			$visualPaginator->itemsPerPage = 10;
			$visualPaginator->itemCount = $dataResult->getCount();
			$this->template->projects = $dataResult->getResults(
				$visualPaginator->getLength(), $visualPaginator->getOffset()
			);
		}
		else {
			$this->template->projects = array();
		}
	}

	/**
	 * Komponenta na vyhledávání mezi projekty
	 * @return SearchOffers
	 */
	public function createComponentSearchProjects() {
		if (!isset($_SESSION['searchParams'])) {
			$_SESSION["searchParams"] = array();
		}
		$component = new SearchOffers($_SESSION["searchParams"], $this->context->database, $this->projectMapper);
		$form = $component->getForm();

		$form->onSuccess[] = function (Form $form) {
			$_SESSION['searchParams'] = $form->values;
			$form->presenter->redirect("Projects:search");
		};
		return $component;
	}

	public function renderMine() {
		$this->onlyForLoggedIn();
		$this->template->mineLive = $this->oUser->getMineProjects(\Entity\Project::LIVE);
		$this->template->mineLocked = $this->oUser->getMineProjects(\Entity\Project::LOCKED);
		$this->template->mineFinished = $this->oUser->getMineProjects(\Entity\Project::FINISHED);
		$this->template->relatedAttending = $this->oUser->getRelatedProjects(\Entity\Project::ATTENDED);
		$this->template->relatedWorkingOn = $this->oUser->getRelatedProjects(\Entity\Project::WORKING_ON);
		$this->template->relatedFinished = $this->oUser->getRelatedProjects(\Entity\Project::FINISHED);
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
