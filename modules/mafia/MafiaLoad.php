<?php
namespace modules\mafia;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;
use modules\mafia\MafiaGame;

class MafiaLoad extends Module {
	
	public static $requiredUserLevel = 10;
	
	public function run() {
		if ($this->getLevel($this->senderNick) < 10)
			return;
		$server = Server::getInstance();
		$game = MafiaGame::getInstance();
		$name = $this->parameters(1 , true);
		if ($name)
			MafiaGame::setGameName($name);
			
		MafiaGame::loadGame();
	}
}
