<?php
/**
* JoomGallery Latest categories
* Copyright (C) 2009 Erftralle
* file: mod_jglatestcat/mod_jglatestcat.php
* version: 1.5.1
* contact:
* license http://www.gnu.org/copyleft/gpl.html GNU/GPL or have a look at mod_jglatestcat/LICENSE.TXT
*
* This program is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the Free Software
* Foundation; either version 2 of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along with this program;
* if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$jg_installed = null;

if( file_exists( JPATH_ROOT.DS.'components'.DS.'com_joomgallery'.DS.'classes'.DS.'interface.class.php' ) ) {
 
  // JoomGallery seems to be installed
  $jg_installed = true;
 
  // include JoomGallery interface class
  require_once(JPATH_ROOT.DS.'components'.DS.'com_joomgallery'.DS.'classes'.DS.'interface.class.php');

  // include syndicate functions only once
  require_once( dirname(__FILE__).DS.'helper.php' );

  //id of actual module instance
  $moduleid = $module->id;

  // create an instance of the helper object
  $jgcat_obj = new modJgLatestCatHelper();

  // get the newest categories from JoomGallery
  $jgcat_rows = $jgcat_obj->fillObject( $params, $dberror, $moduleid );
}

// show the latest categories
require( JModuleHelper::getLayoutPath( 'mod_jglatestcat' ) );
?>
