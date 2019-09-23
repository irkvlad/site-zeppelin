<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/Plugins/JoomShadowbox/joomshadowbox.php $
// $Id: joomshadowbox.php 1961 2010-03-18 13:38:38Z chraneco $
/****************************************************************************************\
**   Plugin 'JoomMultiSlimbox' 1.5                                                         **
**   By: JoomGallery::ProjectTeam                                                       **
**   Copyright (C) 2010 - 2010 Patrick Alt                                              **
**   Released under GNU GPL Public License                                              **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look                       **
**   at administrator/components/com_joomgallery/LICENSE.TXT                            **
\****************************************************************************************/

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

jimport('joomla.plugin.plugin');

/**
 * JoomGallery MultiSlimbox Plugin
 *
 * With this plugin JoomGallery is able to use Slimbox
 * (http://www.digitalia.be/software/slimbox for displaying images.
 * Version 1.5.8 with mootools 1.11
 * Version 1.7.1 with mootools 1.7
 * Version 2.0.4 with jquery
 *
 * @package     Joomla
 * @subpackage  JoomGallery
 * @since       1.5
 */
class plgJoomGalleryJoomMultiSlimbox extends JPlugin
{
  /**
   * Constructor
   *
   * For php4 compatability we must not use the __constructor as a constructor for plugins
   * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
   * This causes problems with cross-referencing necessary for the observer design pattern.
   *
   * @access  protected
   * @param   object    $subject  The object to observe
   * @param   object    $params   The object that holds the plugin parameters
   * @return  void
   * @since   1.5
   */
  function plgJoomGalleryJoomMultiSlimbox(&$subject, $params)
  {
    parent::__construct($subject, $params);
  }

  /**
   * OnJoomOpenImage method
   *
   * Method is called after an image of JoomGallery shall be opened.
   * It modifies the given link in order to use Shadowbox for opening the image.
   *
   * @access  public
   * @param   string  $link     The link to modify
   * @param   object  $image    An object holding the image data
   * @param   string  $img_url  The URL to the image which shall be openend
   * @param   string  $group    The name of an image group, RokBox will make an album out of the images of a group
   * @param   string  $type     'orig' for original image, 'img' for detail image or 'thumb' for thumbnail
   * @return  void
   * @since   1.5
   */
  function onJoomOpenImage(&$link, $image = null, $img_url = null, $group = 'joomgallery', $type = 'orig')
  {
    static $loaded = false;

    if($image)
    {
      if(!$loaded)
      {
        // Get version
        // 0 = 1.58
        // 1 = 1.71
        // 2 = 2.04
        $sbversion      = $this->params->get('slimbox');
        $sbreplmootools = $this->params->get('replace_mootools');
        $sbresizeimage  = $this->params->get('resizeimage');
        $sbresizespeed  = $this->params->get('resizespeed');
        $sbglobexistent = $this->params->get('global_slimbox_existent');

        // Check for correct value
        if($sbresizespeed > 10)
        {
          $sbresizespeed = 10;
        }
        if($sbresizespeed < 1)
        {
          $sbresizespeed = 1;
        }
        // Define language vars
        $script = '    var resizeJsImage = '.$sbresizeimage.';
  var resizeSpeed = '.$sbresizespeed.';
  var joomgallery_image = "'.JText::_('JGS_COMMON_IMAGE', true).'";
  var joomgallery_of = "'.JText::_('JGS_POPUP_OF', true).'";';

        $doc = & JFactory::getDocument();

        switch($sbversion)
        {
          // 1.71, need the mootools upgrade plugin enabled
          case 1:
            if(!$sbglobexistent)
            {
              $doc->addScriptDeclaration($script);
              JHTML::_('behavior.mootools');

              // Replace mootools
              if($sbreplmootools && count($doc->_scripts ))
              {
                foreach ($doc->_scripts as $key => $value)
                {
                  if(stristr($key, 'mootools'))
                  {
                    unset($doc->_scripts[$key]);
                    break;
                  }
                }
                $doc->addScript(JURI::root().'plugins/joomgallery/joommultislimbox/171/js/mootools-core.js');
                $doc->addScript(JURI::root().'plugins/joomgallery/joommultislimbox/171/js/mootools-more.js');
              }

              $doc->addStyleSheet(JURI::root().'plugins/joomgallery/joommultislimbox/171/css/slimbox.css');
              // Include the modified and compressed javascript
              $doc->addScript(JURI::root().'plugins/joomgallery/joommultislimbox/171/js/slimbox.js');
              // For tests include the modified and not compressed source
              //$doc->addScript(JURI::root().'plugins/joomgallery/joommultislimbox/171/src/slimbox.js');
            }
            break;
          // 2.04
          case 2:
            if(!$sbglobexistent)
            {
              $doc->addScriptDeclaration($script);
              $doc->addScript(JURI::root().'plugins/joomgallery/joommultislimbox/204/js/jquery-1.4.3.min.js');
              $doc->addStyleSheet(JURI::root().'plugins/joomgallery/joommultislimbox/204/css/slimbox2.css');
              // Include the modified and compressed javascript
              $doc->addScript(JURI::root().'plugins/joomgallery/joommultislimbox/204/js/slimbox2.js');
              // For tests include the modified and not compressed source
              //$doc->addScript(JURI::root().'plugins/joomgallery/joommultislimbox/204/src/slimbox2.js');
            }
            break;
          // 1.58
          default:
            if(!$sbglobexistent)
            {
              $doc->addScriptDeclaration($script);
              JHTML::_('behavior.mootools');
              $doc->addStyleSheet(JURI::root().'plugins/joomgallery/joommultislimbox/158/css/slimbox.css');
              // Include the modified and compressed javascript
              $doc->addScript(JURI::root().'plugins/joomgallery/joommultislimbox/158/js/slimbox.js');
              // For tests include the modified and not compressed source
              //$doc->addScript(JURI::root().'plugins/joomgallery/joommultislimbox/158/src/slimbox.js');
            }
            break;
        }

        $loaded = true;
      }
      if(strlen($image->imgtext) > 0)
      {
        $link = $img_url.'" rel="lightbox['.$group.'];" title="'.$image->imgtitle.'<br />'.$image->imgtext;
      }
      else
      {
        $link = $img_url.'" rel="lightbox['.$group.'];" title="'.$image->imgtitle;
      }
    }
    else
    {
      // JoomGallery wants to know whether this plugin is enabled
      $link = true;
    }
  }
}