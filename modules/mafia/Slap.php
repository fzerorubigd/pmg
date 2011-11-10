<?php
/**
 * Slap Module
 * Slaps the user given
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
namespace modules\funstuff;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;
use modules\mafia\MafiaGame;

class Slap extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		$server = Server::getInstance();
		$game = MafiaGame::getInstance();
		
		$I = $this->senderNick;	
		if (!$game->isIn($I))
		{
			$server->message($I, "You are not in game ;)");
			return;
		}
		
		$target = $this->parameters(1);
		if (!$game->isIn($target))
		{
			$server->message($I, "$target is not in game ;) so why hate him/her this much?");
			return;
		}
		
		$cleareString = $this->ParseString($this->parameters(2 , true));
		$server->message($this->channel, "$I slaps " . $this->parameters(1) .' '. $cleareString);
		
		
	}
	
	public function ParseString($str){
		$str = trim($str);
		if (!preg_match('/^for/i', $str) && strlen($str) > 0){
			$str = 'for '.$str;
		}
		return $str;
	}
}
?>
