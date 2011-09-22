<?php
/**
 * Identify Module
 * Sends a WHOIS query for the sender to check
 * if they are identified
 *
 * NOTE- THIS IS A SYSTEM MODULE, REMOVING IT MAY
 * 	   REMOVE VITAL FUNCTIONALITY FROM THE BOT
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
namespace modules\system;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;

class Identify extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		$server = Server::getInstance();
		$server->whois($this->senderNick);
		$server->notify($this->senderNick, "We have now sent a query for your identification status, you will receive a message in a moment if the identification was successful");
	}
}
?>