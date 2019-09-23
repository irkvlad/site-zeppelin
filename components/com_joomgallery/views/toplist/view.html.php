<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/views/toplist/view.html.php $
// $Id: view.html.php 3092 2011-05-20 09:56:58Z aha $
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
 * HTML View class for the toplist view
 *
 * @package JoomGallery
 * @since   1.5.5
 */
class JoomGalleryViewToplist extends JoomGalleryView
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
    $params           = &$this->_mainframe->getParams();

    // Header and footer
    JoomHelper::prepareParams($params);

    $backtarget = JRoute::_('index.php?view=gallery'); //see above
    $backtext   = JText::_('JGS_COMMON_BACK_TO_GALLERY');

    // Get number of images and hits in gallery
    $numbers  = JoomHelper::getNumberOfImgHits();

    if(!$params->get('page_title'))
    {
      $params->set('page_title', JText::_('JGS_COMMON_GALLERY'));
    }

    // Load modules at position 'top'
    $modules['top'] = JoomHelper::getRenderedModules('top');
    if(count($modules['top']))
    {
      $params->set('show_top_modules', 1);
    }
    // Load modules at position 'btm'
    $modules['btm'] = JoomHelper::getRenderedModules('btm');
    if(count($modules['btm']))
    {
      $params->set('show_btm_modules', 1);
    }

    $type = JRequest::getCmd('type');
    switch($type)
    {
      case 'lastcommented':
        $rows     = &$this->get('LastCommented');
        $title    = JText::sprintf('JGS_TOPLIST_LAST_COMMENTED_IMAGE', $this->_config->get('jg_toplist'));
        $pathway  = $title;
        break;
      case 'lastadded':
        $rows     = &$this->get('LastAdded');
        $title    = JText::sprintf('JGS_TOPLIST_LAST_ADDED_IMAGE', $this->_config->get('jg_toplist'));
        $pathway  = $title;
        break;
      case 'toprated':
        $rows     = &$this->get('TopRated');
        $title    = JText::sprintf('JGS_TOPLIST_BEST_RATED_IMAGE', $this->_config->get('jg_toplist'));
        $pathway  = $title;
        break;
      default:
        $rows     = &$this->get('MostViewed');
        $title    = JText::sprintf('JGS_TOPLIST_MOST_VIEWED_IMAGE', $this->_config->get('jg_toplist'));
        $pathway  = $title;
        break;
    }

    // Breadcrumbs
    if($this->_config->get('jg_completebreadcrumbs'))
    {
      $breadcrumbs  = &$this->_mainframe->getPathway();
      $breadcrumbs->addItem($title);
    }

    // Check whether the (comments) data rows where delivered by a plugin
    if(isset($rows[0]->delivered_by_plugin) && $rows[0]->delivered_by_plugin)
    {
      $params->set('delivered_by_plugin', 1);
    }

    foreach($rows as $key => $row)
    {
      $rows[$key]->link = JHTML::_('joomgallery.openimage', $this->_config->get('jg_detailpic_open'), $row);

      $cropx    = null;
      $cropy    = null;
      $croppos  = null;
      if($this->_config->get('jg_dyncrop'))
      {
        $cropx    = $this->_config->get('jg_dyncropwidth');
        $cropy    = $this->_config->get('jg_dyncropheight');
        $croppos  = $this->_config->get('jg_dyncropposition');
      }
      $rows[$key]->thumb_src = $this->_ambit->getImg('thumb_url', $row, null, 0, true, $cropx, $cropy, $croppos);

      if($this->_config->get('jg_showauthor'))
      {
        if($row->imgauthor)
        {
          $rows[$key]->authorowner = $row->imgauthor;
        }
        else
        {
          if($this->_config->get('jg_showowner'))
          {
            $rows[$key]->authorowner = JHTML::_('joomgallery.displayname', $row->owner);
          }
          else
          {
            $rows[$key]->authorowner = JText::_('JGS_COMMON_NO_DATA');
          }
        }
      }

      if(!$params->get('delivered_by_plugin'))
      {
        if($type == 'lastcommented' && $this->_config->get('jg_showthiscomment'))
        {
          if($row->userid)
          {
            $rows[$key]->cmtname = JHTML::_('joomgallery.displayname', $row->userid, false);
          }

          $cmttext = $row->cmttext;
          $cmttext = JoomHelper::processText($cmttext);
          if($this->_config->get('jg_bbcodesupport'))
          {
            $cmttext = JHTML::_('joomgallery.bbdecode', $cmttext);
          }
          if($this->_config->get('jg_smiliesupport'))
          {
            $smileys = JoomHelper::getSmileys();
            foreach($smileys as $i => $sm)
            {
              $cmttext = str_replace($i, '<img src="'.$sm.'" border="0" alt="'.$i.'" title="'.$i.'" />', $cmttext);
            }
          }
          $cmttext = stripslashes($cmttext);

          $rows[$key]->processed_cmttext = $cmttext;
        }
      }

      // Show editor links for that image
      $rows[$key]->show_editor_icons = false;
      if(   $this->_config->get('jg_showtoplisteditorlinks') == 1
         && $this->_config->get('jg_userspace') == 1
         && ($this->_config->get('jg_showuserpanel') != 2 || $this->_user->get('aid') == 2)
         && (   ($this->_user->get('gid') > 23 && $this->_config->get('jg_showallpicstoadmin') == 1)
             || ($rows[$key]->owner && $rows[$key]->owner == $this->_user->get('id'))
            )
        )
      {
        $rows[$key]->show_editor_icons = true;
      }

      // Populate additional parameters
      $row->params = new JParameter($row->params);
    }

    // Download Icon
    if(   (($this->_config->get('jg_showtoplistdownload') == 1) && ($this->_user->get('aid') >= 1))
       || (($this->_config->get('jg_showtoplistdownload') == 2) && ($this->_user->get('aid') == 2))
       ||  ($this->_config->get('jg_showtoplistdownload') == 3)
      )
    {
      $params->set('show_download_icon', 1);
    }
    else
    {
      if(($this->_config->get('jg_showtoplistdownload') == 1) && ($this->_user->get('aid') < 1))
      {
        $params->set('show_download_icon', -1);
      }
    }

    // Favourites icon
    if($this->_config->get('jg_favourites') && $this->_config->get('jg_showtoplistfavourite'))
    {
      if(   ($this->_config->get('jg_showdetailfavourite') == 0 && $this->_user->get('aid') >= 1)
         || ($this->_config->get('jg_showdetailfavourite') == 1 && $this->_user->get('aid') == 2)
         || ($this->_config->get('jg_usefavouritesforpubliczip') == 1 && $this->_user->get('aid') < 1)
        )
      {
        if(    $this->_config->get('jg_usefavouritesforzip')
           || ($this->_config->get('jg_usefavouritesforpubliczip') && $this->_user->get('aid') < 1)
          )
        {
          $params->set('show_favourites_icon', 2);
        }
        else
        {
          $params->set('show_favourites_icon', 1);
        }
      }
      else
      {
        if(($this->_config->get('jg_favouritesshownotauth') == 1))
        {
          if($this->_config->get('jg_usefavouritesforzip'))
          {
            $params->set('show_favourites_icon', -2);
          }
          else
          {
            $params->set('show_favourites_icon', -1);
          }
        }
      }
    }

    // Report icon
    if($this->_config->get('jg_toplist_report_images'))
    {
      if($this->_user->get('id') || $this->_config->get('jg_toplist_report_images') == 2)
      {
        $params->set('show_report_icon', 1);

        JHTML::_('behavior.modal');
      }
      else
      {
        if($this->_config->get('jg_report_images_notauth'))
        {
          $params->set('show_report_icon', -1);
        }
      }
    }

    // Set redirect url used in editor links to redirect back to favourites view after edit/delete
    $redirect = '&redirect='.base64_encode(JFactory::getURI()->toString());

    $this->assignRef('params',          $params);
    $this->assignRef('rows',            $rows);
    $this->assignRef('title',           $title);
    $this->assignRef('type',            $type);
    $this->assignRef('pathway',         $pathway);
    $this->assignRef('modules',         $modules);
    $this->assignRef('backtarget',      $backtarget);
    $this->assignRef('backtext',        $backtext);
    $this->assignRef('numberofpics',    $numbers[0]);
    $this->assignRef('numberofhits',    $numbers[1]);
    $this->assignRef('redirect',          $redirect);

    parent::display($tpl);
  }
}