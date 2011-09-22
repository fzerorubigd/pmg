<?php
/**
 * Parsers Module Config
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
namespace modules\parsers\configs;
use awesomeircbot\module\ModuleConfig;
use awesomeircbot\line\ReceivedLineTypes;

class Parsers implements ModuleConfig {
	
	public static $mappedCommands = array(
	);
	
	public static $mappedEvents = array(
		ReceivedLineTypes::NICK => "modules\parsers\NickParser",
		ReceivedLineTypes::JOIN => "modules\parsers\JoinParser",
		ReceivedLineTypes::PART => "modules\parsers\PartParser",
		ReceivedLineTypes::KICK => "modules\parsers\KickParser",
		ReceivedLineTypes::QUIT => "modules\parsers\QuitParser",
		ReceivedLineTypes::MODE => "modules\parsers\ModeParser",
		ReceivedLineTypes::PRIVMSG => "modules\parsers\MessageParser",
		ReceivedLineTypes::CHANMSG => "modules\parsers\MessageParser",
		ReceivedLineTypes::SERVERREPLYTHREEONEONE => "modules\parsers\WhoisResponseParser",
		ReceivedLineTypes::SERVERREPLYTHREETHREEZERO => "modules\parsers\WhoisResponseParser",
		ReceivedLineTypes::SERVERREPLYTHREEZEROSEVEN => "modules\parsers\WhoisResponseParser",
		ReceivedLineTypes::SERVERREPLYTHREEFIVETHREE => "modules\parsers\NamesResponseParser",
		ReceivedLineTypes::SERVERREPLYTHREETHREETWO => "modules\parsers\TopicResponseParser",
	);
	
	public static $mappedTriggers = array(
	);

	public static $help = array(
	);
			
}
?>