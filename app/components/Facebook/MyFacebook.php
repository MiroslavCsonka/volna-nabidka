<?php
use Nette\Application\UI\Control;
use Nette\Security\User;
use Nette\Security\IAuthenticator;

/**
 * Component for work with facebook
 */
class MyFacebook extends Control {

	/** @var int ID aplikace */
	private $appId;

	/** @var string Tajné heslo */
	private $secret;

	/** @var \Facebook Instance oficiální FB classy */
	private $facebook;

	/** @var IAuthenticator authorizace oproti které se to provede */
	private $user;

	public function __construct($appId, $secret, User $user) {
		$this->appId = $appId;
		$this->secret = $secret;
		$this->user = $user;
		$this->facebook = new Facebook(array('appId' => "$this->appId", 'secret' => "$this->secret",));
	}


	public function renderLogin() {
		$this->template->setFile(dirname(__FILE__) . '/login.latte');
		$this->template->render();
	}

	public function renderInit() {
		$this->template->appId = $this->appId;
		$this->template->setFile(dirname(__FILE__) . '/init.latte');
		$this->template->render();
	}

	public function handleAuthenticate() {
		$userInfo = $this->facebook->api('/me');
		$this->user->login('facebook', $userInfo);
		$this->presenter->redirect("this");
	}
}
