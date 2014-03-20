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
	 * the encoded url with the query string (not encodable get params)
	 * @var string
	 */
	protected $encoded = '';

	/**
	 * decoded and not decoded get params
	 * @var array
	 */
	protected $getParams = [];


	/**
	 * @var string
	 * @return void
	 */
	public function setEncoded($encoded) {
		$this->encoded = $encoded;
	}

	/**
	 * @return string
	 */
	public function getEncoded() {
		return $this->encoded;
	}

	/**
	 * @param string $toAdd
	 * @return void
	 */
	public function addEncoded($toAdd) {
		if( strlen($toAdd) ){
			$this->encoded .= strlen($this->encoded) ? '/' : '';
			$this->encoded .= trim($toAdd, '/');
		}
	}

	/**
	 * @var string
	 * @return void
	 */
	public function setGetParams($getParams) {
		parse_str($getParams, $this->getParams);
	}

	/**
	 * @return string
	 */
	public function getGetParams() {
		return http_build_query($this->getSortedGetParams());
	}

	/**
	 * @return array
	 */
	protected function getSortedGetParams() {
		return ksort($this->getParams);
	}

	/**
	 * sets the encoded and get params from the string
	 * The encoded will be edited by encoding the path in the translators
	 * The get params by decoding. 
	 *
	 * @param string
	 * @return void
	 */
	public function initFromUrl($url) {
		$url = preg_replace('/^http(s)?:\/\//', '', $url);

		$queryString = (string) parse_url($url, PHP_URL_QUERY);
		$this->encoded = ltrim( str_replace($paramQuery, '', $url), '?' );
		$this->setGetParams($queryString);
	}

	public function initEncode() {
		// clear the encoded, because all informations has to be present in the GET params. 
		$this->encoded = '';
	}
}
?>