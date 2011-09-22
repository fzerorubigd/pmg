<?php
/**
 * MessageParser Module
 * Parses messages and adds the users
 * to the UserManager
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
use awesomeircbot\user\User;
use awesomeircbot\user\UserManager;
use awesomeircbot\line\ReceivedLine;
use config\Config;

class MessageParser extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		
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