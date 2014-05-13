<?php
use Nette\Security\Permission;

/**
 * Třída zajištující povolení provádění daných akcí
 */
class ACL extends Permission {

	/**
	 * Pouze konfigurační položky
	 */
	public function __construct() {

		$this->addRole('guest');
		$this->addRole('user', 'guest');
		$this->addRole('admin', 'user');

		// Nadefinujeme zdroje se kterými budou provádět akce
		$this->addResource('article');
		$this->addResource('comment');
		$this->addResource('poll');
		$this->addResource('account');
		$this->addResource('backend');
		$this->addResource('role');
		$this->addResource('reference');
		$this->addResource('project');

		$this->allow('user', 'account', 'logout');

		// registrovaný dědí právo od guesta, ale má i právo komentovat
		$this->allow('user', 'comment', 'add');

		// Administrátor může vše
		$this->allow('admin');

	}

}
