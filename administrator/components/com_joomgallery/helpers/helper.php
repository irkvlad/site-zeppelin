<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/helpers/helper.php $
// $Id: helper.php 3092 2011-05-20 09:56:58Z aha $
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
 * JoomGallery Global Helper for the Backend
 *
 * @static
 * @package JoomGallery
 * @since 1.5.5
 */
class JoomHelper
{

  /**
   * Returns all parent categories of a specific category
   *
   * @access  public
   * @param   int     $category The ID of the specific child category
   * @return  array   An array of parent category objects with cid,name,parent
   * @since   1.5.5
   */
  function getAllParentCategories(&$category)
  {
    // Get category structure from ambit
    $ambit    = JoomAmbit::getInstance();
    $cats = $ambit->getCategoryStructure();
    $parents  = array();
    $stopindex = count($cats);
    $startindex = 0;

    // Search for category in $cats
    for ($x=0; $x < $stopindex ; $x++)
    {
      if ($cats[$x]->cid == $category)
      {
        $startindex = $x;
        // Insert category itself in array
        $parents[$category]->cid       = $category;
        $parents[$category]->name      = $cats[$x]->name;
        $parents[$category]->parent    = $cats[$x]->parent;
        $parents[$category]->published = $cats[$x]->published;
      }
    }
    $parentcat = $cats[$startindex]->parent;
    // Iterate reverse from precedor of cat in $startindex to find the parents
    for ($x = $stopindex - 1; $x >= 0; $x--)
    {
      // Parent found
      if ($cats[$x]->cid == $parentcat)
      {
          $parents[$cats[$x]->cid]->cid       = $cats[$x]->cid;
          $parents[$cats[$x]->cid]->name      = $cats[$x]->name;
          $parents[$cats[$x]->cid]->parent    = $cats[$x]->parent;
          $parents[$cats[$x]->cid]->published = $cats[$x]->published;
          $parentcat = $cats[$x]->parent;

          // Rootparent found
          if ($parentcat == 0)
          {
            break;
          }
      }
    }
    // Reverse the array to get the right order
    $parents = array_reverse($parents, true);
    return $parents;
  }

  /**
   * Returns all categories and their sub-categories with published or no images
   *
   * @access  public
   * @param   int     $cat          Category ID
   * @param   boolean $rootcat      True, if $cat shall also be returned as an
   *                                element of the array
   * @param   boolean $noimgcats    True if @return shall also include categories
   *                                with no images
   * @param   boolean $all          True if all categories shall be selected, defaults to true
   * @param   boolean $nohiddencats True, if sub-categories of hidden categories should be
   *                                filtered out, defaults to false
   * @return  array   An array of found categories
   * @since   1.5.5
   */
  function getAllSubCategories($cat, $rootcat = false, $noimgcats = false, $all = true, $nohiddencats = false)
  {
    $cat              = (int) $cat;
    $parentcats       = array();
    $parentcats[$cat] = true;
    $branchfound      = false;
    $allsubcats       = array();

    // Get category structure from ambit
    $ambit = JoomAmbit::getInstance();
    $cats  = $ambit->getCategoryStructure($all);

    // Determine start index in separate loop for better performance
    $startindex = 0;
    $stopindex = count($cats);
    $catfound = false;
    for($i = 0; $i < $stopindex; $i++)
    {
      if($cats[$i]->cid == $cat)
      {
        $startindex = $i;
        $catfound = true;
        break;
      }
    }

    if (!$catfound)
    {
      return $allsubcats;
    }

    // Find all cats which are subcategories of cat
    $hidden = array();
    for($i = $startindex + 1; $i < $stopindex; $i++)
    {
      $parentcat = $cats[$i]->parent;
      if(isset($parentcats[$parentcat]))
      {
        $parentcat = $cats[$i]->cid;
        $parentcats[$parentcat] = true;
        $branchfound = true;

        // Don't include hidden sub-categories
        if($nohiddencats)
        {
          if($cats[$i]->hidden)
          {
            $hidden[$cats[$i]->cid] = true;
          }
          else
          {
            if(isset($hidden[$cats[$i]->parent]))
            {
              $hidden[$cats[$i]->cid] = true;
            }
          }
        }

        if(!isset($hidden[$cats[$i]->cid]) || !isset($hidden[$cats[$i]->parent]))
        {
          if(!$noimgcats)
          {
            // Only categories with images
            if($cats[$i]->piccount > 0)
            {
              // Subcategory with images in array
              $allsubcats[] = $cats[$i]->cid;
            }
          }
          else
          {
            $allsubcats[] = $cats[$i]->cid;
          }
        }
      }
      else
      {
        if($branchfound)
        {
          // branch has been processed completely
          break;
        }
      }
    }

    // Add rootcat
    if($rootcat)
    {
      if(!$noimgcats)
      {
        // Includes images
        if($cats[$startindex]->piccount > 0)
        {
          $allsubcats[] = $cat;
        }
      }
      else
      {
        $allsubcats[] = $cat;
      }
    }

    return $allsubcats;
  }

  /**
   * Wrap text
   *
   * @param   string  $text Text to wrap
   * @param   int     $nr   Number of chars to wrap
   * @return  string  Wrapped text
   * @since   1.0.0
   */
  function processText($text, $nr = 40)
  {
    $mytext   = explode(' ', trim($text));
    $newtext  = array();
    foreach($mytext as $k => $txt)
    {
      if(strlen($txt) > $nr)
      {
        $txt  = wordwrap($txt, $nr, '- ', 1);
      }
      $newtext[]  = $txt;
    }

    return implode(' ', $newtext);
  }

  /**
   * Reads the category path from array.
   * If not set read db and add to array.
   *
   * @param   int     $catid  The ID of the category
   * @return  string  The category path
   * @since   1.0.0
   */
  function getCatPath($catid)
  {
    static $catpath = array();

    if(!isset($catpath[$catid]))
    {
      $database = & JFactory::getDBO();
      $database->setQuery(" SELECT
                              catpath
                            FROM
                              "._JOOM_TABLE_CATEGORIES."
                            WHERE
                              cid= ".$catid
                          );

      if(!$path = $database->loadResult())
      {
        $catpath[$catid] = '';
      }
      else
      {
        $catpath[$catid] = $path.'/';
      }
    }

    return $catpath[$catid];
  }

  /**
   * Resort an array of category objects to ensure, that a parent category
   * is always listed before it's child categories. The function expects a $cats
   * category list, which is already sorted by parent ascending.
   *
   * @access  public
   * @param   array   $cats         Array of category objects to resort
   * @param   array   $catssorted   Resorted category object array
   * @return  void
   * @since   1.5.5
   */
  function sortCategoryList(&$cats, &$catssorted)
  {
    // First create a two dimensional array containing the child category objects
    // for each parent category id
    $children = array();
    foreach($cats as $cat)
    {
      $pcid = $cat->parent;
      $list = isset($children[$pcid]) ? $children[$pcid] : array();
      $list[] = $cat;
      $children[$pcid] = $list;
    }

    // Now resort the given $cats array with the help of the $children array
    JoomHelper::sortCategoryListRecurse(0, $children, $catssorted);
  }

  /**
   * Helper function for JoomHelper::sortCategoryList().
   *
   * @access  public
   * @param   int     $catid          Category id
   * @param   array   $children       Two dimensional array containing the child
   *                                  category objects for each parent category id
   * @param   array   $catssorted     Resorted category object array
   * @return  void
   * @since   1.5.6
   */
  function sortCategoryListRecurse($catid, &$children, &$catssorted)
  {
    if(isset($children[$catid]))
    {
      foreach($children[$catid] as $cat)
      {
        $catssorted[] = $cat;
        JoomHelper::sortCategoryListRecurse($cat->cid, $children, $catssorted);
      }
    }
  }

  /**
   * Returns the rating clause for an SQL - query dependent on the
   * rating calculation method selected.
   *
   * @access  public
   * @param   string  $tablealias   Table alias
   * @return  string  Rating clause
   * @since   1.5.6
   */
  function getSQLRatingClause($tablealias = '')
  {
    $db                   = & JFactory::getDBO();
    $config               = & JoomConfig::getInstance();
    static $avgimgvote    = 0.0;
    static $avgimgrating  = 0.0;
    static $avgdone       = false;

    $maxvoting            = $config->get('jg_maxvoting');
    $imgvotesum           = 'imgvotesum';
    $imgvotes             = 'imgvotes';
    if($tablealias != '')
    {
      $imgvotesum = $tablealias.'.'.$imgvotesum;
      $imgvotes   = $tablealias.'.'.$imgvotes;
    }

    // Standard rating clause
    $clause = 'ROUND(LEAST(IF(imgvotes > 0, '.$imgvotesum.'/'.$imgvotes.', 0.0), '.(float)$maxvoting.'), 2)';

    // Advanced (weigthed) rating clause (Bayes)
    if($config->get('jg_ratingcalctype') == 1)
    {
      if(!$avgdone)
      {
        // Needed values for weighted rating calculation
        $db->setQuery('SELECT
                         count(*) As imgcount,
                         SUM(imgvotes) As sumimgvotes,
                         SUM(imgvotesum/imgvotes) As sumimgratings
                       FROM
                         '._JOOM_TABLE_IMAGES.'
                        WHERE
                          imgvotes > 0'
                      );
        $row = $db->loadObject();
        if($row != null)
        {
          if($row->imgcount > 0)
          {
            $avgimgvote   = round($row->sumimgvotes / $row->imgcount, 2 );
            $avgimgrating = round($row->sumimgratings / $row->imgcount, 2);
            $avgdone      = true;
          }
        }
      }
      if($avgdone)
      {
        $clause = 'ROUND(LEAST(IF(imgvotes > 0, (('.$avgimgvote.'*'.$avgimgrating.') + '.$imgvotesum.') / ('.$avgimgvote.' + '.$imgvotes.'), 0.0), '.(float)$maxvoting.'), 2)';
      }
    }

    return $clause;
  }
  /**
   * Returns the rating of an image
   *
   * @access  public
   * @param   string  $imgid   Image id to get the rating for
   * @return  float   Rating
   * @since   1.5.6
   */
  function getRating($imgid)
  {
    $db     = & JFactory::getDBO();
    $rating = 0.0;

    $db->setQuery('SELECT
                    '.JoomHelper::getSQLRatingClause().' AS rating
                  FROM
                    '._JOOM_TABLE_IMAGES.'
                  WHERE
                    id = '.$imgid
                 );
    if(($result = $db->loadResult()) != null)
    {
      $rating = $result;
    }

    return $rating;
  }
}