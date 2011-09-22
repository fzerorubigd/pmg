<?php
namespace modules\mafia;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;
use modules\mafia\MafiaGame;
use config\Config;

class MafiaDrop extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		if ($this->getLevel($this->senderNick) < 10)
			return;
		$game = MafiaGame::getInstance();
		$user =  $this->parameters(1);
		$game->removeNick($user);
	}
}
