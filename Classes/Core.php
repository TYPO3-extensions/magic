<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Claus Due <claus@wildside.dk>, Wildside A/S
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

/**
 * Core interface for extension Magic
 *
 * This class (static) provides the light-weight methods necessary to register,
 * process, setup and add the requested configuration.
 *
 * @package Magic
 * @subpackage Core
 */
class Tx_Magic_Core {

	/**
	 * ObjectManager registered here to save some overhead processing time in
	 * class loading and object creation internally.
	 *
	 * You are welcome to use this one in your own small classes where you
	 * don't judge dependency injection to be necessary.
	 *
	 * @var Tx_Extbase_Object_ObjectManager
	 * @api
	 */
	public static $objectManager;

	/**
	 * @var Tx_Magic_Annotation_Parser
	 * @api
	 */
	public static $annotationParser;

	/**
	 * @var Tx_Magic_Configuration_Service
	 * @api
	 */
	public static $configurationService;

	/**
	 * @var array
	 */
	public static $registeredExtensionKeys;

	/**
	 * Constructor
	 */
	public static function initializeObject() {
		if (!self::$objectManager) {
			self::$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
			self::$annotationParser = self::$objectManager->create('Tx_Magic_Annotation_Parser');
			self::$configurationService = self::$objectManager->get('Tx_Magic_Configuration_Service');
			self::$registeredExtensionKeys = array();
		}
	}

	/**
	 * Registers an extension key for an extension which has Model classes you
	 * want to process using Magic. All further integration happens through
	 * class property annotations.
	 *
	 * @param string $extensionKey
	 * @api
	 */
	public static function registerExtensionKey($extensionKey) {
		self::initializeObject();
		if (in_array($extensionKey, self::$registeredExtensionKeys) === FALSE) {
			$extensionCollection = self::$objectManager->create('Tx_Magic_Collection_ExtensionCollection');
			$extensionCollection->loadExtensionKey($extensionKey);
			self::$configurationService->renderTcaForExtension($extensionCollection);
			self::$registeredExtensionKeys[$extensionKey] = $extensionCollection;
		}
	}

	/**
	 * Gets an array of registered extension keys
	 *
	 * @return array
	 * @api
	 */
	public static function getRegisteredExtensionKeys($extensionKey) {
		return self::$registeredExtensionKeys;
	}

}

?>