<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/models/mini.php $
// $Id: mini.php 3092 2011-05-20 09:56:58Z aha $
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
 * Mini Joom model
 *
 * @package JoomGallery
 * @since   1.5.5
 */
class JoomGalleryModelMini extends JoomGalleryModel
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
   * Upload categories data array
   *
   * @access  protected
   * @var     array
   */
  var $_uploadcategories;

  /**
   * Parent categories data array
   *
   * @access  protected
   * @var     array
   */
  var $_parentcategories;

  /**
   * Retrieves the images data
   *
   * @access  public
   * @return  array   Array of objects containing the images data from the database
   * @since   1.5.5
   */
  function getImages()
  {
    // Let's load the data if it doesn't already exist
    if(empty($this->_images))
    {
      $limitstart = JRequest::getInt('limitstart');
      $limit      = JRequest::getInt('limit');

      $query = $this->_buildQuery();

      if(!$this->_images = $this->_getList($query, $limitstart, $limit))
      {
        $this->_images = array();
      }
    }

    return $this->_images;
  }

  /**
   * Method to get the total number of images
   *
   * @access  public
   * @return  int     The total number of images
   * @since   1.5.5
   */
  function getTotalImages()
  {
    // Let's load the categories if they doesn't already exist
    if (empty($this->_total))
    {
      $query = $this->_buildQuery();
      $this->_total = $this->_getListCount($query);
    }

    return $this->_total;
  }

  /**
   * Method to get all categories in which the user is allowed to upload images
   *
   * @access  public
   * @return  array   An array holding all the relevant categories
   * since    1.5.7
   */
  function getUploadCategories()
  {
    $query = "SELECT
                cid,
                name
              FROM
                "._JOOM_TABLE_CATEGORIES;
    if($this->_user->get('gid') <= 23)
    {
      $query .= "
              WHERE";
      if($this->_config->get('jg_userowncatsupload'))
      {
        $query .= "
                    owner = ".$this->_user->get('id');
      }
      else
      {
        $query .= "
                    owner != 0";
      }
      if($this->_config->get('jg_category'))
      {
        $query .= "
                OR  cid IN (".$this->_config->get('jg_category').")";
      }
    }

    $this->_db->setQuery($query);
    if($rows = $this->_db->loadObjectList())
    {
      $this->_uploadcategories = $rows;
    }
    else
    {
      $this->_uploadcategories = array();
    }

    return $this->_uploadcategories;
  }

  /**
   * Method to get all categories in which the user is allowed to create sub categories
   *
   * @access  public
   * @return  array   An array holding all the relevant categories
   * since    1.5.7
   */
  function getParentCategories()
  {
    $query = "SELECT
                cid,
                name
              FROM
                "._JOOM_TABLE_CATEGORIES;
    if($this->_user->get('gid') <= 23)
    {
      $query .= "
              WHERE
                    owner = ".$this->_user->get('id');
      if($this->_config->get('jg_usercategory'))
      {
        $query .= "
                OR  cid IN (".$this->_config->get('jg_usercategory').")";
      }
    }

    $this->_db->setQuery($query);
    if($rows = $this->_db->loadObjectList())
    {
      $this->_parentcategories = $rows;
    }
    else
    {
      $this->_parentcategories = array();
    }

    return $this->_parentcategories;
  }

  /**
   * Returns the query for loading the images
   *
   * @access  protected
   * @return  string    The query to be used to retrieve the images data from the database
   * @since   1.5.5
   */
  function _buildQuery()
  {
    $query  = " SELECT
                  jg.id,
                  jg.catid,
                  jg.imgtitle,
                  jg.imgthumbname,
                  jgc.name
                FROM
                  "._JOOM_TABLE_IMAGES." AS jg
                LEFT JOIN
                  "._JOOM_TABLE_CATEGORIES." AS jgc
                ON
                  jgc.cid = jg.catid
                ".$this->_buildWhere();
                #".$this->_buildOrderBy();

    return $query;
  }

  /**
   * Returns the 'where' part of the query for loading the images
   *
   * @access  protected
   * @return  string    The 'where' part of the query
   * @since   1.5.5
   */
  function _buildWhere()
  {
    $catid  = $this->_mainframe->getUserStateFromRequest('joom.mini.catid', 'catid', 0, 'int');
    $search = $this->_mainframe->getUserStateFromRequest('joom.mini.search', 'search', '', 'string');

    $where = array();

    // Ensure that image can be seen later on
    $where[] = 'jgc.published = 1';
    $where[] = 'jgc.access <= '.$this->_user->get('aid');
    $where[] = 'jg.published = 1';
    $where[] = 'jg.approved = 1';
    if($this->_mainframe->getUserState('joom.mini.extended') && !$this->_mainframe->getUserState('joom.mini.showhidden'))
    {
      $where[] = 'jg.hidden     = 0';
      $where[] = 'jgc.hidden    = 0';
      $where[] = 'jgc.in_hidden = 0';
    }

    if($catid)
    {
      $where[] = 'jg.catid = '.$catid;
    }

    if($search)
    {
      $search  = $this->_db->getEscaped($search);
      $where[] = '(jg.imgtitle LIKE \'%'.$search.'%\' OR jg.imgtext LIKE \'%'.$search.'%\')';
    }

    $where = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    return $where;
  }

  /**
   * Returns the 'order by' part of the query for loading the images
   *
   * @access  protected
   * @return  string    The 'order by' part of the query
   * @since   1.5.5
   */
  /*function _buildOrderBy()
  {
    $orderby = '';

    return $orderby;
  }*/
}