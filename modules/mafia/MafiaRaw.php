<?php
namespace modules\mafia;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;
use awesomeircbot\database\Database;
use modules\mafia\MafiaGame;

class MafiaRaw extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		$db = Database::getInstance();
		$db->updateDatabase();
	}
}
