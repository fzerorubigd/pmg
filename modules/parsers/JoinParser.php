<?php
/**
 * JoinParser Module
 * Deals with adding users to the channel
 * users' list when they join a channel
 *
 * NOTE- THIS IS A SYSTEM MODULE, REMOVING IT MAY
 * 	   REMOVE VITAL FUNCTIONALITY FROM THE BOT
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
namespace modules\parsers;

use awesomeircbot\module\Module;
use awesomeircbot\channel\ChannelManager;
use awesomeircbot\user\UserManager;
use awesomeircbot\line\ReceivedLine;

class JoinParser extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		$channel = ChannelManager::get($this->channel);
		$channel->addConnectedNick($this->senderNick);
		ChannelManager::store($this->channel, $channel);
		
		$line = new ReceivedLine($this->runMessage);
		$line->parse();
		
		$user = UserManager::get($this->senderNick);
		$user->nickname = $line->senderNick;
		$user->ident = $line->senderIdent;
		$user->host = $line->senderHost;
		UserManager::store($this->senderNick, $user);
	}
}
?>