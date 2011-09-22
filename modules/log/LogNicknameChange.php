<?php
/**
 * LogNicknameChange Module
 * Logs a nickname change to the database
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
use awesomeircbot\channel\ChannelManager;
use awesomeircbot\line\ReceivedLine;
use awesomeircbot\line\ReceivedLineTypes;

class LogNicknameChange extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		$user = UserManager::get($this->senderNick);
		if (!$user)
			$user = UserManager::get($this->targetNick);
		
		$channels = ChannelManager::getCommonChannels($this->senderNick);
		if (!$channels)
			$channels = ChannelManager::getCommonChannels($this->targetNick);
		
		$db = Database::getInstance();
		
		foreach($channels as $channel) {
			$stmt = $db->prepare("INSERT INTO channel_actions (type, nickname, host, ident, channel_name, target_nick, time) VALUES (?,?,?,?,?,?,?)");
			$stmt->execute(array(ReceivedLineTypes::NICK, $this->senderNick, $user->host, $user->ident, $channel, $this->targetNick, time()));
		}
	}
}
?>