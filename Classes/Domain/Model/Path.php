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
	 * decoded get params
	 *
	 * @var \array
	 * @validate NotEmpty
	 */
	protected $decoded;

	/**
	 * usedDecoded params
	 *
	 * @var \array
	 */
	protected $usedDecoded = [];

	/**
	 * encoded path parts
	 *
	 * @var \array
	 */
	protected $encodedParts = [];

	/**
	 * Returns the decoded
	 *
	 * @return \array $decoded
	 */
	public function getDecoded() {
		return $this->decoded;
	}

	/**
	 * inits the decoded
	 * @param string $url
	 */
	public function initDecoded($url) {
		parse_str( parse_url($url, PHP_URL_QUERY), $this->decoded );
	}

	/**
	 * @return array
	 */
	public function getUnUsedDecoded() {
		return $this->arrayRecursiveDiff($this->decoded, $this->usedDecoded);
	}

	protected function arrayRecursiveDiff($aArray1, $aArray2) {
		$diff = array();

		foreach ($aArray1 as $mKey => $mValue) {
			if (array_key_exists($mKey, $aArray2)) {
				if (is_array($mValue)) {
					$aRecursiveDiff = arrayRecursiveDiff($mValue, $aArray2[$mKey]);
					if (count($aRecursiveDiff)) { $diff[$mKey] = $aRecursiveDiff; }
				} else {
					if ($mValue != $aArray2[$mKey]) {
					  $diff[$mKey] = $mValue;
					}
				}
			} else {
				$diff[$mKey] = $mValue;
			}
		}
		return $diff;
	}

	/**
	 * @param array $usedDecoded
	 */
	public function addUsedDecoded($usedDecoded) {
		// dont think this will work properly, dont know exactly why
		$this->usedDecoded = array_merge_recursive($this->usedDecoded, $usedDecoded);
	}

	/**
	 * @param array $encoded
	 */
	public function addEncodedParts($encodedParts) {
		$this->encodedParts = array_merge($this->encodedParts, $encodedParts);
	}

	/**
	 * @return string
	 */
	public function getEncoded() {
		// TODO trimimplode
		return implode('/', $this->encodedParts) . '?' . http_build_query($this->getUnUsedDecoded());
	}
}
?>