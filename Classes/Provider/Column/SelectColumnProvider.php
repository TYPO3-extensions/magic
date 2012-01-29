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
class Tx_Magic_Provider_Column_SelectColumnProvider extends Tx_Magic_Provider_Column_AbstractColumnProvider implements Tx_Magic_Provider_ColumnProviderInterface {

	/**
	 * @return array
	 */
	public function generateConfiguration() {
		$this->configuration = array(
			'type' => 'select',
			'mode' => $this->options['mode'],
		);
		if (isset($this->options['range'])) {
			$this->configuration['items'] = $this->generateRangeItems($this->options['range'], $this->options['interval']);
		} elseif (isset($this->options['foreignTable'])) {
			$this->configuration['foreign_table'] = $this->options['foreignTable'];
		}

	}

	/**
	 * Generates a set of <option> in a $range (fx 10-50) incremeted by $interval
	 * @param string $rangePair
	 * @param integer $interval
	 */
	public function generateRangeItems($rangePair, $interval) {
		if (intval($interval) === 0) {
			$interval = 1;
		}
		$pair = t3lib_div::trimExplode('-', $rangePair);
		$items = array();
		for ($i=$pair[0]; $i<$pair[1]; $i+=$interval) {
			$item = array(strval($i), strval($i));
			array_push($items, $item);
		}

		return $items;
	}

}

?>