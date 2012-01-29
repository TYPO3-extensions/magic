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
class Tx_Magic_Provider_Column_InlineColumnProvider extends Tx_Magic_Provider_Column_AbstractColumnProvider implements Tx_Magic_Provider_ColumnProviderInterface {

	/**
	 * @return array
	 */
	public function generateConfiguration() {
		$propertyName = $this->propertyName;
		$modelClassName = $this->modelCollection->getClassName();
		$propertyReflection = $this->reflectionService->getPropertyTagValues($modelClassName, $propertyName, 'var');
		$foreignTable = strtolower(array_pop(explode('<', trim($propertyReflection[0], '>'))));
		$foreignField = strtolower(array_pop(explode('_', $modelClassName)));
		$this->configuration = array(
			'type' => 'inline',
			'foreign_table' => $foreignTable,
			'foreign_field' => $foreignField,
			'maxitems'      => 9999,
			'appearance' => array(
				'collapse' => 0,
				'levelLinksPosition' => 'top',
				'showSynchronizationLink' => 1,
				'showPossibleLocalizationRecords' => 1,
				'showAllLocalizationLink' => 1
			),
		);
	}

}

?>