<?php
/**
 * User Manager
 * Tracks online users and their privileges,
 * server modes, identification status and
 * information
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */

namespace awesomeircbot\user;

use awesomeircbot\user\User;
use awesomeircbot\server\Server;
use config\Config;

class UserManager {
	
	/**
	 * Associative array of online tracked users
	 * nick => user object
	 */
	public static $trackedUsers = array();
	
	private function __construct() {
	}
	
	/**
	 * Adds tracking for an online nick and stores
	 * the given user object for them, if their nick is on the
	 * privileged config list it also sends a WHOIS
	 *
	 * @param string online nickname
	 * @param object User object
	 */
	public static function store($nick, $userObject) {
		foreach(Config::$users as $privilegedUser => $level) {
			if ($nick == $privilegedUser) {
				if (!static::$trackedUsers[$nick]) {
					$server = Server::getInstance();
					$server->whois($nick);
				}
			}
		}
		
		static::$trackedUsers[$nick] = $userObject;
	}
	
	/**
	 * Gets the user object for the nick supplied
	 *
	 * @param string online nickname
	 * @return object User object
	 * @return object empty User object
	 */
	public static function get($nick) {
		if (static::$trackedUsers[$nick])
			return static::$trackedUsers[$nick];
		else
			return new User;
	}
	
	/**
	 * Clears any data for the nick supplied
	 *
	 * @param string nicknamr
	 */
	public static function remove($nick) {
		unset(static::$trackedUsers[$nick]);
	}
	
	/**
	 * Renames a user
	 *
	 * @param string original nick
	 * @param string new nick
	 */
	public static function rename($oldNick, $newNick, $newIdent, $newHost) {
		unset(static::$trackedUsers[$oldNick]);
		
		$user = new User;
		$user->nickname = $newNick;
		$user->ident = $newIdent;
		$user->host = $newHost;

		static::store($newNick, $user);
	}
}
?>