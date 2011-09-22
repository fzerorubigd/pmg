<?php
/**
 * Received Line Class
 * Contains methods to parse received lines, check
 * if they match triggers, check if they are commands
 * and several other things
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */

namespace awesomeircbot\line;

use awesomeircbot\line\ReceivedLineTypes;
use awesomeircbot\module\ModuleManager;
use config\Config;

class ReceivedLine {

	public $line;

	public $type;
	public $message;

	public $senderNick;
	public $senderIdent;
	public $senderHost;

	public $channel;
	public $targetNick;

	/**
	 * Construction
	 *
	 * @param string line received from the server
	 */
	public function __construct($line) {
		$this->line = trim($line);
	}

	/**
	 * Parses the raw line that has been given into
	 * the properties at the top of this class
	 */
	public function parse() {

		if (strpos($this->line, "PRIVMSG #") !== false) {

			// Type
			$this->type = ReceivedLineTypes::CHANMSG;

			// Channel
			$workingLine = explode(" :", $this->line);
			$workingLine = explode("PRIVMSG ", $workingLine[0]);
			$this->channel = $workingLine[1];

			// User
			$workingLine = explode(" PRIVMSG", $this->line);
			$workingLine[0] = str_replace(":", "", $workingLine[0]);

				// Nick
				$workingLine = explode("!", $workingLine[0]);
				$this->senderNick = $workingLine[0];

				// Ident
				$workingLine = explode("@", $workingLine[1]);
				$this->senderIdent = $workingLine[0];

				// Host
				$this->senderHost = $workingLine[1];

			// Message
			$workingLine = explode(" :", $this->line, 2);
			$this->message = trim($workingLine[1]);
		}

		else if (strpos($this->line, "PRIVMSG") !== false) {

			// Type
			$this->type = ReceivedLineTypes::PRIVMSG;

			// Channel & Target
			$workingLine = explode(" :", $this->line);
			$workingLine = explode("PRIVMSG ", $workingLine[0]);
			$this->channel = $workingLine[1];
			$this->targetNick = $workingLine[1];

			// User
			$workingLine = explode(" PRIVMSG", $this->line);
			$workingLine[0] = str_replace(":", "", $workingLine[0]);

				// Nick
				$workingLine = explode("!", $workingLine[0]);
				$this->senderNick = $workingLine[0];

				// Ident
				$workingLine = explode("@", $workingLine[1]);
				$this->senderIdent = $workingLine[0];

				// Host
				$this->senderHost = $workingLine[1];

			// Message
			$workingLine = explode(" :", $this->line, 2);
			$this->message = trim($workingLine[1]);
		}

		else if (strpos($this->line, "NICK") !== false) {

			// Type
			$this->type = ReceivedLineTypes::NICK;

			// Target
			$workingLine = explode("NICK ", $this->line);
			$this->targetNick = str_replace(":", "", $workingLine[1]);

			// User
			$workingLine = explode(" NICK", $this->line);
			$workingLine[0] = str_replace(":", "", $workingLine[0]);

				// Nick
				$workingLine = explode("!", $workingLine[0]);
				$this->senderNick = $workingLine[0];

				// Ident
				$workingLine = explode("@", $workingLine[1]);
				$this->senderIdent = $workingLine[0];

				// Host
				$this->senderHost = $workingLine[1];
		}

		else if (strpos($this->line, "PING") !== false) {

			// Type
			$this->type = ReceivedLineTypes::PING;

			// Pinger
			$workingLine = explode(" :", $this->line);
			$this->senderNick = $workingLine[1];
			$this->senderNick = trim($this->senderNick);
		}

		else if (strpos($this->line, "JOIN") !== false) {

			// Type
			$this->type = ReceivedLineTypes::JOIN;

			// Channel
			$workingLine = trim($this->line);
			$workingLine = explode("JOIN :", $workingLine);
			$this->channel = $workingLine[1];

			// User
			$workingLine = explode(" JOIN", $this->line);
			$workingLine[0] = str_replace(":", "", $workingLine[0]);

				// Nick
				$workingLine = explode("!", $workingLine[0]);
				$this->senderNick = $workingLine[0];

				// Ident
				$workingLine = explode("@", $workingLine[1]);
				$this->senderIdent = $workingLine[0];

				// Host
				$this->senderHost = $workingLine[1];
		}

		else if (strpos($this->line, "PART") !== false) {

			// Type
			$this->type = ReceivedLineTypes::PART;

			// Channel
			$workingLine = trim($this->line);
			$workingLine = explode("PART ", $workingLine);
			$workingLine = explode(" :", $workingLine[1]);
			$this->channel = $workingLine[0];

			// User
			$workingLine = explode(" PART", $this->line);
			$workingLine[0] = str_replace(":", "", $workingLine[0]);

				// Nick
				$workingLine = explode("!", $workingLine[0]);
				$this->senderNick = $workingLine[0];

				// Ident
				$workingLine = explode("@", $workingLine[1]);
				$this->senderIdent = $workingLine[0];

				// Host
				$this->senderHost = $workingLine[1];
		}

		else if (strpos($this->line, "KICK") !== false) {

			// Type
			$this->type = ReceivedLineTypes::KICK;

			// Channel
			$workingLine = explode(" ", $this->line);
			$this->channel = $workingLine[2];

			// Target
			$this->targetNick = $workingLine[3];

			// User
			$workingLine = str_replace(":", "", $workingLine[0]);

				// Nick
				$workingLine = explode("!", $workingLine[0]);
				$this->senderNick = $workingLine[0];

				// Ident
				$workingLine = explode("@", $workingLine[1]);
				$this->senderIdent = $workingLine[0];

				// Host
				$this->senderHost = $workingLine[1];
		}

		else if (strpos($this->line, "QUIT") !== false) {

			// Type
			$this->type = ReceivedLineTypes::QUIT;

			// User
			$workingLine = explode(" QUIT", $this->line);
			$workingLine[0] = str_replace(":", "", $workingLine[0]);

				// Nick
				$workingLine = explode("!", $workingLine[0]);
				$this->senderNick = $workingLine[0];

				// Ident
				$workingLine = explode("@", $workingLine[1]);
				$this->senderIdent = $workingLine[0];

				// Host
				$this->senderHost = $workingLine[1];
		}

		else if (strpos($this->line, "MODE") !== false) {

			// Type
			$this->type = ReceivedLineTypes::MODE;

			// User
			$workingLine = explode(" MODE", $this->line);
			$workingLine[0] = str_replace(":", "", $workingLine[0]);

				// Nick
				$workingLine = explode("!", $workingLine[0]);
				$this->senderNick = $workingLine[0];

				// Ident
				$workingLine = explode("@", $workingLine[1]);
				$this->senderIdent = $workingLine[0];

				// Host
				$this->senderHost = $workingLine[1];


			// Channel
			$workingLine = explode(" ", $this->line);
			$this->channel = $workingLine[2];

			// Mode
			$this->message = $workingLine[3];

			// Target nick
			$this->targetNick = $workingLine[4];
		}

		else if ((preg_match('/[2-5][0-9][0-9]/', $this->line)) !== false) {

			// This is a server reply
			// Get the numeric
			$workingLine = explode(" ", $this->line);
			$serverReplyNumeric = $workingLine[1];

			// Work out what the numeric means and parse it
			switch ($serverReplyNumeric) {

				// Whois info line
				case 311:
					$this->type = ReceivedLineTypes::SERVERREPLYTHREEONEONE;

					$workingLine = explode(" ", $this->line, 4);
					$this->message = $workingLine[3];
				break;

				// Whois identified line
				case 330:
					$this->type = ReceivedLineTypes::SERVERREPLYTHREETHREEZERO;

					$workingLine = explode(" ", $this->line, 4);
					$this->message = $workingLine[3];
				break;
				case 307:
					$this->type = ReceivedLineTypes::SERVERREPLYTHREEZEROSEVEN;

					$workingLine = explode(" ", $this->line, 4);
					$this->message = $workingLine[3];
				break;

				// Names reply
				case 353:
					$this->type = ReceivedLineTypes::SERVERREPLYTHREEFIVETHREE;

					$workingLine = explode(" ", $this->line, 3);
					$this->message = $workingLine[2];
				break;
			
				// Topic reply
				case 332:
					$this->type = ReceivedLineTypes::SERVERREPLYTHREETHREETWO;
					
					$workingLine = explode(" ", $this->line, 4);
					$this->message = $workingLine[3];
				break;
			}
		}

	}

	/**
	 * Check if the current line object is a command
	 * by checking if the command character starts the
	 * message
	 *
	 * @return boolean depending on whether or not it is a command
	 */
	public function isCommand() {

		if (!$this->message)
			$this->parse();

		if (($this->type != ReceivedLineTypes::PRIVMSG) && ($this->type != ReceivedLineTypes::CHANMSG))
			return false;

		$splitMessage = str_split($this->message);
		
		if ($splitMessage[0] != Config::$commandCharacter)
			return false;

		return true;
	}

	/**
	 * Check if the current line object is a mapped
	 * event
	 *
	 * @return boolean depending on whether or not it is a mapped event
	 */
	public function isMappedEvent() {

		if (!$this->type)
			$this->parse();

		$module = ModuleManager::$mappedEvents[$this->type];
		if ($module)
			return true;
		else
			return false;
	}

	/**
	 * Check if the current message (if it is a message)
	 * is a mapped REGEX match
	 *
	 * @return boolean depending on whether or not it is a mapped trigger
	 */
	public function isMappedTrigger() {

		if (!$this->type)
			$this->parse();

		if ( ($this->type != ReceivedLineTypes::PRIVMSG) && ($this->type != ReceivedLineTypes::CHANMSG) ) {
			return false;
		}

		foreach(ModuleManager::$mappedTriggers as $regexString => $module) {
			if (preg_match($regexString, $this->message)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Extracts the command from the line if
	 * there is one
	 *
	 * @return string command
	 */
	public function getCommand() {

		$splitMessage = explode(" ", $this->message);
		$command = str_replace(Config::$commandCharacter, "", $splitMessage[0]);
		return $command;
	}

}

?>