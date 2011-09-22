<?php
/**
 * NickParser Module
 * Deals with renaming users in both the
 * Channel and User Manager
 *
 * NOTE- THIS IS A SYSTEM MODULE, REMOVING IT MAY
 * 	   REMOVE VITAL FUNCTIONALITY FROM THE BOT
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
namespace modules\mafia;

use awesomeircbot\module\Module;
use awesomeircbot\line\ReceivedLine;

class MafiaNick extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		$line = new ReceivedLine($this->runMessage);
		$line->parse();
		
		$game = MafiaGame::getInstance();
		
		//ChannelManager::rename($this->senderNick, $this->targetNick);
		//UserManager::rename($this->senderNick, $line->targetNick, $line->senderIdent, $line->senderHost);
		$game->changeNick($this->senderNick, $this->targetNick);
	}
}

