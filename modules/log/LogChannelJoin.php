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
namespace modules\log;

use awesomeircbot\module\Module;
use awesomeircbot\database\Database;
use awesomeircbot\user\UserManager;
use awesomeircbot\line\ReceivedLine;
use awesomeircbot\line\ReceivedLineTypes;

class LogChannelJoin extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		$line = new ReceivedLine($this->runMessage);
		$line->parse();
		
		$user = UserManager::get($this->senderNick);
		$db = Database::getInstance();
		
		$stmt = $db->prepare("INSERT INTO channel_actions (type, nickname, host, ident, channel_name, time) VALUES (?,?,?,?,?,?)");
		$stmt->execute(array(ReceivedLineTypes::JOIN, $this->senderNick, $user->host, $user->ident, $this->channel, time()));
	}
}
?>