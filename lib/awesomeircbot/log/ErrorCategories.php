<?php
/**
 * Bot Log Categories Constant Class
 * Constants for the various categories of action the bot
 * takes
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */

namespace awesomeircbot\log;

class ErrorCategories {

	const DEBUG = 1;
	const NOTICE = 2;
	const WARNING = 4;
	const ERROR = 8;
	const FATAL = 16;
	
	const ALL = 31;
	
	private function __construct() { }
}
?>