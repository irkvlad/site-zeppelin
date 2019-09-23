<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/Plugins/JoomAutoCat/trunk/joomautocat.php $
// $Id: joomautocat.php 3299 2011-08-27 13:49:42Z chraneco $
/******************************************************************************\
**   JoomGallery User Plugin 'AutoCreation of User Categories' 1.5            **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2009 - 2011 Patrick Alt                                    **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html                            **
\******************************************************************************/
/** ### Original Copyright from the example plugin: ###
 * @version   $Id: joomautocat.php 3299 2011-08-27 13:49:42Z chraneco $
 * @package   Joomla
 * @subpackage  JFramework
 * @copyright Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * JoomGallery User Plugin, automatically creates categories in JoomGallery
 *
 * @package     Joomla
 * @subpackage  JoomGallery
 * @since       1.5
 */
class plgUserJoomAutoCat extends JPlugin
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
  function plgUserJoomAutoCat(&$subject, $params)
  {
    parent::__construct($subject, $params);
  }

  /**
   * If the stored user is a new one a new category will be created for him
   *
   * Store user method
   * Method is called after user data is stored in the database
   *
   * @access  public
   * @param   array   Holds the new user data
   * @param   boolean True if a new user is stored
   * @param   boolean True if user was succesfully stored in the database
   * @param   string  Message
   * @return  void
   * @since   1.5
   */
  function onAfterStoreUser($user, $isnew, $success, $msg)
  {
    if($isnew)
    {
      $this->_createCategory($user);
    }
  }

  /**
   * This method should handle any login logic and report back to the subject
   *
   * @access  public
   * @param   array   Holds the user data
   * @param   array   Extra options
   * @return  boolean True on success, false otherwise
   * @since   1.5
   */
  function onLoginUser($user, $options)
  {
    if($this->params->get('onlogin'))
    {
      $db           = & JFactory::getDBO();
      $user_object  = & JFactory::getUser();

      $user['id']   = $user_object->get('id');
      $user['name'] = $user['fullname'];

      $db->setQuery(" SELECT
                        COUNT(cid)
                      FROM
                        #__joomgallery_catg
                      WHERE
                        owner = ".$user['id']
                    );
      if(!$db->loadResult())
      {
        $this->_createCategory($user);
      }
    }

    return true;
  }

  /**
   * Creates the category with the help of the interface class
   *
   * @access  protected
   * @param   array     Holds the user data
   * @return  void
   * @since   1.5
   */
  function _createCategory($user)
  {
    // Get the interface
    require_once(JPATH_ROOT.DS.'components'.DS.'com_joomgallery'.DS.'interface.php');
    $jinterface = new JoomInterface();

    // Create the category
    switch($this->params->get('categoryname'))
    {
      case 0:
        if($jinterface->getJConfig('jg_realname'))
        {
          $category->name = $user['name'];
        }
        else
        {
          $category->name = $user['username'];
        }
        break;
      case 1:
        $category->name   = $user['name'];
        break;
      default:
        $category->name   = $user['username'];
        break;
    }
    $category->owner  = $user['id'];
    if($parent = $this->params->get('parent'))
    {
      $category->parent = intval($parent);
    }
    if($access = $this->params->get('access'))
    {
      $category->access = $access;
    }
    $category->published = $this->params->get('published');

    $jinterface->createCategory($category);
  }
}