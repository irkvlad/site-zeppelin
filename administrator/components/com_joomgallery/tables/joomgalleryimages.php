<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/tables/joomgalleryimages.php $
// $Id: joomgalleryimages.php 3092 2011-05-20 09:56:58Z aha $
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
 * JoomGallery images table class
 *
 * @package JoomGallery
 * @since   1.5.5
 */
class TableJoomgalleryImages extends JTable
{
  /** @var int Primary key */
  var $id           = null;
  /** @var int */
  var $catid        = null;
  /** @var string */
  var $imgtitle     = null;
  /** @var string */
  var $alias        = null;
  /** @var string */
  var $imgauthor    = null;
  /** @var string */
  var $imgtext      = null;
  /** @var string */
  var $imgdate      = null;
  /** @var int */
  var $hits         = 0;
  /** @var int */
  var $imgvotes     = null;
  /** @var int */
  var $imgvotesum   = null;
  /** @var int */
  var $published    = null;
  /** @var int */
  var $hidden       = 0;
  /** @var string */
  var $imgfilename  = null;
  /** @var string */
  var $imgthumbname = null;
  /** @var string */
  var $checked_out  = null;
  /** @var string */
  var $owner        = 0;
  /** @var int */
  var $approved     = null;
  /** @var int */
  var $access       = null;
  /** @var int */
  var $useruploaded = null;
  /** @var int */
  var $ordering     = null;
  /** @var string */
  var $params       = null;
  /** @var string */
  var $metakey      = null;
  /** @var string */
  var $metadesc     = null;

  /**
   * Constructor
   *
   * @access  private
   * @param   object  $db A database connector object
   * @since   1.5.5
   */
  function TableJoomgalleryImages(&$db)
  {
    parent::__construct(_JOOM_TABLE_IMAGES, 'id', $db);
  }

  /**
   * Overloaded check function
   *
   * @access  public
   * @return  boolean True on success, false otherwise
   * @since   1.5.5
   */
  function check()
  {
    if(empty($this->imgtitle))
    {
      $this->setError(JText::_('JG_COMMON_ERROR_IMAGE_MUST_HAVE_TITLE'));
      return false;
    }

    if(empty($this->catid))
    {
      $this->setError(JText::_('JG_COMMON_ERROR_NO_CATEGORY_SELECTED'));
      return false;
    }

    /*// Check whether state is allowed regarding selected category
    if($this->published)
    {
      $query = "SELECT
                  published
                FROM
                  "._JOOM_TABLE_CATEGORIES."
                WHERE
                  cid = ".$this->catid;
      $this->_db->setQuery($query);
      if($category = $this->_db->loadObject())
      {
        if(!$category->published)
        {
          $this->published = 0;
          if($this->id)
          {
            JError::raiseNotice('100', JText::sprintf('JG_COMMON_NOT_ALLOWED_TO_PUBLISH_IMAGE', $this->id));
          }
          else
          {
            JError::raiseNotice('100', JText::_('JG_COMMON_NOT_ALLOWED_TO_PUBLISH_NEW_IMAGE'));
          }
        }
      }
    }*/

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

    // Create the alias only if none was entered and if ID of image is already available
    if($this->id && empty($this->alias))
    {
      $this->alias = $this->imgtitle.'-'.$this->id;
    }

    if(!empty($this->alias))
    {
      $this->alias = JFilterOutput::stringURLSafe($this->alias);
    }

    return true;
  }

  /**
   * Overloaded check function
   *
   * @access  public
   * @return  boolean True on success, false otherwise
   * @since   1.5.7
   */
  function store()
  {
    if(trim(str_replace('-', '', $this->alias)) == '')
    {
      // Store the row in order to get the image ID
      if(!parent::store())
      {
        return false;
      }

      $this->alias = $this->imgtitle.'-'.$this->id;
      $this->alias = JFilterOutput::stringURLSafe($this->alias);

      if(trim(str_replace('-', '', $this->alias)) == '')
      {
        $datenow      = & JFactory::getDate();
        $this->alias  = $datenow->toFormat('%Y-%m-%d-%H-%M-%S');
      }
    }

    return parent::store();
  }

  /**
   * Reorders the images according
   * to the latest changes
   *
   * @access  public
   * @return  boolean True on success, false otherwise
   * @since   1.5.5
   */
  function reorderAll()
  {
    $query = 'SELECT DISTINCT catid
                FROM '.$this->_db->nameQuote($this->_tbl);
    $this->_db->setQuery($query);
    $catids = $this->_db->loadResultArray();

    foreach($catids as $catid)
    {
      $this->reorder('catid = '.$catid);
    }
  }

  /**
   * Returns the ordering value to place a new item first in its group
   *
   * @access  public
   * @param   string  $where  query WHERE clause for selecting MAX(ordering).
   * @return  int     The ordring number
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