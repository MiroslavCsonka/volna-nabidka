<?php

use Nette\Security as NS;
use Nette\Database\Connection;

/**
 * Zajištuje přihlašování do systému
 */
class Authenticator extends Nette\Object implements NS\IAuthenticator {

	/** @var Connection Připojení k DB */
	private $db;

	public function __construct(Connection $db) {
		$this->db = $db;
	}

	/**
	 * Provede přihlášení uživatele
	 * @param  array array($mail,$password) nebo také pole informací co dostane od facebooku
	 * @throws Nette\UnexpectedValueException
	 * @throws Exception
	 * @return Nette\Security\Identity
	 */
	public function authenticate(array $credentials) {
		list($method, $info) = $credentials;

		switch ($method) {
			case 'facebook':
			{
				$wantedData = array('fbuid'     => $info["id"], 'name' => $info["name"],
										  'mail'      => $info["email"], 'gender' => $info["gender"],
										  'activated' => 1,
				);
				$user = $this->update($wantedData);
				if ($user) {
					return new NS\Identity($user->id, $user->role, iterator_to_array($user));
				}
				return NULL;
			}
			case 'basic':
			{
				list($mail, $password) = $info;
				$row = $this->db->table('User')->where('mail', $mail)->fetch();
				if (!$row) {
					throw new Exception("Email '$mail' nebyl nalezen.");
				}
				if ($row->pass !== self::calculateHash($password)) {
					throw new Exception("Nesprávná kombinace hesla a emailu.");
				}
				unset($row->pass);
				return new NS\Identity($row->id, $row->role, $row->toArray());
			}
			default:
				throw new \Nette\UnexpectedValueException("Not supported authentication type : '$method'");
		}
	}

	/**
	 * Zjistí hash hesla
	 * @param  string
	 * @return string
	 */
	public static function calculateHash($password) {
		return $password;
		$hash = sha1($password);
		return md5($hash . str_repeat('*(/!"_%*', 10));
	}

	/**
	 * Zaregistruje neznámého facebook uživatele
	 * @param $data
	 * @return \Nette\Database\Table\ActiveRow
	 */
	private function update($data) {
		// todo :: mít schodný email + FBUID
		$user = $this->db->table("User")->where("mail", $data["mail"])->fetch();
		if ($user) {
			$user->update($data);
		}
		else {
			$this->db->table("User")->insert($data);
		}
		return $this->db->table("User")->where("mail", $data["mail"])->fetch();
	}
}
