<?php
namespace modules\mafia;
defined('MAFIA_TURN')   	|| define('MAFIA_TURN' , 1);
//!defined('PRE_DAY_TURN') 	|| define('PRE_DAY_TURN' , 2);
defined('DAY_TURN')     	|| define('DAY_TURN' , 2);

defined('UNDEF_PPL')    	|| define('UNDEF_PPL' , 0);
defined('NORMAL_PPL')   	|| define('NORMAL_PPL' , 1);
defined('MAFIA_PPL')    	|| define('MAFIA_PPL' , 2);
defined('DR_PPL')       	|| define('DR_PPL' , 3);
defined('DETECTIVE_PPL')  	|| define('DETECTIVE_PPL' , 4);
defined('NOHARM_PPL')       || define('NOHARM_PPL' , 5);

use awesomeircbot\server\Server;
use config\Config;


class MafiaGame 
{
	private static $instanse;
	
	/**
	 * Lobby room (City room), bot must have access to take ownership
	 * @var string
	 */
	static $LOBBY_ROOM = "##PMG";
	/**
	 * 
	 * Mafia room , bot must have access to take ownership
	 * @var unknown_type
	 */
	static $MAFIA_ROOM = "##PMGMafias";
	
	/**
	 * 
	 * Night time out
	 * @var integer
	 */
	static $NIGHT_TIMEOUT = 180;
	
	/**
	 * 
	 * List of in game nicks
	 * @var array
	 */
	private $inGameNicks = array ();
	/**
	 * 
	 * List of in game nicks with data 
	 * @var array
	 */
	private $inGamePart  = array ();
	
	/**
	 * 
	 * Time, MAFIA_TURN and DAY_TURN
	 * @var integer
	 */
	private $state = 0;
	 
	 private $killVotes;
	 private $punishVotes;
	 
	 
	 private $lobbyPass ;
	 private $mafiaPass ;
	 
	 private $drVote;
	 private $detectiveVote;
	 
	 
	 private $nightTurnTime = 0;
	 
	 
	 private $lastDead;
	 private $lastWish;
	 
	 
	 static $SHOW_MAFIA_COUNT = 0;
	 static $WON_STATE_NORMAL = 1; 
	 static $DEAD_IS_TALKING  = 0;
	
	
	/**
	 * 
	 * Set mode for channel
	 * @param string $channel channel name
	 * @param string $mode IRC mode
	 */
	private function setMode($channel ,$mode)
	{
		$this->say("ChanServ","SET $channel MLOCK $mode");
		sleep(1);
	}
	/**
	 * 
	 * Claim channel ownership
	 * @param string $channel
	 * @param string $who
	 */
	
	private function setOp($channel, $who)
	{
		$this->say("ChanServ","OP  " . $channel . " " . $who);
		sleep(1);
	}
	
	/**
	 * 
	 * Say something to channel or people
	 * @param string $who channel/nick
	 * @param string $message
	 */
	
	private function say($who, $message)
	{
		//static $lastTime = 0;
		//static $count = 0;
		
		//Try to avoid flood :D
		//$count++;
/*		if ($lastTime > time() - 3 && $count > 5)
		{
			$lastTime = time();
			$count = 0;
			sleep(1);
		}
		elseif ($lastTime < time() - 3)
		{
			$lastTime = 0;
			$count = 0;
			sleep(1);
		} */
		$server = Server::getInstance();
		$server->message($who,$message);
		sleep(1);
	}	
	
	/**
	 * 
	 * Send /me message
	 * @param string $who nick/channel
	 * @param string $message
	 */
	private function act($who, $message)
	{
		$server = Server::getInstance();
		$server->act($who,$message);
		sleep(1);
	}	
		
	/**
	*
	* Send notice
	* @param string $who nick/channel
	* @param string $message
	*/	
	private function notice($who, $message)
	{
		$server = Server::getInstance();
		$server->notice($who,$message);
		sleep(1);
	}	
	
	/**
	 * 
	 * Set channel topic
	 * @param string $channel
	 * @param string $topic
	 */
	private function topic($channel, $topic)
	{
		$server = Server::getInstance();
		$server->topic($channel, $topic, true);		
		sleep(1);
	}
	
	/**
	 * 
	 * Invite $who to $channel
	 * @param string $who
	 * @param string $channel
	 */
	private function invite($who, $channel)
	{
		$server = Server::getInstance();
		$server->channelInvite($who, $channel);
	}
	
	/**
	 * 
	 * Kick user from channel
	 * @param string $who
	 * @param string $channel
	 * @param string $why
	 */
	
	private function kick($who , $channel , $why)
	{
		$server = Server::getInstance();
		$server->kick($who , $channel , $why);
	}
	
	private function join($channel)
	{
		$server = Server::getInstance();
		$server->join($channel);	
		sleep(1);
	}
	
	/*
	 * 
	 * Do night, set channel to +m every one to -v
	 */
	private function doNight()
	{
		$server = Server::getInstance();
		//1- Set channel mode to modorated
		$this->setMode(self::$LOBBY_ROOM , "+m");
		//Set mode for all alive player to +v
		$mode = " -";
		$ppl = '';
		$cnt = 0;
		foreach ($this->inGamePart as $nick => $part)
		{
			$mode .= "v";
			$ppl  .= " $nick";
			$cnt++;
			if ($cnt >=3 )
			{
				$server->raw("MODE " . self::$LOBBY_ROOM . $mode . $ppl);
				$mode =" -";
				$ppl = '';
				$cnt = 0;
				sleep(1);
			}
		}
		
		//Send the command
		if ($cnt > 0 )
			$server->raw("MODE " . self::$LOBBY_ROOM . $mode . $ppl);	
	}
	
	/**
	 * 
	 * Go to day mode
	 */
	
	private function doDay()
	{
		$server = Server::getInstance();
		//1- Set channel mode to modorated
		$this->setMode(self::$LOBBY_ROOM , "+m");
		//Set mode for all alive player to +v
		$mode = " +";
		$ppl = '';
		$cnt = 0;
		foreach ($this->inGamePart as $nick => $part)
		{
			if ($part['alive'] || self::$DEAD_IS_TALKING)
			{
				$mode .= "v";
				$ppl  .= " $nick";
				$cnt++;
				if ($cnt >=3 )
				{
					$server->raw("MODE " . self::$LOBBY_ROOM . $mode . $ppl);
					$mode =" +";
					$ppl = '';
					$cnt = 0;
					sleep(1);
				}
			}
		}
		
		//Send the command
		if ($cnt > 0 )
			$server->raw("MODE " . self::$LOBBY_ROOM . $mode . $ppl);		
	}	
	/**
	 * 
	 * Random function
	 * @param integer $min
	 * @param integer $max
	 * @return integer
	 */
	static function rand($min, $max)
	{
		if (function_exists("mt_rand"))
			return mt_rand($min,$max);
		else
		{
			echo "mt_rand is not available!!";
			return rand ($min,$max);
		}
	}
		
	/**
	 * 
	 * Its singlton, so its a private method
	 */
	private function __construct()
	{
		//Get ownership of channels
		$this->setOp(self::$LOBBY_ROOM , Config::$nickname);
		$this->setOp(self::$MAFIA_ROOM , Config::$nickname);
		
		//Remove password if any, just in case
		$this->setMode(self::$LOBBY_ROOM, "-k *");
		$this->setMode(self::$MAFIA_ROOM, "-k *");
		//Set private and secret flags :)
		$this->setMode(self::$MAFIA_ROOM, "+ps");
		//Remove modorated flag
		$this->setMode(self::$LOBBY_ROOM, "-m");
		
		//Join to channel (in case of drop on flood :D )
		$this->join(self::$LOBBY_ROOM);
		$this->join(self::$MAFIA_ROOM);		

		//Yet again, set the OP since some time when you join, you need to set it again :(
		$this->setOp(self::$LOBBY_ROOM , Config::$nickname);
		$this->setOp(self::$MAFIA_ROOM , Config::$nickname);
		
		//Change topics
		$this->topic(self::$MAFIA_ROOM , "Welcome to Persian Mafia Game! but leave this channel soon, its not place for you to stay!! - Channel is logged!");
		$this->topic(self::$LOBBY_ROOM , "Register for game to play! see http://cyberrabbits.net/non/mafia/");		
	}
	
	/**
	 * 
	 * Format message and add color to it
	 * @param integer $code IRC Color code, vary in different clients
	 * @param string $message
	 * @return string
	 */
	public static function colorize($code , $message)
	{
		return chr(3) . $code . $message . chr(3) . "1"; 
	}
	
	/**
	 * 
	 * Set message to bold
	 * @param string $message
	 * @return string
	 */
	public static function bold($message)
	{
		return chr(2) . $message . chr(2);
	}
	
	/**
	 * 
	 * Both bold and color
	 * @param integer $code
	 * @param string $message
	 * @return string
	 */
	
	public static function boco($code , $message)
	{
		return MafiaGame::bold(MafiaGame::colorize($code, $message));
	}

	/**
	 * 
	 * Get current game object
	 * @param boolean $force force to recreate?
	 * @return MafiaGame
	 */
	public static function getInstance($force = false)
	{
		if (!self::$instanse)
		{
			self::$instanse = new MafiaGame();
			return self::$instanse;
		}	
		
		if ($force)
		{
			if (count(self::$instanse->inGameNicks))
			{
				$mode = " -";
				$ppl = '';
				foreach (self::$instanse->inGamePart as $nick => $part)
				{
					$mode .= "v";
					$ppl  .= " $nick";
				}
				//Send the command
				$server = Server::getInstance();
				$server->raw("MODE " . self::$LOBBY_ROOM . $mode . $ppl);				
			}
			self::$instanse = new MafiaGame();
		}
		return self::$instanse;
	} 
	
	/**
	 * 
	 * Add nick to game
	 * @param string $nick
	 */
	public function addNick($nick)
	{
		if ($this->state) 
		{
			$this->say($nick , MafiaGame::colorize(2, "The game is already on, sorry!"));
			return;
		};
		
		if ($this->isIn($nick))
		{
			$this->say($nick ,MafiaGame::bold(MafiaGame::colorize(2,"You are already in game, for exit use command !leave")));
			return;
		}
		$this->inGameNicks[strtolower($nick)] = $nick;
		$this->inGamePart[strtolower($nick)] = array ('mode'=>NORMAL_PPL, 'alive'=> true );
		
		$this->say($nick , MafiaGame::colorize(3,$nick) . " Welcome to game, wait for start :D you can read the manual at http://cyberrabbits.net/non/mafia/");
		$this->act( self::$LOBBY_ROOM,MafiaGame::colorize(3,$nick) . " joined to the game :), total players: {$this->getCount()}");
	}
	
	/**
	 * 
	 * User change nick handler
	 * @param string $from old nick
	 * @param string $to new nick
	 */
	public function changeNick($from , $to)
	{
		if ($this->isIn($from))
		{
			$this->inGameNicks[strtolower($to)] = $this->inGameNicks[strtolower($from)];
			$this->inGamePart[strtolower($to)] = $this->inGamePart[strtolower($from)];
			
			unset ($this->inGameNicks[strtolower($from)]);
			unset ($this->inGamePart[strtolower($from)]);
			
			if ($this->state == MAFIA_TURN)
			{
				if (isset($this->killVotes[strtolower($from)]))
				{
					$this->killVotes[strtolower($to)] = $this->killVotes[strtolower($from)];
					unset($this->killVotes[strtolower($from)]);
				}
				
				foreach($this->killVotes as &$votes)
				{
					if (strtolower($votes) == strtolower($from))
					{
						$votes = $to;
					}
				}
				
				if (strtolower($this->drVote) == strtolower($from))
					$this->drVote = $to;
				
			}
			
			if ($this->state == DAY_TURN)
			{
				if (isset($this->punishVotes[strtolower($from)]))
				{
					$this->punishVotes[strtolower($to)] = $this->punishVotes[strtolower($from)];
					unset($this->punishVotes[strtolower($from)]);
				}
				
				foreach($this->punishVotes as &$votes)
				{
					if (strtolower($votes) == strtolower($from))
					{
						$votes = $to;
					}
				}				
			}
			
			$this->act(self::$LOBBY_ROOM, MafiaGame::boco(4,$from) . " Changed his/her nick to " . MafiaGame::boco(2,$to));
		}
	}
	
	/**
	 * 
	 * Check if user is in game 
	 * @param string $nick
	 */
	
	public function isIn($nick)
	{
		return isset($this->inGameNicks[strtolower($nick)]);
	}
	
	/**
	 * 
	 * Remove nick from game
	 * @param string $nick
	 */
	public function removeNick($nick)
	{

		
		if (!$this->isIn($nick)) 
			return;
		if ($this->state) 
		{
			if (!$this->inGamePart[strtolower($nick)]['alive'] )
			{
				$this->say($nick, "You are dead, so its ok to leave. just say goodbye to other people ;D");
				return;
			}
			
			$this->inGamePart[strtolower($nick)]['alive'] = false;

			
			if ($this->state == MAFIA_TURN)
			{
				if (isset($this->killVotes[strtolower($nick)]))
				{
					unset($this->killVotes[strtolower($nick)]);
				}
				
				foreach($this->killVotes as &$votes)
				{
					if (strtolower($votes) == strtolower($nick))
					{
						$votes = false;
					}
				}
				
				if (strtolower($this->drVote) == strtolower($nick))
					$this->drVote = false;
				
			}
			
			if ($this->state == DAY_TURN)
			{
				if (isset($this->punishVotes[strtolower($nick)]))
				{
					unset($this->punishVotes[strtolower($nick)]);
				}
				
				foreach($this->punishVotes as &$votes)
				{
					if (strtolower($votes) == strtolower($nick))
					{
						$votes = false;
					}
				}				
			}
			
			$nick = MafiaGame::boco($nick);
			$this->say(MafiaGame::$LOBBY_ROOM,"User $nick is leaving! all votes to him/her are set to not vote!");			
			$this->say(MafiaGame::$MAFIA_ROOM,"User $nick is leaving! all votes to him/her are set to not vote!");	
		}
		else
		{
			unset($this->inGameNicks[strtolower($nick)]);
			unset($this->inGamePart[strtolower($nick)]);
			$this->say($nick , "See you soon :)");
			$this->say( self::$LOBBY_ROOM,"$nick leave the game :(, total players: {$this->getCount()}");			
		}
	}
	
	/**
	 * 
	 * Get count of registered players
	 * @return integer
	 */
	public function getCount()
	{
		return count($this->inGameNicks);
	}
	
	/**
	 * 
	 * Chack if dr is dead?
	 * @return boolean
	 */
	private function isDrDead()
	{
		foreach ($this->inGamePart as $nick => $data )
		{
			if ($this->inGamePart[strtolower($nick)]['mode'] == DR_PPL)
				return !$this->inGamePart[strtolower($nick)]['alive'];
		}
		return true;
	}

	/**
	*
	* Chack if detective is dead?
	* @return boolean
	*/
	private function isDetectiveDead()
	{
		foreach ($this->inGamePart as $nick => $data )
		{
			if ($this->inGamePart[strtolower($nick)]['mode'] == DETECTIVE_PPL)
				return !$this->inGamePart[strtolower($nick)]['alive'];
		}
		return true;
	}	
	
	/**
	 * 
	 * Say each player what he/she is and ..
	 */
	private function startInfo()
	{
		$this->topic(self::$MAFIA_ROOM , "Game in progress, and every thing is logged. Its mafia room!");
		$this->topic(self::$LOBBY_ROOM , "Game in progress, and every thing is logged. Its city room!");
		foreach ($this->inGamePart as $nick => $data)
		{
			if ($data['mode'] == MAFIA_PPL)
			{
				$this->say($nick, MafiaGame::boco(9,"You are mafia!!"));
				$this->say($nick, "You are mafia :D Please join " . self::$MAFIA_ROOM  . " and " . self::$LOBBY_ROOM);
				$this->say($nick, self::$MAFIA_ROOM  . " Password : " . $this->mafiaPass . " and " . self::$LOBBY_ROOM ); #. " Password : " . $this->lobbyPass);
					
				$this->invite($nick , self::$MAFIA_ROOM);
				$this->invite($nick , self::$LOBBY_ROOM);
				sleep(1);
				$this->say($nick, "Use this command : /join " . self::$MAFIA_ROOM . ' ' . $this->mafiaPass);
			}
			else 
			{
				$this->say($nick, MafiaGame::boco(9,"You are <NOT> mafia!"));
				$this->say($nick, "The game begin, go to sleep! (Join " . self::$LOBBY_ROOM . " room please and " . 
							"stay away from " . self::$MAFIA_ROOM . " its dangerous!)" );
				$this->say($nick, self::$LOBBY_ROOM ); #. " Password : " . $this->lobbyPass);

				$this->invite($nick , self::$LOBBY_ROOM);	
			}
			
			if ($data['mode'] == DR_PPL)
			{
				$this->say($nick,MafiaGame::bold("You are docter!, use " . 
					MafiaGame::colorize(2,"!heal") . " command in PRIVATE to heal one ppl in NIGHT!"));
			}
			
			if ($data['mode'] == DETECTIVE_PPL)
			{
				$this->say($nick,MafiaGame::bold("You are detective!, use " . 
					MafiaGame::colorize(2,"!whois") . " command in PRIVATE to identify one ppl each NIGHT!"));
			}		
			
			if ($data['mode'] == NOHARM_PPL)
			{
				$this->say($nick,MafiaGame::bold("You are Invulnerable! you die only with punish command! "));
			}						
			sleep(2);
		}
		
		$this->say(self::$LOBBY_ROOM ,MafiaGame::boco(2, "The game is on! gg and have fun!"));
			
	}
	
	/**
	 * 
	 * Choose $count from $list
	 * @param array $list
	 * @param integer $count
	 * @return array 
	 */
	
	private function randSelectFrom(&$list , $count)
	{
		$result = array();
		while ($count)
		{
			$count--;
			sort($list); //To fix the broken keys
			$rand = self::rand(0, count($list) - 1);
			$result[] = $list[$rand];
			unset($list[$rand]);
		}
		
		return $result;
	}
	
	/**
	 * 
	 * Start the game!
	 * @param integer $mafia mafia count
	 * @param integer $dr has dr or not, 0 for not
	 * @param integer $detective has detectiv or not, 0 for not
	 * @param integer $noharm has noharm or not, 0 for not
	 */ 
	
	public function start($mafia,$dr = 0,$detective = 0, $noharm = 0)
	{
		$normal = $this->getCount() - $mafia;
		if ($this->state <> 0 )
			return;
		
		$cnt = $mafia;
		if ($dr)
			$cnt++;
		if ($detective)
			$cnt++;
		if ($noharm)
			$cnt++;
		if ($cnt > $this->getCount())
		{
			$this->say(self::$LOBBY_ROOM,sprintf("You need at least %d player but you are %d",$cnt,$this->getCount()));
			return;
		}
		
		$haveDr = $dr ? " One " : " No "; 
		$haveDet = $detective ? " One " : " No ";
		$haveNoHarm = $noharm ? " One " : " No ";
		$this->act(self::$LOBBY_ROOM,self::bold("Starting game with $mafia Mafia(s) and $haveDr Dr and $haveDet Detective and $haveNoHarm Invulnerable."));
		
		$this->setOp(self::$MAFIA_ROOM  , Config::$nickname);
		$this->setOp(self::$LOBBY_ROOM  , Config::$nickname);

		$this->setMode(self::$LOBBY_ROOM , "-m");
		$this->setMode(self::$MAFIA_ROOM , "-m");
		
		//$this->lobbyPass = self::rand(1 , 1000000);
		$this->mafiaPass = self::rand(1 , 1000000);
		
		//First kick all, from mafia channel
		foreach ($this->inGameNicks as $nick => $data)
		{
			$this->kick($nick , self::$MAFIA_ROOM  ,"The game is going to begin!");
		}

		
		$this->setMode(self::$MAFIA_ROOM , "+k " . $this->mafiaPass);
		//$this->setMode(self::$LOBBY_ROOM , "+k " . $this->lobbyPass);
		
		$listOfUsers = array_keys($this->inGameNicks);
		
		if ($dr)
		{
			$result = $this->randSelectFrom($listOfUsers, 1);
			foreach ($result as $who)
				$this->inGamePart[strtolower($who)] = array ('mode'=>DR_PPL, 'alive'=> true );
			$this->say(self::$LOBBY_ROOM, sprintf('DEBUG : Choose %d dr from %d player',count($result),count($listOfUsers) + 1));
		}
		
		
		if ($detective)
		{
			$result = $this->randSelectFrom($listOfUsers, 1);
			foreach ($result as $who)
				$this->inGamePart[strtolower($who)] = array ('mode'=>DETECTIVE_PPL, 'alive'=> true );
			$this->say(self::$LOBBY_ROOM, sprintf('DEBUG : Choose %d detective from %d player',count($result),count($listOfUsers) + 1));
		}
		
		if ($noharm)
		{
			$result = $this->randSelectFrom($listOfUsers, 1);
			foreach ($result as $who)
				$this->inGamePart[strtolower($who)] = array ('mode'=>NOHARM_PPL, 'alive'=> true );
			$this->say(self::$LOBBY_ROOM, sprintf('DEBUG : Choose %d inv from %d player',count($result),count($listOfUsers) + 1));
		}	
		
		$result = $this->randSelectFrom($listOfUsers, $mafia);
		foreach ($result as $who)
			$this->inGamePart[strtolower($who)] = array ('mode'=>MAFIA_PPL, 'alive'=> true );
		$this->say(self::$LOBBY_ROOM, sprintf('DEBUG : Choose %d mafia from %d player',count($result),count($listOfUsers) + $mafia));

		$this->state = MAFIA_TURN;

		$this->startInfo();
		$this->sayStatus();
		return true;
	}
	
	/**
	 * 
	 * Get people type
	 * @param string $nick
	 * @return integer
	 */
	public function getTypeOf($nick)
	{
		if (!$this->isIn($nick))
			return false;
		return $this->inGamePart[strtolower($nick)]['mode'];
	}
	
	/**
	 * 
	 * If people is alive or dead?
	 * @param string $nick
	 * @return boolean
	 */
	public function isAlive($nick)
	{
		if (!$this->isIn($nick))
			return false;
		return $this->inGamePart[strtolower($nick)]['alive'];		
	}
	
	/**
	 * 
	 * Get current state (Day or night?)
	 * @return integer
	 */
	
	public function getState()
	{
		return $this->state;
	}
	
	/**
	 * 
	 * Prepare vote system for kills
	 */
	
	private function prepareKillVote ()
	{
		$this->killVotes = array();
		foreach ($this->inGamePart as $nick => $data)
		{
			if ($data['mode'] == MAFIA_PPL && $data['alive'])
				$this->killVotes[strtolower( $nick)] = false;
		}
	}
	
	/**
	 * 
	 * Prepare vote system for punish
	 */
	
	private function preparePunishVote ()
	{
		$this->punishVotes = array();
		foreach ($this->inGamePart as $nick => $data)
		{
			if ($data['alive'])
				$this->punishVotes[strtolower( $nick)] = false;
		}		
	}
	
	/**
	 * 
	 * Show list of users, in game show their dead/alive status too.
	 * @param string $user who requested?
	 */
	public function listAllUsers($user)
	{
		$this->act($user , "Get user list , player count : " . count($this->inGameNicks));
		$count = 0;
		if ($this->state)
		{
			foreach ($this->inGamePart as $nick => $data)
			{
				if ($data['alive'])
				{
					$code = 2;
					$alive = "ALIVE!";
				}
				else
				{
					$code = 3;
					$alive = "DEAD!";
				}
				$this->say($user, MafiaGame::boco($code, $nick) . " is " . MafiaGame::bold($alive));
				$count ++;
				if ($count>5)
				{
					sleep(1);
					$count = 0;
				}
			}
		}
		else
		{
			foreach ($this->inGameNicks as $nick => $data)
			{
				$this->say($user, MafiaGame::boco(2, $nick) . " is in the game.");
				$count ++;
				if ($count>5)
				{
					sleep(1);
					$count = 0;
				}
			}		
		}
	}
	
	/**
	 * 
	 * Get alive count
	 * @return integer
	 */
	
	public function getAliveCount()
	{
		$result = 0;
		foreach ($this->inGamePart as $nick => $data)
		{
			if ($data['alive'])
				$result++;
		}
		
		return $result;
	}
	
	/**
	 * 
	 * get dead count
	 * @return integer
	 */
	
	public function getDeadCount()
	{
		$result = 0;
		foreach ($this->inGamePart as $nick => $data)
		{
			if (!$data['alive'])
				$result++;
		}
		
		return $result;
	}	
	
	/**
	 * 
	 * Get mafia count
	 * @return integer
	 */
	
	public function getMafiaCount()
	{
		$result = 0;
		foreach ($this->inGamePart as $nick => $data)
		{
			if ($data['mode'] == MAFIA_PPL && $data['alive'])
				$result++;
		}
		
		return $result;
	}	
	
	
	/**
	 * 
	 * Get normal people count
	 * @return integer
	 */
	
	public function getPplCount()
	{
		$result = 0;
		foreach ($this->inGamePart as $nick => $data)
		{
			if ($data['mode'] != MAFIA_PPL  && $data['alive'])
				$result++;
		}
		
		return $result;		
	}
	
	/**
	 * 
	 * Report game status on finish, re-create the game
	 */
	
	public static function report()
	{
		$game = self::getInstance();
		$count = 0;
		foreach ($game->inGamePart as $nick => $data)
		{
			$type = $game->getTypeOf($nick) === MAFIA_PPL ? 'Mafia' : 'NOT Mafia';
			$type .= $game->getTypeOf($nick) === DR_PPL ? ' AND Doctor' : '';
			$type .= $game->getTypeOf($nick) === DETECTIVE_PPL ? ' AND Detective' : '';
			$type .= $game->getTypeOf($nick) === NOHARM_PPL ? ' AND Invulnerable' : '';
			$type = MafiaGame::boco(6,$type);
			$aliveOrDead = $game->isAlive($nick) ? 'Alive' : 'Dead';
			$aliveOrDead = MafiaGame::boco(7,$aliveOrDead);
			$cNick = MafiaGame::boco(2,$nick);
			$game->say(self::$LOBBY_ROOM, "$cNick was $type and is $aliveOrDead "); 
			
			$count++;
			
			if ($count > 5 )
			{
				sleep(1);
				$count = 0;
			}
		}
		$game->setMode(self::$LOBBY_ROOM ,  "-m");
		$game->say(self::$LOBBY_ROOM,"Please leave " . self::$MAFIA_ROOM);
		$game->say(self::$MAFIA_ROOM,"Please leave " . self::$MAFIA_ROOM);
		//last:D kick all, from mafia channel
		$game = self::getInstance();
		foreach ($game->inGameNicks as $nick => $data)
		{
			$game->kick($nick , self::$MAFIA_ROOM  ,"The game is done, you need to leave!");
		}
		
		$rand = self::rand(100000,999999);
		$game->setMode(self::$MAFIA_ROOM , "+k " . $rand);
		
		$server = Server::getInstance();
		
		$mode = " -";
		$ppl = '';
		foreach ($game->inGamePart as $nick => $part)
		{
			$mode .= "v";
			$ppl  .= " $nick";
			$cnt++;
			if ($cnt >=3 )
			{
				$server->raw("MODE " . self::$LOBBY_ROOM . $mode . $ppl);
				$mode =" -";
				$ppl = '';
				$cnt = 0;
				sleep(1);
			}
		}
		
		//Send the command
		if ($cnt > 0)
			$server->raw("MODE " . self::$LOBBY_ROOM . $mode . $ppl);
			
		self::getInstance(true);
	}
	
	/**
	 * 
	 * Say status in start of daay
	 * @param boolean $killed is anyone killed 
	 */
	public function sayStatus($killed = true)
	{
		if ($this->checkWinState())
			return;
		switch ($this->state)
		{
			case MAFIA_TURN :
				$this->prepareKillVote();
				//$this->setMode(self::$LOBBY_ROOM , "+m");
				$this->doNight();
				$this->drVote = $this->isDrDead();
				$this->detectiveVote = $this->isDetectiveDead();
				$this->say(self::$MAFIA_ROOM,MafiaGame::bold("Your turn to kill!! use " .MafiaGame::colorize(2, "!kill") . " command to vote"));
				$this->act(self::$LOBBY_ROOM,"Good night ppl ;)");
				$this->nightTurnTime = time();
				break;
			case DAY_TURN :
				$this->preparePunishVote();
				//$this->setMode(self::$LOBBY_ROOM , "-m");
				$this->doDay();
				$this->act(self::$MAFIA_ROOM,"Your turn to hide!!");
				if ($killed)
				{
					$this->say(self::$LOBBY_ROOM,
									MafiaGame::bold("Hi ppl, there is a dead! lets find the killer and punish him/her. use " .
										MafiaGame::colorize(2, "!punish") . " command"));
				}
				else
				{
					$this->say(self::$LOBBY_ROOM,
									MafiaGame::bold("Hi ppl, No one dead. peeowh!! either its a doctor's job or mafia trick :D, but who care? use " .
										MafiaGame::colorize(2, "!punish") . " command"));					
				}
				break;			
		}
	}
	
	/**
	 * 
	 * Check if game meet the end or not :D
	 * @return boolean
	 */
	public function checkWinState()
	{
		if (self::$SHOW_MAFIA_COUNT)
			$this->act(self::$LOBBY_ROOM, MafiaGame::bold(sprintf("There is %d player, %d dead and %d mafia player",
				$this->getCount(),
				$this->getDeadCount(),
				$this->getMafiaCount() )));
		else
			$this->act(self::$LOBBY_ROOM,MafiaGame::bold(sprintf("There is %d player, %d dead",
				$this->getCount(),
				$this->getDeadCount())));
						
		if (self::$WON_STATE_NORMAL)
		{
			if ($this->getPplCount() == $this->getMafiaCount() )
			{
				$this->act(self::$LOBBY_ROOM ,MafiaGame::boco(3, "Mafia won!"));
				$this->state = 0;
				self::report();
				return true;
			}
		}
		else 
		{
			if ($this->getPplCount() == 0 )
			{
				$this->act(self::$LOBBY_ROOM , MafiaGame::boco(3, "Mafia won!"));
				$this->state = 0;
				self::report();
				return true;
			}		
		}
		
		if ($this->getMafiaCount() == 0)
		{
			$this->act(self::$LOBBY_ROOM , MafiaGame::boco(4, "Ppl won!"));
			$this->state = 0;
			self::report();
			return true;
		}
		
		return false;		
	}

	/**
	 * 
	 * Mafia kill command
	 * @param string $I
	 * @param string $you
	 */
	public function iSayKillYou($I , $you)
	{
		$I = strtolower( $I);
		$you = strtolower($you);
		if ($this->state != MAFIA_TURN )
		{
			$this->say($I ,"Wow! you are mad! its day time!");
			return;
		}
		
		if ((($this->isIn($you) && $this->isAlive($you)) || $you == "*") 
			&& $this->isAlive($I) 
			&& $this->getTypeOf($I) == MAFIA_PPL 
			&& isset($this->killVotes[$I]))
		{
			$this->killVotes[$I] = $you;
			$this->say(self::$MAFIA_ROOM,"$I vote for killing $you");
		}
		else
		{
			$this->say($I,"Your vote not accepted!");
		}

		return $this->nightTimeEnd();
		/*
		foreach ($this->killVotes as $vote)
			if ($vote === false)
				return false;
		$result = array_count_values ($this->killVotes);
		$max = -1;
		$who = '';
		$hasDuplicate = false;
		foreach ($result as $dead => $wanted)
		{
			if ($wanted == $max)
				$hasDuplicate = true;
			elseif ($wanted > $max)
			{
				$who = $dead;
				$max = $wanted;
				$hasDuplicate = false;
			}
		}
		
		if ($hasDuplicate)
		{
			$this->say(self::$MAFIA_ROOM,MafiaGame::colorize(4,"There is a tie! please some one fix his/her vote!"));
			return;
		}
		
		if (!$this->drVote)
		{
			$this->act(self::$LOBBY_ROOM,"Waiting for dr to vote :D");
			return false;
		}		
		
		if ($who != "*" && strtolower($who) != strtolower($this->drVote)){
			$this->inGamePart[strtolower($who)]['alive'] = false;
			$this->state = DAY_TURN;
			$this->say(self::$MAFIA_ROOM, "You kill  " .  MafiaGame::boco(2,  $who));
			$this->say(self::$LOBBY_ROOM, "ALERT!!! They kill " .  MafiaGame::boco(2,  $who) . ", lets find killer!");
			$this->say( $who,MafiaGame::bold("You are dead! please respect others and be quiet. Thanks."));
		}
		else
		{
			$this->state = DAY_TURN;
			$this->say(self::$MAFIA_ROOM, "Nobody killed :D");
			$this->say(self::$LOBBY_ROOM, "No body kiled last night! WOOOW :D but lets hunt some of them!");
		}
		$this->listAllUsers(self::$LOBBY_ROOM);
		$this->sayStatus();
		return $who; */
	}
	
	/**
	 * 
	 * Check for night if ended
	 * @return boolean
	 */
	private function nightTimeEnd()
	{
		foreach ($this->killVotes as $vote)
			if ($vote === false)
			{
				$this->act(self::$LOBBY_ROOM,"Waiting for mafias to vote :D");
				return false;
			}
		$result = array_count_values ($this->killVotes);
		$max = -1;
		$who = '';
		$hasDuplicate = false;
		foreach ($result as $dead => $wanted)
		{
			if ($wanted == $max)
				$hasDuplicate = true;
			elseif ($wanted > $max)
			{
				$who = $dead;
				$max = $wanted;
				$hasDuplicate = false;
			}
		}
		
		if ($hasDuplicate)
		{
			$this->say(self::$MAFIA_ROOM,MafiaGame::colorize(4,"There is a tie! please some one fix his/her vote!"));
			return false;
		}

		if (!$this->drVote)
		{
			$this->act(self::$LOBBY_ROOM,"Waiting for dr to vote !");
			return false;
		}	
		
		if (!$this->detectiveVote)
		{
			$this->act(self::$LOBBY_ROOM,"Waiting for detective to do his job!");
			return false;
		}					
		
		if ($who != "*" && 
			strtolower($who) != strtolower($this->drVote) && 
			$this->inGamePart[strtolower]['mode'] != NOHARM_PPL)
		{
			$this->inGamePart[strtolower($who)]['alive'] = false;
			$this->state = DAY_TURN;
			$this->say(self::$MAFIA_ROOM, "You kill  " .  MafiaGame::boco(2,  $who));
			$this->say(self::$LOBBY_ROOM, "ALERT!!! They kill " .  MafiaGame::boco(2,  $who) . ", lets find killer!");
			$this->say( $who,MafiaGame::bold("You are dead! please respect others and be quiet. Thanks. you can use " .
				MafiaGame::colorize(3, '!wish') . " command ONLY ONCE!! to make your last wish."
				));
			$sayMe = $who;
		}
		else
		{
			$this->state = DAY_TURN;
			$this->say(self::$MAFIA_ROOM, "Nobody killed :D");
			$this->say(self::$LOBBY_ROOM, "No body kiled last night! WOOOW! but lets hunt some of them!");
			$sayMe = false;
		}
		$this->listAllUsers(self::$LOBBY_ROOM);
		$this->sayStatus($sayMe);
		$this->lastDead = $who;
		$this->lastWish = false;
		return $who;					
	}
	
	public function thisIsMyLastWish($I , $wish)
	{
		if (strtolower($I) != strtolower($this->lastDead))
		{
			$this->say($I, "You are not the last dead people!");
			if ($this->isAlive($I))
				$this->say($I , "You are not dead! you are a cheat!");
				
			return;
		}
		
		if ($this->lastWish)
		{
			$this->say($I,"You already said : " . $this->lastWish);
			return;
		}
		
		$this->lastWish = $wish;
		
		$this->say(self::$LOBBY_ROOM, "This is $I's last wish :");
		$this->say(self::$LOBBY_ROOM, $wish);
	}
	
	/**
	 * 
	 * Dr vote
	 * @param string $I
	 * @param string $you
	 */	
	public function iSayHealYou($I , $you)
	{
		$I = strtolower($I);
		$you = strtolower($you);
		if ($this->state != MAFIA_TURN )
		{
			$this->say($I ,"you can not heal, just in night.");
			return;
		}
		
		if ((($this->isIn($you) && $this->isAlive($you)) || $you == "*") 
			&& $this->isAlive($I) 
			&& $this->getTypeOf($I) == DR_PPL)
		{
			$this->drVote = $you;
			$this->act($I,"you heal $you");
		}
		else
		{
			$this->say($I,"You can not heal $you!");
		}
		
		$this->nightTimeEnd();
		
	}	
	
	/**
	 * 
	 * Vote for punish
	 * @param string $I
	 * @param string $you
	 */
	public function iSayPunishYou($I , $you)
	{
		$I = strtolower( $I);
		$you = strtolower($you);
		if ($this->state != DAY_TURN )
		{
			$this->say($I ,"Wow! you are mad! its night!");
			return;
		}
		
		if ($this->isIn($you) && $this->isAlive($you) && $this->isAlive($I) 
			&& isset($this->punishVotes[$I]))
		{
			$this->punishVotes[$I] = $you;
			$this->say(self::$LOBBY_ROOM, MafiaGame::boco(2,  $I) . " vote for punishing " . MafiaGame::boco(2,  $you));
		}
		else
		{
			$this->say($I,"Your vote not accepted!");
		}
		
		//$this->nightTimeEnd();
		foreach ($this->punishVotes as $vote)
			if ($vote === false)
				return false;
		$result = array_count_values ($this->punishVotes);
		$max = -1;
		$who = '';

		$hasDuplicate = false;
		foreach ($result as $dead => $wanted)
		{
			$this->say(self::$LOBBY_ROOM,MafiaGame::boco (2,$dead) . " has $wanted vote(s)");
			if ($wanted == $max)
				$hasDuplicate = true;
			elseif ($wanted > $max)
			{
				$who = $dead;
				$max = $wanted;
				$hasDuplicate = false;
			}
		}
		
		if ($hasDuplicate)
		{
			$this->say(self::$LOBBY_ROOM,MafiaGame::bold("There is a tie! please some one fix his/her vote!"));
			return;
		}
		$this->inGamePart[strtolower($who)]['alive'] = false;
		$this->state = MAFIA_TURN;
		$this->act(self::$MAFIA_ROOM, "Your turn to kill!");
		$this->say(self::$LOBBY_ROOM, "You punish " . MafiaGame::boco(2,$who));
		$this->say( $who,MafiaGame::bold("You are dead! please respect others and be quiet. Thanks."));
		
		$this->sayStatus();
		return $who;
	}	
	
	/**
	 * 
	 * Whois command for detective
	 * @param string $I
	 * @param string $you
	 */
	public function iSayWhoAreYou($I , $you)
	{
		$I = strtolower($I);
		$you = strtolower($you);

		if ($this->state != MAFIA_TURN )
		{
			$this->say($I ,"Stay hidden! they kill you if they find you!");
			return;
		}

		if ($this->detectiveVote && $this->detectiveVote != '*')
		{
			$this->say($I ,"Do not over-do :) you already know too much!");
			return;			
		}
				
		if ((($this->isIn($you)  || $you == "*") 
			&& $this->isAlive($I) 
			&& $this->getTypeOf($I) == DETECTIVE_PPL))
		{
			$this->detectiveVote = $you;
			if ($you != '*')
			{
				$result = $this->getTypeOf($you) == MAFIA_PPL ? " is " . MafiaGame::boco(8," Mafia") :
											" is " . MafiaGame::boco(8," Citizen");
				$this->say($I, $you . $result);
			}
		}
		else
		{
			$this->say($I,"You can not know $you!");
		}
		
		$this->nightTimeEnd();
	}
	
	/**
	 * 
	 * Say vote status to me
	 * @param string $me
	 */
	
	public function whosVote($me)
	{
		$count = 0;
		if (!$this->isIn($me))
		{
			$this->act($me, "You are not in game!");
			return;
		}

		if ($this->state == DAY_TURN)
		{
			foreach ($this->punishVotes as $who => $vote)
			{
				$this->say($me, MafiaGame::boco(2, $who) . " => " . MafiaGame::boco(2,$vote));
				$count++;
				
				if ($count > 5)
				{
					sleep(1);
					$count = 0;
				}
			}			
		}
		else 
		{
			if ($this->getTypeOf($me) == MAFIA_PPL)
			{
				foreach ($this->killVotes as $who => $vote)
				{
					$this->say($me, MafiaGame::boco(2, $who) . " => " . MafiaGame::boco(2,$vote));
					$count++;
					
					if ($count > 5)
					{
						sleep(1);
						$count = 0;
					}					
				}	
			}
			else
			{
				$this->act($me, "Kiding me, right? They kill me if I tell you!");
			}
		}
	}
	
	/**
	 * 
	 * Check time out :D
	 */
	public function checkNightTimeout()
	{
		if ($this->state == MAFIA_TURN)
		{
			if (time() - $this->nightTurnTime > self::$NIGHT_TIMEOUT)
			{
				$this->act(self::$MAFIA_ROOM, MafiaGame::bold("Sorry, time out :D") );
				$this->act(self::$LOBBY_ROOM, MafiaGame::bold("Day time!") );
				
				foreach ($this->killVotes as &$vote)
				{
					if (!$vote)
						$vote = '*';
				}
				
				if (!$this->drVote)
					$this->drVote = '*';
				
				$this->detectiveVote = true;
					
				if (!$this->nightTimeEnd())
				{
					$result = array_count_values ($this->killVotes);
					$max = -1;
					$who = '';
					foreach ($result as $dead => $wanted)
					{
						if ($wanted > $max)
						{
							$who = $dead;
							$max = $wanted;
						}
					}
					$this->act(self::$MAFIA_ROOM,"Kill forced to " . $who);
					if ($who != "*" && strtolower($who) != strtolower($this->drVote)){
						$this->inGamePart[strtolower($who)]['alive'] = false;
						$this->state = DAY_TURN;
						$this->say(self::$MAFIA_ROOM, "You kill  " .  MafiaGame::boco(2,  $who));
						$this->say(self::$LOBBY_ROOM, "ALERT!!! They kill " .  MafiaGame::boco(2,  $who) . ", lets find killer!");
						$this->say( $who,MafiaGame::bold("You are dead! please respect others and be quiet. Thanks."));
						$sayMe = true;
					}
					else
					{
						$this->state = DAY_TURN;
						$this->say(self::$MAFIA_ROOM, "Nobody killed :D");
						$this->say(self::$LOBBY_ROOM, "No body kiled last night! WOOOW! but lets hunt some of them!");
						$sayMe = false;
					}
					$this->listAllUsers(self::$LOBBY_ROOM);
					$this->sayStatus($sayMe);					
										
				}
			}
			
		}
	}
}
