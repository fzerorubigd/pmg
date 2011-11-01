<?php
namespace modules\mafia;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;
use awesomeircbot\database\Database;
use modules\mafia\MafiaGame;

class MafiaRaw extends Module {
	
	public static $requiredUserLevel = 10;
	
	public function run() {
		if ($this->getLevel($this->senderNick) < 10)
			return;
		$server = Server::getInstance();
		$cmd =  $this->parameters(1, true);
		
		$server->raw($cmd);
	}
}
