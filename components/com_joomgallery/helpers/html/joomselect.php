<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/helpers/html/joomselect.php $
// $Id: joomselect.php 3092 2011-05-20 09:56:58Z aha $
/******************************************************************************\
**   JoomGallery  1.5.7                                                       **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2011  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

/**
 * Utility class for creating HTML select lists
 *
 * @static
 * @package JoomGallery
 * @since   1.5.5
 */
class JHTMLJoomSelect
{
  /**
   * Construct HTML List of selectable categories
   *
   * @access  public
   * @param   int     $currentcat catid, current cat or parent
   * @param   string  $cname      Name of the HTML element
   * @param   string  $extra      Some extra code to add to the element
   * @param   int     $orig       A category to ignore (its sub-categories will be filtered out, too)
   * @param   string  $separator  A string with which the categories will be separated in the category paths
   * @param   string  $task       Null/filter
   * @return  string  The HTML output
   * @since   1.0.0
   */
  function categoryList($currentcat, $cname = 'catid', $extra = null, $orig = null, $separator  = ' &raquo; ', $task = null)
  {
    $ambit          = JoomAmbit::getInstance();
    $cats           = $ambit->getCategoryStructure(true);
    $options        = array();
    $level          = 0;
    $tmpCat         = array();
    $filter         = ($cname == 'parent' && $orig != null) ? true : false;
    $origfound      = false;
    $filtercatkeys  = array();

    foreach($cats as $key => $cat)
    {
      // Check, if a certain category and it's subcategories have to be filtered out
      // of the list
      if($filter)
      {
        if(!$origfound)
        {
          if($cat->cid == $orig)
          {
            $origfound            = true;
            $filtercatkeys[$orig] = $key;
          }
        }
        else
        {
          if(isset($filtercatkeys[$cat->parent]))
          {
            $filtercatkeys[$cat->cid] = $key;
          }
        }
      }

      // Determine the level of current category
      if($cat->parent == 0)
      {
        // Starting with new root category
        $level = 0;
      }
      else
      {
        if($cat->parent == $tmpCat[$level]->cid)
        {
          // One category level deeper
          $level++;
        }
        else
        {
          // Determine the correct category level, either same level or level(s) up
          for($i = $level; $i > 0; $i--)
          {
            if($cat->parent == $tmpCat[$i]->parent)
            {
              $level = $i;
              break;
            }
          }
        }
      }

      // Remember category for that level to build up path name later on
      $tmpCat[$level] = $cat;

      // Build select option for that category
      $options[$key] = new stdClass();
      $options[$key]->cid  = $cat->cid;
      $options[$key]->name = $tmpCat[0]->name;
      if($level > 0)
      {
        for($i = 1; $i <= $level; $i++)
        {
          $options[$key]->name .= $separator.$tmpCat[$i]->name;
        }
      }
    }

    // Remove categories to be filtered out
    if(!empty($filtercatkeys))
    {
      foreach($filtercatkeys as $key => $value)
      {
        unset($options[$value]);
      }
    }

    $rootcat = new stdClass();
    $rootcat->cid = 0;
    if($task === 'filter')
    {
      // Select box is working as a filter box
      $rootcat->name = JText::_('JGS_COMMON_ALL');
    }
    else
    {
      $rootcat->name = '';
    }
    array_unshift($options, $rootcat);

    $attribs = 'class="inputbox"';
    if($extra)
    {
      $attribs .= ' '.$extra;
    }

    $output = JHTML::_('select.genericlist', $options, $cname, $attribs, 'cid', 'name', $currentcat);

    return $output;
  }

  /**
   * Construct HTML List of categories created from user and allowed for upload
   *
   * @access  public
   * @param   int     $cid        catid, current cat or parent
   * @param   int     $ignorecat  ignore cat as parent
   * @param   string  $task       upload/editimg/editcat/filter
   * @param   string  $extra      some extra code to add to the element
   * @return  string  The HTML output
   * @since   1.0.0
   */
  function userCategoryList($cid, $ignorecat = null, $task = 'upload', $extra = null)
  {
    $config   = & JoomConfig::getInstance();
    $database = & JFactory::getDBO();
    $user     = & JFactory::getUser();

    $allowedcats  = '';
    $parentignore = array();
    $ignorecats   = '';

    // Get categories defined in backend which allow uploads by users
    if($task == 'upload' || $task == 'editimg' || $task == 'filter')
    {
      if(!empty($config->jg_category))
      {
        $allowedcats = $config->jg_category;
      }
    }
    else
    {
      // Get categories defined in backend which allow creating subcategories by users
      if(!empty($config->jg_usercategory))
      {
        $allowedcats = $config->jg_usercategory;
      }
      else
      {
        $allowedcats = '';
      }
    }

    if($task == 'editcat')
    {
      // Get cat and subcats to be ignored as possible parent
      $ignorecats = array();
      $subcats    = JoomHelper::getAllSubCategories($ignorecat, true, true, true);
      $ignorecats = implode(',', $subcats);
    }

    $query = "  SELECT
                  cid,
                  parent,
                  name
                FROM
                  "._JOOM_TABLE_CATEGORIES;

    if (($task == 'upload' || $task == 'editimg' || $task == 'filter') && !$config->jg_userowncatsupload)
    {
      // Get categories created by all users
      $query .= " WHERE owner != 0";
    }
    else
    {
      // task = editimg/editcat/upload in own categories only
      // Get categories created by user
      $query .= " WHERE owner = ".$user->get('id');
    }
    if(!empty($ignorecats))
    {
       $query .= ' AND cid NOT IN ('.$ignorecats.')';
    }

    if(!empty($allowedcats))
    {
       $query .= ' OR cid IN ('.$allowedcats.')';
    }
    $query .= ' OR cid = '.$cid;

    $database->setQuery($query);
    $rows = $database->loadObjectList('cid');

    $countrows = count($rows);

    if($task == 'editcat')
    {
      $cname = 'parent';
    }
    else
    {
      $cname = 'catid';
    }

    if($countrows == 0)
    {
      // Return a hidden field in order to avoid JavaScript errors
      return '-<input type="hidden" name="'.$cname.'" value="0" />';
    }

    $catPathnamesarr = array();

    // Loop through all cats and construct the path name
    foreach($rows as $key => $cat)
    {
      if($cat->parent != 0)
      {
        // Use helper function to get the parents
        $catPathnamesarr[$key] = JHTML::_('joomgallery.categorypath', $cat->cid, ' &raquo; ', false, false, true);
      }
      else
      {
        $catPathnamesarr[$key] = $cat->name;
      }
    }

    // Fill the array with full pathnames
    foreach($catPathnamesarr as $key => $catPathname)
    {
      $rows[$key]->name = $catPathname;
    }

    // Sort the array with key pathname if more than one element
    if(count($rows) > 1)
    {
      usort($rows, array('JHTMLJoomSelect', 'sortCatArray'));
    }

    // Add empty entry if users are allowed to create main categories
    if($config->get('jg_usermaincat') || ($task === 'filter'))
    {
      $rootcat = new stdClass();
      $rootcat->cid = 0;
      if($task === 'filter')
      {
        // Add an 'All' entry in the case that the select box is working as a filter box
        $rootcat->name = JText::_('JGS_COMMON_ALL');
      }
      else
      {
        $rootcat->name = '';
      }
      array_unshift($rows, $rootcat);
    }

    $attribs = 'class="inputbox"';
    if($extra)
    {
      $attribs .= ' '.$extra;
    }

    $output = JHTML::_('select.genericlist', $rows, $cname, $attribs, 'cid', 'name', $cid);

    return $output;
  }

  /**
   * Construct HTML list of users
   *
   * @access  public
   * @param   array   $active     Array of selected users
   * @param   string  $name       Name of the HTML select list to use
   * @param   boolean $nouser     True, if 'No user' should be included on top of the list
   * @param   string  $javascript Additional code in the select list
   * @param   boolean $reg        True, if registered users should be ignored
   * @param   boolean $realname   True, if the real name of the users shall be used instead of their user names
   * @return  string  The HTML output
   * @since   1.5.5
   */
  function users($active, $name, $nouser = false, $javascript = null, $reg = false, $realname = false)
  {
    $db = & JFactory::getDBO();

    $and = '';
    if($reg)
    {
    // Does not include registered users in the list
      $and = ' AND gid > 18';
    }

    $username = 'username';
    if($realname)
    {
      // The real name should be used in the select box
      $username = 'name';
    }

    $query = 'SELECT
                id AS value,
                '.$username.' AS text
              FROM
                #__users
              WHERE
                      block = 0
                '.$and.'
              ORDER BY
                '.$username;
    $db->setQuery($query);
    if($nouser)
    {
      $users[] = JHTML::_('select.option',  '0', JText::_('JGS_DETAIL_NAMETAGS_SELECT_USER'));
      $users = array_merge($users, $db->loadObjectList());
    }
    else
    {
      $users = $db->loadObjectList();
    }

    return JHTML::_('select.genericlist', $users, $name, 'class="inputbox"'. $javascript, 'value', 'text', $active);
  }

  /**
   * Callback function for sorting an array of objects to assembled names of
   * categories with alle parent categories
   * @see categoryList() and userCategoryList()
   *
   * @access  public
   * @param   object $a Element one
   * @param   object $b Element two
   * @return  int     0 if names equal, -1 if a < b, 1 if a > b
   * @since   1.0.0
   */
  function sortCatArray($a, $b)
  {
    return strcmp($a->name, $b->name);
  }
}