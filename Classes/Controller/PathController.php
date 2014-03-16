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
	 * pathRepository
	 *
	 * @var \Rattazonk\Spurl\Domain\Repository\PathRepository
	 * @inject
	 */
	protected $pathRepository;

	/**
	 * @var array<\Rattazonk\Spurl\Domain\Utility\Translator\TranslatorInterface>
	 */
	protected $translators = [];

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
	 * @inject
	 */
	protected $persistenceManager;

	/**
	 * get the decoded combinations via the indexer
	 * encodes them
	 * and caches them
	 */

	/**
	 * returns the encoded url and caches the result - overrides in db!
	 *
	 * @param string $url
	 * @return string $url
	 */
	public function encodeAction($url) {
		$this->initializeTranslators();
		$path = $this->objectManager->get('\Rattazonk\Spurl\Domain\Model\Path');

		// removes the query string and sets the decoded
		$path->initDecoded($url);
		// TODO gedanken zum cache. Wie abspeichern?
		foreach ($this->translators as $translator) {
			// manipulates the encoded in the path and the usedDecoded
			$translator->encode($path);
		}

		$url = $path->getEncoded();
		if( !isset($this->settings['urlLowerCase']) || (isset($this->settings['urlLowerCase']) && $this->settings['urlLowerCase']) ){
			$url = strtolower($url);
		}
		\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($url);

		$this->cachePath($path);
		die('encodeAction');
		return $path->getEncoded();
	}

	protected function initializeTranslators() {
		foreach ($this->settings['translators'] as $translatorPosition => $translator) {
			if (!isset($translator['class'])) {
				throw new \Exception('Every translator definition in the settings must has a class attribute. ');
			}

			if (isset($translator['settings']['instances'])) {
				foreach ($translator['settings']['instances'] as $instancePosition => $instanceSettings) {
					$instance = $this->objectManager->get($translator['class']);
					$instance->setSettings((array) $instanceSettings);
					$this->translators[$translatorPosition . $instancePosition] = $instance;
				}
			}
		}
	}

	protected function encodeFromCache($url) {

	}

	/**
	 * saves the path in the cache
	 * paths must be unique in both ways!
	 */
	protected function cachePath($path) {
		// TODO what to do with persisted
		if ( $this->persistenceManager->isNewObject($path) ) {
			$this->pathRepository->add($path);
		} else {
			$this->pathRepository->update($path);
		}
	}
}
?>