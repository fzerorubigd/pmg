<?php
namespace modules\mafia;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;
use  awesomeircbot\user\UserManager;
use modules\mafia\MafiaGame;

class MafiaTimeout extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		$game = MafiaGame::getInstance();
		$server = Server::getInstance();
		if ($game->getState() != MAFIA_TURN) return;
		$server->act(MafiaGame::$LOBBY_ROOM,"Checking time out. please wait! request from : " . $this->senderNick);
		$game->checkNightTimeout();
	}
}
