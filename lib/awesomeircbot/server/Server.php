<?php
/**
 * Server class
 * Includes all the necessary functions to use an IRC server
 * e.g. ->message(), ->join(), ->quit()
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */

namespace awesomeircbot\server;
use config\Config;
use awesomeircbot\channel\ChannelManager;
use awesomeircbot\log\ErrorCategories;
use awesomeircbot\log\ErrorLog;

class Server {
	
	protected static $instance;
	
	public static $serverHandle;
	
	protected function __construct() {
	}
	
	/**
	 * Returns an instance of this Server singleton
	 *
	 * @return An instance of this Server
	 */
	public static function getInstance() {
		if (!isset(static::$instance))
			static::$instance = new Server();
		return static::$instance;
	}
	
	/**
	 * Connect to the server
	 *
	 * @return boolean true on success
	 * @return error code on failure
	 */
	public function connect() {
	
		// Establish a connection
		ErrorLog::log(ErrorCategories::NOTICE, "Opening socket to server at " . Config::$serverAddress . ":" . Config::$serverPort);
		static::$serverHandle = fsockopen(Config::$serverAddress, Config::$serverPort);
		
		
		// Check if it worked
		if (!static::$serverHandle) {
			ErrorLog::log(ErrorCategories::FATAL, "Attempt to establish connection failed!");
			return 1;
		}
		else {
			ErrorLog::log(ErrorCategories::NOTICE, "Socket connection to server established");
			return true;
		}
	}
	
	/**
	 * Check if the connection is still established to
	 * the server
	 *
	 * @return boolean depending on connection status
	 */
	public function connected() {
		if (!feof(static::$serverHandle))
			return true;
		else
			return false;
	}
	
	/**
	 * Identify to the server, should be
	 * run immediately after connecting
	 */
	public function identify() {
	
		// Send the identification messages to the server
		ErrorLog::log(ErrorCategories::NOTICE, "Authenticating with the server");
		fwrite(static::$serverHandle, "NICK " . Config::$nickname . "\0\n");
		fwrite(static::$serverHandle, "USER " . Config::$username . " 0 * :" . Config::$realName . "\0\n");
	}
	
	/**
	 * Identify to NickServ
	 */
	public function identifyWithNickServ() {
		// Send the identification message to the server
		ErrorLog::log(ErrorCategories::NOTICE, "Identifying with NickServ with password '" . Config::$nickservPassword ."'");
		$this->message("NickServ", "IDENTIFY " . Config::$nickservPassword);
	}
	
	/**
	 * Join a specified channel
	 *
	 * @param string channel name
	 */
	public function join($channel) {
		
		// Send to the server
		ErrorLog::log(ErrorCategories::NOTICE, "Joining IRC channel '" . $channel);
		fwrite(static::$serverHandle, "JOIN " . $channel . "\0\n");
	}
	
	
	/**
	 * 
	 */
	 
	 public function kick($user, $channel, $reason = false)
	 {
		ErrorLog::log(ErrorCategories::NOTICE, "Kick $user from '" . $channel);
		$message = $reason ? " :$reason" : "";
		fwrite(static::$serverHandle, "KICK " . $channel . " " . $user . $message . "\0\n");	 
	 }
	 
	 public function raw($data)
	 {
		 fwrite(static::$serverHandle, $data . "\0\n");	
	 }
	 
	 public function modeChannel($mode, $channel)
	 {
		 ErrorLog::log(ErrorCategories::NOTICE, "Set mode $mode to $channel" );
		 $this->raw("MODE $channel $mode");
	 }
	
	/**
	 * Part a specified channel
	 *
	 * @param string channel name
	 */
	public function part($channel) {
		
		// Send to the server
		fwrite(static::$serverHandle, "PART " . $channel . "\0\n");
		ErrorLog::log(ErrorCategories::NOTICE, "Parting IRC channel '" . $channel);
		
		// Remove the channel from the ChannelManager
		ChannelManager::remove($channel);
	}
	
	/**
	 * Gets the next line from the server and
	 * returns it
	 *
	 * @return string IRC line
	 */
	public function getNextLine() {
		
		ErrorLog::log(ErrorCategories::DEBUG, "Getting next line from IRC server");
		
		// Check we're connected
		if ($this->connected()) {
			// Get and return the next line
			$return = fgets(static::$serverHandle, 1024);
			ErrorLog::log(ErrorCategories::DEBUG, "Received IRC line from server (" . $return . ")");
			return $return;
		}
		else
			// Not connected? Return false
			ErrorLog::log(ErrorCategories::ERROR, "No longer connected to server!");
			return false;
	}
	
	/**
	  * Sends the QUIT message to the server, closes
	  * the connection and then kills the script
	  */

	public function quit() {
		
		// Quit and disconnect
		fwrite(static::$serverHandle, "QUIT :Bye Bye!\0\n");
		fclose(static::$serverHandle);
		ErrorLog::log(ErrorCategories::NOTICE, "Server quit sent and socket closed");
		
		// Die
		ErrorLog::log(ErrorCategories::NOTICE, "Killing script and all associated processes");
		die();
	}
	
	/** 
	 * Messages the target user or channel with
	 * the passed text
	 */
	 public function message($target, $message) {
	 	
	 	// Send it
	 	ErrorLog::log(ErrorCategories::NOTICE, "Messaging '" . $target . "' with message '" . $message . "'");
	 	fwrite(static::$serverHandle, "PRIVMSG " . $target . " :" . $message . "\0\n");
	 }
	 
	 /**
	  * Sends a notice to the given user, same syntax as the 
	  * message() method
	  */
	 public function notice($target, $message) {
	 	
	 	// Send it
	 	ErrorLog::log(ErrorCategories::NOTICE, "Noticing '" . $target . "' with message '" . $message . "'");
	 	fwrite(static::$serverHandle, "NOTICE " . $target . " :" . $message . "\0\n");
	 }
	 
	 /**
	  * Notifies the given user about an action based
	  * on your config settings for notifications
	  */
	 public function notify($target, $message) {
	 	
	 	// Check config and pass it to the appropriate function
	 	ErrorLog::log(ErrorCategories::DEBUG, "Going to notify '" . $target . "' with message '" . $message . "'");
	 	if (Config::$notificationType == "pm")
	 		$this->message($target, $message);
	 	else
	 		$this->notice($target, $message);
	 }
	 	
	 /**
	  * Act, (/me) a message, same syntax as the message()
	  * method
	  */
	 public function act($target, $message) {
	 	
	 	// Send it
	 	ErrorLog::log(ErrorCategories::NOTICE, "Messaging '" . $target . "' with message '" . $message . "' formatted as an ACTION (/me)");
	 	fwrite(static::$serverHandle, "PRIVMSG " . $target . " :" . chr(1) . "ACTION " . $message . chr(1) . "\0\n");
	 }
	 	
	 
	 /**
	  * Pongs the given server
	  */
	 public function pong($target) {
	 	
	 	// Send it
	 	ErrorLog::log(ErrorCategories::NOTICE, "Sending PONG to server");
	 	fwrite(static::$serverHandle, "PONG " . $target . "\0\n");
	 }
	 
	 /**
	  * Sends a WHOIS query to the server for
	  * the nickname specified
	  *
	  * @param string nickname to whois
	  */
	 public function whois($nickname) {
	 	
	 	// Send it
	 	ErrorLog::log(ErrorCategories::NOTICE, "Sending WHOIS request for '" . $nickname . "'");
	 	fwrite(static::$serverHandle, "WHOIS " . $nickname . "\0\n");
	 }
	 
	 /**
	  * Updates a channel topic
	  *
	  * @param string channel
	  * @param string topic
	  * @param boolean true if you want the bot to update the topic
	  * via chanserv
	  */
	 public function topic($channel, $topic=false, $chanserv=false) {
	 	
		if ($topic) {
			if ($chanserv) {
				ErrorLog::log(ErrorCategories::NOTICE, "Changing topic for '" . $channel . "' to '" . $topic . "' via ChanServ");
				$this->message("ChanServ", "TOPIC " . $channel . " " . $topic);
			}
			else {
				ErrorLog::log(ErrorCategories::NOTICE, "Changing topic for '" . $channel . "' to '" . $topic . "'");
				fwrite(static::$serverHandle, "TOPIC " . $channel . " :" . $topic . "\0\n");
			}
		}
		else {
			ErrorLog::log(ErrorCategories::NOTICE, "Sending request to server for the current topic for '" . $channel . "'");
			fwrite(static::$serverHandle, "TOPIC " . $channel . "\0\n");
		}
	 }
	 
	 /**
	  * Invites the given user to the given channel
	  *
	  * @param string nickname to invite
	  * @param string channel name
	  */
	 public function channelInvite($nick, $channel) {
	 	ErrorLog::log(ErrorCategories::NOTICE, "Sending channel invite for '" . $channel . "' to '" . $nick . "'");
	 	fwrite(static::$serverHandle, "INVITE " . $nick . " " . $channel . "\0\n");
	 }
}
	 
