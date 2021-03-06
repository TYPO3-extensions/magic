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
class Tx_Magic_Configuration_Service implements t3lib_Singleton {

	/**
	 * @var Tx_Magic_Configuration_Columns
	 */
	protected $columnConfigurationService;

	/**
	 * @var Tx_Magic_Configuration_Schema
	 */
	protected $databaseSchemaService;

	/**
	 * @param Tx_Magic_Configuration_Columns $columnConfigurationService
	 */
	public function injectColumnConfigurationService(Tx_Magic_Configuration_Columns $columnConfigurationService) {
		$this->columnConfigurationService = $columnConfigurationService;
	}

	/**
	 * @param Tx_Magic_Configuration_Schema $databaseSchemaService
	 */
	public function injectDatabaseSchemaService(Tx_Magic_Configuration_Schema $databaseSchemaService) {
		$this->databaseSchemaService = $databaseSchemaService;
	}

	/**
	 * Writes to global scope all TCA for an entire extension
	 *
	 * @param Tx_Magic_Collection_ExtensionCollection $extensionCollection
	 * @return void
	 */
	public function renderTcaForExtension(Tx_Magic_Collection_ExtensionCollection $extensionCollection) {
		foreach ($extensionCollection->getModelCollections() as $modelCollection) {
			$this->columnConfigurationService->renderTcaForModel($modelCollection);
			$this->databaseSchemaService->updateDatabaseSchemaForModel($modelCollection);
		}
	}


}

?>