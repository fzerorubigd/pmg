<?php
/* Awesome IRC Bot v2
 * Created by AwesomezGuy/Naikcaj/TheAwesomeGuy/Neon/Jackian/Jack Harley
 * Yes, I have a lot of names, but I no longer use any but the first 2 online
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */

require_once(__DIR__ . "/config/config.php");
require_once(__DIR__ . "/lib/awesomeircbot/awesomeircbot.inc.php");
require_once(__DIR__ . "/modules/modules.inc.php");

use config\Config;

use awesomeircbot\server\Server;

use awesomeircbot\module\ModuleManager;

use awesomeircbot\line\ReceivedLine;
use awesomeircbot\line\ReceivedLineTypes;

use awesomeircbot\command\Command;
use awesomeircbot\event\Event;
use awesomeircbot\trigger\Trigger;

use awesomeircbot\database\Database;

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

passthru('clear');

echo "Welcome to Awesome IRC Bot v2\n";
echo "Created by AwesomezGuy, follow @AwesomezGuy on Twitter\n";

if (Config::$die)
	die("READ THE CONFIG!\n\n");
if (Config::$configVersion != 4)
	die("Your config is out of date, please delete your old config and remake your config from config.example.php\n\n");

ModuleManager::initialize();

$server = Server::getInstance();

$db = Database::getInstance();
$db->updateScriptArrays();
$db->updateDatabase();

echo "\n";

while (true) {
	
	// Connect
	$server->connect();
	
	// Identify
	$server->identify();
	
	sleep(1);
	
	// NickServ
	if (Config::$nickservPassword) 
		$server->identifyWithNickServ();
	
	// Loop through the channels in the config and join them
	foreach(Config::$channels as $channel) {
		$server->join($channel);
	}
	
	// Loop-edy-loop
	$cntUpd = 0;
	while($server->connected()) {
		$line = $server->getNextLine();
		
		$line = new ReceivedLine($line);
		$line->parse();
		
		if ($line->isCommand()) {
			$command = new Command($line);
			$command->execute();
		}
		
		if ($line->isMappedEvent()) {
			$event = new Event($line);
			$event->execute();
		}
		
		if ($line->isMappedTrigger()) {
			$trigger = new Trigger($line);
			$trigger->execute();
		}
		$cntUpd++;
		if ($cntUpd> 1000)
		{
			$db->updateDatabase();
			$cntUpd = 0;
		}
	}
	// Disconnected, Give the server 2 seconds before we attempt a reconnect
	sleep(2);
}
?>
