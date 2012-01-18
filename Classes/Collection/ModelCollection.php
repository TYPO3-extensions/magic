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
 * @subpackage Collection
 */
class Tx_Magic_Collection_ModelCollection {

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $className;

	/**
	 * @var array<Tx_Magic_Annotation_Annotation>
	 */
	protected $annotations;

	/**
	 * @var array<Tx_Magic_Annotation_Annotation>
	 */
	protected $propertyAnnotations;

	/**
	 * @var string
	 */
	protected $extensionKey;

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
	 * @return string
	 */
	public function getClassName() {
		return $this->className;
	}

	/**
	 * @param string $className
	 */
	public function setClassName($className) {
		$this->className = $className;
	}

	/**
	 * @return string
	 */
	public function getTableName() {
		return strtolower($this->className);
	}

	/**
	 * @return array<Tx_Magic_Annotation>
	 */
	public function getAnnotations() {
		return $this->annotations;
	}

	/**
	 * @param array $annotations
	 */
	public function setAnnotations($annotations) {
		$this->annotations = $annotations;
	}

	/**
	 * @param Tx_Magic_Annotation $annotation
	 */
	public function addAnnotation(Tx_Magic_Annotation $annotation) {
		$this->annotations[] = $annotation;
	}

	/**
	 * @param string $annotationName
	 * @return Tx_Magic_Annotation|NULL
	 */
	public function getAnnotationByName($annotationName) {
		foreach ($this->annotations as $annotation) {
			if ($annotation->getName() === $annotationName) {
				return $annotation;
			}
		}
		return NULL;
	}

	/**
	 * @return array<Tx_Magic_Core_Annotation_Annotation>
	 */
	public function getPropertyAnnotations() {
		return $this->propertyAnnotations;
	}

	/**
	 * @param array $propertyAnnotations
	 */
	public function setPropertyAnnotations($propertyAnnotations) {
		$this->propertyAnnotations = $propertyAnnotations;
	}

	/**
	 * @param Tx_Magic_Annotation $propertyAnnotation
	 */
	public function addPropertyAnnotation(Tx_Magic_Annotation $propertyAnnotation) {
		$this->propertyAnnotations[] = $propertyAnnotation;
	}

	/**
	 * @param string $annotationName
	 * @return Tx_Magic_Annotation|NULL
	 */
	public function getPropertyAnnotationByName($annotationName) {
		foreach ($this->propertyAnnotations as $propertyName=>$annotations) {
			foreach ($annotations as $annotation) {
				if ($annotation instanceof Tx_Magic_Annotation === FALSE) {
					var_dump($annotation);
					exit();
					continue;
				}
				if ($annotation->getName() === $annotationName) {
					#var_dump($annotation);
					return $annotation;
				}
			}
		}
		return NULL;
	}

	/**
	 * @return string
	 */
	public function getExtensionKey() {
		return $this->extensionKey;
	}

	/**
	 * @param string $extensionKey
	 */
	public function setExtensionKey($extensionKey) {
		$this->extensionKey = $extensionKey;
	}

}

?>