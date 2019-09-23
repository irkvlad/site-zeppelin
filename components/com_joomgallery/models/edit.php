<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/models/edit.php $
// $Id: edit.php 3098 2011-05-22 10:48:46Z chraneco $
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
 * JoomGallery Edit Image model
 *
 * @package JoomGallery
 * @since   1.5.5
 */
class JoomGalleryModelEdit extends JoomGalleryModel
{
  /**
   * Image ID
   *
   * @access  protected
   * @var     int
   */
  var $_id;

  /**
   * Image data object
   *
   * @access  protected
   * @var     object
   */
  var $_image;

  /**
   * Images number
   *
   * @var integer
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

    $array = JRequest::getVar('id',  0, '', 'array');

    $this->setId((int)$array[0]);
  }

  /**
   * Method to set the image identifier
   *
   * @access  public
   * @param   int     $id The image ID
   * @return  void
   * @since   1.5.5
   */
  function setId($id)
  {
    // Set new image ID if valid and wipe data
    if(!$id)
    {
      $this->_mainframe->redirect(JRoute::_('index.php?view=userpanel', false), JText::_('JGS_COMMON_NO_IMAGE_SPECIFIED'), 'notice');
    }

    // Check access rights
    if(!$this->_adminlogged)
    {
      $authorised = true;
      if(!$this->_user->get('id'))
      {
        $authorised = false;
      }
      else
      {
        $this->_db->setQuery('SELECT
                                id
                              FROM
                                '._JOOM_TABLE_IMAGES.'
                              WHERE
                                    id = '.$id.'
                                AND owner = '.$this->_user->get('id'));
        if(!$this->_db->loadResult())
        {
          $authorised = false;
        }
      }

      if(!$authorised)
      {
        JError::raiseError(401, JText::_('JGS_COMMON_MSG_NOT_ALLOWED_TO_EDIT_IMAGE'));
      }
    }

    $this->_id    = $id;
    $this->_image = null;
  }

  /**
   * Method to get the image data
   *
   * @access  public
   * @return  object  Image data object
   * @since   1.5.5
   */
  function getImage()
  {
    if($this->_loadImage())
    {
      return $this->_image;
    }

    return false;
  }

  /**
   * Method to load the image data
   *
   * @access  protected
   * @return  boolean   True on success, false otherwise
   * @since   1.5.5
   */
  function _loadImage()
  {
    if(empty($this->_image))
    {
      $row = $this->getTable('joomgalleryimages');

      if(!$row->load($this->_id))
      {
        $row->imgtitle      = $this->_mainframe->getUserStateFromRequest('joom.image.imgtitle',       'imgtitle');
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
        #$row->copy_original = $this->_mainframe->getUserStateFromRequest('joom.image.copy_original',  'copy_original', 0, 'int');
      }
      else
      {
        $row->thumb_url = $this->_ambit->getImg('thumb_url', $row);
      }

      $this->_image = $row;
    }

    return true;
  }

  /**
   * Method to store an edited image
   *
   * @access  public
   * @return  boolean True on success, false otherwise
   * @since   1.5.5
   */
  function store($data = null)
  {
    $row = & $this->getTable('joomgalleryimages');

    if(is_null($data))
    {
      $data = JRequest::get('post', 4);
    }

    // Check whether it is a new category
    if($cid = intval($data['id']))
    {
      $isNew = false;

      // Load image data from the database
      $row->load($cid);

      // Read old category ID
      $catid_old  = $row->catid;
    }
    else
    {
      $isNew = true;
    }

    // Bind the form fields to the images table
    if(!$row->bind($data))
    {
      $this->setError($row->getError());

      return false;
    }

    /*if($isNew)
    {
      // Make sure the record is valid
      if(!$row->check())
      {
        $this->setError($row->getError());

        return false;
      }

      //category path for destination category
      $catpath        = JoomHelper::getCatPath($row->catid);
      // source path original and detail
      $detail_catpath = JoomHelper::getCatPath($data['detail_catid']);
      // source path thumbnail
      $thumb_catpath  = JoomHelper::getCatPath($data['thumb_catid']);

      if(!$this->_newImage($row, $catpath, $detail_catpath, $thumb_catpath))
      {
        $this->setError(JText::_('Unable to create new images'));

        return false;
      }

      // Store the entry to the database in order to get the new ID
      if(!$row->store())
      {
        JError::raiseError(0, $row->getError());

        return false;
      }

      //successfully stored new image
      $row->reorder('catid = '.$row->catid);

      return $row->id;
    }
*/
    /*//clear votes if "clear" checked
    if($data['clearPicVotes'])
    {
      $row->imgvotes = 0;
      $row->imgvotesum = 0;
      // delete votes for picture
      $query = "DELETE FROM #__joomgallery_votes WHERE picid = ".$row->id;
      $this->_db->setQuery($query);
      if(!$this->_db->query())
      {
        JError::raiseError(0, $row->getError());

        return false;
      }
    }
*/
    $move = false;
    if(isset($catid_old) && $catid_old != $row->catid)
    {
      // If there isn't an administrator trying to move the image do an access check for the selected category
      $move     = true;
      $checked  = false;
      if(!$this->_adminlogged)
      {
        // For this check whether the category is in the allowed categories
        $catids = explode(',', $this->_config->get('jg_category'));

        if(!in_array($row->catid, $catids))
        {
          // If it isn't in the allowed categories check whether the user owns the category
          // or if 'jg_userowncatsupload' ('Upload images only on own categories') is set to 'No'
          // whether any user owns the category
          $query = 'SELECT
                      cid
                    FROM
                      '._JOOM_TABLE_CATEGORIES.'
                    WHERE
                          cid = '.$row->catid;
          if($this->_config->get('jg_userowncatsupload'))
          {
             $query .= '
                      AND owner = '.$this->_user->get('id');
          }
          else
          {
             $query .= '
                      AND owner != 0';
          }
          $this->_db->setQuery($query);
          if(!$this->_db->loadResult())
          {
            // If that's not the case a category was selected which the user isn't
            // allowed to select.
            // So store the image in the old category and leave a message.
            $move = false;
            $row->catid = $catid_old;

            $this->_mainframe->enqueueMessage(JText::_('JGS_COMMON_MSG_NOT_ALLOWED_STORE_IMAGE_IN_CATEGORY'), 'notice');
          }

          $checked = true;
        }
      }

      if(!$checked)
      {
        // Check whether the new category is a valid one if the check wasn't done afore
        $query = 'SELECT
                    COUNT(cid)
                  FROM
                    '._JOOM_TABLE_CATEGORIES.'
                  WHERE
                        cid = '.$row->catid;
        $this->_db->setQuery($query);
        if(!$this->_db->loadResult())
        {
          // If that's not the case store the image in the old category and leave a message
          $move = false;
          $row->catid = $catid_old;

          $this->_mainframe->enqueueMessage(JText::_('JGS_COMMON_MSG_NO_VALID_CATEGORY_SELECTED'), 'notice');
        }
      }
    }

    if($move && !$this->moveImage($row, $row->catid, $catid_old))
    {
      $this->_mainframe->enqueueMessage(JText::_('JGS_EDITIMAGE_MSG_COULD_NOT_MOVE_IMAGE'), 'notice');

      return false;
    }
    else
    {
      // Make sure the record is valid
      if(!$row->check())
      {
          $this->setError($row->getError());

          return false;
      }

      // Store the entry to the database
      if(!$row->store())
      {
        $this->setError($row->getError());

        return false;
      }
    }

    // Successfully stored image (and moved)
    $row->reorder('catid = '.$row->catid);
    if(isset($catid_old) && $catid_old != $row->catid)
    {
      $row->reorder('catid = '.$catid_old);
    }

    return $row->id;
  }

  /**
   * Method to delete an image
   *
   * @access  public
   * @return  boolean  True on success, false otherwise
   * @since   1.5.5
   */
  function delete()
  {
    jimport('joomla.filesystem.file');

    $row  = & $this->getTable('joomgalleryimages');

    $row->load($this->_id);

    // Database query to check if there are other images which this
    // thumbnail is assigned to and how many of them exist
    $this->_db->setQuery("SELECT
                            COUNT(id)
                          FROM
                            "._JOOM_TABLE_IMAGES."
                          WHERE
                                imgthumbname = '".$row->imgthumbname."'
                            AND id          != ".$row->id."
                            AND catid        = ".$row->catid
                        );
    $thumb_count = $this->_db->loadResult();

    // Database query to check if there are other images which this
    // detail image is assigned to and how many of them exist
    $this->_db->setQuery("SELECT
                            COUNT(id)
                          FROM
                            "._JOOM_TABLE_IMAGES."
                          WHERE
                                imgfilename = '".$row->imgfilename."'
                            AND id         != ".$row->id."
                            AND catid       = ".$row->catid
                        );
    $img_count = $this->_db->loadResult();

    // Delete the thumbnail if there are no other images
    // in same category assigned to it
    if(!$thumb_count)
    {
      $thumb = $this->_ambit->getImg('thumb_path', $row);
      if(!JFile::delete($thumb))
      {
        // If thumbnail is not deleteable raise an error message and abort
        JError::raiseWarning(100, JText::sprintf('JGS_EDITIMAGE_MSG_COULD_NOT_DELETE_THUMB', $thumb));
        return false;
      }
    }

    // Delete the detail if there are no other detail and
    // original images from same category assigned to it
    if(!$img_count)
    {
      $img = $this->_ambit->getImg('img_path', $row);
      if(!JFile::delete($img))
      {
        // If detail is not deleteable raise an error message and abort
        JError::raiseWarning(100, JText::sprintf('JGS_EDITIMAGE_MSG_COULD_NOT_DELETE_IMAGE', $img));
        return false;
      }
      // Original exists?
      $orig = $this->_ambit->getImg('orig_path', $row);
      if(JFile::exists($orig))
      {
        // Delete it
        if(!JFile::delete($orig))
        {
          // If original is not deleteable raise an error message and abort
          JError::raiseWarning(100, JText::sprintf('JGS_EDITIMAGE_MSG_COULD_NOT_DELETE_ORIG', $orig));
          return false;
        }
      }
    }

    // Delete the corresponding database entries of the comments
    $this->_db->setQuery("DELETE
                            FROM
                              "._JOOM_TABLE_COMMENTS."
                            WHERE
                              cmtpic = ".$this->_id
                        );
    if(!$this->_db->query())
    {
      JError::raiseWarning(100, JText::sprintf('JGS_EDITIMAGE_MSG_COULD_NOT_DELETE_COMMENTS', $this->_id));
    }

    // Delete the corresponding database entries of the name tags
    $this->_db->setQuery("DELETE
                          FROM
                            "._JOOM_TABLE_NAMESHIELDS."
                          WHERE
                            npicid = ".$this->_id
                        );
    if(!$this->_db->query())
    {
      JError::raiseWarning(100, JText::sprintf('JGS_EDITIMAGE_MSG_COULD_NOT_DELETE_NAMETAGS', $this->_id));
    }

    // Delete the database entry of the image
    if(!$row->delete())
    {
      JError::raiseWarning(100, JText::sprintf('JGS_EDITIMAGE_MSG_COULD_NOT_DELETE_IMAGE_DATA', $this->_id));
      return false;
    }

    // Image successfully deleted
    $row->reorder('catid = '.$row->catid);

    return true;
  }

  /**
   * Moves image into another category
   * (The given image data is only stored in the database if old and new category are different from each other)
   *
   * @access  public
   * @param   object  $item       Holds the data of the image to move, if it's not an object we will try to retrieve the data from the database
   * @param   int     $catid_new  The ID of the category to which the image should be moved
   * @param   int     $catid_old  The ID of the old category of the image
   * @return  boolean True on success, false otherwise
   * @since   1.5.5
   */
  function moveImage(&$item, $catid_new, $catid_old = 0)
  {
    jimport('joomla.filesystem.file');

    // If we just have the image ID
    if(!is_object($item))
    {
      $id   = intval($item);
      $item = $this->getTable('joomgalleryimages');
      $item->load($id);
      $catid_old = $item->catid;
    }

    // If the image is already in the correct category return true
    if($catid_new == $catid_old)
    {
      return true;
    }

    $catpath_old  = JoomHelper::getCatPath($catid_old);
    $catpath_new  = JoomHelper::getCatPath($catid_new);

    // Database query to check if there are other images which this
    // thumbnail is assigned to and how many of them exist
    $query = "SELECT
                COUNT(id)
              FROM
                "._JOOM_TABLE_IMAGES."
              WHERE
                    imgthumbname  = '".$item->imgthumbname."'
                AND id           != ".$item->id."
                AND catid         = ".$catid_old;
    $this->_db->setQuery($query);
    $thumb_count = $this->_db->loadResult();

    // Check if thumbnail already exists in source directory and
    // if it doesn't already exist in destination directory.
    // If that's the case the file will not be copied.
    $thumb_created  = false;
    $thumb_source   = $this->_ambit->get('thumb_path').$catpath_old.$item->imgthumbname;
    $thumb_dest     = $this->_ambit->get('thumb_path').$catpath_new.$item->imgthumbname;
    if(JFile::exists($thumb_dest))
    {
      JError::raiseNotice(0, JText::_('JGS_EDITIMAGE_MSG_DEST_THUMB_ALREADY_EXISTS'));

      if($thumb_count && JFile::exists($thumb_source))
      {
        JFile::delete($thumb_source);
      }
    }
    else
    {
      if(!JFile::exists($thumb_source))
      {
        JError::raiseWarning(500, JText::sprintf('JGS_EDITIMAGE_MSG_SOURCE_THUMB_NOT_EXISTS', $thumb_source));

        return false;
      }
      else
      {
        // If there is no image remaining in source directory
        // which uses the file
        if(!$thumb_count)
        {
          // Move the thumbnail
          $result = JFile::move($thumb_source, $thumb_dest);
        }
        else
        {
          // Otherwise just copy the thumbnail in order that it remains in the source directory
          $result = JFile::copy($thumb_source, $thumb_dest);
        }
        // If not succesful raise an error message and abort
        if(!$result)
        {
          JError::raiseWarning(100, JText::sprintf('JGS_EDITIMAGE_MSG_COULD_NOT_MOVE_THUMB', JPath::clean($thumb_dest)));

          return false;
        }

        // Set control variable according to the successful move/copy procedure
        $thumb_created = true;
      }
    }

    // Database query to check if there are other images which this
    // file is assigned to and how many of them exist
    $query = "SELECT
                COUNT(id)
              FROM
                "._JOOM_TABLE_IMAGES."
              WHERE
                    imgfilename = '".$item->imgfilename."'
                AND id         != ".$item->id."
                AND catid       = ".$catid_old;
    $this->_db->setQuery($query);
    $img_count    = $this->_db->loadResult();

    // Same procedure with the detail image
    // In case of error roll previous copy/move procedure back
    $img_created  = false;
    $img_source   = $this->_ambit->get('img_path').$catpath_old.$item->imgfilename;
    $img_dest     = $this->_ambit->get('img_path').$catpath_new.$item->imgfilename;
    if(JFile::exists($img_dest))
    {
      JError::raiseNotice(0, JText::_('JGS_EDITIMAGE_MSG_DEST_IMG_ALREADY_EXISTS'));

      if($img_count && JFile::exists($img_source))
      {
        JFile::delete($img_source);
      }
    }
    else
    {
      if(!JFile::exists($img_source))
      {
        JError::raiseWarning(500, JText::sprintf('JGS_EDITIMAGE_MSG_SOURCE_IMG_NOT_EXISTS', $img_source));

        return false;
      }
      else
      {
        if(!$img_count)
        {
          $result = JFile::move($img_source, $img_dest);
        }
        else
        {
          $result = JFile::copy($img_source, $img_dest);
        }
        if(!$result)
        {
          if($thumb_created)
          {
            if(!$thumb_count)
            {
              JFile::move($thumb_dest, $thumb_source);
            }
            else
            {
              JFile::delete($thumb_dest);
            }
          }

          JError::raiseWarning(100, JText::sprintf('JGS_EDITIMAGE_MSG_COULD_NOT_MOVE_IMG', JPath::clean($img_dest)));

          return false;
        }

        // Set control variable according to the successful move/copy procedure
        $img_created = true;
      }
    }

    // Go on with original image
    $orig_source  = $this->_ambit->get('orig_path').$catpath_old.$item->imgfilename;
    $orig_dest    = $this->_ambit->get('orig_path').$catpath_new.$item->imgfilename;
    if(JFile::exists($orig_dest))
    {
      JError::raiseNotice(0, JText::_('JGS_EDITIMAGE_MSG_DEST_ORIG_ALREADY_EXISTS'));

      if($img_count && JFile::exists($orig_source))
      {
        JFile::delete($orig_source);
      }
    }
    else
    {
      if(JFile::exists($orig_source))
      {
        if(!$img_count)
        {
          $result = JFile::move($orig_source, $orig_dest);
        }
        else
        {
          $result = JFile::copy($orig_source, $orig_dest);
        }
        if(!$result)
        {
          if($thumb_created)
          {
            if(!$thumb_count)
            {
              JFile::move($thumb_dest, $thumb_source);
            }
            else
            {
              JFile::delete($thumb_dest);
            }
          }
          if($img_created)
          {
            if(!$img_count)
            {
              JFile::move($img_dest, $img_source);
            }
            else
            {
              JFile::delete($img_dest);
            }
          }

          JError::raiseWarning(100, JText::sprintf('JGS_EDITIMAGE_MSG_COULD_NOT_MOVE_ORIG', JPath::clean($orig_dest)));

          return false;
        }
      }
    }

    // If all folder operations for the image were successful
    // modify the database entry
    $item->catid    = $catid_new;
    $item->ordering = $item->getNextOrder('catid = '.$catid_new);

    // Make sure the record is valid
    if(!$item->check())
    {
      JError::raiseWarning($item->getError());

      return false;
    }

    // Store the entry to the database
    if(!$item->store())
    {
      JError::raiseWarning($item->getError());

      return false;
    }

    return true;
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
   * Method to publish resp. unpublish an image
   *
   * @access  public
   * @return  boolean True on success, false otherwise
   * @since   1.5.7
   */
  function publish()
  {
    $row = &$this->getTable('joomgalleryimages');

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
}