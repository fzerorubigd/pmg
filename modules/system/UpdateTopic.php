<?php
/**
 * UpdateTopic Module
 * loops through all connected channels and updates the topic for
 * them
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
use awesomeircbot\channel\ChannelManager;
use awesomeircbot\line\ReceivedLineTypes;

class UpdateTopic extends Module {
	
	public static $requiredUserLevel = 10;
	
	public function run() {
		$server = Server::getInstance();
		$channels = ChannelManager::getConnectedChannelArray();
		
		$server->notify($this->senderNick, "Flushed topic cache successfully");
		foreach ($channels as $channel) {
			$server->topic($channel);
		}
	}
}
?>