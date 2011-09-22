<?php
/**
 * Cycle Module
 * Parts and rejoins the given channel, or if no channel is
 * supplied, the current channel
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
namespace modules\general;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;

class Cycle extends Module {
	
	public static $requiredUserLevel = 10;
	
	public function run() {
		if ($this->parameters(1))
			$channel = $this->parameters(1);
		else
			$channel = $this->channel;
		
		$server = Server::getInstance();
		$server->part($channel);
		$server->join($channel);
	}
}
?>