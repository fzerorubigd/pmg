<?php
/**
 * Channel Manager
 * Tracks connected channels, their modes, users
 * topic and other things
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */

namespace awesomeircbot\channel;

use awesomeircbot\channel\Channel;

class ChannelManager {
	
	/**
	 * Associative array of connected channels
	 * channel name => channel object
	 */
	public static $connectedChannels = array();
	
	private function __construct() {
	}
	
	/**
	 * Adds tracking for a channel
	 *
	 * @param string channel name
	 * @param object Channel object
	 */
	public static function store($chan, $chanObject) {
		static::$connectedChannels[$chan] = $chanObject;
	}
	
	/**
	 * Gets the channel object for the
	 * channel name specified
	 *
	 * @param string channel name
	 * @return object Channel object
	 * @return object empty Channel object
	 */
	public static function get($chan) {
		if (static::$connectedChannels[$chan])
			return static::$connectedChannels[$chan];
		else
			return new Channel($chan);
	}
	
	/**
	 * Gets a list of channels which the bot
	 * is connected to and returns it as an array
	 *
	 * @return array channels
	 */
	public static function getConnectedChannelArray() {
		$channels = array();
		foreach (static::$connectedChannels as $channel => $channelObject) {
			$channels[] = $channel;
		}
		
		return $channels;
	}	
		
	/**
	 * Clears any data for the channel supplied
	 *
	 * @param string channel name
	 */
	public static function remove($chan) {
		unset(static::$connectedChannels[$chan]);
	}
	
	/**
	 * Renames a user in all the channels they
	 * are connected to
	 *
	 * @param string original nick
	 * @param string new nick
	 */
	public static function rename($oldNick, $newNick) {
		foreach(static::$connectedChannels as $channel => $channelObject) {
			$channelObject->renameConnectedNick($oldNick, $newNick);
			static::$connectedChannels[$channel] = $channelObject;
		}
	}
	
	/**
	 * Removes the given nickname from all the
	 * connected channels by cycling through them
	 *
	 * @param string nickname
	 */
	public static function removeConnectedNickFromAll($nick) {
		foreach(static::$connectedChannels as $channel => $channelObject) {
			$channelObject->removeConnectedNick($nick);
			static::$connectedChannels[$channel] = $channelObject;
		}
	}
	
	/**
	 * Checks if a nickname is connected to any tracked
	 * channels
	 *
	 * @param string nickname
	 * @return boolean depending on whether or not the user is connected to a
	 *			 tracked channel
	 */
	public static function isConnectedToTrackedChannel($nick) {
		foreach(static::$connectedChannels as $channel => $channelObject) {
			if ($channelObject->isConnected($nick))
				return true;
		}
		
		return false;
	}
	
	/**
	 * Returns an array of channels that the bot and user
	 * are both joined
	 *
	 * @param string nickname
	 * @return array channel names
	 */
	public static function getCommonChannels($nick) {
		$channels = array();
		foreach(static::$connectedChannels as $channel => $channelObject) {
			if ($channelObject->isConnected($nick))
				$channels[] = $channelObject->channelName;
		}
		
		return $channels;
	}
}
?>