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
 * @package Magic
 * @subpackage Collection
 */
class Tx_Magic_Collection_ExtensionCollection {

	/**
	 * @var string
	 */
	protected $extensionKey;

	/**
	 * @var string
	 */
	protected $extensionName;

	/**
	 * @var array<Tx_Magic_Collections_ModelCollection>
	 */
	protected $modelCollections;

	/**
	 * @var t3lib_cache_frontend_VariableFrontend
	 */
	protected $dataCache;

	/**
	 * @var string
	 */
	protected $dataCacheModelIdentityPrefix = 'magic_models_';

	/**
	 * @var string
	 */
	protected $dataCacheModelFilesIdentityPrefix = 'magic_modelfiles_';

	/**
	 * Initialize this object instance
	 */
	public function initializeObject() {
		$this->dataCache = $GLOBALS['typo3CacheManager']->getCache('magic_cache');
	}

	/**
	 * @param string $extensionKey
	 */
	public function loadExtensionKey($extensionKey) {
		$this->extensionKey = $extensionKey;
		$this->extensionName = Tx_Extbase_Utility_Extension::convertLowerUnderscoreToUpperCamelCase($extensionKey);
		$modelClassDirectorySubPath = 'Classes/Domain/Model/';
		$modelClassDirectory = t3lib_extMgm::extPath($extensionKey, $modelClassDirectorySubPath);
		$modelClassFiles = $this->dataCache->get($this->dataCacheModelFilesIdentityPrefix . $extensionKey);
		if ($modelClassFiles === FALSE) {
			$modelClassFiles = scandir($modelClassDirectory);
			$this->dataCache->set($this->dataCacheModelFilesIdentityPrefix . $extensionKey, $modelClassFiles);
		}
		foreach ($modelClassFiles as $modelClassFileName) {
			if (is_file($modelClassDirectory . $modelClassFileName)) {
				$modelName = basename($modelClassFileName, '.php');
				$className = 'Tx_' . $this->extensionName . '_Domain_Model_' . $modelName;
				$modelCollection = $this->dataCache->get($this->dataCacheModelIdentityPrefix . $className);
				if ($modelCollection === FALSE) {
					$annotations = Tx_Magic_Core::$annotationParser->getClassAnnotations($className);
					$propertyAnnotations = Tx_Magic_Core::$annotationParser->getPropertyAnnotations($className);
					$modelCollection = Tx_Magic_Core::$objectManager->create('Tx_Magic_Collection_ModelCollection');
					$modelCollection->setName($modelName);
					$modelCollection->setClassName($className);
					$modelCollection->setPropertyAnnotations($propertyAnnotations);
					$modelCollection->setAnnotations($annotations);
					$modelCollection->setExtensionKey($this->extensionKey);
					$this->dataCache->set($this->dataCacheIdentityPrefix . $className, $modelCollection);
				}
				$this->addModelCollection($modelCollection);
			}
		}
	}

	/**
	 * @return array<Tx_Magic_Collection_ModelCollection>
	 */
	public function getModelCollections() {
		return $this->modelCollections;
	}

	/**
	 * @param Tx_Magic_Collection_ModelCollection $modelCollection
	 */
	public function addModelCollection(Tx_Magic_Collection_ModelCollection $modelCollection) {
		$hash = spl_object_hash($modelCollection);
		$this->modelCollections[$hash] = $modelCollection;
	}

}

?>