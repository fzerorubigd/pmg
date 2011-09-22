<?php
/**
 * NamesResponseParser Module
 * Parses names responses and adds the
 * users to the channel in ChannelManager
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

class NamesResponseParser extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		
		// Channel
		$workingLine = explode(" ", $this->runMessage);
		$channel = $workingLine[4];
		
		// Names
		$workingLine = explode(" :", $this->runMessage);
		$names = trim($workingLine[1]);
		$names = explode(" ", $names);
		
		// Fetch the channel object
		$channelObject = ChannelManager::get($channel);
		
		// Loop through the names adding them to the channel and UserManager
		foreach ($names as $name) {
			$privileges = false;
			
			if (strpos($name, "~") === 0)
				$privileges = "~";
			if (strpos($name, "&") === 0)
				$privileges = "&";
			if (strpos($name, "@") === 0)
				$privileges = "@";
			if (strpos($name, "%") === 0)
				$privileges = "%";
			if (strpos($name, "+") === 0)
				$privileges = "+";
				
			$nick = str_replace(array("~", "@", "&", "%", "+"), "", $name);
				
			$channelObject->addConnectedNick($nick, $privileges);
			$user = UserManager::get($nick);
			$user->nickname = $nick;
			UserManager::store($nick, $user);
		}
		
		// Store away the channel object
		ChannelManager::store($channel, $channelObject);
			
	}
}
?>