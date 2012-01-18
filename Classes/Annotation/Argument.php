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
 * @subpackage Core/Annotation
 */
class Tx_Magic_Core_Annotation_Argument {

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var array<Tx_Magic_Core_Annocation_ArgumentValue>
	 */
	protected $values = array();

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return array<Tx_Magic_Core_Annotation_ArgumentValue>
	 */
	public function getValues() {
		return $this->values;
	}

	/**
	 * @param array $values
	 */
	public function setValues($values) {
		$this->values = array();
		foreach ((array) $values as $value) {
			$this->addValue($value);
		}
	}

	/**
	 * @param Tx_Magic_Core_Annotation_ArgumentValue $value
	 */
	public function addValue(Tx_Magic_Core_Annotation_ArgumentValue $value) {
		$hash = spl_object_hash($value);
		$this->values[$hash] = $value;
	}

	/**
	 * @param Tx_Magic_Core_Annotation_ArgumentValue $value
	 */
	public function removeValue(Tx_Magic_Core_Annotation_ArgumentValue $value) {
		$hash = spl_object_hash($value);
		if (isset($this->values[$hash])) {
			unset($this->values[$hash]);
		}
	}

	/**
	 * @return array
	 */
	public function getArrayCopy() {
		$returnValue = array();
		foreach ($this->values as $value) {
			$returnValue[$value->getName()] = $value->getValue();
		}
		return $returnValue;
	}

}

?>