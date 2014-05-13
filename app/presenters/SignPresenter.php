<?php

use Nette\Application\UI\Form, \Nette\Mail\Message;

/**
 * Presenter zastřešující akce ohledně přihlašování, odhlašování, registraci
 */
class SignPresenter extends BasePresenter {


	public function renderRegister() {
		if ($this->user->isLoggedIn()) {
			$this->redirect('Homepage:');
		}
	}

	/**
	 * Formulářový prvek na registraci
	 * @return \Nette\Application\UI\Form
	 */
	public function createComponentRegister() {
		$form = new \our_forms\Register();
		$form->onSuccess[] = callback($this, 'registerSuccess');
		$form->onValidate[] = callback($this, 'checkUnique');
		return $form;
	}

	/**
	 * Validace registračního formuláře na duplicity (Email a jméno je unikátní)
	 * @param \Nette\Application\UI\Form $form
	 */
	public function checkUnique(Form $form) {
		/** @var $userMapper \Mapper\User */
		$userMapper = $this->context->userMapper;
		$userByName = $userMapper->getByName($form->values->name);
		if ($userByName instanceof Entity\User) {
			$form->addError("Jméno je již zabráno");
		}
		$userByEmail = $userMapper->getByEmail($form->values->mail);
		if ($userByEmail instanceof \Entity\User) {
			$form->addError("Email je již zabrán");
		}
	}

	/**
	 * Zpracuje registrační formulář
	 * @param \Nette\Application\UI\Form $form
	 */
	public function registerSuccess(Form $form) {
		$values = $form->getValues();
		unset($values['passwordAgain']);
		$user = $this->context->userMapper->register($values);
		if ($user instanceof \Entity\User) {
			$mail = new Message;
			$mail->setFrom('Volná nabídka <info@volnanabidka.cz>');
			$mail->setSubject("Registrace v systému volnanabidka.cz");
			$mail->addTo($values['mail']);
			$mail->setHtmlBody("registroval jsem se u volnanabidka.cz");
			$mail->send();
		}
		$this->fm(
			$user instanceof \Entity\User, "Registrace proběhla v pořádku", "Při registraci nastala chyba"
		);
		$this->redirect("this");
	}

	/**
	 * Odhlásí uživatele ze systému
	 */
	public function actionOut() {
		$this->user->logout(TRUE);
		$this->fm(TRUE, "Byl jste odhlášen");
		$this->redirect("Homepage:");
	}
}
