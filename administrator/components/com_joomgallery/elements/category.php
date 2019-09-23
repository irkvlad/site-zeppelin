<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/elements/category.php $
// $Id: category.php 3092 2011-05-20 09:56:58Z aha $
/****************************************************************************************\
**   JoomGallery  1.5.7                                                                 **
**   By: JoomGallery::ProjectTeam                                                       **
**   Copyright (C) 2008 - 2011  JoomGallery::ProjectTeam                                **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                            **
**   Released under GNU GPL Public License                                              **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look                       **
**   at administrator/components/com_joomgallery/LICENSE.TXT                            **
\****************************************************************************************/

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

/**
 * Renders a category list element
 *
 * @package     JoomGallery
 * @subpackage  Parameter
 * @since       1.5.5
 */
class JElementCategory extends JElement
{
  /**
   * Element name
   *
   * @access  protected
   * @var     string
   */
  var $_name = 'Category';

  function fetchElement($name, $value, &$node, $control_name)
  {
    require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomgallery'.DS.'includes'.DS.'defines.php';
    JLoader::register('JoomExtensions', JPATH_ADMINISTRATOR.DS.'components'.DS._JOOM_OPTION.DS.'helpers'.DS.'extensions.php');
    JLoader::register('JoomHelper',     JPATH_BASE.DS.'components'.DS._JOOM_OPTION.DS.'helpers'.DS.'helper.php');
    JLoader::register('JoomConfig',     JPATH_BASE.DS.'components'.DS._JOOM_OPTION.DS.'helpers'.DS.'config.php');
    JLoader::register('JoomAmbit',      JPATH_BASE.DS.'components'.DS._JOOM_OPTION.DS.'helpers'.DS.'ambit.php');
    JTable::addIncludePath(             JPATH_ADMINISTRATOR.DS.'components'.DS._JOOM_OPTION.DS.'tables');
    JHTML::addIncludePath(JPATH_BASE.DS.'components'.DS._JOOM_OPTION.DS.'helpers'.DS.'html');

    $html = JHTML::_('joomselect.categorylist', $value, $control_name.'['.$name.']');

    return $html;
  }
}