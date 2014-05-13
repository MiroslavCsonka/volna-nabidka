<?php
/**
 * Implementátoři se zaručují, ze se jim bude dát poslat zpráva a také získat jejich zprávy.
 */
interface MessageAble {

	/**
	 * Přidá zprávu subjektu
	 * @param Message         $message
	 * @param Entity\User|int $user
	 * @return mixed
	 */
	public function addMessage(\Message $message, $user);

	/**
	 * Získá zprávy
	 * @return mixed
	 */
	public function getMessages();

}