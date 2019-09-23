<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/models/image.php $
// $Id: image.php 3092 2011-05-20 09:56:58Z aha $
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
 * Image model
 *
 * @package JoomGallery
 * @since   1.5.5
 */
class JoomGalleryModelImage extends JoomGalleryModel
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
  var $_data;

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

    $array = JRequest::getVar('cid',  0, '', 'array');
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
    // Set ID and wipe data
    $this->_id    = $id;
    $this->_data  = null;
  }

  /**
   * Retrieves the image data
   *
   * @access  public
   * @return  object  Image data object
   * @since   1.5.5
   */
  function &getData()
  {
    $row = $this->getTable('joomgalleryimages');
    $row->load($this->_id);

    if(!$this->_id)
    {
      $row->imgtitle      = $this->_mainframe->getUserStateFromRequest('joom.image.imgtitle',       'imgtitle');
      $row->imgtext       = $this->_mainframe->getUserStateFromRequest('joom.image.imgtext',        'imgtext');
      $row->imgauthor     = $this->_mainframe->getUserStateFromRequest('joom.image.imgauthor',      'imgauthor');
      $row->owner         = $this->_mainframe->getUserStateFromRequest('joom.image.owner',          'owner');
      $row->metadesc      = $this->_mainframe->getUserStateFromRequest('joom.image.metadesc',       'metadesc');
      $row->metakey       = $this->_mainframe->getUserStateFromRequest('joom.image.metakey',        'metakey');
      $row->published     = $this->_mainframe->getUserStateFromRequest('joom.image.published',      'published', 1, 'int');
      $row->imgfilename   = $this->_mainframe->getUserStateFromRequest('joom.image.imgfilename',    'imgfilename');
      $row->imgthumbname  = $this->_mainframe->getUserStateFromRequest('joom.image.imgthumbname',   'imgthumbname');
      $row->catid         = $this->_mainframe->getUserStateFromRequest('joom.image.catid',          'catid', 0, 'int');
      // Source category for original and detail picture
      $row->detail_catid  = $this->_mainframe->getUserStateFromRequest('joom.image.detail_catid',   'detail_catid', 0, 'int');
      // Source category for thumbnail
      $row->thumb_catid   = $this->_mainframe->getUserStateFromRequest('joom.image.thumb_catid',    'thumb_catid', 0, 'int');
      $row->copy_original = $this->_mainframe->getUserStateFromRequest('joom.image.copy_original',  'copy_original', 0, 'int');
    }

    $this->_data = $row;

    return $row;
  }

  /**
   * Method to store an image
   *
   * @access  public
   * @param   array   $data     The data of the image to store, if null we will use the data of the current request
   * @param   array   $details  Some additional data of the image to store, if null we will use the data of the current request
   * @param   array   $metadata Meta data of the image to store, if null we will use the data of the current request
   * @param   array   $files    Image files to upload, if null we will use the data of the current request
   * @param   array   $params   Additional parameters of the image, if null we will use the data of the current request
   * @return  int     The image ID on success, boolean false otherwise
   * @since   1.5.5
   */
  function store($data = null, $details = null, $metadata = null, $files = null, $params = null)
  {
    $row = & $this->getTable('joomgalleryimages');

    if(is_null($data))
    {
      $data = JRequest::get('post', 4);
    }
    if(is_null($details))
    {
      $details  = JRequest::getVar('details', array(), 'post', 'array');
    }
    if(is_null($metadata))
    {
      $metadata = JRequest::getVar('meta', array(), 'post', 'array');
    }
    if(is_null($params))
    {
      $params = JRequest::getVar('params', array(), 'post', 'array');
    }

    // Check whether it is a new image
    if($cid = intval($data['cid']))
    {
      $isNew = false;

      // Read image from database
      $row->load($cid);

      // Read old category ID
      $catid_old  = $row->catid;
    }
    else
    {
      $isNew = true;
    }

    // Bind the form fields to the image table
    if(!$row->bind($data))
    {
      JError::raiseError(0, $row->getError());
      return false;
    }
    if(!$row->bind($details))
    {
      JError::raiseError(0, $row->getError());
      return false;
    }
    if(!$row->bind($metadata))
    {
      JError::raiseError(0, $row->getError());
      return false;
    }

    // Additional parameters, if set
    if(count($params))
    {
      // Build parameter INI string
      $txt = array();
      foreach($params as $k => $v)
      {
        $txt[] = $k.'='.$v;
      }
      $row->params = implode("\n", $txt);
    }

    if($isNew)
    {
      // Approve image
      $row->approved = 1;

      // Set date of image
      $date = JFactory::getDate();
      $row->imgdate = $date->toMySQL();

      // Make sure the record is valid
      if(!$row->check())
      {
        $this->setError($row->getError());
        return false;
      }

      // Category path for destination category
      $catpath        = JoomHelper::getCatPath($row->catid);
      // Source path for original and detail image
      $detail_catpath = JoomHelper::getCatPath($data['detail_catid']);
      // Source path for thumbnail
      $thumb_catpath  = JoomHelper::getCatPath($data['thumb_catid']);

      if(!$this->_newImage($row, $catpath, $detail_catpath, $thumb_catpath, $data['copy_original']))
      {
        $this->setError(JText::_('JGA_IMGMAN_MSG_ERROR_CREATING_NEW_IMAGES'));
        return false;
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

      // Successfully stored new image
      $row->reorder('catid = '.$row->catid);
      return $row->id;
    }

    // Get new image files
    if(is_null($files))
    {
      $files = JRequest::getVar('files', '', 'files');
    }

    // Clear votes if 'clearvotes' is checked
    if(isset($data['clearvotes']) && $data['clearvotes'])
    {
      $row->imgvotes    = 0;
      $row->imgvotesum  = 0;
      // Delete votes for image
      $query = "DELETE FROM "._JOOM_TABLE_VOTES." WHERE picid = ".$row->id;
      $this->_db->setQuery($query);
      if(!$this->_db->query())
      {
        JError::raiseError(0, $row->getError());
        return false;
      }
    }
    // Clear hits if 'clearhits' is checked
    if(isset($data['clearhits']) && $data['clearhits'])
    {
      $row->hits = 0;
    }

    // Upload and handle new image files
    $types = array('thumb', 'img', 'orig');
    foreach($types as $type)
    {
      if(isset($files['tmp_name'][$type]) && $files['tmp_name'][$type])
      {
        jimport('joomla.filesystem.file');

        // Possibly the file name has to be changed because of another image format
        $temp_filename = $files['name'][$type];
        $columnname = 'imgfilename';
        if($type == 'thumb')
        {
          $columnnae = 'imgthumbname';
        }
        $filename = $row->$columnname;
        $new_ext = JFile::getExt($temp_filename);
        $old_ext = JFile::getExt($filename);
        if($new_ext != $old_ext)
        {
          $row->$columnname = substr_replace($row->$columnname, '.'.$new_ext, - (strlen($old_ext) + 1));
        }

        // Upload the file
        $file = $this->_ambit->getImg($type.'_path', $row);
        //JFile::delete($file);
        if(!JFile::upload($files['tmp_name'][$type], $file))
        {
          JError::raiseWarning(500, JText::sprintf('JG_UPLOAD_ERROR_UPLOADING', $this->_ambit->getImg($type.'_path', $row)));

          // Revert database entry
          $row->$columnname = $filename;
        }
        // Resize image
        $debugoutput = '';
        switch($type)
        {
          case 'thumb':
            $return = JoomFile::resizeImage($debugoutput,
                                            $file,
                                            $file,
                                            $this->_config->get('jg_useforresizedirection'),
                                            $this->_config->get('jg_thumbwidth'),
                                            $this->_config->get('jg_thumbheight'),
                                            $this->_config->get('jg_thumbcreation'),
                                            $this->_config->get('jg_thumbquality')
                                            );
            break;
          case 'img':
            $return = JoomFile::resizeImage($debugoutput,
                                            $file,
                                            $file,
                                            false,
                                            $this->_config->get('jg_maxwidth'),
                                            false,
                                            $this->_config->get('jg_thumbcreation'),
                                            $this->_config->get('jg_picturequality'),
                                            true
                                            );
            break;
          default:
            break;
        }
      }
    }

    $move = false;
    if(isset($catid_old) && $catid_old != $row->catid)
    {
      $move = true;

      // Check whether the new category is a valid one
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

        $this->_mainframe->enqueueMessage(JText::_('JGA_COMMON_MSG_NO_VALID_CATEGORY_SELECTED'), 'notice');
      }
    }

    // Move the image if necessary (the data is stored in function moveImage because
    // we have ensured that the old and new category ID are different from each other)
    if($move && !$this->moveImage($row, $row->catid, $catid_old))
    {
      $this->_mainframe->enqueueMessage(JText::_('JGA_COULD_NOT_MOVE_IMAGE'), 'notice');

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
    if(isset($catid_old) AND $catid_old != $row->catid)
    {
      $row->reorder('catid = '.$catid_old);
    }

    return $row->id;
  }

  /**
   * Method to copy/move the files for a new image
   *
   * @access  protected
   * @param   object    $row            Holds the data of the new image.
   * @param   string    $catpath        The catpath of the new image
   * @param   string    $detail_catpath The catpath of the detail image to copy
   * @param   string    $thumb_catpath  The catpath of the thumbnail to copy
   * @param   int       $copy_original  Indicates whether the original image should be copied, too
   * @return  boolean   True on success, false otherwise
   * @since   1.5.5
   */
  function _newImage(&$row, $catpath, $detail_catpath, $thumb_catpath, $copy_original)
  {
    jimport('joomla.filesystem.file');

    // Check if thumbnail already exists, if so don't move through the
    // following actions
    if(!JFile::exists($this->_ambit->get('thumb_path').$catpath.$row->imgthumbname))
    {
      $thumb_exists = false;
      // If the destination thumbnail directory doesn't exist
      if(!JFolder::exists($this->_ambit->get('thumb_path').$catpath))
      {
        // Raise an error message and abort
        JError::raiseWarning(100, JText::sprintf('JGA_FOLDER_NOT_EXISTENT', $this->_ambit->get('thumb_path').$catpath));
        return false;
      }
      else
      {
        // Otherwise try to copy the thumbnail from source to destination
        $resthu = JFile::copy(JPath::clean($this->_ambit->get('thumb_path').$thumb_catpath.$row->imgthumbname),
                              JPath::clean($this->_ambit->get('thumb_path').$catpath.$row->imgthumbname));

        // Not succesful
        if(!$resthu)
        {
          // Raise an error message and abort
          JError::raiseWarning(100, JText::sprintf('JGA_ERROR_COPYING_THUMB', $this->_ambit->get('thumb_path').$catpath.$row->imgthumbname));
        }
      }
    }
    else
    {
      // If thumbnail already exists set a control variable to avoid
      // deleting it in case of aborting the function
      $thumb_exists = true;
    }

    // Same procedure like thumbnail for copying the detail image
    // In case of error delete the copied thumbnail from destination
    if(!JFile::exists($this->_ambit->get('img_path').$catpath.$row->imgfilename))
    {
      $img_exists = false;
      if(!JFolder::exists($this->_ambit->get('img_path').$catpath))
      {
        if(!$thumb_exists)
        {
          JFile::delete($this->_ambit->get('thumb_path').$catpath.$row->imgthumbname);
        }
        JError::raiseWarning(100, JText::sprintf('JGA_FOLDER_NOT_EXISTENT', $this->_ambit->get('img_path').$catpath));
        return false;
      }
      else
      {
        $resimg = JFile::copy(JPath::clean($this->_ambit->get('img_path').$detail_catpath.$row->imgfilename),
                              JPath::clean($this->_ambit->get('img_path').$catpath.$row->imgfilename));
        if(!$resimg)
        {
          if(!$thumb_exists)
          {
            JFile::delete($this->_ambit->get('thumb_path').$catpath.$row->imgthumbname);
          }
          JError::raiseWarning(100, JText::sprintf('JGA_ERROR_COPYING_IMAGE', $this->_ambit->get('img_path').$catpath.$row->imgfilename));
          return false;
        }
      }
    }
    else
    {
      $img_exists = true;
    }

    // If setted to create an original image do the following action
    // Otherwise do not copy the image
    if($copy_original)
    {
      // It already exists in destination directory
      if(!JFile::exists($this->_ambit->get('orig_path').$catpath.$row->imgfilename))
      {
        $orig_exists = false;
        if(JFile::exists($this->_ambit->get('orig_path').$detail_catpath.$row->imgfilename))
        {
          // Use the path to original images from now on
          $imagepath = $this->_ambit->get('orig_path').$detail_catpath;
        }
        else
        {
          // Image doesn't exist
          // Useon the path to detail images from now and use detail image as original image
          $imagepath = $this->_ambit->get('img_path').$detail_catpath;
        }
        if(!JFolder::exists($this->_ambit->get('orig_path').$catpath))
        {
          // Directory doesn't exist, so delete the thumbnail and the detail image already created
          if(!$thumb_exists)
          {
            JFile::delete($this->_ambit->get('thumb_path').$catpath.$row->imgthumbname);
          }
          if(!$img_exists)
          {
            JFile::delete($this->_ambit->get('img_path').$catpath.$row->imgfilename);
          }
          // Raise an error message and abort
          JError::raiseWarning(100, JText::sprintf('JGA_FOLDER_NOT_EXISTENT', $this->_ambit->get('orig_path').$catpath));
          return false;
        }
        else
        {
          // Destination directory exists, so try to copy the image from source to destination
          $resorig = JFile::copy(JPath::clean($imagepath.$row->imgfilename),
                                 JPath::clean($this->_ambit->get('orig_path').$catpath.$row->imgfilename));
          // Not succesful
          if(!$resorig)
          {
            // Delete thumbnail and detail image if already exists
            if(!$thumb_exists)
            {
              JFile::delete($this->_ambit->get('thumb_path').$catpath.$row->imgthumbname);
            }
            if(!$img_exists)
            {
              JFile::delete($this->_ambit->get('img_path').$catpath.$row->imgfilename);
            }
            JError::raiseWarning(100, JText::sprintf('JGA_ERROR_COPYING_ORIGINAL%s', $this->_ambit->get('orig_path').$catpath.$row->imgfilename));
            return false;
          }
        }
      }
      else
      {
        $orig_exists = true;
      }
    }

    // Store the record
    // If not succesful raise an error messages and abort
    if(!$row->store())
    {
      // Delete the thumbnail, detail image and original image already created
      if(!$thumb_exists)
      {
        JFile::delete($this->_ambit->get('thumb_path').$catpath.$row->imgthumbname);
      }
      if(!$img_exists)
      {
        JFile::delete($this->_ambit->get('img_path').$catpath.$row->imgfilename);
      }
      if(!$orig_exists)
      {
        JFile::delete($this->_ambit->get('orig_path').$catpath.$row->imgfilename);
      }
      JError::raiseError(0, $row->getError());

      return false;
    }

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

    // If the image is already in the correct category return false, no message will be set
    if($catid_new == $catid_old)
    {
      return false;
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
      JError::raiseNotice(0, JText::_('JGA_COMMON_DEST_THUMB_ALREADY_EXISTS'));

      if($thumb_count && JFile::exists($thumb_source))
      {
        JFile::delete($thumb_source);
      }
    }
    else
    {
      if(!JFile::exists($thumb_source))
      {
        JError::raiseWarning(500, JText::sprintf('JGA_COMMON_SOURCE_THUMB_NOT_EXISTS', $thumb_source));

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
          JError::raiseWarning(100, JText::sprintf('JGA_COULD_NOT_MOVE_THUMB', JPath::clean($thumb_dest)));

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
      JError::raiseNotice(0, JText::_('JGA_COMMON_DEST_IMG_ALREADY_EXISTS'));

      if($img_count && JFile::exists($img_source))
      {
        JFile::delete($img_source);
      }
    }
    else
    {
      if(!JFile::exists($img_source))
      {
        JError::raiseWarning(500, JText::sprintf('JGA_COMMON_SOURCE_IMG_NOT_EXISTS', $img_source));

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

          JError::raiseWarning(100, JText::sprintf('JGA_COULD_NOT_MOVE_IMG', JPath::clean($img_dest)));

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
      JError::raiseNotice(0, JText::_('JGA_COMMON_DEST_ORIG_ALREADY_EXISTS'));

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

          JError::raiseWarning(100, JText::sprintf('JGA_COULD_NOT_MOVE_ORIGINAL', JPath::clean($orig_dest)));

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
}