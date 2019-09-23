<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/elements/owner.php $
// $Id: owner.php 3092 2011-05-20 09:56:58Z aha $
/****************************************************************************************\
**   JoomGallery  1.5.7                                                                 **
**   By: JoomGallery::ProjectTeam                                                       **
**   Copyright (C) 2008 - 2011  JoomGallery::ProjectTeam                                **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                            **
**   Released under GNU GPL Public License                                              **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look                       **
**   at administrator/components/com_joomgallery/LICENSE.TXT                            **
\****************************************************************************************/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Renders an owner list element
 *
 * @package JoomGallery
 * @since   1.5.5
 */
class JElementOwner extends JElement
{
  /**
   * Element name
   *
   * @access  protected
   * @var     string
   */
  var  $_name = 'Owner';

  function fetchElement($name, $value, &$node, $control_name)
  {
    return JHTML::_('joomselect.users', $control_name.'['.$name.']', $value, true, array(), null, false, 0);
  }
}