<?php
/**
 * Help Module
 * Responds to help requests
 *
 * NOTE- THIS IS A SYSTEM MODULE, REMOVING IT MAY
 * 	   REMOVE VITAL FUNCTIONALITY FROM THE BOT
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
namespace modules\system;

use awesomeircbot\module\Module;
use awesomeircbot\help\HelpManager;
use awesomeircbot\server\Server;
use config\Config;

class Help extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		$command = $this->parameters(1);
		
		if ($command) {
			$subcommand = $this->parameters(2);
		
			if ($subcommand != "") {
				$description = HelpManager::getDescription($command, $subcommand);
				$parameters = HelpManager::getParameters($command, $subcommand);
				$subcommandsString = false;
			}
			else {
				$description = HelpManager::getDescription($command);
				$parameters = HelpManager::getParameters($command);
				$subcommands = HelpManager::getSubcommands($command);
				foreach($subcommands as $id => $subcommandOfCommand) {
					
					if ($subcommandOfCommand != "BASE")
						$subcommandsString .= " " . $subcommandOfCommand;
				}
			}
			
			$server = Server::getInstance();
			$server->notify($this->senderNick, "************************************");
			$server->notify($this->senderNick, "Help for " . Config::$commandCharacter . $command . " " . $subcommand);
			$server->notify($this->senderNick, " ");
			$server->notify($this->senderNick, $description);
			$server->notify($this->senderNick, chr(2) . "Syntax: " . chr(2) . Config::$commandCharacter . $command . " " . $subcommand . " " . $parameters);
			if ($subcommandsString)
				$server->notify($this->senderNick, chr(2) . "Subcommands:" . chr(2) . $subcommandsString);
			$server->notify($this->senderNick, "************************************");
		}
		else {
			$commands = HelpManager::getCommandList();
			foreach($commands as $id => $commandFromList) {
					$commandsString .= " " . $commandFromList;
			}
			
			$server = Server::getInstance();
			$server->notify($this->senderNick, "************************************");
			$server->notify($this->senderNick, "Welcome to AwesomeBot v2 Help");
			$server->notify($this->senderNick, "Follow @AwesomezGuy on Twitter http://twitter.com/AwesomezGuy");
			$server->notify($this->senderNick, "");
			$server->notify($this->senderNick, chr(2) . "Commands: " . chr(2) . $commandsString);
			$server->notify($this->senderNick, chr(2) . "Getting help with commands: " . chr(2) . Config::$commandCharacter . "help <command to get help for>");
			$server->notify($this->senderNick, "************************************");
		}
	}
}
?>