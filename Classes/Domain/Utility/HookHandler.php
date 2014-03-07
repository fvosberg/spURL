<?php
namespace Rattazonk\Spurl\Domain\Utility;

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
class HookHandler {
	/**
	 * @var string
	 */
	protected $controllerClassName = '\Rattazonk\Spurl\Controller\PathController';

	public function hookTypoScriptLinkCreation(&$params, $pObj) {
		$encoded = $this->cachedEncode($params['LD']['totalURL']);

		if (is_string($encoded)) {
			$params['LD']['totalURL'] = $encoded;
		} else {
			$this->getExtbaseBootstrap()->initialize(array(
				'pluginName' => 'Spurl',
				'extensionName' => 'Spurl',
				'vendorName' => 'Rattazonk'
			));
			$controller = $this->getObjectManager()->get($this->controllerClassName);
			$params['LD']['totalURL'] = call_user_func(array($controller, 'encodeTypoScriptLinkAction'), $params['LD']['totalURL']);
		}
	}

	protected function cachedEncode($url) {
		var_dump('cached encode ' . $url);
	}

	protected function getObjectManager() {
		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
	}

	protected function getExtbaseBootstrap() {
		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Core\Bootstrap');
	}

	public function decode($_params, &$pObj) {
		$encoded = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('HTTP_HOST') . '/' . $pObj->siteScript;
		$encoded = addslashes($encoded);
		// TODO mysql injection

		// no extbase due to performance
		$path = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'decoded', // select
			'tx_spurl_domain_model_path', // table
			'encoded = "' . $requestedUri . '"',
			'', // groupBy
			'', // orderBy
			'', // limit
			'' // uidIndexField
		);

		if ( isset($path[0]) ) {
			$decodedParams = json_encode( $path['decoded'] );
			$pObj->id = $decodedParams['id'];
			$pObj->mergingWithGetVars($decodedParams);
		} else {
			// TODO 404
			// var_dump('set to 4');
			// $pObj->id = 4;
			// $pObj->mergingWithGetVars(['L' => 0]);
		}
	}
}
?>