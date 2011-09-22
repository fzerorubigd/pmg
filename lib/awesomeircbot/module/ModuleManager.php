<?php
/**
 * Module manager
 * Modules register with this and can then be triggered
 * by a trigger line
 * The Module Manager will handle calling the module and
 * outputting the return
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */

namespace awesomeircbot\module;

use awesomeircbot\user\UserManager;
use awesomeircbot\help\HelpManager;
use config\Config;

class ModuleManager {
	
	/**
	 * Associative array of commands to modules
	 * e.g. quit => modules\QuitFromServer
	 */
	public static $mappedCommands = array();
	
	/**
	 * Associative array of event types to modules
	 * e.g. ReceivedLineTypes::PING => modules\Pong
	 */
	public static $mappedEvents = array();
	
	/**
	 * Associative array of REGEX strings to modules
	 * e.g. /[0-9]/ => modules\RandomNumberThingyMaJig
	 */
	public static $mappedTriggers = array();
	
	/**
	 * Array of folder names in /modules which have
	 * been loaded into the bot
	 */
	protected static $loadedModuleFolders = array();
	
	/**
	 * This is a static class, it should not be instantiated
	 */
	private function __construct() {
	}
	
	/**
	 * Run a user command
	 *
	 * @param string command
	 * @param string full message the user sent
	 * @param string nickname of the user who sent the command
	 * @param string channel the message was sent on
	 */
	public static function runCommand($command, $message, $nick, $channel) {
		
		$module = static::$mappedCommands[$command];
		if (!$module)
			return 1;
		
		if ($module::$requiredUserLevel) {
			$user = UserManager::get($nick);
			
			if (!$user->isIdentified) {
				return 2;
			}
			else {
				if ($module::$requiredUserLevel > Config::$users[$nick])
					return 2;
			}
		}
		
		if (strpos("#", $channel) !== false) {
			if ($module::$requiredChannelPrivilege) {
				$channel = ChannelManager::get($channel);
				
				if (!$channel->hasPrivilegeOrHigher($nick))
					return 2;
			}
		}
				
		$moduleInstance = new $module($message, $nick, $channel);
		
		$moduleInstance->run();
		return true;
	}
	
	/**
	 * Map a command to a module
	 *
	 * @param string command to activate upon
	 * @param string module full namespace to activate
	 */
	public static function mapCommand($command, $module) {
		static::$mappedCommands[$command] = $module;
	}
	
	/**
	 * Unmaps a command
	 *
	 * @param string command to remove mapping from
	 */
	public static function unmapCommand($command) {
		unset(static::$mappedCommands[$command]);
	}
	
	/**
	 * Run an event
	 *
	 * @param integer numerical event type (see awesomeircbot\line\ReceivedLineTypes)
	 * @param string The full line which triggered the mapping
	 * @param string The channel on which it was sent, if applicable
	 * @param string The nickname of the sender of the activating line
	 * @param string The target of the line, if applicable
	 */
	public static function runEvent($eventType, $line, $channel=false, $senderNick=false, $targetNick=false) {
		
		if (!static::$mappedEvents[$eventType])
			return 1;
		
		foreach(static::$mappedEvents[$eventType] as $mappedEvent) {
			$module = $mappedEvent;
			$moduleInstance = new $module($line, $senderNick, $channel, $eventType, $targetNick);
			$moduleInstance->run();
		}
		
		return true;
	}
	
	/**
	 * Map an event to a module
	 *
	 * @param string event numerical type to activate upon (see awesomeircbot\line\ReceivedLineTypes)
	 * @param string module full namespace to activate
	 */
	public static function mapEvent($event, $module){
		$exists = false;
		
		if (static::$mappedEvents) {
			foreach(static::$mappedEvents[$event] as $id => $mappedModule) {
				if ($module == $mappedModules)
					$exists = true;
			}
		}
		
		if (!$exists)
			static::$mappedEvents[$event][] = $module;
	}
	
	/**
	 * Removes a module from the mapping of an event
	 *
	 * @param string event numerical type
	 * @param string module full namespace to remove
	 */
	public static function unmapEvent($event, $module) {
		foreach(static::$mappedEvents[$event] as $id => $mappedModule) {
			if ($module == $mappedModule)
				unset(static::$mappedEvents[$event][$id]);
		}
	}
	
	/**
	 * Runs the module associated with a trigger
	 *
	 * @param string full message the user sent
	 * @param string nickname of the user who sent the command
	 * @param string channel the message was sent on
	 */
	public static function runTrigger($message, $senderNick, $channel) {
		
		foreach(static::$mappedTriggers as $regexString => $module) {
			if (preg_match($regexString, $message)) {
				
				if ($module::$requiredUserLevel) {
					$user = UserManager::get($senderNick);
					
					if (!$user->isIdentified) {
						return 2;
					}
					else {
						if ($module::$requiredUserLevel > Config::$users[$senderNick])
							return 2;
					}
				}
				
				if (strpos("#", $channel) !== false) {
					if ($module::$requiredChannelPrivilege) {
						$channel = ChannelManager::get($channel);
						
						if (!$channel->hasPrivilegeOrHigher($senderNick))
							return 2;
					}
				}
						
				$moduleInstance = new $module($message, $senderNick, $channel);
				$moduleInstance->run();
				return true;
			}
		}
	}
	
	/**
	 * Map a regex string to a module
	 *
	 * @param string regex string in message to act upon
	 * @param string module full namespace to activate
	 */
	public static function mapTrigger($regexString, $module) {
		static::$mappedTriggers[$regexString] = $module;
	}
	
	/**
	 * Unmaps a regex string
	 *
	 * @param string regex string to unmap
	 */
	public static function unmapTrigger($regexString, $module) {
		unset(static::$mappedTriggers[$regexString]);
	}
	
	/**
	 * Initializes by loading all module sets located
	 * in /modules
	 */
	public static function initialize() {
		
		// Load the system stuff first so as to ensure it's executed before
		// additional modules
		$modulePacks[] = "Parsers";
		$modulePacks[] = "System";
		
		$folder = opendir(__DIR__ . "/../../../modules");
		$modulePacks = array();
		while (($file = readdir($folder)) !== false) {
			if (($file != ".") && ($file != "..") && ($file != "modules.inc.php")) {
				$folder2 = opendir(__DIR__ . "/../../../modules/" . $file . "/configs");
				while (($file2 = readdir($folder2)) !== false) {
					if (($file2 != ".") && ($file2 != "..") && (!in_array($file2, $modulePacks))) {
						$modulePacks[] = str_replace(".php", "", $file2);
					}
				}
				closedir($folder2);
			}
		}
		closedir($folder);
		
		foreach ($modulePacks as $modulePack) {
			$moduleNamespace = "modules\\" . strtolower($modulePack) . "\configs\\" . $modulePack;
			static::loadModuleConfig($moduleNamespace);
		}
	}
	
	/**
	 * Load a module config which contains multiple
	 * modules that need to be loaded
	 *
	 * @param string full namespace of the module config
	 */
	public static function loadModuleConfig($moduleConfig) {
		foreach($moduleConfig::$mappedCommands as $command => $module)
			static::mapCommand($command, $module);
			
		foreach($moduleConfig::$mappedEvents as $event => $module)
			static::mapEvent($event, $module);
			
		foreach($moduleConfig::$mappedTriggers as $regexString => $module)
			static::mapTrigger($regexString, $module);
			
		foreach($moduleConfig::$help as $command => $commandData) {
			HelpManager::registerCommand($command, $commandData["BASE"]["description"], $commandData["BASE"]["parameters"]);
			
			foreach($commandData as $subcommand => $subcommandData) {
				if ($subcommand != "BASE")
					HelpManager::registerSubcommand($command, $subcommand, $subcommandData["description"], $subcommandData["parameters"]);
			}
		}
		
		$moduleConfig = explode("\\", $moduleConfig);
		static::$loadedModuleFolders[] = $moduleConfig[1];
	}
	
	/**
	 * Unload a module config and all it's mappings from the
	 * bot. Basically the opposite of above
	 *
	 * @param string full namespace of the module config
	 */
	public static function unloadModuleConfig($moduleConfig) {
		foreach($moduleConfig::$mappedCommands as $command => $module)
			static::unmapCommand($command, $module);
			
		foreach($moduleConfig::$mappedEvents as $event => $module)
			static::unmapEvent($event, $module);
			
		foreach($moduleConfig::$mappedTriggers as $regexString => $module)
			static::unmapTrigger($regexString, $module);
			
		foreach($moduleConfig::$help as $command => $commandData) {
			HelpManager::unregisterCommand($command);
		}
		
		$moduleConfig = explode("\\", $moduleConfig);
		foreach (static::$loadedModuleFolders as $id => $loadedModuleFolder) {
			if ($moduleConfig[1] == $loadedModuleFolder)
				unset(static::$loadedModuleFolders[$id]);
		}
	}
	
	/**
	 * Checks if a config name is a loaded module config in
	 * /modules
	 *
	 * @param string config name
	 * @return boolean depending on load status
	 */
	public static function isLoaded($configName) {
		$configName = str_replace(".php", "", $configName);
		if (in_array(strtolower($configName), static::$loadedModuleFolders))
			return true;
		else
			return false;
	}
	
}