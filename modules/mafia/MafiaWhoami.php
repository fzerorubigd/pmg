<?php
namespace modules\mafia;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;
use modules\mafia\MafiaGame;

class MafiaWhoami extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		$game = MafiaGame::getInstance();
		
		$I = $this->senderNick;
		$game->whoAmI($I);
	}
}
