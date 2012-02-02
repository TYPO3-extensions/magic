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
 * @subpackage Configuration
 */
class Tx_Magic_Configuration_Schema implements t3lib_Singleton {

	/**
	 * @var Tx_Magic_Provider_Schema_StandardSchemaProvider
	 */
	protected $standardSchemaProvider;

	/**
	 * @param Tx_Magic_Provider_Schema_StandardSchemaProvider $standardSchemaProvider
	 */
	public function injectStandardSchemaProvider(Tx_Magic_Provider_Schema_StandardSchemaProvider $standardSchemaProvider) {
		$this->standardSchemaProvider = $standardSchemaProvider;
	}

	/**
	 * Updates the database schema according to Model configuration
	 *
	 * @param Tx_Magic_Collection_ModelCollection $modelCollection
	 * @return void
	 */
	public function updateDatabaseSchemaForModel(Tx_Magic_Collection_ModelCollection $modelCollection) {
		$updateRequired = FALSE;
		$currentSchema = $this->getCurrentSchemaForModel($modelCollection);
		$expectedSchema = $this->getExpectedSchemaForModel($modelCollection);
		list ($currentSchemaFields, $currentSchemaIndices) = array_values($currentSchema);
		list ($expectedSchemaFields, $expectedSchemaIndices) = array_values($expectedSchema);
		foreach ($expectedSchemaIndices as $key => $index) {
			if ($currentSchemaIndices[$key] !== $index) {
				$updateRequired = TRUE;
				break;
			}
		}
		foreach ($expectedSchemaFields as $key => $fieldDefinition) {
			if ($currentSchemaFields[$key] !== $fieldDefinition) {
				$updateRequired = TRUE;
				break;
			}
		}
		if ($updateRequired === TRUE) {
			$this->executeSchemaUpdate($modelCollection->getTableName(), $currentSchema, $expectedSchema);
		}
	}


	/**
	 * @param Tx_Magic_Collection_ModelCollection $modelCollection
	 */
	public function getCurrentSchemaForModel(Tx_Magic_Collection_ModelCollection $modelCollection) {
		$schema = $this->standardSchemaProvider->getCurrentFieldDefinitions($modelCollection);
		if (is_array($schema['fields'])) ksort($schema['fields']);
		if (is_array($schema['extra'])) ksort($schema['extra']);
		return $schema;
	}

	/**
	 * @param Tx_Magic_Collection_ModelCollection $modelCollection
	 */
	public function getExpectedSchemaForModel(Tx_Magic_Collection_ModelCollection $modelCollection) {
		$schema = $this->standardSchemaProvider->getModelFieldDefinitions($modelCollection);
		if (is_array($schema['fields'])) ksort($schema['fields']);
		if (is_array($schema['extra'])) ksort($schema['extra']);
		return $schema;
	}

	/**
	 * @param string $tableName
	 * @param array $currentSchemaFields
	 * @param array $expectedSchemaFields
	 */
	protected function executeSchemaUpdate($tableName, array $currentSchemaFields, array $expectedSchemaFields) {
		if ($GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', $tableName, '', '', '', 1) === FALSE) {
			$GLOBALS['TYPO3_DB']->sql_query('CREATE TABLE `' . $tableName . '` (`uid` int(11) unsigned NOT NULL);');
		}
		foreach ($expectedSchemaFields as $sectionName=>$section) {
			foreach ($section as $name=>$sqlColumnDefinition) {
				if (isset($currentSchemaFields[$sectionName][$name]) && $currentSchemaFields[$sectionName][$name] !== $expectedSchemaFields[$sectionName][$name]) {
					$GLOBALS['TYPO3_DB']->sql_query('ALTER TABLE `' . $tableName . '` MODIFY ' . $sqlColumnDefinition);
				} else if (isset($currentSchemaFields[$sectionName][$name]) === FALSE) {
					$GLOBALS['TYPO3_DB']->sql_query('ALTER TABLE `' . $tableName . '` ADD ' . $sqlColumnDefinition);
				}
			}
		}
	}

}

?>