<?php
namespace Rattzonk\Spurl\Controller;

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
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$paths = $this->pathRepository->findAll();
		$this->view->assign('paths', $paths);
	}

	/**
	 * action new
	 *
	 * @param \Rattzonk\Spurl\Domain\Model\Path $newPath
	 * @dontvalidate $newPath
	 * @return void
	 */
	public function newAction(\Rattzonk\Spurl\Domain\Model\Path $newPath = NULL) {
		$this->view->assign('newPath', $newPath);
	}

	/**
	 * action create
	 *
	 * @param \Rattzonk\Spurl\Domain\Model\Path $newPath
	 * @return void
	 */
	public function createAction(\Rattzonk\Spurl\Domain\Model\Path $newPath) {
		$this->pathRepository->add($newPath);
		$this->flashMessageContainer->add('Your new Path was created.');
		$this->redirect('list');
	}

	/**
	 * action edit
	 *
	 * @param \Rattzonk\Spurl\Domain\Model\Path $path
	 * @return void
	 */
	public function editAction(\Rattzonk\Spurl\Domain\Model\Path $path) {
		$this->view->assign('path', $path);
	}

	/**
	 * action update
	 *
	 * @param \Rattzonk\Spurl\Domain\Model\Path $path
	 * @return void
	 */
	public function updateAction(\Rattzonk\Spurl\Domain\Model\Path $path) {
		$this->pathRepository->update($path);
		$this->flashMessageContainer->add('Your Path was updated.');
		$this->redirect('list');
	}

}
?>