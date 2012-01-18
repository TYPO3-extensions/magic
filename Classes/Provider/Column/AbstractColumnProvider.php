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
 * @subpackage Provider/Column
 */
class Tx_Magic_Provider_Column_AbstractColumnProvider {

	/**
	 * @var Tx_Magic_Collection_ModelCollection
	 */
	protected $modelCollection;

	/**
	 * @var Tx_Extbase_Reflection_Service
	 */
	protected $reflectionService;

	/**
	 * @param Tx_Extbase_Reflection_Service $reflectionService
	 */
	public function injectReflectionService(Tx_Extbase_Reflection_Service $reflectionService) {
		$this->reflectionService = $reflectionService;
	}

	/**
	 * @param Tx_Magic_collection_ModelCollection $modelCollection
	 */
	public function setModelCollection($modelCollection) {
		$this->modelCollection = $modelCollection;
	}

	/**
	 * @param array $configuration
	 * @param string $label
	 * @param integer $exclude
	 * @return array
	 */
	protected function render($propertyName, $configuration, $label=NULL, $exclude=0) {
		$tableName = $this->modelCollection->getTableName();
		$extensionKey = $this->modelCollection->getExtensionKey();
		$underScoredPropertyName = Tx_Extbase_Utility_Extension::convertCamelCaseToLowerCaseUnderscored($propertyName);
		if ($label === NULL) {
			$label = 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/locallang_db.xml:' . $tableName . '.' . $underScoredPropertyName;
		}
		return array(
			'exclude' => $exclude,
			'label' => $label,
			'config' => $configuration
		);
	}

}

?>