<?php
/**
 * Message Module
 * Messages the given user/channel with the given
 * message
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
namespace modules\general;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;

class Message extends Module {
	
	public static $requiredUserLevel = 5;
	
	public function run() {
		$server = Server::getInstance();
		$server->message($this->parameters(1), $this->parameters(2, true));
		$server->notify($this->senderNick, '"' . $this->parameters(2, true) . '" sent to ' . $this->parameters(1) . "successfully");
	}
}
?>