<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/tables/joomgallerycategories.php $
// $Id: joomgallerycategories.php 3619 2012-02-11 14:55:49Z chraneco $
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
 * JoomGallery categories table class
 *
 * @package JoomGallery
 * @since   1.5.5
 */
class TableJoomgalleryCategories extends JTable
{
  /** @var int Primary key */
  var $cid          = null;
  /** @var int */
  var $owner        = 0;
  /** @var string */
  var $name         = null;
  /** @var string */
  var $alias        = null;
  /** @var int */
  var $parent       = 0;
  /** @var string */
  var $description  = null;
  /** @var int */
  var $ordering     = 0;
  /** @var string */
  var $access       = 0;
  /** @var int */
  var $published    = 0;
  /** @var int */
  var $hidden       = 0;
  /** @var int */
  var $in_hidden    = 0;
  /** @var string */
  var $catimage     = null;
  /** @var int */
  var $img_position = null;
  /** @var string */
  var $catpath      = null;
  /** @var string */
  var $params       = null;
  /** @var string */
  var $metakey      = null;
  /** @var string */
  var $metadesc     = null;

  /**
   * Helper variable for checking whether
   * 'hidden' state is changed
   *
   * @access  private
   * @var     int
   */
  var $_hidden = 0;

  /**
   * Helper variable for checking whether
   * 'in_hidden' is changed
   *
   * @access  private
   * @var     int
   */
  var $_in_hidden = 0;

  /**
   * Constructor
   *
   * @access  private
   * @param   object  $db A database connector object
   * @since   1.5.5
   */
  function TableJoomgalleryCategories(&$db)
  {
    parent::__construct(_JOOM_TABLE_CATEGORIES, 'cid', $db);
  }

  /**
   * Overloaded load function, loads a specific row.
   *
   * @access  public
   * @param   mixed   The primary key, if it is not specified the value of the current key is used
   * @return  boolean True on success, false otherwise
   * @since   1.5.7
   */
  function load($oid = null)
  {
    if(!parent::load($oid))
    {
      return false;
    }
 
    // Store the current values of 'hidden' and 'in_hidden' in
    // order to be able to detect changes of this state later on
    $this->_hidden    = $this->hidden;
    $this->_in_hidden = $this->in_hidden;

    return true;
  }

  /**
   * Overloaded check function, validates the row.
   * This method should always be called afore calling 'store'.
   *
   * @access  public
   * @return  boolean True on success, false otherwise
   * @since   1.5.5
   */
  function check()
  {
    if(empty($this->name))
    {
      $this->setError(JText::_('JG_COMMON_ERROR_CATEGORY_MUST_HAVE_TITLE'));
      return false;
    }

    JFilterOutput::objectHTMLSafe($this->name);

    // For the the next two checks get published state,
    // hidden state and access level of parent category
    if($this->parent)
    {
      $query = "SELECT
                 published,
                 access,
                 hidden,
                 in_hidden
               FROM
                "._JOOM_TABLE_CATEGORIES."
               WHERE
                 cid = ".(int) $this->parent;
      $this->_db->setQuery($query);
      if(!$parent = $this->_db->loadObject())
      {
        $this->setError($this->_db->getErrorMsg());

        return false;
      }

      // Check whether state and access level are allowed regarding parent categories
      if($this->published || $this->access != 2)
      {
        if(!$parent->published && $this->published)
        {
          $this->published = 0;
          if($this->cid)
          {
            JError::raiseNotice('100', JText::sprintf('JG_COMMON_NOT_ALLOWED_TO_PUBLISH_CATEGORY', $this->cid));
          }
          else
          {
            JError::raiseNotice('100', JText::_('JG_COMMON_NOT_ALLOWED_TO_PUBLISH_NEW_CATEGORY'));
          }
        }
        if($parent->access > $this->access)
        {
          $this->access = $parent->access;
          JError::raiseNotice('100', JText::_('JG_COMMON_ACCESS_LEVEL_FOR_CATEGORY_NOT_ALLOWED'));
        }
      }
    }

    // Check whether 'in_hidden' flag has to be set to 1 or 0
    if(!$this->parent)
    {
      $this->in_hidden = 0;
    }
    else
    {
      if($parent->hidden || $parent->in_hidden)
      {
        $this->in_hidden = 1;
      }
      else
      {
        $this->in_hidden = 0;
      }
    }

    // Trim slashes from catpath
    $this->catpath = trim($this->catpath, '/');

    if(empty($this->alias))
    {
      if(!empty($this->catpath))
      {
        $catpath  = explode('/', trim($this->catpath, '/'));
        $segments = array();
        foreach($catpath as $segment)
        {
          $segment    = str_replace('_', ' ', rtrim(rtrim($segment, '0123456789'), '_'));
          $segment    = JFilterOutput::stringURLSafe($segment);
          if($segment)
          {
            $segments[] = $segment;
          }
        }
        $this->alias = implode('/', $segments);
      }
    }
    else
    {
      $alias = explode('/', trim($this->alias, '/'));
      $segments = array();
      foreach($alias as $segment)
      {
        $segment    = JFilterOutput::stringURLSafe($segment);
        if($segment)
        {
          $segments[] = $segment;
        }
      }
      $this->alias = implode('/', $segments);
    }

    if(trim(str_replace('-', '', $this->alias)) == '' && !empty($this->catpath))
    {
      $datenow      = & JFactory::getDate();
      $this->alias  = $datenow->toFormat('%Y-%m-%d-%H-%M-%S');
    }

    // clean up keywords -- eliminate extra spaces between phrases
    // and cr (\r) and lf (\n) characters from string
    if(!empty($this->metakey))
    {
      // array of characters to remove
      $bad_characters = array("\n", "\r", "\"", '<', '>');
      // remove bad characters
      $after_clean = JString::str_ireplace($bad_characters, '', $this->metakey);
      // create array using commas as delimiter
      $keys = explode(',', $after_clean);
      $clean_keys = array(); 
      foreach($keys as $key)
      {
        // ignore blank keywords
        if(trim($key))
        {  
          $clean_keys[] = trim($key);
        }
      }
      // put array back together delimited by ', '
      $this->metakey = implode(', ', $clean_keys);
    }
    
    // clean up description -- eliminate quotes and <> brackets
    if(!empty($this->metadesc))
    {
      $bad_characters = array("\"", '<', '>');
      $this->metadesc = JString::str_ireplace($bad_characters, '', $this->metadesc);
    }

    return true;
  }

  /**
   * Overloaded store function
   *
   * @access  public
   * @return  boolean True on success, false otherwise
   * @since   1.5.7
   */
  function store()
  {
    if(!parent::store())
    {
      return false;
    }

    // If there aren't any sub-categories there isn't anything to do anymore
    $cats = JoomHelper::getAllSubCategories($this->cid, false, true, true, false);
    if(!count($cats))
    {
      return true;
    }

    // Set state of all sub-categories
    // according to the settings of this category
    $query = "UPDATE
                "._JOOM_TABLE_CATEGORIES."
              SET
                published = ".$this->published.",
                access = IF(".$this->access." = 1 AND access < 1, 1, access),
                access = IF(".$this->access." = 2 AND access < 2, 2, access)
              WHERE
                cid IN (".implode(',', $cats).")";
    $this->_db->setQuery($query);
    if(!$this->_db->query())
    {
      $this->setError($this->_db->getErrorMsg());

      return false;
    }

    // Set 'in_hidden' of all sub-categories
    // according to hidden state of this category
    // (but only if there was a change of this state)
    if(   ($this->_hidden != $this->hidden && !$this->in_hidden)
      ||  $this->_in_hidden != $this->in_hidden
      )
    {
      if($this->hidden == 0 && $this->in_hidden == 0)
      {
        // If 'hidden' is 0 only the categories
        // which aren't set to hidden must be changed
        // because they form a hidden group themselves
        // anyway and have to stay hidden
        $cats = JoomHelper::getAllSubCategories($this->cid, false, true, true, true);
      }

      $query = "UPDATE
                  "._JOOM_TABLE_CATEGORIES."
                SET
                  in_hidden = ".(int) ($this->hidden || $this->in_hidden)."
                WHERE
                  cid IN (".implode(',', $cats).")";
      $this->_db->setQuery($query);
      if(!$this->_db->query())
      {
        $this->setError($this->_db->getErrorMsg());

        return false;
      }
    }

    return true;
  }

  /**
   * Reorders the categories according
   * to the latest changes
   *
   * @access  public
   * @return  boolean True on success, false otherwise
   * @since   1.5.5
   */
  function reorderAll()
  {
    $query = 'SELECT DISTINCT parent
                FROM '.$this->_db->nameQuote($this->_tbl);
    $this->_db->setQuery($query);
    $parents = $this->_db->loadResultArray();

    foreach($parents as $parent)
    {
      $this->reorder('parent = '.$parent);
    }
  }

  /**
   * Returns the ordering value to place a new item first in its group
   *
   * @access  public
   * @param   string $where query WHERE clause for selecting MAX(ordering).
   * @return  int    The ordring number
   * @since   1.5.5
   */
  function getPreviousOrder($where = '')
  {
    if(!in_array('ordering', array_keys($this->getProperties())))
    {
      $this->setError(get_class($this).' does not support ordering');
      return false;
    }

    $query = 'SELECT MIN(ordering)' .
        ' FROM ' . $this->_tbl .
        ($where ? ' WHERE '.$where : '');

    $this->_db->setQuery($query);
    $maxord = $this->_db->loadResult();

    if($this->_db->getErrorNum())
    {
      $this->setError($this->_db->getErrorMsg());
      return false;
    }

    return $maxord - 1;
  }
}
