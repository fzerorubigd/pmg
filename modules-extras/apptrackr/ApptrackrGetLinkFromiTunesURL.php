<?php
/**
 * ApptrackrGetLinkFromiTunesURL Module
 * Gets an Apptrackr link for the given iTunes URL
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */
namespace modules\apptrackr;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;

class ApptrackrGetLinkFromiTunesURL extends Module {
	
	public static $requiredUserLevel = 0;
	
	public function run() {
		
		$itunesURL = $this->parameters(1);
			
		// Find out the application ID
		$request = array(
			'object' => 'App',
			'action' => 'scrape',
			'args' => array(
				'itunes_url' => $itunesURL,
				'fields' => array("appid")
			)
		);
		
		$wrapper = array('request' => json_encode($request));
		$wrapper = urlencode(json_encode($wrapper));
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.apptrackr.org/');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'request=' . $wrapper);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$returnData = curl_exec($ch);
		curl_close($ch);
		
		$working = json_decode($returnData);
		
		$responseCode = $working->code;
		$jsonDataBlock = $working->data;
		$signature = $working->signature;
		
		$dataBlock = json_decode($jsonDataBlock);
		$appID = $dataBlock->appid;
		$appID+=0;
		
		// Find out what the latest version and app name are
		$request = array(
			'object' => 'App',
			'action' => 'getDetails',
			'args' => array(
				'app_id' => $appID,
				'fields' => array("latest_version", "name")
			)
		);
		
		$wrapper = array('request' => json_encode($request));
		$wrapper = urlencode(json_encode($wrapper));
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.apptrackr.org/');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'request=' . $wrapper);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$returnData = curl_exec($ch);
		curl_close($ch);
		
		$working = json_decode($returnData);
		
		$responseCode = $working->code;
		$jsonDataBlock = $working->data;
		$signature = $working->signature;
		
		$dataBlock = json_decode($jsonDataBlock);
		$latestVersion = $dataBlock->app->latest_version;
		$appName = $dataBlock->app->name;
		
		// Now get some links
		$request = array(	
			'object' => 'Link',
			'action' => 'get',
			'args' => array(
				'app_id' => $appID,
			)
		
		);
		$wrapper = array('request' => json_encode($request));
		$wrapper = urlencode(json_encode($wrapper));
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.apptrackr.org/');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'request=' . $wrapper);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$returnData = curl_exec($ch);
		curl_close($ch);
		
		$working = json_decode($returnData);
		
		$responseCode = $working->code;
		$jsonDataBlock = $working->data;
		$signature = $working->signature;
		
		$dataBlock = json_decode($jsonDataBlock);
		$latestVersionLinks = $dataBlock->links->$latestVersion;
		
		$server = Server::getInstance();
		
		if ($responseCode != 200)
			$server->notify($this->senderNick, "Sorry, we could not find any links for that app. API returned code " . $responseCode);
		else
			$server->message($this->channel, $appName . " - v" . $latestVersion . " - " . $latestVersionLinks[0]->url);
	}
}
?>