<?php
/**
 * Log Module Config
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
namespace modules\mafia\configs;
use awesomeircbot\module\ModuleConfig;
use awesomeircbot\line\ReceivedLineTypes;

class Mafia implements ModuleConfig {
	
	public static $mappedCommands = array(
		"list"	=>  "modules\mafia\MafiaList",
		"vote"	=>  "modules\mafia\MafiaVote",
		"heal"	=>  "modules\mafia\MafiaHeal",
		"whois"	=>  "modules\mafia\MafiaWhois",
		"opt"	=>  "modules\mafia\MafiaOpt",
		"upd"     => "modules\mafia\MafiaRaw",
		"kick"     => "modules\mafia\MafiaKick",
		"drop"     => "modules\mafia\MafiaDrop",
		"register" => "modules\mafia\MafiaJoinGame",
		"leave" => "modules\mafia\MafiaLeaveGame",
		"kill" => "modules\mafia\MafiaKill",
		"punish" => "modules\mafia\MafiaPunish",
		"start" => "modules\mafia\MafiaStartGame",
		"restart" => "modules\mafia\MafiaReStartGame",
		"validate" => "modules\mafia\MafiaValidate",
		"timeout" => "modules\mafia\MafiaTimeout",
		"count" => "modules\mafia\MafiaCount",
		"wish" => "modules\mafia\MafiaWish",
		"voice" => "modules\mafia\MafiaVoice",
		"whoami" => "modules\mafia\MafiaWhoami",
		"save" => "modules\mafia\MafiaSave",
		"load" => "modules\mafia\MafiaLoad",
		"name" => "modules\mafia\MafiaName",
		"slap" => "modules\mafia\Slap",
		"mafia" => "modules\mafia\Mafia",
	);
	
	public static $mappedEvents = array(
		ReceivedLineTypes::JOIN => "modules\mafia\MafiaJoinChanel",
		ReceivedLineTypes::NICK => "modules\mafia\MafiaNick",
	);
	
	public static $mappedTriggers = array(
	);

	public static $help = array(
		"register" => array(
			"BASE" => array(
				"description" => "Join to the game",
				"parameters" => ""
			)
		),
		"leave" => array(
			"BASE" => array(
				"description" => "Leave the game",
				"parameters" => ""
			)
		),
		"kill" => array(
			"BASE" => array(
				"description" => "Mafia command, kill a ppl",
				"parameters" => "nick"
			)
		),
		"punish" => array(
			"BASE" => array(
				"description" => "Day command punish a ppl",
				"parameters" => "nick"
			)
		),
		"start" => array(
			"BASE" => array(
				"description" => "Start game",
				"parameters" => "mafiacount havedr"
			)
		),	
		"restart" => array(
			"BASE" => array(
				"description" => "ReStart game to register again",
				"parameters" => ""
			)
		),
		
		"raw" => array(
			"BASE" => array(
				"description" => "Send raw message ",
				"parameters" => "rawmessage"
			)
		),
		"heal" => array(
			"BASE" => array(
				"description" => "Heal in night time",
				"parameters" => "pplname"
			)
		),
		"list" => array(
			"BASE" => array(
				"description" => "List all registered and in game state",
				"parameters" => ""
			)
		),				
	);
			
}
?>
