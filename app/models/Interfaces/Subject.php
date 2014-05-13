<?php

/**
 * Observer design patterm
 */
interface Subject {

	public function registerObserver(\Observer $observer);

	public function unregisterObservers();

	public function notifyObservers(\Message $message);
}