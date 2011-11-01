<?php
namespace modules\mafia;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;
use modules\mafia\MafiaGame;

class MafiaKick extends Module {
	
	public static $requiredUserLevel = 10;
	
	public function run() {
		if ($this->getLevel($this->senderNick) < 10)
			return;
		$server = Server::getInstance();
		$user =  $this->parameters(1);
		$channel = $this->parameters(2);
		$message = $this->parameters(3);
		$server->kick($user, $channel,$message);
	}
}
