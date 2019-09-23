<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/Module/JoomTreeview/trunk/mod_jgtreeview.php $
// $Id: mod_jgtreeview.php 2902 2011-03-13 14:47:32Z erftralle $
/**
* Module JoomGallery Treeview
* by JoomGallery::Project Team
* @package JoomGallery
* @copyright JoomGallery::Project Team
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
* This program is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the Free Software
* Foundation, either version 2 of the License, or (at your option) any later
* version.
*
* This program is distributed in the hope that it will be useful, but WITHOUT
* ANY WARRANTY, without even the implied warranty of MERCHANTABILITY or FITNESS
* FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along with
* this program; if not, write to the Free Software Foundation, Inc.,
* 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$jg_installed = false;

if(file_exists(JPATH_ROOT . DS . 'components' . DS . 'com_joomgallery' . DS . 'interface.php'))
{
  // include JoomGallery's interface class
  require_once(JPATH_ROOT . DS . 'components' . DS . 'com_joomgallery' . DS . 'interface.php');

  // include helper functions only once
  require_once(dirname(__FILE__). DS . 'helper.php');

  // create an instance of the helper object
  $jgTreeviewHelper = new modJgTreeViewHelper();

  // check gallery version
  if($jgTreeviewHelper->getGalleryVersion() >= '1.5.7')
  {
    // correct version of JoomGallery seems to be installed
    $jg_installed = true;

    // get all categories from JoomGallery
    $jgcat_rows = $jgTreeviewHelper->fillObject($params, $dberror, $module->id);
  }
}
// show JoomGallery's category tree
require(JModuleHelper::getLayoutPath('mod_jgtreeview'));