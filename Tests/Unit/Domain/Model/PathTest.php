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
class PathTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {
	/**
	 * @var \Rattazonk\Spurl\Domain\Model\Path
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = $this->objectManager->get('\Rattazonk\Spurl\Domain\Model\Path');
	}

	public function tearDown() {
		unset($this->fixture);
	}

	// KANN ICH TESTEN, OB DER OBJECTMANAGER VORHANDEN IST?

	/**
	 * should extract the get params and the path parts from the given url
	 *
	 * @test
	 */
	public function setEncodedUrl() {
		$this->fixture->setEncodedUrl('http://rattazonk.de/blog/?tx_blogit_posts%5Bpost%5D=1&tx_blogit_posts%5Baction%5D=show');
		$this->assertEquals([
				'tx_blogit_posts' => [
					'post' => '1',
					'action' => 'show'
				]
			],
			$this->fixture->getInitParams()
		);
		$this->assertEquals(
			['rattazonk.de', 'blog'],
			$this->fixture->getInitPathParts()
		);

		$this->fixture->setEncodedUrl('http://rattazonk.com/foo/bar/');
		$this->assertEquals( [], $this->fixture->getInitParams() );
		$this->assertEquals(
			['rattazonk.com', 'foo', 'bar'],
			$this->fixture->getInitPathParts()
		);
	}

	/**
	 * TODO What is about the dependency on the dictionary Translator
	 * @test
	 */
	public function initDictionaryTranslators() {
		$this->fixture->initTranslators([
			10 => [
				'class' => '\Rattazonk\Spurl\Domain\Translator\DictionaryTranslator',
				'settings' => ['foo' => 'bar']
			],
			20 => [
				'class' => '\Rattazonk\Spurl\Domain\Translator\DictionaryTranslator',
				'settings' => ['bla' => 'blupp']
			]
		]);

		$translators = $this->fixture->getTranslators();
		$this->assertCount(2, $translators);
		$this->assertContainsOnlyInstancesOf('\Rattazonk\Spurl\Domain\Translator\DictionaryTranslator', $translators);

		$this->assertEquals(['foo' => 'bar'], $translators[10]->getSettings());
		$this->assertSame($this->fixture, $translators[10]->getPath());
		$this->assertEquals(['bla' => 'blupp'], $translators[20]->getSettings());
		$this->assertSame($this->fixture, $translators[20]->getPath());

		return $this->fixture;
		// TODO exceptions for api protection
	}

	/**
	 * @test
	 */
	public function initDictionaryTranslatorsApiProtection() {
		$this->markTestIncomplete('This test has not been implemented yet');
	}

	/**
	 * @test
	 * @depends initDictionaryTranslators
	 */
	public function initDictionaryTranslatorsMultiple($fixture) {
		$fixture->initTranslators([
			5 => [
				'class' => '\Rattazonk\Spurl\Domain\Translator\DictionaryTranslator',
				'settings' => ['foo2' => 'bar2']
			],
			20 => [
				'class' => '\Rattazonk\Spurl\Domain\Translator\DictionaryTranslator',
				'settings' => ['bla2' => 'blupp2']
			]
		]);

		$translators = $fixture->getTranslators();
		$this->assertCount(3, $translators);
		$this->assertContainsOnlyInstancesOf('\Rattazonk\Spurl\Domain\Translator\DictionaryTranslator', $translators);

		$this->assertEquals(['foo2' => 'bar2'], $translators[5]->getSettings());
		$this->assertEquals(['foo' => 'bar'], $translators[10]->getSettings());
		$this->assertEquals(['bla2' => 'blupp2'], $translators[20]->getSettings());

		return $fixture;
	}

	/**
	 * @test
	 * @depends initDictionaryTranslatorsMultiple
	 */
	public function translatorPointer($fixture) {
		$fixture->resetTranslatorPointer();
		$this->assertInstanceOf('\Rattazonk\Spurl\Domain\Translator\DictionaryTranslator', $fixture->getCurrentTranslator());
		$this->assertTrue($fixture->nextTranslator(), 'The second should be filled. ');
		$this->assertInstanceOf('\Rattazonk\Spurl\Domain\Translator\DictionaryTranslator', $fixture->getCurrentTranslator());
		$this->assertTrue($fixture->nextTranslator(), 'The third should be filled. ');
		$this->assertInstanceOf('\Rattazonk\Spurl\Domain\Translator\DictionaryTranslator', $fixture->getCurrentTranslator());
		$this->assertFalse($fixture->nextTranslator(), 'There should be no fourth. ');

		return $fixture;
	}

	/**
	 * @test
	 * @depends translatorPointer
	 */
	public function setTranslatorPointerToNegativeOne($fixture) {
		$fixture->setTranslatorPointerToNegativeOne();
		$this->assertTrue($fixture->nextTranslator(), 'The first should be filled. ');
		$this->assertInstanceOf('\Rattazonk\Spurl\Domain\Translator\DictionaryTranslator', $fixture->getCurrentTranslator());
		$this->assertTrue($fixture->nextTranslator(), 'The second should be filled. ');
		$this->assertInstanceOf('\Rattazonk\Spurl\Domain\Translator\DictionaryTranslator', $fixture->getCurrentTranslator());
		$this->assertTrue($fixture->nextTranslator(), 'The third should be filled. ');
		$this->assertInstanceOf('\Rattazonk\Spurl\Domain\Translator\DictionaryTranslator', $fixture->getCurrentTranslator());
		$this->assertFalse($fixture->nextTranslator(), 'There should be no fourth. ');
	}
}
?>