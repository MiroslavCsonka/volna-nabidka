<?php

/**
 * Observer design patterm
 */
interface Observer {

	public function notify(\Message $message);
}
