<?php
/**
 * Invite Module
 * Invites a user to either the given channel, or if no channel
 * is supplied, the current channel
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
namespace modules\general;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;
use awesomeircbot\channel\ChannelManager;

class Invite extends Module {
	
	public static $requiredChannelPrivilege = "@";
	public static $requiredUserLevel = 10;
	public function run() {
		if ($this->parameters(2))
			$channel = $this->parameters(2);
		else
			$channel = $this->channel;
		
		$channelObject = ChannelManager::get($channel);
		if ($channelObject->hasPrivilegeOrHigher($this->senderNick, "@")) {
			$server = Server::getInstance();
			$server->channelInvite($this->parameters(1), $channel);
		}
	}
}
?>
