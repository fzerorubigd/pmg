<?php
/**
 * Formatted Message Class
 * Can be used to format a message with CTCP compliant
 * formatting (colours, bold, etc)
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */

namespace awesomeircbot\line;

class FormattedMessage {

	public $message;
	
	/**
	 * Construction
	 *
	 * Formats the message after it is passed in
	 *
	 * @param string message, formatted with BBCode like
	 *		     formatting, see the GitHub wiki for more
	 */
	public function __construct($message) {
		$this->message = $message;
	}
}
?>
	
	