<?php
/**
 * Error Class
 * An action/error the bot takes
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */

namespace awesomeircbot\log;

class Error {

	public $type;
	public $message;
	
	public function __construct($type, $message) {
		if (($type > 0) && ($type < 5))
			$this->type = $type;
		else
			return false;
		
		$this->message = $message;
	}
}
?>