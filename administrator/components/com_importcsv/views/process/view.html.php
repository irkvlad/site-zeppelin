<?php
/**
* @version		$Id: view.html.php
* @package		Joomla
* @subpackage	Import CSV
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for Import CSV component
 *
 * @static
 * @package		Joomla
 * @subpackage	Import CSV
 * @since 1.5
 */
class ImportCSVViewProcess extends JView
{
	function display($tpl = null)
	{
		global $mainframe;
		$config =& JComponentHelper::getParams('com_importcsv');

		// Do not allow cache
		JResponse::allowCache(false);

		JHTML::_('behavior.mootools');
		$document =& JFactory::getDocument();
		parent::display($tpl);
	}
}
