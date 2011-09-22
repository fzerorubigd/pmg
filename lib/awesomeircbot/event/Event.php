<?php
/**
 * Event class
 * If it appears that a line received is
 * a mapped event, we use the event class
 * to execute the function associated with
 * the event
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */

namespace awesomeircbot\event;

use awesomeircbot\module\ModuleManager;

class Event {
	
	// The full line received which triggered the event
	public $fullLine;
	
	// The nickname of the sender/server that sent the line
	public $senderNick;
	
	// The target of the action, if applicable
	public $targetNick;
	
	// The type (numerical, see awesomeircbot\line\ReceivedLineTypes)
	public $type;
	
	// The channel, if applicable
	public $channel;
	
	/**
	 * Construction
	 *
	 * @param object the ReceivedLine object for the line which triggered the event
	 */
	public function __construct($lineObject) {
		$this->fullLine = $lineObject->line;
		$this->senderNick = $lineObject->senderNick;
		$this->type = $lineObject->type;
		$this->channel = $lineObject->channel;
		$this->targetNick = $lineObject->targetNick;
	}
	
	/**
	 * Execute the mapped module through ModuleManager
	 */
	public function execute() {
		ModuleManager::runEvent($this->type, $this->fullLine, $this->channel, $this->senderNick, $this->targetNick);
	}
}
?>