<?php
/**
 * Module Module (ironic)
 * Administrates loaded module configs
 *
 * NOTE- THIS IS A SYSTEM MODULE, REMOVING IT MAY
 * 	   REMOVE VITAL FUNCTIONALITY FROM THE BOT
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
namespace modules\system;

use awesomeircbot\module\Module;
use awesomeircbot\module\ModuleManager;

use awesomeircbot\server\Server;

class ModuleControls extends Module {
	
	public static $requiredUserLevel = 10;
	
	public function run() {
		
		$action = $this->parameters(1);
		$server = Server::getInstance();
		
		switch ($action) {
			case "load":
				$moduleNamespace = "modules\\" . strtolower($this->parameters(2)) . "\configs\\" . $this->parameters(2);
				ModuleManager::loadModuleConfig($moduleNamespace);
				$server->notify($this->senderNick, "Action completed");
			break;
			
			case "unload":
				$moduleNamespace = "modules\\" . strtolower($this->parameters(2)) . "\configs\\" . $this->parameters(2);
				ModuleManager::unloadModuleConfig($moduleNamespace);
				$server->notify($this->senderNick, "Action completed");
			break;
			
			default:
				$folder = opendir(__DIR__ . "/..");
				$modulePacks = array();
				while (($file = readdir($folder)) !== false) {
					if (($file != ".") && ($file != "..") && ($file != "modules.inc.php")) {
						$folder2 = opendir(__DIR__ . "/../" . $file . "/configs");
						while (($file2 = readdir($folder2)) !== false) {
							if (($file2 != ".") && ($file2 != "..")) {
								$modulePacks[] = str_replace(".php", "", $file2);
							}
						}
						closedir($folder2);
					}
				}
				closedir($folder);
				
				$server->message($this->senderNick, "************************************");
				$server->message($this->senderNick, "Module Pack Listing");
				$server->message($this->senderNick, " ");
				
				foreach ($modulePacks as $modulePack) {
					$modulePackLength = strlen($modulePack);
					$spacesToAdd = 25 - $modulePackLength;
					
					for($i=1;$i<$spacesToAdd;$i++) {
						$spaces .= " ";
					}
					if (ModuleManager::isLoaded($modulePack))
						$server->message($this->senderNick, $modulePack . $spaces . " - " . chr(2) . chr(3) . "3Loaded" . chr(3) . chr(2));
					else
						$server->message($this->senderNick, $modulePack . $spaces . " - " . chr(2) . chr(3) . "4Not Loaded" . chr(3) . chr(2));
					unset($spaces);
				}
				
				$server->message($this->senderNick, "************************************");
			break;
				
		}
	}
}
?>