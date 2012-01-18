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
class Tx_Magic_Annotation_Parser {

	/**
	 * @var Tx_Extbase_Reflection_Service
	 */
	protected $reflectionService;

	/**
	 * @param Tx_Extbase_Reflection_Service $reflectioService
	 */
	public function injectReflectionService(Tx_Extbase_Reflection_Service $reflectioService) {
		$this->reflectionService = $reflectioService;
	}

	/**
	 * Gets all property annotations from $className based on a provided scope
	 * (fx Magic\Field) to match the @Magic\Field annotations.
	 *
	 * @param string $className
	 * @return array<Tx_Magic_Annotation>
	 * @api
	 */
	public function getPropertyAnnotations($className) {
		$propertyNames = $this->reflectionService->getClassPropertyNames($className);
		$annotations = array();
		foreach ($propertyNames as $propertyName) {
			$propertyReflection = new ReflectionProperty($className, $propertyName);
			$propertyComment = $propertyReflection->getDocComment();
			$propertyAnnotations = $this->parseDocCommentAnnotations($propertyComment);
			if (count($propertyAnnotations) > 0) {
				$annotations[$propertyName] = $propertyAnnotations;
			}
		}
		return $annotations;
	}

	/**
	 * @param string $className
	 * @return array<Tx_Magic_Annotation>
	 */
	public function getClassAnnotations($className) {
		$classReflection = new ReflectionClass($className);
		$classComment = $classReflection->getDocComment();
		$annotations = $this->parseDocCommentAnnotations($classComment);
		return $annotations;
	}

	/**
	 * @param string $comment
	 * @return array<Tx_Magic_Annotation>
	 */
	protected function parseDocCommentAnnotations($comment) {
		$lines = explode(LF, $comment);
		$annotations = array();
		foreach ($lines as $line) {
			$line = trim(ltrim(trim($line), '*'));
			if (strpos($line, '@') === FALSE) {
				continue;
			}
			list ($scope, $arguments) = $this->parseAnnotationArguments($line);
			if (empty($scope)) {
				continue;
			}
			$annotation = Tx_Magic_Core::$objectManager->create('Tx_Magic_Annotation');
			$annotation->setName($scope);
			$annotation->setArguments($arguments);
			$annotations[$scope] = $annotation;
		}
		return $annotations;
	}

	/**
	 * @param string $string
	 * @return array
	 */
	protected function parseAnnotationArguments($string) {
		$tagAndValue = array();
		if (preg_match('/@[a-z0-9\\\\]+\\\\([a-z0-9]+)(?:\\((.*)\\))?$/i', $string, $tagAndValue) === 0) {
			$tagAndValue = preg_split('/\s/', $line, 2);
		}
		$values = array_pop($tagAndValue);
		$tag = array_pop($tagAndValue);
		$caret = 0;
		$lastOperationCaret = 0;
		$length = strlen($values);
		$expecting = '';
		$array = NULL;
		$arrayName = NULL;
		$parameters = array();
		$inset = FALSE;
		while ($caret < $length) {
			$character = $values[$caret];
			if ($expecting !== '' && strpos($expecting, $character) === FALSE) {
				throw new Exception('Magic Core parsing exception. Invalid expression in ' . $string . ' at character position ' . $caret, 1326411981);
			}
			if ($character === '=') {
				$parameterName = substr($values, $lastOperationCaret, $caret - $lastOperationCaret);
				$expecting = '"{';
				$lastOperationCaret = $caret;
			} elseif ($character === '{') {
					// begin collecting for an array
				$array = array();
				$expecting = '';
				$lastOperationCaret = $caret + 1;
				$arrayName = $parameterName;
				$parameterName = '';
			} elseif ($character === '"') {
				if ($inset === FALSE) {
					$inset = TRUE;
					$expecting = '';
				} else {
					$inset = FALSE;
					$expecting = ',}';
					$parameterValue = substr($values, $lastOperationCaret + 1, $caret - $lastOperationCaret - 1);
				}
				$lastOperationCaret = $caret;
			} elseif ($character === '}') {
					// end array processing, assign value
				$parameterValue = $array;
				$parameterName = $arrayName;
				$expecting = '';
				$lastOperationCaret = $caret;
				$array = NULL;
				$arrayName = NULL;
			} elseif ($character === ',') {
				$expecting = '';
			}
			if ($parameterValue) {
				$parameterName = trim($parameterName, '", ');
				if ($array !== NULL) {
					if ($parameterName !== '') {
						$array[$parameterName] = $parameterValue;
					} else {
						array_push($array, $parameterValue);
					}
				} else {
					$parameters[$parameterName] = $parameterValue;
				}
				$parameterName = $parameterValue = NULL;
			}
			$caret++;
		}
		if ($inset === TRUE) {
			throw new Exception('Magic Core parsing exception. Invalid expression in ' . $string . ' at character position ' . $caret, 1326411981);
		}
		return array($tag, $parameters);

    }

}

?>