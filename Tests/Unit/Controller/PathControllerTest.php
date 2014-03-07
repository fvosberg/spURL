<?php
namespace Rattazonk\Spurl\Tests;
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
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class Tx_Spurl_Controller_PathController.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage spURL
 *
 * @author Frederik Vosberg <frederik.vosberg@rattazonk.de>
 */
class PathControllerTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {
	/**
	 * @var \Rattazonk\Spurl\Domain\Model\Path
	 */
	protected $path;

	/**
	 * @var \Rattazonk\Spurl\Domain\Controller\PathController
	 */
	protected $pathController;

	public function setUp() {
		$this->path = $this->objectManager->get('\Rattazonk\Spurl\Domain\Model\Path');
		$this->pathController = $this->objectManager->get('\Rattazonk\Spurl\Controller\PathController');
	}

	public function tearDown() {
		unset($this->path);
		unset($this->pathController);
	}

	/**
	 * @test
	 */
	public function encodeAction() {
		// integration test?
		// $this->setProtectedProperty(
		// 	$this->pathController,
		// 	'settings',
		// 	[
		// 		'translators' => [
		// 			10 => [
		// 				'class' => '\Rattazonk\Spurl\Domain\Translator\DictionaryTranslator',
		// 				'instances' => [
		// 					10 => [
		// 						'encoded' => 'rattazonk.com',
		// 						'decoded' => [
		// 							'L' => 0
		// 						]
		// 					],
		// 					20 => [
		// 						'onlyDecode' => 1,
		// 						'encoded' => 'www.rattazonk.com',
		// 						'decoded' => [
		// 							'L' => 0
		// 						]
		// 					],
		// 					30 => [
		// 						'encoded' => 'rattazonk.de',
		// 						'decoded' => [
		// 							'L' => 1
		// 						]
		// 					],
		// 					40 => [
		// 						'onlyDecode' => 1,
		// 						'encoded' => 'www.rattazonk.de',
		// 						'decoded' => [
		// 							'L' => 1
		// 						]
		// 					]
		// 				]
		// 			]
		// 		]
		// 	]
		// );
	}

	protected function setProtectedProperty($instance, $propertyName, $value) {
		$reflection = new ReflectionClass($instance);
		$reflectionProperty = $reflection->getProperty($propertyName);
		$reflectionProperty->setAccessible(true);

		$reflection_property->setValue($instance, $value);
	}

}
?>