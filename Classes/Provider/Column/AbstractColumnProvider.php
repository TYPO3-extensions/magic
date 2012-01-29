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
abstract class Tx_Magic_Provider_Column_AbstractColumnProvider {

	/**
	 * @var Tx_Magic_Collection_ModelCollection
	 */
	protected $modelCollection;

	/**
	 * @var Tx_Extbase_Reflection_Service
	 */
	protected $reflectionService;

	/**
	 * @var string
	 */
	protected $propertyName;

	/**
	 * @var array
	 */
	protected $configuration = array();

	/**
	 * @var array
	 */
	protected $options = array();

	/**
	 * @var string
	 */
	protected $label;

	/**
	 * @var integer
	 */
	protected $exclude;


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
	 * @param string $propertyName
	 */
	public function setPropertyName($propertyName) {
		$this->propertyName = $propertyName;
	}

	/**
	 * @param array $configuration
	 */
	public function setConfiguration($configuration) {
		$this->configuration = $configuration;
	}

	/**
	 * @param array $options
	 */
	public function setOptions($options) {
		$this->options = (array) $options;
	}

	/**
	 * @param string $label
	 */
	public function setLabel($label) {
		$this->label = $label;
	}

	/**
	 * @param integer $exclude
	 */
	public function setExclude($exclude) {
		$this->exclude = $exclude;
	}

	/**
	 * @param mixed $offset
	 * @return boolean
	 */
	public function offsetExists($offset) {
		if (count($this->configuration) === 0) $this->generateConfiguration();
		return isset($this->configuration[$offset]);
	}

	/**
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset) {
		if (count($this->configuration) === 0) $this->generateConfiguration();
		return $this->configuration[$offset];
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value) {
		if (count($this->configuration) === 0) $this->generateConfiguration();
		$this->configuration[$offset] = $value;
	}

	/**
	 * @param mixed $offset
	 */
	public function offsetUnset($offset) {
		if (count($this->configuration) === 0) $this->generateConfiguration();
		unset($this->configuration[$offset]);
	}

}

?>