<?php
/**
 * Harass Module
 * Adds nicknames or hosts to the
 * harass list
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
namespace modules\funstuff;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;
use awesomeircbot\data\DataManager;

class Harass extends Module {
	
	public static $requiredUserLevel = 5;
	
	public function run() {
		
		switch ($this->parameters(1)) {
			case "add":
				switch ($this->parameters(2)) {
					case "nick":
						$harassedNicks = DataManager::retrieve("harassedNicks");
						if (!$harassedNicks)
							$harassedNicks = array();
						
						$harassedNicks[] = $this->parameters(3);
						DataManager::store("harassedNicks", $harassedNicks);
						
						$server = Server::getInstance();
						$server->notify($this->senderNick, $this->parameters(3) . " added to harass list");
					break;
					case "host":
						$harassedHosts = DataManager::retrieve("harassedHosts");
						if (!$harassedHosts)
							$harassedHosts = array();
						
						$harassedHosts[] = $this->parameters(3);
						DataManager::store("harassedHosts", $harassedHosts);
						
						$server = Server::getInstance();
						$server->notify($this->senderNick, "REGEX string '" . $this->parameters(3) . "' added to harass list");
					break;
				}
			break;
			
			case "del":
				switch ($this->parameters(2)) {
					case "nick":
						$harassedNicks = DataManager::retrieve("harassedNicks");
						foreach($harassedNicks as $id => $harassedNick) {
							if ($harassedNick == $this->parameters(3))
								unset($harassedNicks[$id]);
								$success = true;
						}
						DataManager::store("harassedNicks", $harassedNicks);
						
						$server = Server::getInstance();
						if ($success)
							$server->notify($this->senderNick, $this->parameters(3) . " removed from harass list");
						else
							$server->notify($this->senderNick, "No harass entry found matching the nickname " . $this->parameters(3));
					break;
					case "host":
						$harassedHosts = DataManager::retrieve("harassedHosts");
						$success = false;
						foreach($harassedHosts as $id => $harassedHost) {
							if ($harassedHost == $this->parameters(3))
								unset($harassedHosts[$id]);
								$success = true;
						}
						DataManager::store("harassedHosts", $harassedHosts);
						
						$server = Server::getInstance();
						if ($success)
							$server->notify($this->senderNick, $this->parameters(3) . " removed from harass list");
						else
							$server->notify($this->senderNick, "No harass entry found matching the REGEX hostname '" . $this->parameters(3) . "'");
					break;
				}
			break;
		}
	}
}
?>