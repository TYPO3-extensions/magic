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
 * @subpackage Provider/Schema
 */
class Tx_Magic_Provider_Schema_StandardSchemaProvider extends Tx_Magic_Provider_Schema_AbstractSchemaProvider implements t3lib_Singleton {

	/**
	 * @param Tx_Magic_Collection_ModelCollection $modelCollection
	 */
	public function getCurrentFieldDefinitions(Tx_Magic_Collection_ModelCollection $modelCollection) {
		$createSyntaxResult = $GLOBALS['TYPO3_DB']->sql_query('SHOW CREATE TABLE ' . $modelCollection->getTableName());
		if ($createSyntaxResult === FALSE) {
			return array();
		}
		$fields = array();
		$extra = array();
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($createSyntaxResult);
		$lines = t3lib_div::trimExplode("\n", $row['Create Table']);
		$lines = array_slice($lines, 1, count($lines) - 2);
		foreach ($lines as $line) {
			$pair = t3lib_div::trimExplode(' ', $line, TRUE, 2);
			$key = trim($pair[0], '`');
			if (preg_match('/[A-Z]/', $key)) {
				array_push($extra, trim($line, ','));
			} else {
				$fields[$key] = trim($line, ',');
			}
		}
		return array(
			'fields' => $fields,
			'extra' => $extra
		);
	}

	/**
	 * @param Tx_Magic_Collection_ModelCollection $modelCollection
	 */
	public function getModelFieldDefinitions(Tx_Magic_Collection_ModelCollection $modelCollection) {
		$extra = $this->getDefaultIndexDefinitions($modelCollection);
		$fields = $this->getDefaultFieldDefinitions($modelCollection);
		foreach ($modelCollection->getPropertyAnnotations() as $propertyName=>$annotations) {
			$underscoredPropertyName = Tx_Extbase_Utility_Extension::convertCamelCaseToLowerCaseUnderscored($propertyName);
			if (isset($annotations['OneToMany']) === TRUE || isset($annotations['ManyToOne']) === TRUE || isset($annotations['OneToOne']) === TRUE || isset($annotations['ManyToMany']) === TRUE) {
				$definition['type'] = 'integer';
				$definition['size'] = 11;
			} else if (isset($annotations['Column']) === FALSE) {
				continue;
			} else {
				$definition = $annotations['Column']->getArguments();
			}
			$definition['nullable'] = ($definition['nullable'] !== 'TRUE');
			$definitionString = $this->getFieldDefinitionString($underscoredPropertyName, $definition);
			$fields[$underscoredPropertyName] = $definitionString;
		}
		return array(
			'fields' => $fields,
			'extra' => $extra
		);
	}

	/**
	 * @param Tx_Magic_Collection_ModelCollection $modelCollection
	 */
	public function getDefaultIndexDefinitions(Tx_Magic_Collection_ModelCollection $modelCollection) {
		$primaryKeyColumns = $modelCollection->getAnnotationByName('PrimaryKey')->getArgument('columns');
		$keyColumns = $modelCollection->getAnnotationByName('Key')->getArgument('columns');
		$extra = array();
		$keyDefinition = 'PRIMARY KEY (`' . implode('`,`', $primaryKeyColumns) . '`)';
		array_push($extra, $keyDefinition);
		foreach ($keyColumns as $keyName => $keyColumn) {
			$keyDefinition = 'KEY `' . $keyName . '` (`' . implode('`,`', t3lib_div::trimExplode(',', $keyColumn)) . '`)';
			array_push($extra, $keyDefinition);
		}
		return $extra;
	}

	/**
	 * @param Tx_Magic_Collection_ModelCollection $modelCollection
	 */
	public function getDefaultFieldDefinitions(Tx_Magic_Collection_ModelCollection $modelCollection) {
		return array(
			'uid' => "`uid` int(11) unsigned NOT NULL AUTO_INCREMENT",
			'crdate' => "`crdate` int(11) unsigned NOT NULL DEFAULT '0'",
			'cruser_id' => "`cruser_id` int(11) unsigned NOT NULL DEFAULT '0'",
			'deleted' => "`deleted` tinyint(4) unsigned NOT NULL DEFAULT '0'",
			'hidden' => "`hidden` tinyint(4) unsigned NOT NULL DEFAULT '0'",
			'endtime' => "`endtime` int(11) unsigned NOT NULL DEFAULT '0'",
			'l10n_diffsource' => "`l10n_diffsource` mediumblob",
			'l10n_parent' => "`l10n_parent` int(11) NOT NULL DEFAULT '0'",
			'pid' => "`pid` int(11) NOT NULL DEFAULT '0'",
			'starttime' => "`starttime` int(11) unsigned NOT NULL DEFAULT '0'",
			'sys_language_uid' => "`sys_language_uid` int(11) NOT NULL DEFAULT '0'",
			't3_origuid' => "`t3_origuid` int(11) NOT NULL DEFAULT '0'",
			't3ver_count' => "`t3ver_count` int(11) NOT NULL DEFAULT '0'",
			't3ver_id' => "`t3ver_id` int(11) NOT NULL DEFAULT '0'",
			't3ver_label' => "`t3ver_label` varchar(255) NOT NULL DEFAULT ''",
			't3ver_move_id' => "`t3ver_move_id` int(11) NOT NULL DEFAULT '0'",
			't3ver_oid' => "`t3ver_oid` int(11) NOT NULL DEFAULT '0'",
			't3ver_stage' => "`t3ver_stage` int(11) NOT NULL DEFAULT '0'",
			't3ver_state' => "`t3ver_state` tinyint(4) NOT NULL DEFAULT '0'",
			't3ver_tstamp' => "`t3ver_tstamp` int(11) NOT NULL DEFAULT '0'",
			't3ver_wsid' => "`t3ver_wsid` int(11) NOT NULL DEFAULT '0'",
			'tstamp' => "`tstamp` int(11) unsigned NOT NULL DEFAULT '0'"
		);
	}

	/**
	 * @param string $name
	 * @param array $definition
	 * @return string
	 */
	protected function getFieldDefinitionString($name, $definition) {
		$fieldtype = $fieldDefinition = '';
		$default = (isset($definition['default']) === TRUE ? $definition['default'] : NULL);
		switch ($definition['type']) {
			case 'integer':
			case 'int':
				$signed = ($definition['signed'] !== 'FALSE');
				$fieldType = 'int(' . (isset($definition['size']) ? $definition['size'] : 11) . ')';
				$fieldDefinition = ($signed === FALSE ? 'unsighed ' : '');
				$default = ($default !== NULL ? $default : '0');
				break;
			case 'varchar':
				$fieldType = 'varchar(' . (isset($definition['size']) ? $definition['size'] : 255) . ')';
				$default = ($defaut !== NULL ? $default : '');
				break;
			case 'text':
				#$default = '';
			default:
				$fieldType = $definition['type'];
				break;

		}
		$nullable = ($definition['nullable'] === 'TRUE' ? 'NULL' : 'NOT NULL');
		$segments = array('`' . $name . '`', $fieldType);
		if ($fieldDefinition) {
			array_push($segments, $fieldDefinition);
		}
		$segments = array_merge($segments, array($nullable));
		if ($default !== NULL) {
			array_push($segments, "DEFAULT '" . $default . "'");
		}
		return implode(' ', $segments);
	}

}

?>