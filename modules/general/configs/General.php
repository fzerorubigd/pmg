<?php
/**
 * General Module Config
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
namespace modules\general\configs;
use awesomeircbot\module\ModuleConfig;
use awesomeircbot\line\ReceivedLineTypes;

class General implements ModuleConfig {
	
	public static $mappedCommands = array(
		"join" => "modules\general\Join",
		"message" => "modules\general\Message",
		"act" => "modules\general\Act",
		"invite" => "modules\general\Invite",
		"part" => "modules\general\Part",
		"cycle" => "modules\general\Cycle"
	);
	
	public static $mappedEvents = array(
	);
	
	public static $mappedTriggers = array(
	);

	public static $help = array(
		"join" => array(
			"BASE" => array(
				"description" => "Joins the given channel",
				"parameters" => "<#channel>"
			)
		),
		"part" => array(
			"BASE" => array(
				"description" => "Parts the given channel, or if no channel is supplied, the current channel",
				"parameters" => "[<#channel>]"
			)
		),
		"message" => array(
			"BASE" => array(
				"description" => "Messages the provided nick/channel with the provided message",
				"parameters" => "<nick/#channel> <message>"
			)
		),
		"invite" => array(
			"BASE" => array(
				"description" => "Invites the given user to the given channel, or if no channel is supplied, the current channel",
				"parameters" => "<nick> [<#channel>]"
			)
		),
		"cycle" => array(
			"BASE" => array(
				"description" => "Parts and then rejoins the given channel, or if no channel is supplied, the current channel",
				"parameters" => "[<#channel>]"
			)
		),
		"act" => array(
			"BASE" => array(
				"description" => "Messages the provided nick/channel with the provided message, formatted as an ACTION message (/me)",
				"parameters" => "<nick/#channel> <message>"
			)
		),
	);
}
?>