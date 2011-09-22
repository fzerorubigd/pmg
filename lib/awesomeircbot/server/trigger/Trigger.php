<?php
/**
 * Trigger Class
 * If it appears that a message sent matches
 * a REGEX mapping, we use the trigger class
 * to run the module associated with it
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */

namespace awesomeircbot\trigger;

use awesomeircbot\module\ModuleManager;
use awesomeircbot\user\UserManager;
use awesomeircbot\server\Server;

class Trigger {
	
	// The full message sent by the user
	public $fullMessage;
	
	// The nickname of the sender/commander
	public $senderNick;
	
	// The channel the message was sent on
	public $channel;
	
	/**
	 * Construction
	 *
	 * @param object the ReceivedLine object for the command
	 */
	public function __construct($lineObject) {
		$this->fullMessage = $lineObject->message;
		$this->senderNick = $lineObject->senderNick;
		$this->channel = $lineObject->channel;
	}
	
	/**
	 * Execute the command through ModuleManager
	 */
	public function execute() {
		$return = ModuleManager::runTrigger($this->fullMessage, $this->senderNick, $this->channel);
			
	}
}
?>