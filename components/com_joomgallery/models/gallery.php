<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/models/gallery.php $
// $Id: gallery.php 3092 2011-05-20 09:56:58Z aha $
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
 * Gallery view model
 *
 * @package JoomGallery
 * @since   1.5.5
 */
class JoomGalleryModelGallery extends JoomGalleryModel
{
  /**
   * Categories data array
   *
   * @access  private
   * @var     array
   */
  var $_categories;

  /**
   * Categories number
   *
   * @access  private
   * @var     int
   */
  var $_total = null;

  /**
   * Categories data array (without empty categories)
   *
   * @access  private
   * @var     array
   */
  var $_categorieswithoutempty;

  /**
   * Categories number (without empty categories)
   *
   * @access  private
   * @var     int
   */
  var $_totalwithoutempty = null;

  /**
   * Returns the array of all available main categories of the gallery
   *
   * @access  public
   * @return  array   Categories objects array
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
    // Let's load the categories if they doesn't already exist
    if (empty($this->_total))
    {
      $query = $this->_buildQuery();
      $this->_total = $this->_getListCount($query);
    }

    return $this->_total;
  }

  /**
   * Returns the array of all available main categories
   * of the gallery without empty categories
   *
   * @access  public
   * @return  array   Categories objects array
   * @since   1.5.7
   */
  function getCategoriesWithoutEmpty()
  {
    if($this->_loadCategoriesWithoutEmpty())
    {
      // We still have to select the categories according to the pagination
      $limit      = $this->_config->get('jg_catperpage');#JRequest::getVar('limit', 0, '', 'int');
      $limitstart = JRequest::getInt('limitstart', 0);
      $cats = array_slice($this->_categorieswithoutempty, $limitstart, $limit);

      return $cats;
    }

    return array();
  }

  /**
   * Method to get the total number of categories without empty categories
   *
   * @access  public
   * @return  int     The total number of categories without empty ones
   * @since   1.5.7
   */
  function getTotalWithoutEmpty()
  {
    // Let's calculate the number if it doesn't already exist
    if(empty($this->_totalwithoutempty))
    {
      if(!$this->_loadCategoriesWithoutEmpty())
      {
        return 0;
      }
    }

    return $this->_totalwithoutempty;
  }

  /**
   * Method to get the data of a random image
   *
   * @access  public
   * @return  object  The data of a random image
   * @since   1.5.5
   */
  function getRandomImage($catid = 0, $random_catid = 0)
  {
    $query = "  SELECT
                  *,
                  c.access
                FROM
                  "._JOOM_TABLE_IMAGES." AS p
                LEFT JOIN
                  "._JOOM_TABLE_CATEGORIES." AS c
                ON
                  c.cid = p.catid";
    if($this->_config->get('jg_showrandomcatthumb') == 1)
    {
      $query.= "
                WHERE
                      p.catid = ".$catid;
    }
    else
    {
      if($this->_config->get('jg_showrandomcatthumb') >= 2)
      {
        $query.= "
                WHERE
                      p.catid = ".$random_catid;
      }
    }
    $query.= "
                  AND p.published = 1
                  AND p.approved  = 1
                  AND p.hidden   != 1
                  AND c.access   <= ".$this->_user->get('aid')."
                  AND c.published = 1
              ";
    
    $this->_db->setQuery($query);
    if(!$rows = $this->_db->loadObjectList())
    {
      return false;
    }

    $row = $rows[mt_rand(0, count($rows)-1)];

    return $row;
  }
  
  /**
   * Loads the data of all available categories from the database
   *
   * @access  private
   * @return  boolean True on success, false otherwise
   * @since   1.5.5
   */
  function _loadCategories()
  {    
    // Let's load the data if it doesn't already exist
    if(empty($this->_categories))
    {
      // Get the pagination request variables
      $limit      = $this->_config->get('jg_catperpage');#JRequest::getVar('limit', 0, '', 'int');
      $limitstart = JRequest::getInt('limitstart', 0);

      $query = $this->_buildQuery();

      if(!$rows = $this->_getList($query, $limitstart, $limit))
      {
        return false;
      }

      $this->_categories = $rows;
    }

    return true;
  }

  /**
   * Loads the data of all available categories from the database
   * and checks whether the categories are empty.
   * This method stores only the categories which aren't empty after that.
   *
   * @access  private
   * @return  boolean True on success, false otherwise
   * @since   1.5.7
   */
  function _loadCategoriesWithoutEmpty()
  {
    // Let's load the data if it doesn't already exist
    if(empty($this->_categorieswithoutempty))
    {
      $query = $this->_buildQuery();
      if(!$cats = $this->_getList($query))
      {
        return false;
      }

      foreach($cats as $key => $cat)
      {
        // Get all sub-categories for each category which contain images
        $subcategories = JoomHelper::getAllSubCategories($cat->cid, true);
        if(!count($subcategories))
        {
          unset($cats[$key]);
        }
      }

      $this->_categorieswithoutempty  = $cats;
      $this->_totalwithoutempty       = count($cats);
    }

    return true;
  }

  /**
   * Returns the query for loading all available categories of the gallery
   *
   * @access  private
   * @return  string  The query to be used to retrieve the categories from the database
   * @since   1.5.5
   */
  function _buildQuery()
  {
    $query = "SELECT 
                *
              FROM 
                "._JOOM_TABLE_CATEGORIES." AS c
              ".$this->_buildWhere()."
              ".$this->_buildOrderby();

    return $query;
  }

  /**
   * Returns the 'where' part of the query for
   * loading all available categories from the database
   *
   * @access  private
   * @return  string  The 'where' part of the query for loading all categories
   * @since   1.5.5
   */
  function _buildWhere()
  {
    /*$filtercat = JRequest::getInt('filter');*/

    $where = array();

    $where[] = "c.published = 1";
    $where[] = 'c.hidden    = 0';
    $where[] = "c.parent    = 0";

    if(!$this->_config->get('jg_showrmsmcats'))
    {
      $where[] = "c.access <= ".$this->_user->get('aid');
    }

    if($this->_config->get('jg_hideemptycats'))
    {
      $where[] = "(((SELECT COUNT(id) FROM "._JOOM_TABLE_IMAGES." AS i WHERE i.catid = c.cid) != 0)
                OR 
                  ((SELECT COUNT(cid) FROM "._JOOM_TABLE_CATEGORIES." AS sc WHERE sc.parent = c.cid) != 0))";
    }

    /*//Filter by type
    switch($filtercat) {
      //published
      case 1:
        $where[] = 'published = 1';
        break;
      //not published
      case 2:
        $where[] = 'published = 0';
        break;
      //user categories
      case 3:
        $where[] = 'owner IS NOT NULL';
        break;
      //admin categories
      case 4:
        $where[] = 'owner IS NULL';
        break;
      default:
        break;
    }

    if ($searchtext = JRequest::getString('search'))
    {
      $filter   = $this->_db->Quote('%'.$this->_db->getEscaped($searchtext, true).'%', false);
      $where[]  = "(c.name LIKE $filter OR c.description LIKE $filter)";
    }*/

    $where = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    return $where;
  }

  /**
   * Returns the 'order by' part of the query for
   * loading all available categories from the database
   *
   * @access  private
   * @return  string  The 'order by' part of the query for loading all categories
   * @since   1.5.5
   */
  function _buildOrderBy()
  {
    if($this->_config->get('jg_ordercatbyalpha'))
    {
      $orderby = "ORDER BY c.name";
    }
    else
    {
      $orderby = "ORDER BY c.ordering";
    }

    /*$sort = JRequest::getInt('sort');

    $sortorder  = '';
    switch($sort) {
      case 0:
        $sortorder = 'c.ordering ASC';
        break;
      /*case 1:
        $sortorder = 'c.ordering DESC';
        break;*/
      /*case 2:
        $sortorder = 'c.catpath ASC';
        break;
      case 3:
        $sortorder = 'c.catpath DESC';
        break;
      case 4:
        $sortorder = 'c.cid ASC';
        break;
      case 5:
        $sortorder = 'c.cid DESC';
        break;
      case 6:
        $sortorder = 'c.name ASC';
        break;
      case 7:
        $sortorder = 'c.name DESC';
        break;
      case 8:
        $sortorder = 'c.owner ASC';
        break;
      case 9:
        $sortorder = 'c.owner DESC';
        break;
      default:
        break;
    }

    if ($sortorder != ''){
      $orderby = 'ORDER BY '.$sortorder;
    }*/

    return $orderby;
  }
}