<?php
/**
 * QuitParser Module
 * Deals with removing users from the
 * channel list and from the UserManager when
 * they quit
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

class QuitParser extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		ChannelManager::removeConnectedNickFromAll($this->senderNick);
		UserManager::remove($this->senderNick);
	}
}
?>