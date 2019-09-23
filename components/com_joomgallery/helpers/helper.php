<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/helpers/helper.php $
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
 * JoomGallery Global Helper
 *
 * @static
 * @package JoomGallery
 * @since   1.5.5
 */
class JoomHelper
{
  /**
   * Fix text for output in JavaScript Code
   *
   * @access  public
   * @param   string  $text The text to fix
   * @return  string  The fixed text
   * @since   1.0.0
   */
  function fixForJS($text)
  {
    $text = str_replace("\"", "\&quot;", $text);
    $text = str_replace("'",  "\'", $text);
    $text = preg_replace('/[\n\t\r]*/', '', $text);

    return $text;
  }

  /**
   * Wrap text
   *
   * @access  public
   * @param   string  $text The text to wrap
   * @param   int     $nr   Number of chars to wrap
   * @return  string  The wrapped text
   * @since   1.0.0
   */
  function processText($text, $nr = 40)
  {
    $mytext   = explode(' ', trim($text));
    $newtext  = array();
    $config = & JoomConfig::getInstance();

    foreach($mytext as $k => $txt)
    {
      if(strlen($txt) > $nr)
      {
        // Do not wrap BBcode [url] and [email]
        if(
              !$config->get('jg_bbcodesupport')
          ||  (   stripos($txt,'[url') === false && stripos($txt,'[/url]') === false
              &&  stripos($txt,'[email') === false && stripos($txt,'[/email]') === false
              )
          )
        {
          $txt  = wordwrap($txt, $nr, '- ', true);
        }
      }

      $newtext[]  = $txt;
    }

    return implode(' ', $newtext);
  }

  /**
   * Reads the category path from array
   * If not set read database and add to array
   *
   * @access  public
   * @param   int     $catid  The ID of the category of which the catpath is requested
   * @return  string  The catpath of the category
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
        $catpath[$catid] = '/';
      }
      else
      {
        $catpath[$catid] = $path.'/';
      }
    }

    return $catpath[$catid];
  }

  /**
   * Check the upload time of image and determine if it is within a setted span
   * of time and so marked as NEW
   *
   * @access  public
   * @param   int     $uptime   Upload time in seconds
   * @param   int     $daysnew  Span of time in days
   * @return  string  The HTML output of the new icon or empty string
   * @since   1.0.0
   */
  function checkNew($uptime, $daysnew)
  {
    $isnew = '';

    // Get the seconds from starting time of Unix Epoch (January 1 1970 00:00:00 GMT)
    // to now in seconds
    $thistime   = time();
    // Calculate the seconds according to days setted for new
    // See configuration manager
    $timefornew = 86400 * $daysnew;
    // If span of time since upload is lower than span of time setted in config
    if(($thistime - strtotime($uptime)) < $timefornew)
    {
      // Show the 'new' image
      $isnew = JHTML::_('joomgallery.icon', 'new.png', 'New');
    }

    return $isnew;
  }

  /**
   * Checks images of category and possibly sub-categories
   * and calls checkNew() to decide if NEW
   *
   * @access  public
   * @param   string  $catids_values  IDs of categories ('x,y')
   * @return  string  HTML output of the new icon or empty string
   * @since   1.0.0
   */
  function checkNewCatg($cid)
  {
    $config = & JoomConfig::getInstance();
    $db     = & JFactory::getDBO();
    $user   = & JFactory::getUser();
    $isnewcat = '';

    // Get all sub-categories including the current category
    $catids = JoomHelper::getAllSubCategories($cid, true);

    if(count($catids))
    {
      // Implode array to a comma separated string if more than one element in array
      $catid_values = implode(',', $catids);
      // Search in db the categories in $catids_values
      $db->setQuery( "SELECT
                        MAX(imgdate)
                      FROM
                        "._JOOM_TABLE_IMAGES." AS a
                      LEFT JOIN
                        "._JOOM_TABLE_CATEGORIES." AS c
                      ON
                        c.cid = a.catid
                      WHERE
                        a.catid IN ($catid_values)
                    ");
      $maxdate = $db->loadResult();
      if($db->getErrorNum())
      {
        JError::raiseWarning(500, $db->getErrorMsg());
      }

      // If maxdate = NULL no image found
      // Otherwise check the date to 'new'
      if($maxdate)
      {
        $isnewcat = JoomHelper::checkNew($maxdate, $config->get('jg_catdaysnew'));
      }
    }

    // If no new found at all
    // Return empty string
    return $isnewcat;
  }

  /**
   * Construct page title
   *
   * @access  public
   * @param   string  $text     The structure of the page title to use
   * @param   string  $catname  The name of the category which is currently displayed
   *                            or in which the currently displayed image is
   * @param   string  $imgtitle The name of the image which is currently displayed
   * @return  string  modified title
   * @since   1.0.0
   */
  function createPagetitle($text, $catname = '', $imgtitle = '')
  {
    preg_match_all('/(\[\!.*?\!\])/i', $text, $results);
    define('JGS_COMMON_CATEGORY', JText::_('JGS_COMMON_CATEGORY'));
    define('JGS_COMMON_IMAGE', JText::_('JGS_COMMON_IMAGE'));
    for($i = 0; $i<count($results[0]); $i++)
    {
      $replace = str_replace('[!', '', $results[0][$i]);
      $replace = str_replace('!]', '', $replace);
      $replace = trim($replace);
      $replace2 = str_replace('[!', '\[\!', $results[0][$i]);
      $replace2 = str_replace('!]', '\!\]', $replace2);
      $text = preg_replace('/('.$replace2.')/ie', $replace, $text);
    }
    $text = str_replace('#cat', $catname,   $text);
    $text = str_replace('#img', $imgtitle,  $text);

    return $text;
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
   * @param   boolean $all          True if all categories shall be selected, defaults to false
   * @param   boolean $nohiddencats True, if hidden categories and it's subcategories should be
   *                                filtered out, defaults to true
   * @return  array   An array of found categories
   * @since   1.5.5
   */
  function getAllSubCategories($cat, $rootcat = false, $noimgcats = false, $all = false, $nohiddencats = true)
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

        if(!isset($hidden[$cats[$i]->cid]))
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
          // Branch has been processed completely
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
   * Returns all parent categories of a specific category
   *
   * @access  public
   * @param   int     $category The ID of the specific child category
   * @param   boolean $child    True, if category itself shall also be returned, defaults to false
   * @param   boolean $all      True if all categories shall be shown, defaults to false
   * @return  array   An array of parent category objects with cid,name,parent
   * @since   1.5.5
   */
  function getAllParentCategories($category, $child = false, $all = false)
  {
    // Get category structure from ambit
    $ambit      = JoomAmbit::getInstance();
    $cats       = $ambit->getCategoryStructure($all);
    $parents    = array();
    $stopindex  = count($cats);
    $startindex = 0;

    // Search for category in $cats
    for($x = 0; $x < $stopindex ; $x++)
    {
      if($cats[$x]->cid == $category)
      {
        $startindex = $x;
        // Insert category itself in array if needed
        if($child)
        {
          $parents[$category]->cid       = $category;
          $parents[$category]->name      = $cats[$x]->name;
          $parents[$category]->parent    = $cats[$x]->parent;
          $parents[$category]->published = $cats[$x]->published;
        }
      }
    }

    $parentcat = $cats[$startindex]->parent;
    // Iterate reverse from precedor of cat in $startindex to find the parents
    for($x = $stopindex-1; $x >= 0; $x--)
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
   * Returns all available smileys in an array
   *
   * @access  public
   * @return  array   An array with the smileys
   * @since   1.5.0
   */
  function getSmileys()
  {
    $config = & JoomConfig::getInstance();

    $path = 'components/com_joomgallery/assets/images/smilies/'.$config->jg_smiliescolor.'/';

    $smileys                      = array();
    $smileys[':smile:']           = $path.'sm_smile.gif';
    $smileys[':cool:']            = $path.'sm_cool.gif';
    $smileys[':grin:']            = $path.'sm_biggrin.gif';
    $smileys[':wink:']            = $path.'sm_wink.gif';
    $smileys[':none:']            = $path.'sm_none.gif';
    $smileys[':mad:']             = $path.'sm_mad.gif';
    $smileys[':sad:']             = $path.'sm_sad.gif';
    $smileys[':dead:']            = $path.'sm_dead.gif';

    if($config->get('jg_anismilie'))
    {
      $smileys[':yes:']           = $path.'sm_yes.gif';
      $smileys[':lol:']           = $path.'sm_laugh.gif';
      $smileys[':smilewinkgrin:'] = $path.'sm_smilewinkgrin.gif';
      $smileys[':razz:']          = $path.'sm_bigrazz.gif';
      $smileys[':roll:']          = $path.'sm_rolleyes.gif';
      $smileys[':eek:']           = $path.'sm_bigeek.gif';
      $smileys[':no:']            = $path.'sm_no.gif';
      $smileys[':cry:']           = $path.'sm_cry.gif';
    }

    $dispatcher = & JDispatcher::getInstance();
    $dispatcher->trigger('onJoomGetSmileys', array(&$smileys));

    return $smileys;
  }

  /**
   * At the moment just a wrapper function for JModuleHelper::getModules()
   *
   * @access  public
   * @param   string  $pos  The position name
   * @return  array   An array of module objects
   * @since   1.5.0
   */
  function getModules($pos)
  {
    $view     = JRequest::getCmd('view');

    $position = 'jg_'.$pos;
    $modules  = & JModuleHelper::getModules($position);

    $views = array( ''            => 'gal',
                    'gallery'     => 'gal',
                    'category'    => 'cat',
                    'detail'      => 'dtl',
                    'toplist'     => 'tpl',
                    'search'      => 'sea',
                    'favourites'  => 'fav',
                    'userpanel'   => 'usp',
                    'upload'      => 'upl'
                  );
    if(isset($views[$view]))
    {
      $position = $position.'_'.$views[$view];
      $ind_mods = & JModuleHelper::getModules($position);
      $modules  = array_merge($modules, $ind_mods);
    }

    $ind_mods = & JModuleHelper::getModules($position.'_'.$view);
    $modules  = array_merge($modules, $ind_mods);

    return $modules;
  }

  /**
   * Renders modules provided by getModules()
   *
   * @access  public
   * @param   string  $pos  The position name
   * @return  array   An array of rendered modules
   * @since   1.5.5
   */
  function getRenderedModules($pos)
  {
    static $renderer;

    $modules = JoomHelper::getModules($pos);

    if(count($modules))
    {
      if(!isset($renderer))
      {
        $document = &JFactory::getDocument();
        $renderer = $document->loadRenderer('module');
      }

      $style  = -2;
      $params = array('style' => $style);

      foreach($modules as $key => $module)
      {
        $modules[$key]->rendered = $renderer->render($module, $params);
      }
    }

    return $modules;
  }

  /**
   * Sets all params for the output depending on the view and the config settings
   *
   * @access  public
   * @param   $params The parameter object
   * @return  void
   * @since   1.5.5
   */
  function prepareParams(&$params)
  {
    $config = & JoomConfig::getInstance();
    $user   = & JFactory::getUser();
    $view   = JRequest::getCmd('view');

    // Gallery Title
    if(!JRequest::getInt('Itemid') && $config->get('jg_showgalleryhead'))
    {
      $params->set('show_page_title', 1);
    }

    // Pathway
    if(!$params->get('disable_global_info') && ($view != 'gallery' || $config->get('jg_showgallerysubhead')))
    {
      // Pathway in the header
      if($config->get('jg_showpathway') == 1 || $config->get('jg_showpathway') == 3)
      {
        $params->set('show_header_pathway', 1);
      }
      // Pathway in the footer
      if($config->get('jg_showpathway') >= 2)
      {
        $params->set('show_footer_pathway', 1);
      }
    }

    // Search in the header
    if(!$params->get('disable_global_info') && ($config->get('jg_search') == 1 || $config->get('jg_search') == 3))
    {
      $params->set('show_header_search', 1);
    }
    //Search in the footer
    if(!$params->get('disable_global_info') && $config->get('jg_search') >= 2)
    {
      $params->set('show_footer_search', 1);
    }

    // Backlink in the header
    if(!$params->get('disable_global_info') && ($config->get('jg_showbacklink') == 1 || $config->get('jg_showbacklink') == 3))
    {
      $params->set('show_header_backlink', 1);
    }
    // Backlink in the footer
    if(!$params->get('disable_global_info') && $config->get('jg_showbacklink') >= 2)
    {
      $params->set('show_footer_backlink', 1);
    }

    // All images
    if(!$params->get('disable_global_info'))
    {
      // All Images in the header
      if($config->get('jg_showallpics') == 1 || $config->get('jg_showallpics') == 3)
      {
        $params->set('show_header_allpics', 1);
      }
      // All Images in the footer
      if($config->get('jg_showallpics') >= 2)
      {
        $params->set('show_footer_allpics', 1);
      }
    }

    // All hits
    if(!$params->get('disable_global_info'))
    {
      // All Hits in the header
      if($config->get('jg_showallhits') == 1 || $config->get('jg_showallhits') == 3)
      {
        $params->set('show_header_allhits', 1);
      }
      // All Hits in the footer
      if($config->get('jg_showallhits') >= 2)
      {
        $params->set('show_footer_allhits', 1);
      }
    }

    // Link to userpanel in the header
    if(!$params->get('disable_global_info') && $config->get('jg_userspace') == 1)
    {
      if(   (($config->get('jg_showuserpanel') == 1) && ($user->get('aid') > 0))
         || (($config->get('jg_showuserpanel') > 0 ) && ($user->get('aid') == 2))
         || ($config->get('jg_showuserpanel') == 3)
        )
      {
        if($user->get('aid') != 0)
        {
          $params->set('show_mygal', 1);
        }
        else
        {
          $params->set('show_mygal_no_access', 1);
        }
      }
    }

    // Link to favourites in the header
    if(!$params->get('disable_global_info') && $config->get('jg_favourites'))
    {
      if($view != 'favourites')
      {
        if(   (($config->get('jg_showdetailfavourite') == 0) && ($user->get('aid') >= 1))
           || (($config->get('jg_showdetailfavourite') == 1) && ($user->get('aid') == 2))
           || (($config->get('jg_usefavouritesforpubliczip') == 1) && ($user->get('aid') < 1))
          )
        {
          if( ($config->get('jg_usefavouritesforzip') == 1)
             || (($config->get('jg_usefavouritesforpubliczip') == 1) && ($user->get('aid') < 1))
            )
          {
            $params->set('show_favourites', 1);
          }
          else
          {
            $tooltip_text = JText::_('JGS_COMMON_FAVOURITES_DOWNLOAD_TIPTEXT', true);
            if($config->get('jg_zipdownload') && $view != 'createzip')
            {
              $tooltip_text .= ' '.JText::_('JGS_COMMON_DOWNLOADZIP_DOWNLOAD_ALLOWED_TIPTEXT', true);
            }
            $params->set('show_favourites', 2);
            $params->set('favourites_tooltip_text', $tooltip_text);
          }
        }
        else
        {
          if(($config->get('jg_favouritesshownotauth') == 1/*) && ($user->get('aid') < 1*/))
          {
            if($config->get('jg_usefavouritesforzip') == 1)
            {
              $params->set('show_favourites', 3);
            }
            else
            {
              $params->set('show_favourites', 4);
            }
          }
        }
      }
      else
      {
        if(     $config->get('jg_zipdownload')
            || ($user->get('id') < 1 && $config->get('jg_usefavouritesforpubliczip'))
          )
        {
          $params->set('show_favourites', 5);
        }
      }
    }

    // Toplist
    if( (     $config->get('jg_whereshowtoplist') == 0
          || ($config->get('jg_whereshowtoplist')  > 0 && $view == 'gallery')
          || ($config->get('jg_whereshowtoplist') == 2 && $view == 'category')
        )
      &&
        !$params->get('disable_global_info')
      )
    {
      // Toplist in the header
      if(    $config->get('jg_showtoplist') > 0
          && $config->get('jg_showtoplist') < 3
        )
      {
        $params->set('show_header_toplist', 1);
      }
      // Toplist in the footer
      if($config->get('jg_showtoplist') > 1)
      {
        $params->set('show_footer_toplist', 1);
      }
    }

    // RM/SM Legend in the footer
    if($config->get('jg_rmsm') == 1 && ($view == 'gallery' || $view == 'category'))
    {
      $params->set('show_rmsm_legend', 1);
    }

    // Separator in the footer
    if($view == 'detail' || $view == 'favourites' || $view == 'search' || $view == 'toplist')
    {
      $params->set('show_footer_separator', 1);
    }

    // Credits in the footer
    if($config->get('jg_suppresscredits'))
    {
      $params->set('show_credits', 1);
    }
  }

  /**
   * Creates the target and the label of the backlinks
   *
   * @TODO: All the queries in this function are unnecessary,
   * because all the information is already loaded in the models
   *
   * @access  public
   * @param   object  $params The parameter object
   * @param   int     $id     ID of the current category if we are in category view, ID of current image if we are in detail view
   * @return  array   0 => target, 1 => label
   * @since   1.5.5
   */
  function getBackLink(&$params, $id = 0)
  {
    $database = & JFactory::getDBO();
    $view = JRequest::getCmd('view');

    // Disable backlink in gallery view
    if($view == 'gallery')
    {
      $params->set('show_header_backlink', 0);
      $params->set('show_footer_backlink', 0);
    }

    if($view == 'category')
    {
      // Sub-category and category view
      $query = "  SELECT
                    parent
                  FROM
                    "._JOOM_TABLE_CATEGORIES."
                  WHERE
                    cid = ".$id."
                  ";
      $database->setQuery($query);
      $catid = $database->loadResult();
      if($catid != 0)
      {
        // Sub-category -> parent category
        $target = JRoute::_('index.php?view=category&catid='.$catid);
        $label  = JText::_('JGS_COMMON_BACK_TO_CATEGORY');
      }
      else
      {
        // Category view -> gallery view
        $target = JRoute::_('index.php?view=gallery');
        $label  = JText::_('JGS_COMMON_BACK_TO_GALLERY');
      }
    }
    else
    {
      if($view == 'detail')
      {
        // Detail view -> category view
        $query = "  SELECT
                      catid
                    FROM
                      "._JOOM_TABLE_IMAGES."
                    WHERE
                      id = ".$id."
                    ";
        $database->setQuery($query);
        $catid = $database->loadResult();

        $target = JRoute::_('index.php?view=category&catid='.$catid).'#category';
        $label  = JText::_('JGS_COMMON_BACK_TO_CATEGORY');
      }
      else
      {
        // General
        $target = "javascript:history.back();";
        $label  = JText::_('JGS_COMMON_BACK');
      }
    }

    return array($target, $label);
  }

  /**
   * Counts images and hits in gallery or a category and their sub-categories
   *
   * @access  public
   * @param   int     $cat      Category ID or 0 to return images/hits of gallery
   * @param   boolean $rootcat  True to count the images/hits also in category = $cid
   * @return  array   0 = Number of images in categories->subcategories....
   *                  1 = Number of hits in categories->subcategories....
   * @since   1.5.7
   */
  function getNumberOfImgHits($cat = 0, $rootcat = true)
  {
    $cat        = (int) $cat;
    $imgHitsarr = array();
    $imgcount   = 0;
    $hitcount   = 0;

    // Get category structure from ambit
    $ambit = JoomAmbit::getInstance();
    $cats  = $ambit->getCategoryStructure();

    if($cat == 0)
    {
      // Count images/hits in gallery
      $catsSize = count($cats);

      for($i = 0; $i < $catsSize; $i++)
      {
        $imgcount += $cats[$i]->piccount;
        $hitcount += $cats[$i]->hitcount;
      }
    }
    else
    {
      // Determine start index in separate loop for better performance
      $startindex = 0;
      $branchfound      = false;
      $parentcats       = array();
      $parentcats[$cat] = true;

      $stopindex = count($cats);
      for($i = 0; $i < $stopindex; $i++)
      {
        if($cats[$i]->cid == $cat)
        {
          $startindex = $i;
          if($rootcat)
          {
            $imgcount += $cats[$i]->piccount;
            $hitcount += $cats[$i]->hitcount;
          }
          break;
        }
      }

      // Count all images in branch
      $hidden = array();
      for($i = $startindex + 1; $i < $stopindex; $i++)
      {
        $parentcat = $cats[$i]->parent;
        if(isset($parentcats[$parentcat]))
        {
          $parentcat = $cats[$i]->cid;
          $parentcats[$parentcat] = true;

          // Don't count images and hits of hidden categories
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

          if(!isset($hidden[$cats[$i]->cid]))
          {
            $hitcount += $cats[$i]->hitcount;
            $imgcount += $cats[$i]->piccount;
          }

          $branchfound = true;
        }
        else
        {
          if($branchfound)
          {
            // Branch has been processed completely
            break;
          }
        }
      }

    }

    $imgHitsarr[0] = number_format($imgcount, 0, JText::_('JGS_COMMON_DECIMAL_SEPARATOR'), JText::_('JGS_COMMON_THOUSANDS_SEPARATOR'));
    $imgHitsarr[1] = number_format($hitcount, 0, JText::_('JGS_COMMON_DECIMAL_SEPARATOR'), JText::_('JGS_COMMON_THOUSANDS_SEPARATOR'));

    return $imgHitsarr;
  }

  /**
   * Resort an array of category objects to ensure, that a parent category
   * is always listed before it's child categories. The function expects a $cats
   * category list, which is already sorted by parent ascending.
   *
   * @access  public
   * @param   array   $cats       Array of category objects to resort
   * @param   array   $catssorted Resorted category object array
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
   * @param   int     $catid      Category id
   * @param   array   $children   Two dimensional array containing the child
   *                              category objects for each parent category id
   * @param   array   $catssorted Resorted category object array
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
   * @param   string  $tablealias The table alias to use
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
}