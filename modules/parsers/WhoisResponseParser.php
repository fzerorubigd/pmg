<?php
/**
 * WhoisResponseParser Module
 * Parses whois responses and adds them
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
use awesomeircbot\line\ReceivedLineTypes;
use awesomeircbot\user\User;
use awesomeircbot\user\UserManager;
use config\Config;

class WhoisResponseParser extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		if ($this->eventType == ReceivedLineTypes::SERVERREPLYTHREEONEONE) {
			$workingLine = explode(" ", $this->runMessage, 8);
			$nick = $workingLine[3];
			$ident = $workingLine[4];
			$host = $workingLine[5];
			$realName = str_replace(":", "", $workingLine[7]);
			$realName = trim($realName);
			
			$user = new User;
			$user->nickname = $nick;
			$user->ident = $ident;
			$user->host = $host;
			$user->realName = $realName;
			UserManager::store($nick, $user);
		}
		else if (($this->eventType == ReceivedLineTypes::SERVERREPLYTHREETHREEZERO) || ($this->eventType == ReceivedLineTypes::SERVERREPLYTHREEZEROSEVEN)) {
			$workingLine = explode(" ", $this->runMessage);
			$nick = $workingLine[3];
			
			$user = UserManager::get($nick);
			$user->isIdentified = true;
			UserManager::store($nick, $user);
			
			if (Config::$users[$nick])
				$permissionLevel = Config::$users[$nick];
			else
				$permissionLevel = 0;
				
			$server = Server::getInstance();
			$server->notify($nick, "You have been successfully authorized via NickServ with the permission level " . $permissionLevel);
		}
	}
}
?>