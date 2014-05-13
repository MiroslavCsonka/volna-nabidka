<?php

use Nette\Utils\Html, Nette\Application\UI\Form;

class AdministrationPresenter extends BasePresenter {

	public function startUp() {
		parent::startup();
		if (!$this->user->isAllowed('backend', 'see')) {
			$this->bfm('Sem nemáte přístup');
			$this->redirect('Homepage:');
		}
	}

	public function createComponentUsersGrid() {
		$grid = new \Grido\Grid();
		$grid->setModel($this->context->database->table('User'));
		/** @var $roles array Assoc pole rolí, ve které uživatel může mít */
		$roles = $this->context->parameters['roles'];
		$presenter = $this;

		$grid->addColumn('name', 'Jméno')
			 ->setSortable()
			 ->setFilter();

		$grid->addColumn('mail', 'E-Mail', \Grido\Columns\Column::TYPE_MAIL)
			 ->setSortable()
			 ->setFilter();
		$grid->addColumn('role', 'Role')
			 ->setSortable()
			 ->setCustomRender(function ($item) use ($roles) {
				 $span = HTML::el('span')->setText($roles[$item->role]);
				 switch ($item->id) {
					 case 35:
						 return $span->title('vůl');
					 case 29:
						 return $span->title('I\'m your creator');
					 case 30:
						 return $span->title('I\'m your creator');
					 default:
						 return $span;
				 }
			 });

		$grid->addColumn('roleChange', 'Změna role')
			 ->setSortable()
			 ->setCustomRender(function ($item) use ($roles, $presenter) {
				 switch ($item->role) {
					 case 'user' :
						 $url = $presenter->link('setRole!', (int)$item->id, 'admin');
						 return Html::el('a')
							  ->href($url)
							  ->setHtml('&uarr;')
							  ->title('Povýšit na admina');
					 case 'admin' :
						 $url = $presenter->link('setRole!', (int)$item->id, 'user');
						 return Html::el('a')
							  ->href($url)
							  ->setHtml('&darr;')
							  ->title('Ponížit na běžného uživatele');
					 default:
						 throw new \Nette\UnexpectedValueException("Not supported role '$item->role'");
				 }
			 });

		$grid->addFilter('role', 'Role', \Grido\Filters\Filter::TYPE_SELECT, array(
			$this->context->parameters['roles']
		))
			 ->setColumn('role');

		$grid->addColumn('activated', 'Aktivní')
			 ->setReplacement($this->context->parameters['userStates']);

		$grid->addColumn('telephone', 'Tel. číslo')
			 ->setSortable()
			 ->setFilter();


		$grid->addAction('ban', 'Zabanovat')
			 ->setCustomRender(function ($row) use ($presenter) {
				 if ($row->activated == -1) {
					 return Html::el('a', 'odbanovat')->href($presenter->link('Administration:activateUser', $row->id));
				 }
				 elseif ($row->activated == 0) {
					 return Html::el('a', 'aktivovat')->href($presenter->link('Administration:activateUser', $row->id));
				 }
				 else {
					 return Html::el('a', 'zabanovat')->href($presenter->link('Administration:banUser', $row->id));
				 }
			 });

		$grid->setFilterRenderType(\Grido\Filters\Filter::RENDER_OUTER);

		return $grid;
	}


	public function handleSetRole($userId, $newRole) {
		if (!$this->user->isAllowed('role', 'change')) {
			$this->bfm('Nemáte oprávnění na tuto akci');
			$this->redirect('Homepage:');
		}

		/** @var $mapper \Mapper\User */
		$mapper = $this->context->userMapper;
		$user = $mapper->find($userId);
		if ($user instanceof \Entity\User) {
			if (isset($this->context->parameters['roles'][$newRole])) {
				$status = $user->update(array('role' => $newRole));
				$this->fm($status, 'Role byla aktualizována', 'Role nebyla změněna');
			}
			else {
				$this->bfm("Neznámá role '$newRole'");
			}
		}
		else {
			$this->bfm("Uživatel s číslem '$userId' nebyl nalezen");
		}

		$this->redirect('this');
	}

	public function createComponentCitiesGrid() {
		$grid = new \Grido\Grid();
		$grid->setModel($this->context->database->table('City'));

		$grid->addColumn('name', 'Jméno');

		$grid->addAction('edit', 'Upravit');

		$grid->addAction('removeCity', 'Odebrat');

		return $grid;
	}

	public function createComponentAddCity() {
		$form = new BaseForm();

		$form->addText('name', 'Jméno');

		$form->addSubmit('submitButton', 'Přidat!');

		$form->onSuccess[] = callback($this, 'addCitySuccess');

		return $form;
	}

	public function addCitySuccess(Form $form) {
		$status = $this->context->cityMapper->insert($form->getValues(TRUE));
		$this->fm($status, 'Město bylo přidáno', 'Město se nepovedlo přidat, nejspíš je už v seznamu');
		$this->redirect('Administration:cities');
	}

	public function actionRemoveCity($id) {
		$city = $this->context->cityMapper->find($id);
		if ($city instanceof \Entity\City) {
			$status = $city->delete();
			$this->fm($status, 'Město bylo odebráno');
		}
		else {
			$this->bfm('Město nenalezeno');
		}
		$this->redirect('Administration:cities');
	}

	public function actionBanUser($id) {
		$user = $this->context->userMapper->find($id);
		if ($user instanceof \Entity\User) {
			$status = $user->setStateTo(\Entity\User::BANNED);
			$this->fm($status, "Účet uživatele $user->name byl zablokován.");
		}
		else {
			$this->bfm('Uživatel nenalezen');
		}
		$this->redirect('Administration:users');
	}

	public function actionActivateUser($id) {
		$user = $this->context->userMapper->find($id);
		if ($user instanceof \Entity\User) {
			$status = $user->setStateTo(\Entity\User::ACTIVATED);
			$this->fm($status, "Účet uživatele $user->name byl aktivován.");
		}
		else {
			$this->bfm('Uživatel nenalezen');
		}
		$this->redirect('Administration:users');
	}


}
