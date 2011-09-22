<?php
namespace modules\mafia;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;
use modules\mafia\MafiaGame;

class MafiaKill extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		$server = Server::getInstance();
		$game = MafiaGame::getInstance();
		
		$I = $this->senderNick;
		if ($game->getState() != MAFIA_TURN)
		{
			$server->message($I, "Not killing time!");
			return;
		}
		
		if (!$game->isIn($I))
		{
			$server->message($I, "You are not in game ;) may be next time");
			return;
		}
		$you = $this->parameters(1);
		if (!$game->isIn($you) && $you != "*")
		{
			$server->message($I, "$you is not in game ;) so why hate him/her this much?");
			return;
		}
		$game->iSayKillYou($I , $you);
	}
}
