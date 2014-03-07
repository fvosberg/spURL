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
 * Test case for class \Rattazonk\Spurl\Domain\Model\Path.
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
class DictionaryTranslatorTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {
	/**
	 * @var \Rattazonk\Spurl\Domain\Translator\DictionaryTranslator
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = $this->objectManager->get('\Rattazonk\Spurl\Domain\Translator\ModelTranslator');
		$this->fixture->setSettings([
			'dict' => [
				10 => [
                    'identifier' => '',
                    'model' => '\Rattazonk\Spurl\Domain\Model\Page',
                    'repository' => '\Rattazonk\Spurl\Domain\Repository\PageRepository',

                    'encoded' => [
                    	'parts' => [
	                        10 => [
	                        	'_typoScriptNodeValue' => 'parentPage',
	                            'type' => getter,
	                            'hierarchical' => 1,
	                            'parent' => parentPage,
	                            'parentAttribute' => title
	                        ],

	                        20 => [
	                        	'_typoScriptNodeValue' => 'title',
	                        	'type' => 'getter'
	                        ]
	                    ],
	                    'format' => "%s/%s"
                    ],

                    'decoded' => [
                    	'uid' => [
                    		'_typoScriptNodeValue' => 'uid',
                    		'type' => 'getter'
                    	]
                    ]
                }
			]
		]);
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function decode() {
		$path = $this->getMock('\Rattazonk\Spurl\Domain\Model\Path');
		$path->expects($this->any())
			->method('getNotProcessedPathParts')
			->will($this->returnValue([
				'blog',
				'dontTranslate' // because blog could not be decoded
			]));
		$path->expects($this->once())
			->method('addProcessedPathPart')
			->with($this->equalTo('blog'));

		$this->fixture->setPath($path);
		$this->fixture->decode();
		$this->assertEquals(['uid' => 1337], $this->fixture->getDecodedParams());
	}
}
?>