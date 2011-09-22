<?php
/**
 * Help class
 * Used for a command or subcommand entry
 * into the help system
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */

namespace awesomeircbot\help;

class Help {
	
	// Description
	public $description;
	
	/**
	 * Parameters
	 * <required> [<optional>]
	 */
	public $parameters;
	
	/**
	 * Construction
	 *
	 * @param string description
	 * @param string parameters
	 */
	public function __construct($description, $parameters) {
		$this->description = $description;
		$this->parameters = $parameters;
	}
}
?>