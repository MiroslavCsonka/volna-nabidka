<?php

use Nette\Database\Connection;

/**
 * Třída která se stará o reset hesla
 */
class PasswordResetter {

	/** @var Connection */
	private $db;

	public function __construct(Connection $db) {
		$this->db = $db;
	}

	/**
	 * Vygeneruje uživateli kód na reset hesla a vrátí ho
	 * @param \Entity\User $user
	 * @return string|bool Vrací reset kód, FALSE v případě erroru
	 */
	public function stageForReset(\Entity\User $user) {
		$code = $this->generateResetCode();
		$res = (bool)$this->db->table('User')->where('id = ?', $user->id)->update(array("resetCode" => $code));
		return $res ? $code : FALSE;
	}

	/**
	 * Vygeneruje reset kód
	 * return string reset kód
	 */
	private function generateResetCode() {
		$base = range("$", ">");
		$cnt = rand(3, 5);
		$code = "";
		for ($i = 0; $i < $cnt; $i++) {
			$code .= $base[rand(0, 26)];
			$code .= microtime();
		}
		return md5($code);
	}

	/**
	 * Resetne heslo uživateli
	 * @param string $resetCode
	 * @return array|bool Vrací heslo a email usera, FALSE když se stane chyba
	 */
	public function resetPassword($resetCode) {
		$row = $this->db->table('User')->where('resetCode', $resetCode)->fetch();
		if (!$row) {
			return FALSE;
		}
		$newPass = $this->generatePassword();
		$res = (bool)$this->db->table('User')->find($row->id)->update(
			array(
				"pass"      => $newPass,
				"resetCode" => NULL
			)
		);
		$out = array(
			"pass" => $newPass,
			"mail" => $row->mail
		);
		return $res ? $out : FALSE;
	}

	/**
	 * Vygeneruje heslo
	 * @return string Heslo
	 */
	private function generatePassword() {
		$base = range("$", ">");
		$cnt = rand(3, 5);
		$string = "";
		for ($i = 0; $i < $cnt; $i++) {
			$string .= $base[rand(0, 26)];
			$string .= microtime();
		}
		$md5 = md5($string);
		return substr($md5, 0, 7);
	}

}

