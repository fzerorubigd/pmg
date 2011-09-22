<?php
/**
 * Channel Class
 * Class for an IRC channel
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */

namespace awesomeircbot\channel;

class Channel {
	
	/**
	 * Channel information including name,
	 * topic, modes, etc.
	 */
	public $channelName;
	public $topic;
	public $modes = array();
	
	/**
	 * Array of users that are
	 * connected to the channel
	 */
	public $connectedNicks = array();
	
	/**
	 * An associative array of nicknames
	 * and the privileges they hold in the
	 * channel
	 * nick => privilege character (~, &, @, %, +)
	 */
	public $privilegedNicks = array();
	
	/**
	 * Construction
	 *
	 * @param string channel name
	 */
	public function __construct($channel) {
		$this->channelName = $channel;
	}
	
	/**
	 * Adds a nickname to the array of connected
	 * nicknames, checking if it exists first
	 *
	 * @param string nickname to add
	 * @param string privilege character (~, &, @, %, +)
	 */
	public function addConnectedNick($nick, $privileges=false) {
		
		if (!$nick)
			return;
		
		if (in_array($nick, $this->connectedNicks) === false) {
			$this->connectedNicks[] = $nick;
		
			if ($privileges)
				$this->privilegedNicks[$nick] = $privileges;
		}
	}
	
	/**
	 * Adds a privilege to a nickname
	 *
	 * @param string nickname
	 * @param string privilege character (~, &, @, %, +)
	 */
	public function addPrivilege($nick, $privilege) {
		
		if (!$this->privilegedNicks[$nick]) {
			$this->privilegedNicks[$nick] = $privilege;
			return;
		}
		
		$currentPrivilege = $this->privilegedNicks[$nick];
		$newPrivilege = $privilege;
		
		if ($currentPrivilege == "~")
			return;
		
		if ($currentPrivilege == "&") {
			if ($newPrivilege == "~") {
				$this->privilegedNicks[$nick] = $newPrivilege;
			}
			return;
		}
		
		if ($currentPrivilege == "@") {
			if (($newPrivilege == "&") || ($newPrivilege == "~")) {
				$this->privilegedNicks[$nick] = $newPrivilege;
			}
			return;
		}
		
		if ($currentPrivilege == "%") {
			if (($newPrivilege == "@") || ($newPrivilege == "&") || ($newPrivilege == "~")) {
				$this->privilegedNicks[$nick] = $newPrivilege;
			}
			return;
		}
		
		if ($currentPrivilege == "+") {
			if (($newPrivilege == "%") || ($newPrivilege == "@") || ($newPrivilege == "&") || ($newPrivilege == "~")) {
				$this->privilegedNicks[$nick] = $newPrivilege;
			}
			return;
		}
		
	}
	
	/**
	 * Removes a nickname from the array
	 * of connected nicknames
	 *
	 * @param string nickname to remove
	 */
	public function removeConnectedNick($nick) {
		foreach($this->connectedNicks as $id => $connectedNick) {
			if ($connectedNick == $nick) {
				unset($this->connectedNicks[$id]);
			}
		}
		
		unset($this->privilegedNicks[$nick]);
	}
	
	/**
	 * Renames a user in the channel
	 *
	 * @param string original nick
	 * @param string new nick
	 */
	public function renameConnectedNick($oldNick, $newNick) {
		$this->connectedNicks[] = $newNick;
		
		if ($this->privilegedNicks[$oldNick]) {
			$this->privilegedNicks[$newNick] = $this->privilegedNicks[$oldNick];
			unset($this->privilegedNicks[$oldNick]);
		}
		
		$this->removeConnectedNick($oldNick);
	}
	
	/**
	 * Checks if a nickname is connected to the
	 * channel
	 *
	 * @param string nickname
	 * @return boolean depending on whether or not user is connected
	 */
	public function isConnected($nick) {
		if (in_array($nick, $this->connectedNicks))
			return true;
		else
			return false;
	}
	
	/**
	 * Checks if a connected nick has the given privilege
	 * or a higher privilege
	 *
	 * @param string nickname
	 * @param string privilege character (~, &, @, %, +)
	 * @return boolean true if nick has privilege or higher
	 *	     boolean false if not
	 */
	public function hasPrivilegeOrHigher($nick, $privilege) {
		
		$userPrivilege = $this->privilegedNicks[$nick];
		
		if (!$userPrivilege)
			return false;
		
		if ($userPrivilege == "~")
			return true;
		
		if ($userPrivilege == "&") {
			if ($privilege == "~") {
				return false;
			}
			return true;
		}
		
		if ($userPrivilege == "@") {
			if (($privilege == "&") || ($$privilege == "~")) {
				return false;
			}
			return true;
		}
		
		if ($userPrivilege == "%") {
			if (($privilege == "@") || ($privilege == "&") || ($privilege == "~")) {
				return false;
			}
			return true;
		}
		
		if ($userPrivilege == "+") {
			if (($privilege == "%") || ($privilege == "@") || ($privilege == "&") || ($privilege == "~")) {
				return false;
			}
			return true;
		}
	}
}
?>