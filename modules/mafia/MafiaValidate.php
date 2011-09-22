<?php
namespace modules\mafia;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;
use  awesomeircbot\user\UserManager;
use modules\mafia\MafiaGame;

class MafiaValidate extends Module {
	
	public static $requiredUserLevel = 0;
	
	public static $lastValidateTime = 0;
	public static $lastValidateUser = '';
	
	
	private function findUser($user)
	{
		$game = MafiaGame::getInstance();
		$server = Server::getInstance();
		
		foreach(UserManager::$trackedUsers as $registered => $data)
		{
			if (strtolower($user) == strtolower($registered))
			{
				$user = $registered;
				$found = true;
			}
		}
		if (!$found) 
		{
			$server->act($this->senderNick, "User $user not found in registered user!");
			$game->removeNick($user);
			return false;
		}
		return $user;
	}
	
	public function run() {
		$game = MafiaGame::getInstance();
		$server = Server::getInstance();
		
		$user =  $this->parameters(1);
		
		if (self::$lastValidateTime == 0 )
		{
			$user = $this->findUser($user);
			if (!$user)
				return;
			UserManager::remove($user);
			$server->whois($user);
			self::$lastValidateTime = time();
			self::$lastValidateUser = $user;
		}
		
		if (time() - self::$lastValidateTime > 60)
		{
			$who = $this->findUser($user);
			if (!$who)
			{
				$game->removeNick($who);				
			}
			
			$server->act($this->senderNick , "Validation of user " . self::$lastValidateUser . " done!");
			
			self::$lastValidateTime = 0;
			self::$lastValidateUser = '';
		}
		
		if (strtolower($user) == strtolower(self::$lastValidateUser) && self::$lastValidateUser != '')
		{
			$server->act($this->senderNick,"Another validate of user " . self::$lastValidateUser . " in progress! wait please.");
		}

	}
}
