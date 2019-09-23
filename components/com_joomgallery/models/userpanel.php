<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/models/userpanel.php $
// $Id: userpanel.php 3092 2011-05-20 09:56:58Z aha $
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
 * JoomGallery User Panel Model
 *
 * @package JoomGallery
 * @since   1.5.5
 */
class JoomGalleryModelUserpanel extends JoomGalleryModel
{
  /**
   * Images data array
   *
   * @access  protected
   * @var     array
   */
  var $_images;

  /**
   * Images number
   *
   * @access  protected
   * @var     int
   */
  var $_total = null;

  /**
   * Categories data array
   *
   * @access  protected
   * @var     array
   */
  var $_categories;

  /**
   * Set to true if the current user is an administrator
   *
   * @access  protected
   * @var     boolean
   */
  var $_adminlogged = false;

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

    if($this->_user->get('gid') > 23)
    {
      $this->_adminlogged = true;
    }
  }

  /**
   * Retrieves the images data
   *
   * @access  public
   * @return  array   An array of images objects
   * @since   1.5.5
   */
  function getImages()
  {
    if($this->_loadImages())
    {
      return $this->_images;
    }

    return array();
  }

  /**
   * Method to get the total number of images
   *
   * @access  public
   * @return  int     The total number of images
   * @since   1.5.5
   */
  function getTotal()
  {
    // Let's load the data if it doesn't already exist
    if (empty($this->_total))
    {
      $query = $this->_buildQuery();
      $this->_total = $this->_getListCount($query);
    }

    return $this->_total;
  }

  /**
   * Retrieves the categories data from the database
   *
   * @access  public
   * @return  array   An array of categories
   * @since   1.5.5
   */
  function getCategories()
  {
    // Let's load the data if it doesn't already exist
    if(empty($this->_categories))
    {
      $query = "  SELECT
                    cid
                  FROM
                    "._JOOM_TABLE_CATEGORIES;
      if(!$this->_config->get('jg_userowncatsupload'))
      {
        $query .= "
                  WHERE
                        owner != 0";
      }
      else
      {
        $query .= "
                  WHERE
                        owner = ".$this->_user->get('id');
      }

      $jg_category      = $this->_config->get('jg_category');
      $jg_usercategory  = $this->_config->get('jg_usercategory');
      if(!empty($jg_category))
      {
        $query .= "
                    OR  cid IN (".$this->_config->get('jg_category').")";
      }

      if($this->_config->get('jg_usercat') && !empty($jg_usercategory))
      {
        $query .= "
                    OR  (cid IN (".$this->_config->get('jg_usercategory').") AND access <= ".$this->_user->get('aid').")";
      }

      $this->_db->setQuery($query);

      $this->_categories  = $this->_db->loadResultArray();
    }

    return $this->_categories;
  }

  /**
   * Returns true if the current user is an administrator, false otherwise
   *
   * @access  public
   * @return  boolean True if the current user is an administrator, false otherwise
   * @since   1.5.5
   */
  function getAdminLogged()
  {
    return $this->_adminlogged;
  }

  /**
   * Loads the images data from the database
   *
   * @access  protected
   * @return  boolean   True on success, false otherwise
   * @since   1.5.5
   */
  function _loadImages()
  {
    // Let's load the data if it doesn't already exist
    if(empty($this->_images))
    {
      jimport('joomla.filesystem.file');

      $query = $this->_buildQuery();

      // Get the pagination request variables
      $limit      = JRequest::getInt('limit', 0);
      $limitstart = JRequest::getInt('limitstart', 0);

      if(!$rows = $this->_getList($query, $limitstart, $limit))
      {
        return false;
      }

      $this->_images = $rows;
    }

    return true;
  }

  /**
   * Returns the query to get the images rows from the database
   *
   * @access  protected
   * @return  string    The query to get the image rows from the database
   * @since   1.5.5
   */
  function _buildQuery()
  {
    $query = "SELECT
                *
              FROM
                "._JOOM_TABLE_IMAGES."
              ".$this->_buildWhere()."
              ".$this->_buildOrderby();

    return $query;
  }

  /**
   * Returns the 'where' part of the query to get the images rows from the database
   *
   * @access  protected
   * @return  string    The 'where' part of the query
   * @since   1.5.5
   */
  function _buildWhere()
  {
    // Filter by type
    $filter = JRequest::getInt('filter');
    // Filter by category
    $catid  = JRequest::getInt('catid');
    // Search
    $search = trim(JRequest::getString('search'));

    $where  = array();

    switch($filter)
    {
      case 1: // Approved
        $where[] = 'approved = 1';
        break;
      case 2: // Not approved
        $where[] = 'approved = 0';
        break;
      case 3: // Published
        $where[] = 'published = 1';
        break;
      case 4: // Not published
        $where[] = 'published = 0';
        break;
      default:
        break;
    }

    // The admin/superadmin will see all images, if the regarding backend option is enabled
    if(!$this->_adminlogged || !$this->_config->get('jg_showallpicstoadmin'))
    {
      $where[] = 'owner = '.$this->_user->get('id');
    }

    if($catid)
    {
      $where[]   = 'catid = '.$catid;
    }

    if(!empty($search))
    {
      $search   = $this->_db->Quote('%'.$this->_db->getEscaped($search, true).'%', false);
      $where[]  = "(LOWER(imgtitle) LIKE $search OR LOWER(imgtext) LIKE $search)";
    }

    $where = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    return $where;
  }

  /**
   * Returns the 'order by' part of the query to get the images rows from the database
   *
   * @access  protected
   * @return  string    The 'order by' part of the query
   * @since   1.5.5
   */
  function _buildOrderBy()
  {
    $sordercat = JRequest::getInt('ordering');

    switch($sordercat)
    {
      case 1:
        $sortorder = 'imgdate DESC';
        break;
      case 2:
        $sortorder = 'imgtitle ASC';
        break;
      case 3:
        $sortorder = 'imgtitle DESC';
        break;
      case 4:
        $sortorder = 'hits ASC';
        break;
      case 5:
        $sortorder = 'hits DESC';
        break;
      case 6:
        $sortorder = 'catid ASC,imgtitle ASC';
        break;
      case 7:
        $sortorder = 'catid ASC,imgtitle DESC';
        break;
      default:
        $sortorder = 'imgdate ASC';
        break;
    }

    return 'ORDER BY '.$sortorder;
  }
}