<?php
/**
 * ModeParser Module
 * Deals with channel modes and privileges
 *
 * NOTE- THIS IS A SYSTEM MODULE, REMOVING IT MAY
 * 	   REMOVE VITAL FUNCTIONALITY FROM THE BOT
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
namespace modules\parsers;

use awesomeircbot\module\Module;
use awesomeircbot\channel\ChannelManager;
use awesomeircbot\line\ReceivedLine;

class ModeParser extends Module {

	public static $requiredUserLevel = 0;

	public function run() {
		$channel = ChannelManager::get($this->channel);

		$line = new ReceivedLine($this->runMessage);
		$line->parse();

		$modes = array();
		if (strlen($line->message) > 2) {
			$split = str_split($line->message);
			foreach ($split as $id => $character) {
				if ($id == 0)
					$symbol = $split[0];
				else
					$modes[] = $symbol . $character;
			}
		}
		else {
			$modes[] = $line->message;
		}

		foreach ($modes as $mode) {
			switch ($mode) {
				case "+v":
					$channel->addPrivilege($line->targetNick, "+");
				break;
				case "+h":
					$channel->addPrivilege($line->targetNick, "%");
				break;
				case "+o":
					$channel->addPrivilege($line->targetNick, "@");
				break;
				case "+a":
					$channel->addPrivilege($line->targetNick, "&");
				break;
				case "+q":
					$channel->addPrivilege($line->targetNick, "~");
				break;	
			}
		}

		ChannelManager::store($this->channel, $channel);
	}
}
?>