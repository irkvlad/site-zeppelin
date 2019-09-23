<?php
/**
 * @version		$Id: controller.php 10381 2008-06-01 03:35:53Z pasamio $
 * @package		Joomla
 * @subpackage	ImportCSV
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

/**
 * Import CSV Component Controller
 *
 * @package		Joomla
 * @subpackage	ImportCSV
 * @version 1.5
 */
class ImportCSVController extends JController
{
	/**
	 * Display the view
	 */
	function display()
	{
		global $mainframe;
		$document = &JFactory::getDocument();
		$vType	= $document->getType();
		// Get/Create the view
		$viewToUse=JRequest::getCmd( 'view', 'process' );
		$view = &$this->getView($viewToUse, $vType);
		// Display the view
		$view->display();
	}
}
