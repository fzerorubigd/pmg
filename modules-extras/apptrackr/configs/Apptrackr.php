<?php
/**
 * Apptrackr Module Config
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
 
namespace modules\apptrackr\configs;
use awesomeircbot\module\ModuleConfig;
use awesomeircbot\line\ReceivedLineTypes;

class Apptrackr implements ModuleConfig {
	
	public static $mappedCommands = array(
		"getlink" => "modules\apptrackr\ApptrackrGetLinkFromiTunesURL",
	);
	
	public static $mappedEvents = array(
		//ReceivedLineTypes::SERVERREPLYTHREETHREETWO => "modules\apptrackr\UpdateReportsNumberInTopic",
	);
	
	public static $mappedTriggers = array(
	);
	
	public static $help = array(
		"getlink" => array(
			"BASE" => array(
				"description" => "Gets a download link for an iTunes URL from Apptrackr", 
				"parameters" => "<iTunes URL>"
			)
		),
	);
}
?>