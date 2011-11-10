<?php
/**
 * FunStuff Module Config
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
 
namespace modules\funstuff\configs;
use awesomeircbot\module\ModuleConfig;
use awesomeircbot\line\ReceivedLineTypes;

class FunStuff implements ModuleConfig {
	
	public static $mappedCommands = array(
	//	"slap" => "modules\\funstuff\Slap",
		"harass" => "modules\\funstuff\Harass",
	);
	
	public static $mappedEvents = array(
		ReceivedLineTypes::CHANMSG => "modules\\funstuff\CheckHarass",
	);
	
	public static $mappedTriggers = array(
		"/(h|H)(e|E)(r|R)(p|P)/" => "modules\\funstuff\Derp",
	);
	
	public static $help = array(
//		"slap" => array(
//			"BASE" => array(
//				"description" => "Slaps the given user", 
//				"parameters" => "<nickname>"
//			)
//		),
		
		"harass" => array(
			"BASE" => array(
				"description" => "Lists out all the nicknames and hostnames currently on the harass list",
				"parameters" => false
			),
			"add" => array(
				"description" => "Adds nicknames/hostnames to the harass list",
				"parameters" => "<nick|host> <nickname>"
			),
			"del" => array(
				"description" => "Deletes nicknames/hostnames from the harass list",
				"parameters" => "<nick|host> <nickname>"
			),
		)
	);
}
?>
