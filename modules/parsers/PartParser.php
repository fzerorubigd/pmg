<?php
/**
 * PartParser Module
 * Deals with removing users from the
 * channel list
 *
 * NOTE- THIS IS A SYSTEM MODULE, REMOVING IT MAY
 * 	   REMOVE VITAL FUNCTIONALITY FROM THE BOT
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
namespace modules\parsers;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;
use awesomeircbot\channel\ChannelManager;
use awesomeircbot\user\UserManager;
use awesomeircbot\data\DataManager;

class PartParser extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		$channel = ChannelManager::get($this->channel);
		$channel->removeConnectedNick($this->senderNick);
		ChannelManager::store($this->channel, $channel);
		
		if (!ChannelManager::isConnectedToTrackedChannel($this->senderNick))
			UserManager::remove($this->senderNick);
	}
}
?>