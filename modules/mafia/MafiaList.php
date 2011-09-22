<?php
namespace modules\mafia;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;
use modules\mafia\MafiaGame;

class MafiaList extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		$game = MafiaGame::getInstance();
		$game->listAllUsers($this->senderNick);
	}
}
