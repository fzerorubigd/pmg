<?php
namespace modules\mafia;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;
use modules\mafia\MafiaGame;

class MafiaCount extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		$game = MafiaGame::getInstance();
		$server = Server::getInstance();
		
		$server->message($this->senderNick, "Player count : " . $game->getCount());
	}
}
