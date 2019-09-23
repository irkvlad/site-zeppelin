<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/controllers/ftpupload.php $
// $Id: ftpupload.php 3092 2011-05-20 09:56:58Z aha $
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
 * JoomGallery FTP Upload Controller
 *
 * @package JoomGallery
 * @since   1.5.5
 */
class JoomGalleryControllerFtpupload extends JoomGalleryController
{
  /**
   * Constructor
   *
   * @access  protected
   * @return  void
   * @since   1.5.5
   */
  function __construct()
  {
    parent::__construct();

    // Set view
    JRequest::setVar('view', 'ftpupload');
  }

  /**
   * Uploads the selected images
   *
   * @access  public
   * @return  void
   * @since   1.5.5
   */
  function upload()
  {
    require_once JPATH_COMPONENT.DS.'helpers'.DS.'upload.php';
    $uploader = new JoomUpload();
    if($uploader->upload(JRequest::getCmd('type', 'ftp')))
    {
      $msg  = JText::_('JG_UPLOAD_MSG_SUCCESSFULL');
      $url  = $this->_ambit->getRedirectUrl();

      // Set custom redirect if we are asked for that
      if($redirect = JRequest::getVar('redirect', '', '', 'base64'))
      {
        $url_decoded  = base64_decode($redirect);
        if(JURI::isInternal($url))
        {
          $url = $url_decoded;
        }
      }

      $this->setRedirect(JRoute::_($url, false), $msg);
    }
    else
    {
      if($error = $uploader->getError())
      {
        $this->setRedirect($this->_ambit->getRedirectUrl(), $error, 'error');
      }
    }
  }
}