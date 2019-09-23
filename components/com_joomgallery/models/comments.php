<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/models/comments.php $
// $Id: comments.php 3092 2011-05-20 09:56:58Z aha $
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
 * Comments Model
 *
 * Saves and removes comments.
 *
 * @package JoomGallery
 * @since   1.5.5
 */
class JoomGalleryModelComments extends JoomGalleryModel
{
  /**
   * The ID of the image the comment belongs to
   *
   * @access  protected
   * @var     int
   */
  var $_id;

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

    $id = JRequest::getInt('id');
    $this->setId($id);
  }

  /**
   * Method to set the image ID
   *
   * @access  public
   * @param   int     Image ID number
   * @since   1.5.5
   */
  function setId($id)
  {
    // Set new image ID if valid
    if(!$id)
    {
      JError::raiseError(500, JText::_('JGS_COMMON_NO_IMAGE_SPECIFIED'));
    }
    $this->_id  = $id;
  }

  /**
   * Method to get the image ID
   *
   * @access  public
   * @return  int     The image ID
   * @since   1.5.5
   */
  function getId()
  {
    return $this->_id;
  }

  /**
   * Method to save a new comment
   *
   * @access  public
   * @return  int     1 on success, 2 on success but approval necessary, boolean false otherwise
   * @since   1.5.5
   */
  function save()
  {
    // Check for hacking attempt
    $this->_db->setQuery("SELECT
                            COUNT(id)
                          FROM
                            "._JOOM_TABLE_IMAGES." AS a
                          LEFT JOIN
                            "._JOOM_TABLE_CATEGORIES." AS c ON c.cid = a.catid
                          WHERE
                                a.published = 1
                            AND a.approved  = 1
                            AND a.id        = ".$this->_id."
                            AND c.access   <= ".$this->_user->get('aid')."
                       ");

    if(   !$this->_db->loadResult()
      ||  !$this->_config->get('jg_showcomment')
      || (!$this->_config->get('jg_anoncomment') && $this->_user->get('aid') < 1)
      )
    {
      die('Hacking attempt, aborted!');
    }

    // Comment text
    $text = trim(JFilterInput::clean(JRequest::getVar('cmttext', '', 'post')));
    if(!$text)
    {
      $this->_mainframe->redirect(JRoute::_('index.php?view=detail&id='.$this->_id.'#joomcommentform', false),
                                  JText::_('JGS_NO_COMMENT_ENTERED'), 'notice');
    }

    // Name of the one who comments
    if($this->_user->get('id'))
    {
      $name = $this->_user->get('name');
    }
    else
    {
      if($this->_config->get('jg_namedanoncomment'))
      {
        $name   = trim(JFilterInput::clean(JRequest::getVar('cmtname', '', 'post')));
        if(!$name)
        {
          $name = JText::_('JGS_COMMON_GUEST');
        }
      }
      else
      {
        $name   = JText::_('JGS_COMMON_GUEST');
      }
    }

    // Captcha
    $valid = true;
    $plugins  = $this->_mainframe->triggerEvent('onJoomCheckCaptcha');
    foreach($plugins as $key => $result)
    {
      if(is_array($result) && isset($result['valid']) && !$result['valid'])
      {
        $valid = false;
        if(isset($result['error']) && $result['error'])
        {
          $msg = $result['error'];
        }
        else
        {
          $msg = JText::_('JGS_DETAIL_MSG_COMMENT_SECURITY_CODE_WRONG');
        }
        break;
      }
    }

    if(!$valid)
    {
      $this->_mainframe->setUserState('joom.comments.name', $name);
      $this->_mainframe->setUserState('joom.comments.text', $text);
      $this->_mainframe->redirect(JRoute::_('index.php?view=detail&id='.$this->_id.'#joomcommentform', false),
                                  $msg, 'notice');
    }

    // Check whether the comment has to be approved by administrators
    if(   !$this->_config->get('jg_approvecom')
      ||  ($this->_config->get('jg_approvecom') == 1 && $this->_user->get('aid') > 0 )
      )
    {
      $approved = 1;

      // Load image data
      $image    = &$this->getTable('joomgalleryimages');
      $image->load($this->_id);

      // Message about new comment to image owner
      // If comments have to be approved by administrators
      // this message will be sent as soon as the comment was approved
      if(     $this->_config->get('jg_msg_comment_toowner')
          &&  $image->owner
          &&  $image->owner != $this->_user->get('id')
        )
      {
        // Load image data
        $row = &$this->getTable('joomgalleryimages');
        $row->load($this->_id);

        require_once(JPATH_COMPONENT.DS.'helpers'.DS.'messenger.php');
        $messenger  = new JoomMessenger();
        $message    = array(
                            'from'      => $this->_user->get('id'),
                            'recipient' => $image->owner,
                            'subject'   => JText::_('JGS_MESSAGE_NEW_COMMENT_TO_OWNER_SUBJECT'),
                            'body'      => JText::sprintf('JGS_MESSAGE_NEW_COMMENT_TO_OWNER_BODY', $name, $image->imgtitle, $this->_id),
                            'type'      => $messenger->getType('comment')
                          );
      }
    }
    else
    {
      $approved = 0;

      // Message about new comment
      require_once(JPATH_COMPONENT.DS.'helpers'.DS.'messenger.php');
      $messenger  = new JoomMessenger();

      $message    = array(
                            'from'      => $this->_user->get('id'),
                            'subject'   => JText::_('JGS_MESSAGE_NEW_COMMENT_SUBJECT'),
                            'body'      => JText::sprintf('JGS_MESSAGE_NEW_COMMENT_BODY', $name),
                            'mode'      => 'comment'
                          );
    }

    // Change \r\n or \n to <br />
    $text = nl2br(stripcslashes($text));
    $date = & JFactory::getDate();
    $row  = & $this->getTable('joomgallerycomments');

    $row->cmtpic    = $this->_id;
    $row->cmtip     = $_SERVER['REMOTE_ADDR'];
    $row->userid    = $this->_user->get('id');
    $row->cmtname   = $name;
    $row->cmttext   = $text;
    $row->cmtdate   = $date->toMySQL();
    $row->published = 1;
    $row->approved  = $approved;

    // Trigger event 'onJoomBeforeComment'
    $plugins  = $this->_mainframe->triggerEvent('onJoomBeforeComment', array(&$row));
    if(in_array(false, $plugins, true))
    {
      return false;
    }

    if(!$row->store())
    {
      $this->setError(JText::_('JGS_ERROR_SAVING_COMMENT'));
      return false;
    }

    if(isset($messenger))
    {
      $messenger->send($message);
    }

    $this->_mainframe->triggerEvent('onJoomAfterComment', array($row));

    if($approved)
    {
      return 1;
    }
    else
    {
      return 2;
    }
  }

  /**
   * Method to delete a comment
   *
   * @access  public
   * @return  boolean True on success, false otherwise
   * @since   1.5.5
   */
  function remove()
  {
    if(    $this->_user->get('gid') < 24
        && $this->_user->get('gid') != 20
      )
    {
      JError::raiseError(500, JText::_('JGS_COMMON_PERMISSION_DENIED'));
    }

    $cmtid = JRequest::getInt('cmtid');

    $this->_db->setQuery("DELETE
                          FROM
                            "._JOOM_TABLE_COMMENTS."
                          WHERE
                                cmtid   = ".$cmtid."
                            AND cmtpic  = ".$this->_id."
                        ");
    if(!$this->_db->query())
    {
      $this->setError(JText::_('JGS_ERROR_DELETING_COMMENT'));
      return false;
    }

    return true;
  }
}