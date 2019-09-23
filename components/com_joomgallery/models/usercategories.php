<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/models/usercategories.php $
// $Id: usercategories.php 3092 2011-05-20 09:56:58Z aha $
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
 * JoomGallery User Categories Model
 *
 * @package JoomGallery
 * @since   1.5.5
 */
class JoomGalleryModelUsercategories extends JoomGalleryModel
{
  /**
   * Categories data array
   *
   * @access  protected
   * @var     array
   */
  var $_categories;

  /**
   * Categories number
   *
   * @access  protected
   * @var     int
   */
  var $_total = null;

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
   * Retrieve the category data
   *
   * @access  public
   * @return  array     Array of objects containing the category data
   * @since   1.5.5
   */
  function getCategories()
  {
    if($this->_loadCategories())
    {
      return $this->_categories;
    }

    return array();
  }

  /**
   * Method to get the total number of categories
   *
   * @access  public
   * @return  int     The total number of categories
   * @since   1.5.5
   */
  function getTotal()
  {
    // Let's load the data if it doesn't already exist
    if(empty($this->_total))
    {
      $query = $this->_buildQuery();
      $this->_total = $this->_getListCount($query);
    }

    return $this->_total;
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
   * Loads the categories data from the database
   *
   * @access  protected
   * @return  boolean   True on success, false otherwise
   * @since   1.5.5
   */
  function _loadCategories()
  {
    // Let's load the data if it doesn't already exist
    if(empty($this->_categories))
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

      $this->_categories = $rows;
    }

    return true;
  }

  /**
   * Returns the query to get the category rows from the database
   *
   * @access  protected
   * @return  string    The query to be used to retrieve the category rows from the database
   * @since   1.5.5
   */
  function _buildQuery()
  {
    $query = "SELECT
                cid,
                name,
                catimage,
                parent,
                published,
                hidden,
                ( SELECT
                    COUNT(cid)
                  FROM
                    "._JOOM_TABLE_CATEGORIES." AS b
                  WHERE
                    b.parent = a.cid
                ) AS children,
                ( SELECT
                    COUNT(id)
                  FROM
                    "._JOOM_TABLE_IMAGES." AS i
                  WHERE
                    i.catid = a.cid
                ) AS images
              FROM
                "._JOOM_TABLE_CATEGORIES." AS a
              ".$this->_buildWhere()."
              ";#.$this->_buildOrderby();

    return $query;
  }

  /**
   * Returns the 'where' part of the query to get the category rows from the database
   *
   * @access  protected
   * @return  string    The 'where' part of the query
   * @since   1.5.5
   */
  function _buildWhere()
  {
    #$filter     = JRequest::getInt('filter');
    #$catid      = JRequest::getInt('catid');
    #$searchtext = JRequest::getString('search');

    // Filter by type
    $filter = JRequest::getInt('filter', null);

    $where  = array();

    switch($filter)
    {
      case 1: // Published
        $where[] = 'published = 1';
        break;
      case 2: // Not published
        $where[] = 'published = 0';
        break;
      default:
        break;
    }

    /*if($searchtext)
    {
      $filter   = $this->_db->Quote('%'.$this->_db->getEscaped($searchtext, true).'%', false);
      $where[]  = "(LOWER(a.imgtitle) LIKE $filter OR LOWER(a.imgtext) LIKE $filter)";
    }*/

    // The admin/superadmin will see all categories, if the regarding backend option is enabled
    if(!$this->_adminlogged || !$this->_config->get('jg_showallpicstoadmin'))
    {
      $where[] = 'owner = '.$this->_user->get('id');
    }

    $where = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    return $where;
  }

  /**
   * Returns the 'order by' part of the query to get the category rows from the database
   *
   * @access  protected
   * @return  string The 'order by' part of the query
   * @since   1.5.5
   */
  /*function _buildOrderBy()
  {
    $sordercat = JRequest::getInt('ordering');

    switch($sordercat)
    {
      case 1:
        $sortorder = 'imgdate DESC';
        break;
      case 2:
        $sortorder = 'imgtext ASC';
        break;
      case 3:
        $sortorder = 'imgtext DESC';
        break;
      case 4:
        $sortorder = 'hits ASC';
        break;
      case 5:
        $sortorder = 'hits DESC';
        break;
      case 6:
        $sortorder = 'catid ASC,imgtext ASC';
        break;
      case 7:
        $sortorder = 'catid ASC,imgtext DESC';
        break;
      default:
        $sortorder = 'imgdate ASC';
        break;
    }

    return 'ORDER BY '.$sortorder;
  }*/
}