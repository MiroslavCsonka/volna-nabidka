<?php

/**
 * Presenter zastřešující akce nad jedním uživatelem
 */
class UserPresenter extends BasePresenter {

	/** @var \Mapper\User Spravuje instance User */
	private $userMapper;

	/** @var \Mapper\Project */
	private $projectMapper;

	public function startup() {
		parent::startup();
		$this->userMapper = $this->context->userMapper;
		$this->projectMapper = $this->context->projectMapper;
	}

	/**
	 * Vykreslí profil uživatele
	 * @param int    $id
	 * @param string $nick
	 */
	public function renderDefault($id, $nick) {
		$user = $this->userMapper->find($id);
		if (!$user instanceof \Entity\User) {
			$this->redirect("User:notfound");
		}
		$city = $user->getCity();
		(!is_null($city)) ? $this->template->city = $city : $this->template->city = NULL;
		// REFERENCES
		$dataResult = $this->context->referenceMapper->getReferencesByUser($user);
		/** @var VisualPaginator */
		$visualPaginator = $this["vP"]->paginator;
		$visualPaginator->itemsPerPage = 10;
		$visualPaginator->itemCount = $dataResult->getCount();
		$this->template->references = $dataResult->getResults(
			$visualPaginator->getLength(), $visualPaginator->getOffset()
		);

		// END OF REFERENCES
		$dataResult = $this->projectMapper->getFinishedByUser($user);
		// Chceme první 4 projekty (sekce vedle statistik)
		$this->template->recentProjects = $dataResult->getResults(4);
		$this->template->id = $id;
		$this->template->currentUser = $user;
		// Pro sebe si projekty nevykreslujeme
		if ($this->oUser instanceof \Entity\User && $this->oUser->id != $id) {
			$this->template->projectsForInvitation = $this->projectMapper->getForInvitation($this->oUser, $user);
		}
	}

	/**
	 * Komponenta na zobrazení stránkování
	 * @param $name  string Jméno komponenty
	 * @return VisualPaginator
	 */
	public function createComponentVP($name) {
		return new \VisualPaginator($this, $name);
	}

	/**
	 * Formulář na odeslání zprávy
	 * @return our_forms\messageForm
	 */
	public function createComponentMessageForm() {
		$component = new WriteMessageComponent();

		return $component;
	}

	public function handleRemoveReference($referenceId) {
		if ($this->user->isAllowed('reference', 'remove')) {
			$reference = $this->context->referenceMapper->find($referenceId);
			if ($reference instanceof \Entity\Reference) {
				$status = $reference->delete();
				$this->fm($status, 'Reference byla odebrána', 'Nepodařilo se odebrat referenci');
			}
			else {
				$this->bfm("Reference s číslem '$referenceId' nebyla nalezena");
			}
		}
		else {
			$this->bfm('Nemáte právo odebrat referenci');
		}
		$this->redirect('this');
	}

	/**
	 * Obslouží request na poslání pozvánky
	 * @param int $projectId ID projektu na který chceme uživatele pozvat
	 * @param int $userId    ID recievera
	 */
	public function handleSendInvitation($projectId, $userId) {
		$project = $this->projectMapper->find($projectId);
		$reciever = $this->userMapper->find($userId);
		if ($project instanceof \Entity\Project && $reciever instanceof \Entity\User) {
			// Projekty na které může $oUser $recievera pozvat
			$projects = $this->projectMapper->getForInvitation($this->oUser, $reciever);
			$validIds = \Arrays::pickUp($projects, 'id');
			if ($project->isOwner($this->oUser) && in_array($project->id, $validIds) && $this->oUser->id != $reciever->id) {
				$project->registerObserver($reciever);
				$message = new Message(
					"Byl jste pozván na projekt: <a href='" . $this->link('Project:detail', $project->id) . "'>" . $project->name . "</a> jeho vlastníkem
                               <a href='" . $this->link('User:default', $this->oUser->id) . "'>" . $this->oUser->name . "</a>.",
					new DateTime()
				);
				$result = $project->invite($reciever, $message);
				$this->fm($result, "Pozvánka na projekt byla úspěšně odeslána", "Nepodařilo se odeslat pozvánku na projekt");
			}
			else {
				$this->bfm($this->context->parameters['notAuthorized']);
			}
		}
		else {
			$this->bfm($this->context->parameters['notAuthorized']);
		}
		$this->redirect("this");
	}

}

