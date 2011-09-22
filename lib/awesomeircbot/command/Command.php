<?php
/**
 * Command Class
 * If it appears that a message sent is
 * a user command, we use the command class
 * to execute the function associated with
 * the command
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */

namespace awesomeircbot\command;

use awesomeircbot\module\ModuleManager;
use awesomeircbot\user\UserManager;
use awesomeircbot\server\Server;
use config\Config;

class Command {
	
	// The full command message sent by the user
	public $fullMessage;
	
	// The nickname of the sender/commander
	public $senderNick;
	
	// The channel the command was sent on
	public $channel;
	
	// The command
	public $command;
	
	/**
	 * Construction
	 *
	 * @param object the ReceivedLine object for the command
	 */
	public function __construct($lineObject) {
		$this->fullMessage = $lineObject->message;
		$this->senderNick = $lineObject->senderNick;
		$this->channel = $lineObject->channel;
		$this->command = $lineObject->getCommand();
	}
	
	/**
	 * Execute the command through ModuleManager
	 */
	public function execute() {
		$return = ModuleManager::runCommand($this->command, $this->fullMessage, $this->senderNick, $this->channel);
		if ($return !== true) {
			if ($return == 2) {
				$server = Server::getInstance();
				$server->notify($this->senderNick, "You do not have permission to use this command. Please identify via NickServ if you have privileges, then type " . Config::$commandCharacter . "identify");
			}
		}
			
	}
}
?>