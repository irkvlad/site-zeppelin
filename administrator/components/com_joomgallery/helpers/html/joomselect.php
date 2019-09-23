<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/helpers/html/joomselect.php $
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
 * Utility class for creating HTML Grids
 *
 * @static
 * @package JoomGallery
 * @since   1.5.5
 */
class JHTMLJoomSelect
{

  /**
   * Construct HTML list of all categories
   *
   * @access  public
   * @param   int     $currentcat An array of category IDs which shall be preselected
   * @param   string  $cname      Name of HTML select according to $_POST variable
   * @param   string  $extra      HTML Code
   * @param   int     $orig       catid - filled if category shall be excluded
   * @param   string  $separator  character to separate the elements in path
   * @return  string  HTML Code
   * @since   1.0.0
   */
  function categoryList($currentcat, $cname = 'catid', $extra = null, $orig = null, $separator  = ' &raquo; ')
  {
    $ambit          = JoomAmbit::getInstance();
    $cats           = $ambit->getCategoryStructure();
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
    $rootcat->name = '';
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
   * Construct HTML list of allowed categories in backend
   *
   * @access  public
   * @param   array   $selected   An array of category IDs which shall be preselected
   * @param   string  $name       The name of the list
   * @param   string  $extra      Some extra HTML code to put into the tag of the list
   * @param   string  $separator  character to separate the elements in path
   * @return  string  The HTML output of the list
   * @since   1.0.0
   */
  function allowedCategoryList($selected, $name = 'jg_category[]', $extra = null, $separator  = ' &raquo; ')
  {
    $ambit          = JoomAmbit::getInstance();
    $cats           = $ambit->getCategoryStructure();
    static $options = array();
    $level          = 0;
    $tmpCat         = array();

    if(empty($options))
    {
      foreach($cats as $key => $cat)
      {
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
        if($cat->owner == 0)
        {
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
      }

      $rootcat = new stdClass();
      $rootcat->cid = 0;
      $rootcat->name = '';
      array_unshift($options, $rootcat);
    }

    $attribs = 'class="inputbox" multiple="multiple" size="6"';
    if($extra)
    {
      $attribs .= ' '.$extra;
    }

    // Build the html select list
    return  JHTML::_('select.genericlist', $options, $name, $attribs, 'cid', 'name', $selected);
  }

  /**
   * Construct HTML list of users
   *
   * @access  public
   * @param   string  $name       Name of the HTML select list to use
   * @param   array   $active     Array of selected users
   * @param   boolean $nouser     True, if 'No user' should be included on top of the list
   * @param   array   $additional Additional entries to add
   * @param   string  $javascript Additional code in the select list
   * @param   boolean $reg        True, if registered users should be ignored
   * @param   int     $multiple   Size of the box if it shall be a multiple select box, 0 otherwise
   * @return  string  The HTML output
   * @since   1.5.5
   */
  function users($name, $active, $nouser = false, $additional = array(), $javascript = null, $reg = false, $multiple = 6)
  {
    $db     = & JFactory::getDBO();
    $config = & JoomConfig::getInstance();

    $type = $config->get('jg_realname') ? 'name' : 'username';

    $users = array();

    if($nouser)
    {
      $users[] = JHTML::_('select.option',  '0', JText::_('JGA_COMMON_NO_USER'));
    }

    foreach($additional as $key => $value)
    {
      $users[] = JHTML::_('select.option',  $key, $value);
    }

    $and = '';
    if($reg)
    {
    // does not include registered users in the list
      $and = ' AND gid > 18';
    }

    $query = 'SELECT
                id AS value,
                '.$type.' AS text
              FROM
                #__users
              WHERE
                      block = 0
                '.$and.'
              ORDER BY
                '.$type;
    $db->setQuery($query);
    $users = array_merge($users, $db->loadObjectList());

    $multiple_box = '';
    if($multiple > 0)
    {
      $multiple_box = ' multiple="multiple" size="'.$multiple.'"';
    }

    return JHTML::_('select.genericlist', $users, $name, 'class="inputbox"'.$multiple_box.' '.$javascript, 'value', 'text', $active);
  }

  /**
   * Callback function for sorting an array of objects to assembled names of
   * categories with alle parent categories
   * @see categoryList()
   *
   * @access  public
   * @param   object  $a
   * @param   object  $b
   * @return  0 if names equal, -1 if a < b, 1 if a > b
   * @since   1.0.0
   */
  function sortCatArray($a,$b)
  {
    return strcmp($a->name, $b->name);
  }
}