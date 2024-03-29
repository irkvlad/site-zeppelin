<?php
/************************************************************************************
 mod_allnews (up to v2.0) for Joomla v1.5.0 by Thierry S,                          
 mod_allnews (from 2.0 up to now) for Joomla v1.5 by Olinad       				    
                                                                                 
 @author: Thierry S.                                                              
 @author: Olinad - dan@cdh.it   

This file is a modification of standard Joomla hidden parameter.
Original file's copyright: 
 @version		$Id:hidden.php 6961 2007-03-15 16:06:53Z tcp $
 @package		Joomla.Framework
 @subpackage	Parameter
 @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 @license		GNU/GPL
 Joomla! is free software. This version may have been modified pursuant
 to the GNU General Public License, and as distributed it includes or
 is derivative of works licensed under the GNU General Public License or
 other free or open source software licenses.

 ----- This file is part of the AllNews Module -----

    AllNews Module is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    AllNews is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this module.  If not, see <http://www.gnu.org/licenses/>.
************************************************************************************/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a hidden element
 *
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

class JElementflexicheck extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'flexicheck';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$filename = 'components/com_flexicontent/index.html';
			if (file_exists($filename)) {
				echo '<div style="text-align: center; font-weight: bold;">FLEXIcontent настройки получены</div>';
			} else {
				echo '<div style="text-align: center; font-weight: bold;">FLEXICONTENT не установлен<br/>Поля FLEXICONTENT скрыты в настройках модуля<br/>FLEXICONTENT можно бесплатно скачать <a href="http://www.flexicontent.org/">ЗДЕСЬ</a></div>';
			} 
	}

	function fetchTooltip($label, $description, &$xmlElement, $control_name='', $name='') {
		return false;
	}
}
