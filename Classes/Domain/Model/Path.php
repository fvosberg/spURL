<?php
namespace Rattazonk\Spurl\Domain\Model;

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
class Path extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var array
	 */
	protected $initPathParts = [];

	/**
	 * @var array
	 */
	protected $initParams = [];

	/**
	 * @var array<>
	 */
	protected $translators = [];

	/**
	 * a flag to reduce the current and next to next.
	 * @var bool
	 */
	protected $translatorPointerOnNegativeOne = TRUE;

	public function initializeObject() {
		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
	}

	/**
	 * initializes the initParams and initPathParts from an encoded Url
	 * @param string 	$encodedUrl
	 * @return void
	 */
	public function setEncodedUrl($encodedUrl) {
		$encodedUrl = preg_replace('/^http(s)?:\/\//', '', $encodedUrl);

		$paramQuery = (string) parse_url($encodedUrl, PHP_URL_QUERY);
		$encodedUrl = str_replace('?' . $paramQuery, '', $encodedUrl);


		$this->initPathParts = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('/', $encodedUrl, TRUE);
		parse_str($paramQuery, $this->initParams);
	}

	/**
	 * @return array
	 */
	public function getInitParams() {
		return $this->initParams;
	}

	/**
	 * @return array
	 */
	public function getInitPathParts() {
		return $this->initPathParts;
	}

	public function initTranslators($translatorDefinitions) {
		foreach ($translatorDefinitions as $key => $translatorDefinition) {
			$this->translators[$key] = $this->objectManager->get($translatorDefinition['class']);
			$this->translators[$key]->setSettings($translatorDefinition['settings']);
			$this->translators[$key]->setPath($this);
		}
	}

	public function getTranslators() {
		return $this->translators;
	}

	public function resetTranslatorPointer() {
		reset($this->translators);
	}

	/**
	 * sets the pointer to the next translator and returns a boolean
	 * @return boolean
	 */
	public function nextTranslator() {
		if ($this->translatorPointerOnNegativeOne) {
			$this->translatorPointerOnNegativeOne = FALSE;
			reset($this->translators);
			return TRUE;
		} else {
			return (bool) next($this->translators);
		}
	}

	public function getCurrentTranslator() {
		$this->translatorPointerOnNegativeOne = FALSE;
		return current($this->translators);
	}

	public function setTranslatorPointerToNegativeOne() {
		$this->translatorPointerOnNegativeOne = TRUE;
	}

	public function getNotProcessedPathParts() {

	}

	public function addProcessedPathPart() {

	}
}
?>