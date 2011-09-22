<?php
/**
 * DataManager class
 * Handles data which modules can store and retrieve
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */

namespace awesomeircbot\data;

class DataManager {
	
	protected static $data = array();
	
	/**
	 * Retrieves data for a module
	 * Retrieves under the identifier of the modules
	 * full namespace unless $module is provided
	 *
	 * @param string the identifier under which the data was stored
	 * @param string optionally, override the module name to access another
	 * module's data
	 */
	public function retrieve($request, $module=false) {
		 
		 $trace = debug_backtrace();
		 if (!$module)
		 	$module = $trace[1]['class'];
		 
		 if (DataManager::$data[$module][$request])
		 	return DataManager::$data[$module][$request];
		 else
		 	return false;
	}
	
	/**
	 * Stores data under an identifier
	 * Stores under the identifier of the modules
	 * full namespace unless $module is provided
	 *
	 * @param string the identifier to store the data under
	 * @param mixed the data to store, supports objects, arrays, strings, etc.
	 * @param string optionally, override the module name to store in another
	 * module's space
	 */
	public function store($id, $data, $module=false) {
		
		$trace = debug_backtrace();
		if (!$module)
			$module = $trace[1]['class'];
		
		if (!DataManager::$data[$module])
			DataManager::$data[$module] = array();
		
		DataManager::$data[$module][$id] = $data;
		
		return true;
	}
}
?>