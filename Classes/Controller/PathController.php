<?php
namespace Rattazonk\Spurl\Controller;

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
class PathController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \Rattazonk\Spurl\Domain\Model\Path
	 */
	protected $path;

	/**
	 * encodes the decoded URL from the TypoScript link creation to a pretty spURL
	 * @param string 	$decodedUrl 	URL with get params
	 * @return string 	$encodedUrl		spURL
	 */
	public function encodeTypoScriptLinkAction($decodedUrl) {
		// REFATOR NEW API TODO
		$this->path = $this->objectManager->get('\Rattazonk\Spurl\Domain\Model\Path');
		\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->settings);
		die();
		$this->path->setConfig($this->settings['pathParts']);
		return $this->path->encode($decodedUrl);
	}

	/**
	 * @param string 	$encodedUrl
	 * @return array 	getParams
	 */
	public function decodeAction($encodedUrl) {
		throw new \Exception( "DEPRECATED due to ddos. " );
		$path = $this->objectManager->get('\Rattazonk\Spurl\Domain\Model\Path');
		$path->setEncodedUrl($encodedUrl);

		// TODO gehirnfurzenglisch ersetzen
		// load the settings of the root page to get the translators for the path parts till page path
		// TODO config the Root page uid
		$pageId = 1;
		$settings = $this->getSettingsOfPage($pageId);
		$path->initTranslators($settings['translators']);
		var_dump($settings['translators'][20]['settings']);
die();
		// whenever the id got decoded we are stopping to retrieve the settings from this page
		$path->setTranslatorPointerToNegativeOne();
		while ( $path->nextTranslator() ) {
			$currentTranslator = $path->getCurrentTranslator();
			$currentTranslator->decode();
			$lastGetParams = $currentTranslator->getDecodedParams();
			if (isset($lastGetParams['id']) && (int) $lastGetParams['id'] != $pageId) {
				$pageId = (int) $lastGetParams['id'];
				$settings = $this->getSettingsOfPage($pageId);
				$path->initTranslators($settings['pathParts']);
			}
		}
		// decoded and init paramsu
		return $path->getParams();
	}

	protected function getSettingsOfPage($pageId) {
		// do not instantiate every ... TODO
		$typoScriptService = $this->objectManager->get('TYPO3\CMS\Extbase\Service\TypoScriptService');
		$config = $typoScriptService->convertTypoScriptArrayToPlainArray(
			$this->getTypoScriptTemplateOfPage($pageId)
		);
		return $config['plugin']['tx_spurl']['settings'];
	}

	/**
	 * dangerous method, because we depend highly on the internal structure of the TypoScriptFrontendController
	 * we don't use the global TSFE to get the typoscript templates for the indexer and dont destroy the configuration of the current page
	 * pages should be loaded from the spUrl cache, so it is not a performance issue
	 *
	 * @param int $pageId 	The id of the page we want to load the typoscript from
	 * @return array
	 */
	protected function getTypoScriptTemplateOfPage($pageId) {
		// dont use the objectmanager, because it will fail, because of the classInfo ... maybe TODO
		$tsfe = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
			'\TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController',
			$TYPO3_CONF_VARS,
			$pageId,
			\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('type'),
			\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('no_cache'),
			\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('cHash'),
			\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('jumpurl'),
			\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('MP'),
			\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('RDCT')
		);
		// $this->typoScriptFrontendController->id = $pageId;
		$tsfe->initFEuser();
		$tsfe->determineId();
		$tsfe->getPageAndRootline();
		$tsfe->initTemplate();
		$tsfe->getFromCache();
		$tsfe->getConfigArray();

		return $tsfe->tmpl->setup;
	}
}
?>