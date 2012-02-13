<?php
/**
 * LogChannelJoin Module
 * Logs a channel join to the database
 *
 * NOTE- THIS IS A SYSTEM MODULE, REMOVING IT MAY
 * 	   REMOVE VITAL FUNCTIONALITY FROM THE BOT
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
namespace modules\mafia;

use awesomeircbot\module\Module;
use awesomeircbot\database\Database;
use awesomeircbot\user\UserManager;
use awesomeircbot\line\ReceivedLine;
use awesomeircbot\line\ReceivedLineTypes;
use awesomeircbot\server\Server;

class MafiaJoinChanel extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		$line = new ReceivedLine($this->runMessage);
		$line->parse();
		
		$user = UserManager::get($this->senderNick);
		
		$server = Server::getInstance();
		//$server->message($user->nickname, "Welcome to Persian Mafia game. This channel is logged, check this for more info : http://cyberrabbits.net/non/mafia/");		
	}
}
