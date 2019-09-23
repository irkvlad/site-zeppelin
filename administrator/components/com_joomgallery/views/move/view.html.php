<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/views/move/view.html.php $
// $Id: view.html.php 3092 2011-05-20 09:56:58Z aha $
/******************************************************************************\
**   JoomGallery  1.5.7                                                       **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2011  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

/**
 * HTML View class for the move view
 *
 * @package JoomGallery
 * @since   1.5.5
 */
class JoomGalleryViewMove extends JoomGalleryView
{
  /**
   * HTML view display method
   *
   * @access  public
   * @param   string  $tpl  The name of the template file to parse
   * @return  void
   * @since   1.5.5
   */
  function display($tpl = null)
  {
    JToolBarHelper::title(JText::_('JGA_IMGMAN_IMAGE_MANAGER').' :: '.JText::_('JGA_IMGMAN_MOVE_IMAGE'));
    JToolbarHelper::save('move', 'JGA_COMMON_TOOLBAR_SAVE');
    JToolbarHelper::cancel('cancel', 'JGA_COMMON_TOOLBAR_CANCEL');
    //JToolbarHelper::spacer();
    //JToolbarHelper::custom('cpanel', 'config.png', 'config.png', 'JGA_COMMON_TOOLBAR_CPANEL', false);
    JToolbarHelper::spacer();

    $catid = $this->_mainframe->getUserStateFromRequest('joom.move.catid', 'catid', 0, 'int');
    $items = $this->get('Images');
    $lists = array();
    $lists['cats'] = JHTML::_('joomselect.categorylist', $catid, 'catid', 'class="inputbox" size="1" ');
    $this->assignRef('items',     $items);
    $this->assignRef('lists',     $lists);

    parent::display($tpl);
  }
}
