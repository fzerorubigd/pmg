<?php
/**
 * CheckHarass Module
 * Checks if a user has been added to the
 * harass list and harasses them if they are
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
namespace modules\funstuff;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;
use awesomeircbot\channel\ChannelManager;
use awesomeircbot\user\UserManager;
use awesomeircbot\data\DataManager;

class CheckHarass extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		$harassedNicks = DataManager::retrieve("harassedNicks", "modules\\funstuff\Harass");
		$harassedHosts = DataManager::retrieve("harassedHosts", "modules\\funstuff\Harass");
		
		if ($harassedNicks) {
			if (in_array($this->senderNick, $harassedNicks) !== false) {
				$server = Server::getInstance();
				$server->message($this->channel, "Shutup " . $this->senderNick . "! We all hate you.");
				return true;
			}
		}
		
		if ($harassedHosts) {
			$user = UserManager::get($this->senderNick);
			$host = $user->host;
			
			foreach($harassedHosts as $harassedHost) {
				if (preg_match($harassedHost, $host)) {
					$server = Server::getInstance();
					$server->message($this->channel, "Shutup " . $this->senderNick . "! We all hate you.");
					return true;
				}
			}
		}
	}
}
?>