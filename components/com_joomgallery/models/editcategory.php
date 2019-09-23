<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/models/editcategory.php $
// $Id: editcategory.php 3092 2011-05-20 09:56:58Z aha $
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
 * JoomGallery Edit Category model
 *
 * @package JoomGallery
 * @since   1.5.5
 */
class JoomGalleryModelEditcategory extends JoomGalleryModel
{
  /**
   * Category ID
   *
   * @access  protected
   * @var     int
   */
  var $_id;

  /**
   * Holds the category data
   *
   * @access  protected
   * @var     object
   */
  var $_category;

  /**
   * Thumbnail file names
   *
   * @access  protected
   * @var     array
   */
  var $_thumbnails;

  /**
   * Holds the data of the existing user groups
   *
   * @access  protected
   * @var     array
   */
  var $_groups;

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

    $array = JRequest::getVar('catid',  0, '', 'array');

    $this->setId((int)$array[0]);
  }

  /**
   * Method to set the category ID and wipe data
   *
   * @access  public
   * @param   int     $id Category ID
   * @return  void
   * @since   1.5.5
   */
  function setId($id)
  {
    if($id && !$this->_adminlogged)
    {
      $authorised = true;
      if(!$this->_user->get('id'))
      {
        $authorised = false;
      }
      else
      {
        $this->_db->setQuery('SELECT
                                cid
                              FROM
                                '._JOOM_TABLE_CATEGORIES.'
                              WHERE
                                    cid = '.$id.'
                                AND owner = '.$this->_user->get('id'));
        if(!$this->_db->loadResult())
        {
          $authorised = false;
        }
      }

      if(!$authorised)
      {
        JError::raiseError(401, JText::_('JGS_COMMON_MSG_NOT_ALLOWED_TO_EDIT_CATEGORY'));
      }
    }

    // Set ID and wipe data
    $this->_id        = $id;
    $this->_category  = null;
  }

  /**
   * Retrieves the category data
   *
   * @access  public
   * @return  object  Holds the category data from the database
   * @since   1.5.5
   */
  function getCategory()
  {
    if($this->_loadCategory())
    {
      return $this->_category;
    }

    return false;
  }

  /**
   * Retrieves the thumbnail file names
   *
   * @access  public
   * @return  array   Holds the thumbnail file names
   * @since   1.5.5
   */
  function getThumbnails()
  {
    if($this->_loadThumbnails())
    {
      return $this->_thumbnails;
    }

    return array();
  }

  /**
   * Retrieves the available user groups
   *
   * @access  public
   * @return  array   An array of objects containing the data of the user groups
   * @since   1.5.5
   */
  function getGroups()
  {
    if($this->_loadGroups())
    {
      return $this->_groups;
    }

    return array();
  }

  /**
   * Returns true if the current user is an administrator
   *
   * @access  public
   * @return  boolean True, if the current user is an administrator, false otherwise
   * @since   1.5.5
   */
  function getAdminLogged()
  {
    return $this->_adminlogged;
  }

  /**
   * Method to store a category
   *
   * @access  public
   * @return  int     Category ID on success, boolean false otherwise
   * @since   1.5.5
   */
  function store()
  {
    $row = &$this->getTable('joomgallerycategories');

    $data = JRequest::get('post', 4);

    // Check whether it is a new category
    if($cid = intval($data['cid']))
    {
      $isNew = false;

      // Load category from the database
      $row->load($cid);

      // Read old category name
      $catname_old  = $row->name;
      // Read old parent assignment
      $parent_old   = $row->parent;
    }
    else
    {
      $isNew = true;

      // Check whether the user is allowed to create categories
      if(!$this->_adminlogged)
      {
        if(!$this->_config->get('jg_usercat'))
        {
          $this->_mainframe->redirect(JRoute::_('index.php?view=userpanel', false), JText::_('JGS_EDITCATEGORY_MSG_NOT_ALLOWED_CREATE_CATEGORIES'), 'notice');
        }

        $this->_db->setQuery("SELECT
                                COUNT(cid)
                              FROM
                                "._JOOM_TABLE_CATEGORIES."
                              WHERE
                                owner = ".$this->_user->get('id')."
                            ");
        $count = $this->_db->loadResult();
        if($count >= $this->_config->get('jg_maxusercat'))
        {
          $this->_mainframe->redirect(JRoute::_('index.php?view=usercategories', false), JText::_('JGS_EDITCATEGORY_MSG_NOT_ALLOWED_CREATE_MORE_USERCATEGORIES'), 'notice');
        }
      }
    }

    // Bind the form fields to the category table
    if(!$row->bind($data))
    {
      JError::raiseError(0, $row->getError());
      return false;
    }

    // If there isn't an administrator trying to create or edit the category and if
    // the category will be moved do an access check for the selected parent category
    $valid_parent = true;
    $row->parent = intval($row->parent);
    if(!$this->_adminlogged && ($isNew || $parent_old != $row->parent))
    {
      // For this check whether the parent category is in the allowed categories
      $catids = explode(',', $this->_config->get('jg_usercategory'));

      if((!$this->_config->get('jg_usercategory') || !in_array($row->parent, $catids)) && (!$this->_config->get('jg_usermaincat') || $row->parent != 0))
      {
        // If it isn't in the allowed categories check whether the user owns the parent category
        $query = 'SELECT
                    cid
                  FROM
                    '._JOOM_TABLE_CATEGORIES.'
                  WHERE
                        cid   = '.$row->parent.'
                    AND owner = '.$this->_user->get('id');
        $this->_db->setQuery($query);
        if(!$this->_db->loadResult())
        {
          // If that's not the case a parent category was selected
          // which the user isn't allowed to select.
          $valid_parent = false;
        }
      }
    }

    if($isNew)
    {
      // Check whether the user is allowed to store the category into the specified parent category or as a main category
      if(!$valid_parent)
      {
        $this->_mainframe->redirect(JRoute::_('index.php?view=editcategory', false), JText::_('JGS_EDITCATEGORY_MSG_NOT_ALLOWED_STORE_CATEGORY_IN_PARENT'), 'error');
      }

      // Set the owner of the category
      $row->owner = $this->_user->get('id');

      // Make sure the record is valid
      if(!$row->check())
      {
        $this->setError($row->getError());
        return false;
      }

      // Store the entry to the database in order to get the new ID
      if(!$row->store())
      {
        JError::raiseError(0, $row->getError());
        return false;
      }

      JFilterOutput::objectHTMLSafe($row->name);
      $catpath = JoomFile::fixFilename($row->name).'_'.$row->cid;

      if($row->parent)
      {
        $parent_catpath = JoomHelper::getCatPath($row->parent);
        $catpath        = $parent_catpath . $catpath;
      }

      if(!$this->_createFolders($catpath))
      {
        $this->setError(JText::_('JGS_EDITCATEGORY_MSG_UNABLE_CREATE_FOLDERS'));
        return false;
      }
      else
      {
        $row->catpath = $catpath;

        // Make sure the record is valid
        if(!$row->check())
        {
          $this->setError($row->getError());
          return false;
        }

        // Store the entry to the database
        if(!$row->store())
        {
          JError::raiseError(0, $row->getError());
          return false;
        }
      }

      // New category successfully created
      $row->reorder('parent = '.$row->parent);
      return $row->cid;
    }

    // Move the category folder, if parent assignment or category name changed
    if($parent_old != $row->parent || $catname_old != $row->name)
    {
      // Check whether the user is allowed to move the category into the specified parent category
      if(!$valid_parent)
      {
        // If not store the category in the old parent category and leave a message.
        $row->parent = $parent_old;

        if(!$row->store())
        {
            JError::raiseError(100, $row->getError());

            return false;
        }

        $this->_mainframe->enqueueMessage(JText::_('JGS_COMMON_MSG_NOT_ALLOWED_STORE_IMAGE_IN_CATEGORY'), 'notice');
      }

      // Save old path
      $catpath_old    = $row->catpath;

      // Make the new category title safe
      JFilterOutput::objectHTMLSafe($row->name);

      $catpath = JoomFile::fixFilename($row->name).'_'.$row->cid;
      if($row->parent)
      {
        $parent_catpath = JoomHelper::getCatPath($row->parent);
        $catpath        = $parent_catpath . $catpath;
      }

      // Move folders, only if the catpath has changed
      if($catpath_old != $catpath && !$this->_moveFolders($catpath_old, $catpath))
      {
        $this->setError(JText::_('JGS_EDITCATEGORY_MSG_UNABLE_MOVE_FOLDERS'));
        return false;
      }

      // Update catpath in the database
      $row->catpath = $catpath;

      // Modify catpath of all sub-categories in the database
      $this->_updateNewCatpath($row->cid, $catpath_old, $catpath);
    }

    // Make sure the record is valid
    if(!$row->check())
    {
      $this->setError($row->getError());
      return false;
    }

    // Store the entry to the database
    if(!$row->store())
    {
      JError::raiseError(0, $row->getError());
      return false;
    }

    // Category successfully saved (and moved)
    $row->reorder('parent = '.$row->parent);
    if(isset($parent_old) && $parent_old != $row->parent)
    {
      $row->reorder('parent = '.$parent_old);
    }

    return $row->cid;
  }

  /**
   * Method to delete one or more categories
   *
   * @access  public
   * @return  boolean  True on success, false otherwise
   * @since   1.5.5
   */
  function delete()
  {
    // Database query to check assigned images to category
    $this->_db->setQuery("SELECT
                            COUNT(id)
                          FROM
                            "._JOOM_TABLE_IMAGES."
                          WHERE
                            catid = ".$this->_id
                        );
    if($this->_db->loadResult())
    {
      $msg = JText::sprintf('JGS_EDITCATEGORY_MSG_CATEGORY_CONTAINS_IMAGES', $this->_id);
      $this->setError($msg);
      return false;
    }

    // Database query to check whether there are any sub-categories assigned
    $this->_db->setQuery("SELECT
                            COUNT(cid)
                          FROM
                            "._JOOM_TABLE_CATEGORIES."
                          WHERE
                            parent = ".$this->_id
                        );
    if($this->_db->loadResult())
    {
      $msg = JText::sprintf('JGS_EDITCATEGORY_MSG_CATEGORY_CONTAINS_SUBCATEGORIES', $this->_id);
      $this->setError($msg);
      return false;
    }

    $catpath = JoomHelper::getCatPath($this->_id);
    if(!$this->_deleteFolders($catpath))
    {
      $this->setError(JText::_('JGS_EDITCATEGORY_MSG_UNABLE_DELETE_DIRECTORIES'));
      return false;
    }

    $row = & $this->getTable('joomgallerycategories');
    if(!$row->delete($this->_id))
    {
      $this->setError($row->getError());
      return false;
    }

    // Category successfully deleted
    $row->reorder('parent = '.$row->parent);

    return true;
  }

  /**
   * Loads the category data
   *
   * @access  protected
   * @return  boolean   True
   * @since   1.5.5
   */
  function _loadCategory()
  {
    if(empty($this->_category))
    {
      $row = $this->getTable('joomgallerycategories');

      if($row->load($this->_id))
      {
        $row->catpath       = JoomHelper::getCatPath($this->_id);

        if($row->catimage)
        {
          $row->catimage_src = $this->_ambit->getImg('thumb_url', $row->catimage, null, $this->_id);
        }
        else
        {
          $row->catimage_src = 'images/blank.png';
        }

        /*$row->imgtitle      = $this->_mainframe->getUserStateFromRequest('joom.image.imgtitle',       'imgtitle');
        $row->imgtext       = $this->_mainframe->getUserStateFromRequest('joom.image.imgtext',        'imgtext');
        $row->imgauthor     = $this->_mainframe->getUserStateFromRequest('joom.image.imgauthor',      'imgauthor');
        $row->owner         = $this->_mainframe->getUserStateFromRequest('joom.image.owner',          'owner');
        $row->published     = $this->_mainframe->getUserStateFromRequest('joom.image.published',      'published', 1, 'int');
        $row->imgfilename   = $this->_mainframe->getUserStateFromRequest('joom.image.imgfilename',    'imgfilename');
        $row->imgthumbname  = $this->_mainframe->getUserStateFromRequest('joom.image.imgthumbname',   'imgthumbname');
        $row->catid         = $this->_mainframe->getUserStateFromRequest('joom.image.catid',          'catid', 0, 'int');
        $row->thumb_url     = null;
        //Source category for original and detail picture
        #$row->detail_catid  = $this->_mainframe->getUserStateFromRequest('joom.image.detail_catid',   'detail_catid', 0, 'int');
        //Source category for thumbnail
        #$row->thumb_catid   = $this->_mainframe->getUserStateFromRequest('joom.image.thumb_catid',    'thumb_catid', 0, 'int');
        #$row->copy_original = $this->_mainframe->getUserStateFromRequest('joom.image.copy_original',  'copy_original', 0, 'int');*/
      }
      else
      {
        //$row->thumb_url = $this->_ambit->getImg('thumb_url', $row);*/
        $row->published = 1;
      }

      $this->_category = $row;
    }

    return true;
  }

  /**
   * Loads the file names of all thumbnails of the
   * approved images of the current category
   *
   * @access  protected
   * @return  boolean   True on success, false otherwise
   * @since   1.5.5
   */
  function _loadThumbnails()
  {
    // Let's load the data if it doesn't already exist
    if(empty($this->_thumbnails))
    {
      $this->_db->setQuery("SELECT
                              imgthumbname
                            FROM
                              "._JOOM_TABLE_IMAGES."
                            WHERE
                                     catid = ".$this->_id."
                              AND approved = 1
                            ORDER BY
                              imgthumbname
                          ");
      if(!$array = $this->_db->loadResultArray())
      {
        return false;
      }

      $this->_thumbnails = $array;
    }

    return true;
  }

  /**
   * Loads the name and the ID of all user groups
   *
   * @access  protected
   * @return  boolean   True on success, false otherwise
   * @since   1.5.5
   */
  function _loadGroups()
  {
    // Let's load the data if it doesn't already exist
    if(empty($this->_groups))
    {
      // TODO: What to do if we are in Joomla 1.6?
      $query = "SELECT
                  id AS value,
                  name AS text
                FROM
                  #__groups";

      // If Admin logged all levels will be displayed
      if(!$this->get('AdminLogged') && $this->_id)
      {
        if(empty($this->_category))
        {
          // TODO: What to do if _loadCategory returns false?
          $this->_loadCategory();
        }

        // Read parent category
        $parent = & JTable::getInstance('joomgallerycategories', 'Table');
        $parent->load($this->_category->parent);
        $query .= "
                WHERE
                      id >= ".$parent->access."
                  AND id <= ".$this->_user->get('aid');
      }

      $query .= "
                ORDER BY id";

      $this->_db->setQuery($query);

      if(!$rows = $this->_db->loadObjectList())
      {
        return false;
      }

      $this->_groups = $rows;
    }

    return true;
  }

  /**
   * Update of category path in the database for sub-categories
   * if a parent category has been moved or the name has changed.
   *
   * Recursive call to each level of depth.
   *
   * TODO: Use category structure for this
   *
   * @access  protected
   * @param   string  $catids_values  ID(s) of the categories to update (comma separated)
   * @param   string  $oldpath        Former relative category path
   * @param   string  $newpath        New relative category path
   * @return  void
   * @since   1.0.0
   */
  function _updateNewCatpath($catids_values, &$oldpath, &$newpath)
  {
    // Query for sub-categories with parent in $catids_values
    $this->_db->setQuery("SELECT
                            cid
                          FROM
                            "._JOOM_TABLE_CATEGORIES."
                          WHERE
                            parent IN ($catids_values)
                        ");

    $subcatids = $this->_db->loadResultArray();

    if($this->_db->getErrorNum())
    {
      JError::raiseWarning(500, $this->_db->getErrorMsg());
    }

    // Nothing found, return
    if(!count($subcatids))
    {
      return;
    }

    $row = & JTable::getInstance('joomgallerycategories', 'Table');
    foreach($subcatids as $subcatid)
    {
      $row->load($subcatid);
      $catpath = $row->catpath;

      // Replace former category path with the new one
      $catpath = str_replace($oldpath.'/', $newpath.'/', $catpath);

      // Then save it
      $row->catpath = $catpath;
      if(!$row->store())
      {
        JError::raiseError(500, $row->getError());
      }
    }

    // Split the array in comma separated string
    $catids_values = implode (',', $subcatids);

    // Call again with sub-categories as parent
    $this->_updateNewCatpath($catids_values, $oldpath, $newpath);
  }

  /**
   * Creates the folders for a category
   *
   * @access  protected
   * @param   string    The category path for the category
   * @return  boolean   True on success, false otherwise
   * @since   1.5.5
   */
  function _createFolders($catpath)
  {
    $catpath = JPath::clean($catpath);

    // Create the folder of the category for the original images
    if(!JFolder::create($this->_ambit->get('orig_path').$catpath))
    {
      // If not successfull
      return false;
    }
    else
    {
      // Copy an index.html file into the new folder
      JoomFile::copyIndexHtml($this->_ambit->get('orig_path').$catpath);

      // Create the folder of the category for the detail images
      if(!JFolder::create($this->_ambit->get('img_path').$catpath))
      {
        // If not successful
        JFolder::delete($this->_ambit->get('orig_path').$catpath);
        return false;
      }
      else
      {
        // Copy an index.html file into the new folder
        JoomFile::copyIndexHtml($this->_ambit->get('img_path').$catpath);

        // Create the folder of the category for the thumbnails
        if(!JFolder::create($this->_ambit->get('thumb_path').$catpath))
        {
          // If not successful
          JFolder::delete($this->_ambit->get('orig_path').$catpath);
          JFolder::delete($this->_ambit->get('img_path').$catpath);
          return false;
        }
        else
        {
          // Copy an index.html file into the new folder
          JoomFile::copyIndexHtml($this->_ambit->get('thumb_path').$catpath);
        }
      }
    }

    return true;
  }

  /**
   * Moves folders of an existing category
   *
   * @access  protected
   * @param   string    $src  The source category path
   * @param   string    $dest The destination category path
   * @return  boolean   True on success, false otherwise
   * @since   1.5.5
   */
  function _moveFolders($src, $dest)
  {
    $orig_src   = JPath::clean($this->_ambit->get('orig_path').$src);
    $orig_dest  = JPath::clean($this->_ambit->get('orig_path').$dest);
    $img_src    = JPath::clean($this->_ambit->get('img_path').$src);
    $img_dest   = JPath::clean($this->_ambit->get('img_path').$dest);
    $thumb_src  = JPath::clean($this->_ambit->get('thumb_path').$src);
    $thumb_dest = JPath::clean($this->_ambit->get('thumb_path').$dest);

    // Move the folder of the category for the original images
    $return = JFolder::move($orig_src, $orig_dest);
    if($return !== true)
    {
      // If not successfull
      JError::raiseWarning(100, $return);
      return false;
    }
    else
    {
      // Move the folder of the category for the detail images
      $return = JFolder::move($img_src, $img_dest);
      if($return !== true)
      {
        // If not successful
        JFolder::move($orig_dest, $orig_src);
        JError::raiseWarning(100, $return);
        return false;
      }
      else
      {
        // Move the folder of the category for the thumbnails
        $return = JFolder::move($thumb_src, $thumb_dest);
        if($return !== true)
        {
          // If not successful
          JFolder::move($orig_dest, $orig_src);
          JFolder::move($img_dest, $img_src);
          JError::raiseWarning(100, $return);
          return false;
        }
      }
    }

    return true;
  }

  /**
   * Deletes folders of an existing category
   *
   * @access  protected
   * @param   string    $catpath  The catpath of the category
   * @return  boolean   True on success, false otherwise
   * @since   1.5.5
   */
  function _deleteFolders($catpath)
  {
    if(!$catpath)
    {
      return false;
    }

    $orig_path  = JPath::clean($this->_ambit->get('orig_path').$catpath);
    $img_path   = JPath::clean($this->_ambit->get('img_path').$catpath);
    $thumb_path = JPath::clean($this->_ambit->get('thumb_path').$catpath);

    // Delete the folder of the category for the original images
    if(!JFolder::delete($orig_path))
    {
      // If not successfull
      return false;
    }
    else
    {
      // Delete the folder of the category for the detail images
      if(!JFolder::delete($img_path))
      {
        // If not successful
        if(JFolder::create($orig_path))
        {
          JoomFile::copyIndexHtml($orig_path);
        }

        return false;
      }
      else
      {
        // Delete the folder of the category for the thumbnails
        if(!JFolder::delete($thumb_path))
        {
          // If not successful
          if(JFolder::create($orig_path))
          {
            JoomFile::copyIndexHtml($orig_path);
          }
          if(JFolder::create($img_path))
          {
            JoomFile::copyIndexHtml($img_path);
          }

          return false;
        }
      }
    }

    return true;
  }

  /**
   * Method to publish resp. unpublish a category
   *
   * @access  public
   * @return  boolean True on success, false otherwise
   * @since   1.5.7
   */
  function publish()
  {
    $row = &$this->getTable('joomgallerycategories');

    $row->load($this->_id);

    // Remember old state for check at the end whether the change was successful
    $published = $row->published;

    $row->published = 1 - $row->published;

    if(!$row->check())
    {
      $this->setError($row->getError());
      return false;
    }

    if(!$row->store())
    {
      $this->setError($row->getError());
    }

    // If publishing or unpublishung wasn't successful, return false
    if($row->published == $published)
    {
      return false;
    }

    return true;
  }

  /**
   * Retrieves the data for creating the orderings drop down list
   *
   * @access  public
   * @param   int     $parent Parent category which has to be included into the list independent of it's access state
   * @return  array   An array of JHTML select options with the ordering numbers
   *                  and the category names
   * @since   1.5.7
   */
  function getOrderings($parent = null)
  {
    if(empty($this->_orderings))
    {
      $query = 'SELECT
                  parent,
                  ordering,
                  name
                FROM
                  '._JOOM_TABLE_CATEGORIES;
      if(!$this->_adminlogged)
      {
        // Include parent categories belonging to the owner
        $query .= '
                WHERE
                  owner = '.$this->_user->get('id');

        // Include parent categories which are dedicated for user category creation
        if(!empty($this->_config->jg_usercategory))
        {
          $query .= ' OR parent IN ('.$this->_config->jg_usercategory.')';
        }

        // Include parent category given by optional function parameter
        if($parent != null)
        {
          $query .= ' OR parent = '.(int)$parent;
        }

        // Include top level categories if user is allowed to create them
        if($this->_config->jg_usermaincat)
        {
          $query .= ' OR parent = 0';
        }
      }
      $query .= '
                ORDER BY
                  ordering
              ';

      $this->_db->setQuery($query);

      if(!$cats = $this->_db->loadObjectList())
      {
        $this->setError($this->_db->getError());
        return array();
      }

      $orderings  = array();
      $ordcount   = array();
      $catcount   = count($cats);
      for($i = 0; $i < $catcount; $i++)
      {
        if(!isset($orderings[$cats[$i]->parent]))
        {
          // Add entry for 'First'
          $orderings[$cats[$i]->parent][] = JHTML::_('select.option', 0, '0  '.JText::_('FIRST'));
          $ordcount[$cats[$i]->parent]    = 0;
        }

        $ord = ++$ordcount[$cats[$i]->parent];
        $orderings[$cats[$i]->parent][] = JHTML::_('select.option', $ord, $ord.'  ('.addslashes($cats[$i]->name).')');
      }
      // Add entry for 'Last'
      foreach($orderings as $key => $ordering)
      {
        $ord = ++$ordcount[$key];
        $orderings[$key][] = JHTML::_('select.option', $ord, $ord.'  '.JText::_('LAST'));
      }

      $this->_orderings = $orderings;
    }

    return $this->_orderings;
  }
}