<?php
namespace Rattazonk\Spurl\Domain\Translator;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Frederik Vosberg <frederik.vosberg@rattazonk.de>, Rattazonk
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
 ***************************************************************/

/**
 *
 *
 * @package spurl
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class ModelTranslator extends AbstractTranslator implements TranslatorInterface {

	/**
	 * @var \Rattazonk\Spurl\Domain\Model\Path
	 */
	protected $path;

	/**
	 * @var array
	 */
	protected $getParams;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/**
	 * @param \Rattazonk\Spurl\Domain\Model\Path $path
	 */
	public function encode(\Rattazonk\Spurl\Domain\Model\Path $path) {
		$this->path = $path;
		$this->getParams = $path->getGetParams();

		if ( $this->matches() ) {
			$this->initModel();
			$encoded = isset( $this->settings['identifier'] ) ? $this->settings['identifier'] : '';

			$encodedParts = [];
			foreach( $this->settings['encoded']['parts'] as $encodedPart ){
				$encodedParts[] = $this->encodePart( $encodedPart );
			}
			$encodedParts = array_filter( $encodedParts );

			$encoded .= $this->formatEncoded( $encodedParts );
		}

		$path->addEncoded( $encoded );
	}

	protected function matches() {
		return count( array_diff($getParams, $this->settings['decoded']) )
			== count($this->getParams) - count($this->settings['decoded']);
	}

	protected function initModel() {
		$repository = $this->objectManager->get($this->settings['repository']);

		$this->query = $repository->createQuery();
		$this->initModelIdentifierConditions();
		$this->query->matching(
			$this->query->logicalAnd(
				$this->modelIdentifierConditions
			)
		);
		$this->model = $this->query->execute();

		if( count($this->model) == 1 ) {
			$this->model = $this->model->getFirst();
		} else {
			throw new \LogicException(count( $this->model ) . " models found. Must be one. ");
		}
	}

	protected function initModelIdentifierConditions() {
		$identifiers = $this->getModelIdentifiers();
		$this->modelIdentifierConditions = [];
		foreach( $identifiers as $paramName => $fieldName){
			$this->modelIdentifierConditions[] = $this->query->equals( $fieldName, $this->getParams[$paramName] );
		}
	}

	protected function getModelIdentifiers() {
		$identifiers = [];
		foreach( $this->settings['decoded'] as $paramName => $config) {
			if( isset($config['_typoScriptNodeValue'])
				&& isset($config['type'])
				&& $config['type'] == 'db' ) {
				$identifiers[$paramName] = $config['_typoScriptNodeValue'];
			}
		}
		return $identifiers;
	}

	protected function encodePart($partConfig) {
		// var_dump($partConfig['type']);
		// TODO Refactor with strategy pattern?
		if( $partConfig['type'] == 'attribute' ){
			\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->model);
			$encoded = call_user_func([$this->model, 'get' . ucfirst($partConfig['_typoScriptNodeValue'])], []);
		} else if( $partConfig['type'] == 'hierarchical' ){
			$parentGetter = 'get' . ucfirst( $partConfig['parent'] );
			$attributeGetter = 'get' . ucfirst( $parentConfig['_typoScriptNodeValue'] );
			$currentModel = $this->model;
			while(is_object( $currentModel = call_user_func([$currentModel, $parentGetter], []) )){
				\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($currentModel);
				$encoded .= call_user_func([ $currentModel, $parentValueGetter ], []);
			}
		} else {
			throw new \LogicException("part type not implemented. ");
		}
		return $encoded;
	}

	protected function formatEncoded($parts) {
		$formats = (array) $this->settings['encoded']['format'];
		if(isset( $formats['_typoScriptNodeValue'] )){
			array_unshift($formats, $formats['_typoScriptNodeValue']);
			unset( $formats['_typoScriptNodeValue'] );
		}

		foreach( $formats as $format ){
			$encoded = vsprintf($format, $parts);
			if(strlen( $encoded )){ break; }
		}
		if(strlen( $encoded ) < 1){
			throw new \Exception("Error at formating path parts. ");
		}
		return $encoded;
	}
}
?>