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
 * Default Columns Provider
 *
 * Provides default columns required by TCA according to ModelCollection
 *
 * @package Magic
 * @subpackage Provider/Columns
 */
class Tx_Magic_Provider_Columns_DefaultColumnsProvider implements Tx_Magic_Provider_ColumnsProviderInterface {

	/**
	 * @param Tx_Magic_Collection_ModelCollection $modelCollection
	 * @return array
	 */
	public function getColumns(Tx_Magic_Collection_ModelCollection $modelCollection) {
		$tableName = $modelCollection->getTableName();
		return array(
			'sys_language_uid' => array(
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
				'config' => array(
					'type' => 'select',
					'foreign_table' => 'sys_language',
					'foreign_table_where' => 'ORDER BY sys_language.title',
					'items' => array(
						array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
						array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
					),
				),
			),
			'l10n_parent' => array(
				'displayCond' => 'FIELD:sys_language_uid:>:0',
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
				'config' => array(
					'type' => 'select',
					'items' => array(
						array('', 0),
					),
					'foreign_table' => $tableName,
					'foreign_table_where' => 'AND ' . $tableName . '.pid=###CURRENT_PID### AND ' . $tableName . '.sys_language_uid IN (-1,0)',
				),
			),
			'l10n_diffsource' => array(
				'config' =>array(
					'type' => 'passthrough',
				),
			),
			't3ver_label' => array(
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
				'config' => array(
					'type' => 'input',
					'size' => 30,
					'max' => 255,
				)
			),
			'hidden' => array(
				'exclude' => 1,
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
				'config' => array(
					'type' => 'check',
				),
			),
			'starttime' => array(
				'exclude' => 1,
				'l10n_mode' => 'mergeIfNotBlank',
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
				'config' => array(
					'type' => 'input',
					'size' => 13,
					'max' => 20,
					'eval' => 'datetime',
					'checkbox' => 0,
					'default' => 0,
					'range' => array(
						'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
					),
				),
			),
			'endtime' => array(
				'exclude' => 1,
				'l10n_mode' => 'mergeIfNotBlank',
				'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
				'config' => array(
					'type' => 'input',
					'size' => 13,
					'max' => 20,
					'eval' => 'datetime',
					'checkbox' => 0,
					'default' => 0,
					'range' => array(
						'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
					),
				),
			),
		);
	}

}

?>