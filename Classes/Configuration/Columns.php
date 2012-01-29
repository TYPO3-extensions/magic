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
class Tx_Magic_Configuration_Columns {


	/**
	 * Writes to global scope the TCA necessary to drive the Model configured
	 * in ModelCollection which was pased when loading the ext_tables.php file.
	 *
	 * @param Tx_Magic_Collection_ModelCollection $modelCollection
	 * @return void
	 */
	public function renderTcaForModel(Tx_Magic_Collection_ModelCollection $modelCollection) {
		global $TCA;
		$tableName = $modelCollection->getTableName();
		$TCA[$tableName] = $this->renderRootBlock($modelCollection);
	}

	/**
	 * @param Tx_Magic_Collection_ModelCollection $modelCollection
	 */
	public function renderRootBlock(Tx_Magic_Collection_ModelCollection $modelCollection) {
		$tableName = $modelCollection->getTableName();
		$extensionKey = $modelCollection->getExtensionKey();
		t3lib_extMgm::addLLrefForTCAdescr($tableName, 'EXT:' . $extensionKey . '/Resources/Private/Language/locallang_csh_' . $tableName . '.xml');
		t3lib_extMgm::allowTableOnStandardPages($tableName);
		return array(
			'ctrl' => array(
				'title'	=> 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/locallang_db.xml:' . $tableName,
				'label' => $this->renderLabelFields($modelCollection),
				'tstamp' => 'tstamp',
				'crdate' => 'crdate',
				'cruser_id' => 'cruser_id',
				'dividers2tabs' => TRUE,
				'versioningWS' => 2,
				'versioning_followPages' => TRUE,
				'origUid' => 't3_origuid',
				'languageField' => 'sys_language_uid',
				'transOrigPointerField' => 'l10n_parent',
				'transOrigDiffSourceField' => 'l10n_diffsource',
				'delete' => 'deleted',
				'enablecolumns' => $modelCollection->getAnnotationByName('EnableFields')->getArgument('properties'),
				'iconfile' => t3lib_extMgm::extRelPath($extensionKey) . 'Resources/Public/Icons/' . $tableName . '.gif',
			),
			'types' => $this->renderTypesBlock($modelCollection),
			'interface' => $this->renderInterfaceBlock($modelCollection),
			'palettes' => $this->renderPalettesBlock($modelCollection),
			'columns' => $this->renderColumnsBlock($modelCollection),
		);
	}

	/**
	 * @param Tx_Magic_Collection_ModelCollection $modelCollection
	 * @return array
	 */
	protected function renderTypesBlock(Tx_Magic_Collection_ModelCollection $modelCollection) {
		$properties = array();
		foreach ($modelCollection->getPropertyAnnotations() as $propertyName=>$annotations) {
			$underscoredPropertyName = Tx_Extbase_Utility_Extension::convertCamelCaseToLowerCaseUnderscored($propertyName);
			array_push($properties, $underscoredPropertyName);
		}
		return array(
			'1' => array(
				'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, '
				. implode(', ', $properties) . ',--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'
			),
		);
	}

	/**
	 * @param Tx_Magic_Collection_ModelCollection $modelCollection
	 * @return array
	 */
	protected function renderInterfaceBlock(Tx_Magic_Collection_ModelCollection $modelCollection) {
		$properties = array(
			'sys_language_uid', 'l10n_parent', 'l10n_diffsource', 'hidden'
		);
		foreach (array_keys($modelCollection->getPropertyAnnotations()) as $propertyName) {
			if (strpos($propertyName, '_') !== 0) {
				array_push($properties, Tx_Extbase_Utility_Extension::convertCamelCaseToLowerCaseUnderscored($propertyName));
			}
		}
		return array(
			'showRecordFieldList' => implode(', ', $properties)
		);
	}

	/**
	 * @param Tx_Magic_Collection_ModelCollection $modelCollection
	 * @return array
	 */
	protected function renderPalettesBlock(Tx_Magic_Collection_ModelCollection $modelCollection) {
		return array(
			'1' => array('showitem' => ''),
		);
	}

	/**
	 * @param Tx_Magic_Collection_ModelCollection $modelCollection
	 * @return array
	 */
	protected function renderColumnsBlock(Tx_Magic_Collection_ModelCollection $modelCollection) {
		$columns = $this->renderColumnDefaults($modelCollection);
		$tableName = $modelCollection->getTableName();
		$extensionKey = $modelCollection->getExtensionKey();
		foreach ($modelCollection->getPropertyAnnotations() as $propertyName=>$annotations) {
			$exclude = 0; // TODO: parse exclude-field annotation
			$label = ''; // TODO: parse label annotation
			$underScoredPropertyName = Tx_Extbase_Utility_Extension::convertCamelCaseToLowerCaseUnderscored($propertyName);
			if ($label == '') {
				$label = 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/locallang_db.xml:' . $tableName . '.' . $underScoredPropertyName;
			}
			$provider = $this->resolveColumnProvider($propertyName, $modelCollection);
			$provider->setPropertyName($propertyName);
			$provider->setModelCollection($modelCollection);
			$provider->setOptions($annotations['Field']->getArgument('options'));
			$columns[$underScoredPropertyName] = array(
				'exclude' => intval($exclude),
				'label' => $label,
				'config' => $provider
			);
		}
		return $columns;
	}

	/**
	 * @param Tx_Magic_Collection_ModelCollection $modelCollection
	 * @return array
	 */
	protected function renderColumnDefaults(Tx_Magic_Collection_ModelCollection $modelCollection) {
		$columnsAnnotation = $modelCollection->getAnnotationByName('Columns');
		if ($columnsAnnotation) {
			$providerArgument = $columnsAnnotation->getArgument('provider');
			if ($providerArgument) {
				$providerClassName = $providerArgument->getValue();
			}
		}
		if (class_exists($providerClassName) === FALSE) {
			$providerClassName = 'Tx_Magic_Provider_Columns_StandardColumnsProvider';
		}
		$instance = Tx_Magic_Core::$objectManager->get($providerClassName);
		return $instance->getColumns($modelCollection);
	}

	/**
	 * @param string $propertyName
	 * @param Tx_Magic_Collection_ModelCollection $modelCollection
	 * @return Tx_Magic_Provider_ColumnProviderInterface
	 */
	protected function resolveColumnProvider($propertyName, Tx_Magic_Collection_ModelCollection $modelCollection) {
		$propertyAnnotations = $modelCollection->getPropertyAnnotations();
		$propertyAnnotation = $propertyAnnotations[$propertyName]['Field'];
		$defaultProviderClassName = 'Tx_Magic_Provider_Column_StandardColumnProvider';
		if ($propertyAnnotation === NULL) {
			return Tx_Magic_Core::$objectManager->create($defaultProviderClassName);
		}
		if ($propertyAnnotation->getArgument('provider')) {
			$providerClassName = $propertyConfiguration->getArgument('provider');
		} else if ($propertyAnnotation->getArgument('type')) {
			$providerClassName = 'Tx_Magic_Provider_Column_' . ucfirst($propertyAnnotation->getArgument('type')) . 'ColumnProvider';
		} else {
			$providerClassName = $defaultProviderClassName;
		}
		if (class_exists($providerClassName) === FALSE) {
			throw new Exception('Invalid ColumnProvider class: ' . $providerClassName, 1326645658);
		} else if (in_array('Tx_Magic_Provider_ColumnProviderInterface', class_implements($providerClassName)) === FALSE) {
			throw new Exception('ColumProvider ' . $providerClassName . ' must implement interface Tx_Magic_Provider_ColumnProviderInterface', 1326645948);
		}
		return Tx_Magic_Core::$objectManager->get($providerClassName);
	}

	/**
	 * @param Tx_Magic_Collection_ModelCollection $modelCollection
	 * @return string
	 */
	protected function renderLabelFields(Tx_Magic_Collection_ModelCollection $modelCollection) {
		$labelAnnotation = $modelCollection->getAnnotationByName('Label');
		if ($labelAnnotation instanceof Tx_Magic_Annotation) {
			$labelFields = implode(',', $labelAnnotation->getArgument('properties'));
		} else {
			$labelFields = 'uid';
		}
		return $labelFields;
	}

}

?>