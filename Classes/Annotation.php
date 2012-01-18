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
 * @subpackage Annotation
 */
class Tx_Magic_Annotation {

	/**
	 * @var string
	 */
	protected $name;

	/**
	 *
	 * @var array<Tx_Magic_Annotation_Argument>
	 */
	protected $arguments;

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
	 * @return array<Tx_Magic_Annotation_Argument>
	 */
	public function getArguments() {
		return $this->arguments;
	}

	/**
	 * @param array $arguments
	 */
	public function setArguments($arguments) {
		$this->arguments = (array) $arguments;
	}

	/**
	 * @param string $argumentName
	 * @return Tx_Magic_Annotation_Argument|NULL
	 */
	public function getArgument($argumentName) {
		if (isset($this->arguments[$argumentName])) {
			return $this->arguments[$argumentName];
		} else {
			return NULL;
		}
	}

}

?>