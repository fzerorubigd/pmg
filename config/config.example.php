<?php
/* Configuration file for Awesome IRC Bot
 * Rename this file to config.php and edit the options below
 */
namespace config;

use awesomeircbot\log\ErrorCategories;

class Config {
	
	/**
	 * Description/Name of the Server
	 * This will be used in the DB for logging
	 * and the verbose output to command line
	 */
	public static $serverName = "Rizon";
	
	/**
	 * Connection details
	 * Make sure to fill these out correctly
	 * $serverAddress - Host of the IRC server, e.g. irc.rizon.net
	 * $serverPort - Port of the IRC server, SSL is NOT supported, 
	 * 6667 on nearly all networks
	 */
	public static $serverAddress = "irc.rizon.net";
	public static $serverPort = 6667;
	
	/**
	 * Bot details
	 * These will be used for identifying the
	 * bot to the server
	 * $nickname - Nickname for bot to use, e.g. AwesomeBot
	 * $username - Username/IDENT, can be any string, e.g. Awesome
	 * $realName - Real Name/Gecos, e.g. Awesome Bot
	 * $nickservPassword - If the bot's nick is register, then 
	 * this is the password for bot to identify with using /msg 
	 * NickServ IDENTIFY
	 */
	public static $nickname = "AwesomezzzBot";
	public static $username = "AwesomeSauce";
	public static $realName = "Awesome Thingy";
	public static $nickservPassword = "";
	
	/**
	 * Channels to join
	 * This should be an array of channel
	 * names with the hash
	 * e.g. array("#Chat", "#Help");
	 */
	public static $channels = array(
		"#LinkHunters"
	);
	
	/**
	 * Users
	 * Associative array of users to user levels
	 * registered nick => user level
	 */
	public static $users = array(
		"Naikcaj" => 10,
	);
	
	/**
	 * Bot customizations
	 * It is optional to edit these, they will
	 * work just fine the way they are
	 * $commandCharacter - Character to prefix module commands with
	 * $notificationType - Type of message to use to notify you about
	 *			     command outcomes, etc.
	 *			     "notice" or "pm"
	 */
	public static $commandCharacter = ".";
	public static $notificationType = "notice";
	
	/**
	 * Verbose output types, ranging from FATAL down to DEBUG.
	 * Use ALL for all messages
	 * Use & to subtract and | to add
	 * e.g. ALL & NOTICE & DEBUG
	 *      All without notices or debug
	 */
	public static $verboseOutput = ErrorCategories::ALL;
	
	/**
	 * Change the below line from true to false
	 * to prove you have read the config
	 */
	public static $die = false;
	
	/**
	 * --------------------------------------
	 * DO NOT EDIT ANYTHING BELOW THIS LINE
	 * --------------------------------------
	 */
	 
	/**
	 * The version of the config this is, on
	 * runtime the bot checks if you have an up to date config
	 * and notifies you if you do not
	 * This allows you to seamlessly pull from git without
	 * worrying about a corrupt config
	 */
	public static $configVersion = 4;
}
?>