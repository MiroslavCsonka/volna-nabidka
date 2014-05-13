<?php

use Nette\Application\UI\Form,
	 Nette\Mail\Message;

/**
 * Předek všech presenterů, zde se nastavuje uživatel, authorizator, nastavuje se šabloně databáze
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {

	/** @var \Entity\User Náš uživatel v aplikaci !!NE Nette\Security\User!! */
	protected $oUser = NULL;

	public function startup() {
		parent::startup();
		// Nette nestartuje sessionu tak strašně moc jak tvrdí, takže si jí pro nepřihlášenýho uživatele musíme nastartovat tak jak tak
		$this->getSession()->start();
		if ($this->getUser()->isLoggedIn()) {
			$this->oUser = $this->context->userMapper->find($this->getUser()->getId());
			if ($this->oUser instanceof \Entity\User) {
				$this->template->numOfNotifications = $this->context->notificationMapper->getNotificationsByUser($this->oUser, TRUE);
				if ($this->oUser->is(\Entity\User::BANNED)) {
					$this->user->logout(TRUE);
					$this->bfm('Byl vám odebrán přístup, pokud si myslíte, že došlo k omylu, kontaktujte nás prosím');
				}
				elseif ($this->oUser->is(\Entity\User::INACTIVATED)) {
					$this->user->logout(TRUE);
					$this->bfm('Aktivujte si účet pomocí emailu prosím.');
				}
			}
			else {
				$this->user->logout(TRUE);
				$this->bfm('Omlouváme se vám, ale nastala chyba v přihlašování. Pokud bude tento problém přetrvávat, kontaktujte nás prosím');
			}
		}
		$this->template->oUser = $this->oUser;
	}

	/**
	 * Formulářový prvek na přihlášení
	 * @return \Nette\Application\UI\Form
	 */
	public function createComponentLogin() {
		$form = new \our_forms\Login();
		$form->onSuccess[] = callback($this, 'loginSuccess');
		return $form;
	}

	/**
	 * Vyrobí komponentu na reset hesla
	 */
	public function createComponentPasswordReset() {
		$form = new \our_forms\PasswordReset();
		$form->onValidate[] = function (Form $form) {
			$values = $form->getValues();
			$user = $form->presenter->context->userMapper->getByEmail($values->mail);
			if (!$user instanceof \Entity\User) {
				$form->addError("Uživatel s takovým emailem není registrován");
			}
		};
		$form->onSuccess[] = callback($this, 'passResetSuccess');
		return $form;
	}

	public function passResetSuccess(Form $form) {
		$values = $form->getValues();
		$user = $this->context->userMapper->getByEmail($values->mail);
		$resetter = new \PasswordResetter($this->context->database);
		$code = $resetter->stageForReset($user);
		if ($code !== FALSE) {
			$httpRequst = $this->context->httpRequest;
			$url = $httpRequst->getUrl();
			$mail = new Message();
			$mail->setFrom('Volná Nabídka <info@volnanabidka.cz>')
				 ->addTo($user->mail)
				 ->setSubject('Obnova hesla na serveru www.volnanabidka.cz')
				 ->setBody("Dobrý den,\n
        byla přijata žádost na obnovu hesla na váš účet.\n
        Pokud jste ji neodesílal vy, tento email ignorujte.\n
        Pokud chcete obnovit heslo klikněte prosím na následující link:\n
        " . $url->host . $this->link("Reset:default", $code) . "
        Přejeme pěkný den.")
				 ->send();
			$this->fm(TRUE, "Byl vám zaslán email s potvrzením o obnovení hesla");
		}
		else {
			$this->bfm("Nepodařilo se obnovit vám heslo");
		}
		$this->redirect("Homepage:");
	}

	/**
	 * Zpracuje přihlašovací formulář
	 * @param \Nette\Application\UI\Form $form
	 */
	public function loginSuccess(Form $form) {
		try {
			$this->user->login('basic', array($form->values->mail, $form->values->pass));
		} catch (Exception $e) {
			$msg = $e->getMessage();
			if (!empty($msg)) {
				$this->flashMessage("Chyba: " . $msg, Form::ERROR);
			}
		}
		$this->redirect('this');
		$this->redirect('this');
	}

	/**
	 * Nastaví flash message
	 * @param bool       $status
	 * @param string     $success
	 * @param string     $error
	 */
	public function fm($status, $success, $error = NULL) {
		if ($status) {
			$this->flashMessage($success, Form::SUCCESS);
		}
		else {
			if ($this->context->parameters['vampMode']) {
				$this->flashMessage("Za co nás máte?", Form::ERROR);
			}
			else {
				$this->flashMessage(empty($error) ? 'Omlouváme se, ale nastala chyba ' : $error, Form::ERROR);
			}
		}
	}

	/**
	 * Pokud uživatel není přihlášen, bude poslán na titulní stránku
	 */
	public function onlyForLoggedIn() {
		if (!$this->user->isLoggedIn()) {
			if ($this->context->params['vampMode']) {
				$this->flashMessage("Za co nás máte?", Form::ERROR);
			}
			else {
				$this->bfm("Nemáte přístup k této stránce, prosím přihlašte se, nebo kontaktujde admin team");
			}
			$this->redirect("Homepage:");
		}
	}

	/**
	 * Komponenta pro práci s FB
	 * @return MyFacebook
	 */
	public function createComponentFacebook() {
		$facebook = $this->context->parameters["facebook"];
		return new MyFacebook($facebook["appId"], $facebook["secret"], $this->user);
	}

	/**
	 * @param null $class
	 * @return Nette\Templating\ITemplate
	 */
	protected function createTemplate($class = NULL) {
		$template = parent::createTemplate($class);
		$template->registerHelperLoader('Helpers::loader');
		return $template;
	}

	/**
	 * Zkratka na napsání chybové hlášky uživateli
	 * @param string $string
	 */
	public function bfm($string) {
		$this->fm(FALSE, '', $string);
	}

}
