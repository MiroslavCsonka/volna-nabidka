<?php
/**
 * Implementátoři tohoto interfacu se zaručují, že mají vlastníka a to \Entity\User.
 * Vlastník se dá získat metodou getOwner() a dá se porovnat, zda je \Entity\User vlastník implementátora.
 */
interface OwnAble {

	/**
	 * Zjistí zda uživatel je vlastníkem daného předmětu
	 * @param mixed $user
	 * @return bool
	 */
	public function isOwner($user);

	/**
	 * Získá ownera daného předmětu
	 * @return \Entity\User
	 */
	public function getOwner();
}