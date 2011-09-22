<?php
/**
 * Derp Module
 * Says derp
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
namespace modules\funstuff;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;

class Derp extends Module {
	
	public static $requiredUserLevel = 5;
	
	public function run() {
		$server = Server::getInstance();
		$server->message($this->channel, "derp");
	}
}
?>