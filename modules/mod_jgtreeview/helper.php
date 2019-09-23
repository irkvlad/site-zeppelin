<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/Module/JoomTreeview/trunk/helper.php $
// $Id: helper.php 3192 2011-07-14 20:29:59Z erftralle $
/**
* Module JoomGallery Treeview
* by JoomGallery::Project Team
* @package JoomGallery
* @copyright JoomGallery::Project Team
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
* This program is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the Free Software
* Foundation, either version 2 of the License, or (at your option) any later
* version.
*
* This program is distributed in the hope that it will be useful, but WITHOUT
* ANY WARRANTY, without even the implied warranty of MERCHANTABILITY or FITNESS
* FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along with
* this program; if not, write to the Free Software Foundation, Inc.,
* 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// uncomment to enable debug output with FirePHP (you MUST have installed it)
// define('_JG_TREEVIEW_DEBUG', 1);
if(defined('_JG_TREEVIEW_DEBUG'))
{
  // FirePHP
  require_once(JPATH_ROOT. DS . 'libraries/FirePHPCore/FirePHP.class.php');
}
/**
 * Helper class for module JoomGallery Treeview
 */
class modJgTreeViewHelper extends JoomInterface
{
  /**
   * Entry function
   *
   * @param   object  $params     backend parameters
   * @param   string  $dberror    database error
   * @param   int     $module_id  the id of the module
   */
  function fillObject(&$params, &$dberror, $module_id)
  {
    if(defined('_JG_TREEVIEW_DEBUG'))
    {
      // FirePHP
      ob_start();
      $this->firephp = FirePHP::getInstance(true);
    }

    // read the backend parameters
    $this->getParams($params, $module_id);

    // create and include the dynamic css according to backend settings
    $this->renderCSS();

    // get the newest categories
    return($this->getJgCats($dberror));
  }
  /**
   * Get the the backend parameters and set the module configuration
   *
   * @param object $params      backend parameters
   * @param int    $module_id   module id
   */
  function getParams(&$params, $module_id)
  {
    // get the backend parameters and add them to the config
    $itemid = intval($params->get('cfg_itemid', 0));
    if($itemid > 0)
    {
      $this->addConfig('Itemid', $itemid);
    }
    $this->addConfig('name', addslashes(trim($params->get('cfg_name', JText::_('JGS_COMMON_GALLERY')))));
    $this->addConfig('grid', $params->get('cfg_grid', 1) == 1 ? 'true' : 'false');
    $this->addConfig('bgcolor_active_node', $params->get('cfg_bgcolor_active_node', '#C0D2EC'));
    $this->addConfig('show_open_close_all', intval($params->get('cfg_show_open_close_all', 1)));
    $this->addConfig('show_credits', intval( $params->get('cfg_show_credits', 1)));
    $this->addConfig('sort_cat', $params->get('cfg_sort_cat', 'name ASC'));
    $this->addConfig('sort_subcat', $params->get('cfg_sort_subcat', 'name ASC'));
    $this->addConfig('max_len_catname', intval( $params->get('cfg_max_len_catname', 0)));
    $this->addConfig('blacklist_cats', $this->cleanCSV($params->get('cfg_blacklist_cats', '')));
    $this->addConfig('root_catid', intval($params->get('cfg_root_catid', 0)));

    // icon theme
    $icon_theme_path = JURI::root() . 'modules/mod_jgtreeview/assets/img/';
    $icon_theme = $params->get('cfg_icon_theme', '');
    if(!empty($icon_theme))
    {
      $icon_theme_path .= $icon_theme;
      $icon_theme_path .= '/';
    }
    $this->addConfig('icon_theme_path', $icon_theme_path);
    if(defined('_JG_TREEVIEW_DEBUG'))
    {
      $this->firephp->log($icon_theme_path, 'icon_theme_path');
    }

    // module id
    $this->addConfig('modid', $module_id);

    // get JoomGallery's configuration parameters for displaying [RM] and [SM] categories
    $cfg_show_rmsm = $this->getJConfig('jg_rmsm') === false ? 0 : intval($this->getJConfig('jg_rmsm'));
    $cfg_show_rmsm_cats = $this->getJConfig('jg_showrmsmcats') === false ? 0 : intval($this->getJConfig('jg_showrmsmcats'));
    $filter_cats = false;
    $show_rmsm = false;
    $show_rmsm_cats = false;
    if($cfg_show_rmsm == 0 && $cfg_show_rmsm_cats == 0)
    {
      $filter_cats = true;
    }
    else
    {
      if($cfg_show_rmsm == 1)
      {
        $show_rmsm = true;
      }
      if($cfg_show_rmsm_cats == 1)
      {
        $show_rmsm_cats = true;
      }
    }
    $this->addConfig('filter_cats', $filter_cats);
    $this->addConfig('show_rmsm', $show_rmsm);
    $this->addConfig('show_rmsm_cats', $show_rmsm_cats);
    $this->addConfig('showhidden', $params->get('cfg_showhidden', 0));
    $this->addConfig('show_always_expanded', $params->get('cfg_show_always_expanded', 0));
    $this->addConfig('disable_new', $params->get('cfg_disable_new', 0));
  }
  /**
   * Get all categories from JoomGallery
   *
   * @param   string  $dberror      database error
   * @return  object  $jgcat_rows   category objects
   */
  function getJgCats(&$dberror)
  {
    $dberror='';
    $jgcat_rows_sorted = Array();

    // get all categories from JoomGallery
    $db  = &JFactory::getDBO();

    // first load all first level categories
    $query = 'SELECT
                cid,
                name,
                parent,
                access,
                hidden
              FROM
                ' . _JOOM_TABLE_CATEGORIES . '
              WHERE
                    published = 1
                AND parent    = 0';
    if(!$this->getConfig('showhidden'))
    {
      $query .= '
                AND hidden    = 0';
    }
    $query .= '
              ORDER BY
                ' . $this->getConfig('sort_cat');

    $db->setQuery($query);
    $jgcat_rows = $db->loadObjectList();
    if($jgcat_rows == null)
    {
      if($db->getErrorNum())
      {
        $dberror = JText::_('JG_TREEVIEW_DB_ERROR_LBL') . ': ' . $db->getErrorMsg();
      }
      else
      {
        $dberror = JText::_('JG_TREEVIEW_NO_CATEGORIES_FOUND_LBL');
      }
    }
    if($dberror == '')
    {
      // now load all other categories
      $query = 'SELECT
                  cid,
                  name,
                  parent,
                  access,
                  hidden
                FROM
                  ' . _JOOM_TABLE_CATEGORIES . '
                WHERE
                      published = 1
                  AND parent   != 0';
      if(!$this->getConfig('showhidden'))
      {
        $query .= '
                  AND hidden    = 0';
      }
      $query .= '
                ORDER BY
                  parent ASC, ' . $this->getConfig('sort_subcat');

      $db->setQuery( $query );
      $jgsubcat_rows = $db->loadObjectList();

      if($jgsubcat_rows == null)
      {
        if($db->getErrorNum())
        {
          $dberror = JText::_('JG_TREEVIEW_DB_ERROR_LBL') . ': ' . $db->getErrorMsg();
        }
      }
      else
      {
       $jgcat_rows = array_merge($jgcat_rows, $jgsubcat_rows);
      }
    }
    if($dberror == '')
    {
      JoomHelper::sortCategoryList($jgcat_rows, $jgcat_rows_sorted);
    }
    return($jgcat_rows_sorted);
  }
  /**
   * Get category id for a given picture id
   *
   * @param   int     $picid    picture id
   * @return  int     $catid    category id
   */
  function getCatIdByPicId($picid)
  {
    $catid = 0;
    $db  = &JFactory::getDBO();
    $query = 'SELECT
                id,
                catid
              FROM
                ' . _JOOM_TABLE_IMAGES . '
              WHERE
                id = ' . $picid;
    $db->setQuery($query);
    $pic_row = $db->loadObject();
    if($pic_row != null)
    {
      $catid = $pic_row->catid;
    }
    return($catid);
  }
  /**
   * Create and include the dynamic css
   * according to backend settings
   *
   */
  function renderCSS()
  {
    $document = &JFactory::getDocument();
    // add style for background color of active resp. selected node
    $css  = "    .dtree a.nodeSel {\n"
          . "      background-color:" . $this->getConfig('bgcolor_active_node') . ";\n"
          . "    }";
    $document->addStyleDeclaration($css);
  }
  /**
   * Delivers treeview node name and node link for a given category row object
   *
   * @param   object  $row    category row
   * @param   var     $aid    user access id
   * @param   string  $name   treeview node name for category
   * @param   string  $link   treeview node link for category
   *
   */
  function getTreeNodeNameAndLink($row, $aid, &$cat_name, &$cat_link)
  {
    $rm_or_sm = '';
    if($this->getConfig('filter_cats') == false || $aid >= $row->access)
    {
      // shorten category name
      $max_len = $this->getConfig('max_len_catname');
      if($max_len > 0) {
        $abr = '...';
        if(strlen($row->name) >= $max_len && $max_len > strlen($abr))
        {
          $row->name = substr($row->name, 0, $max_len - strlen($abr));
          $row->name .= $abr;
        }
      }
      // check access rights
      if($aid >= $row->access)
      {
        $cat_name = addslashes(trim( $row->name));
        $cat_link = JRoute::_('index.php?option=com_joomgallery&view=category&catid=' . $row->cid . $this->getJoomId());
      }
      else
      {
        $cat_name = ($this->getConfig('show_rmsm_cats') == true ? addslashes(trim($row->name)) : JText::_('JG_TREEVIEW_NO_ACCESS_LBL'));
        $cat_link = '';
      }
    }
    if($this->getConfig('show_rmsm') == true)
    {
      if(intval($row->access) == 1)
      {
        $rm_or_sm = '<span class="dtreermsm">[RM]</span>';
      }
      elseif(intval($row->access) == 2)
      {
        $rm_or_sm = '<span class="dtreermsm">[SM]</span>';
      }
      $cat_name .= $rm_or_sm;
    }
    if($this->getJConfig('jg_showcatasnew') && !$this->getConfig('disable_new'))
    {
      $isnew = JoomHelper::checkNewCatg($row->cid);
    }
    else
    {
      $isnew = '';
    }
    $cat_name .= $isnew;
  }
  /**
   * Returns the treeview node to open
   *
   * @return    $openToNode
   */
  function getTreeOpenToNode()
  {
    $option = JRequest::getVar('option', '', '', 'string');
    $view   = JRequest::getVar('view', '', '', 'string');
    $catid  = JRequest::getVar('catid', 0, '', 'int');
    $picid =  JRequest::getVar('id', 0, '', 'int');

    $openToNode = -2;
    if($option == 'com_joomgallery')
    {
      switch($view)
      {
        case 'gallery':
          // gallery's home is requested
          $openToNode = 0;
          break;
        case 'category':
          // category
          $openToNode = $catid;
          break;
        case 'detail':
          $openToNode = $this->getCatIdByPicId($picid);
          break;
        default:
          $openToNode = -1;
          break;
      }
    }
    return($openToNode);
  }
  /**
   * Function to clean a CSV lists.
   *
   * @param    string    $csv_list
   * @return   string    $csv_list  cleaned CSV list
   */
  function cleanCSV($csv_list)
  {
    $search[0]     = '/[^0-9,]/m';
    $search[1]     = '/,{2,}/m';
    $search[2]     = '/,+$/m';
    $search[3]     = '/^,+/m';
    $replace[0]    = ',';
    $replace[1]    = ',';
    $replace[2]    = '';
    $replace[3]    = '';
    $csv_list = preg_replace($search, $replace, trim($csv_list));

    return($csv_list);
  }
  /**
   * Function to check whether a category has to be hidden because of JoomGallery's
   * backend setting for 'jg_hideemptycats'.
   *
   * @param    int      catid
   * @return   boolean  True if category has to be hidden
   */
  function hideCategory($catid)
  {
    $hide = false;
    if($this->getJConfig('jg_hideemptycats'))
    {
      $subcatids = JoomHelper::getAllSubCategories($catid, true, ($this->getJConfig('jg_hideemptycats') == 1), false, ($this->getConfig('showhidden') == 0));
      // If 'jg_hideemptycats' is set to 1 the root category will always be in $subcatids, so check check
      // whether there are images in it
      if(   !count($subcatids)
        ||  (   count($subcatids) == 1 && $this->getJConfig('jg_hideemptycats') == 1
              && !count(JoomHelper::getAllSubCategories($catid, true, false, false, ($this->getConfig('showhidden') == 0)))
            )
        )
      {
        $hide  = true;
      }
    }
    return($hide);
  }
  /**
   * Adds the dTree resources and builds up the neccessary javascript html output to
   * show the category tree
   *
   * @param    object    $jgcat_rows
   * @return   string    $script
   */
  function buildTreeview(&$jgcat_rows)
  {
    $script = '';

    // include dTree script, dTree and jgtreeview styles
    $document = &Jfactory::getDocument();
    $document->addStyleSheet(JURI::root() . 'modules/mod_jgtreeview/assets/css/dtree.css');
    $document->addStyleSheet(JURI::root() . 'modules/mod_jgtreeview/assets/css/jgtreeview.css');
    $document->addScript(JURI::root() . 'modules/mod_jgtreeview/assets/js/dtree.js');

    // get user's access id
    $user =& JFactory::getUser();
    $aid = $user->aid;

    // get module id
    $modid = $this->getConfig('modid');

    // valid parent categories - needed to filter child categories
    // with no parent available because of access rights (show_rmsm
    // AND show_rmsm_cats both equal zero)
    $validParentCats = array();

    // array with categories to filter
    $blacklistCats = explode(',', $this->getConfig('blacklist_cats'));

    // get root category ID
    $root_catid = $this->getConfig('root_catid');

    // create dTree object
    $script .= "      var jgTreeView" . $modid . "= new dTree('jgTreeView" . $modid . "', " . "'" . $this->getConfig('icon_theme_path') . "');" . "\n";
    // dTree configuration
    $script .= "      jgTreeView" . $modid . ".config.useCookies = true;" . "\n";
    $script .= "      jgTreeView" . $modid . ".config.inOrder = true;" . "\n";
    $script .= "      jgTreeView" . $modid . ".config.useSelection = true;" . "\n";
    $script .= "      jgTreeView" . $modid . ".config.useLines = " . $this->getConfig('grid') . ";" . "\n";

    $root_node_ok = false;
    if($root_catid == 0)
    {
      $root_link = JRoute::_('index.php?option=com_joomgallery&view=gallery' . $this->getJoomId());
      $root_name = $this->getConfig('name');
      $root_locked = 'false';
      $root_node_ok = true;
    }
    else
    {
      foreach($jgcat_rows AS $row)
      {
        if($row->cid == $root_catid)
        {
          if(($this->getConfig('filter_cats') == false || $aid >= $row->access)
             && !in_array($root_catid, $blacklistCats) && !$this->hideCategory($row->cid)
            )
          {
            $this->getTreeNodeNameAndLink($row, $aid, $root_name, $root_link);
            $root_locked = ($aid >= $row->access ? 'false' :'true');
            $root_node_ok = true;
            break;
          }
        }
      }
    }
    // add root node
    if($root_node_ok == true)
    {
      $script .= "      jgTreeView" . $modid . ".add(" . $root_catid . ", -1, ";
      $script .= "'" . $root_name . "', ";
      $script .= "'" . $root_link . "', ";
      $script .= "'" . $root_locked . "');" ."\n";
      $validParentCats[$root_catid] = true;
    }

    foreach($jgcat_rows AS $row)
    {
      // get treview node name and node link
      $this->getTreeNodeNameAndLink($row, $aid, $cat_name, $cat_link);

      // add nodes
      if($row->parent == $root_catid)
      {
        if(($this->getConfig('filter_cats') == false || $aid >= $row->access)
            && !in_array($row->cid, $blacklistCats) && $root_node_ok == true
            && !$this->hideCategory($row->cid)
          )
        {
          // it is a parent node with node id = $root_catid
          $script .= "      jgTreeView" . $modid . ".add(" . $row->cid . ", " . $root_catid . ", ";
          $script .= "'" . $cat_name . "', ";
          $script .= "'" . $cat_link . "', ";
          $script .= "'" . ($aid >= $row->access ? 'false' : 'true') . "');" ."\n";
          $validParentCats[$row->cid] = true;
        }
      }
      else
      {
        if(($this->getConfig('filter_cats') == false || $aid >= $row->access)
           && isset($validParentCats[$row->parent]) && !in_array($row->cid, $blacklistCats)
           && !$this->hideCategory($row->cid)
          )
        {
          // it is a child node
          $script .= "      jgTreeView" . $modid . ".add(" . $row->cid . ", " . $row->parent . ", ";
          $script .= "'" . $cat_name . "', ";
          $script .= "'" . $cat_link . "', ";
          $script .= "'" . ($aid >= $row->access ? 'false' : 'true') . "');" ."\n";
          $validParentCats[$row->cid] = true;
        }
      }
    }
    $script .= "      document.write(jgTreeView" . $modid . ");" . "\n";

    $openToNode = $this->getTreeOpenToNode();
    $script .= "      switch(" . $openToNode . ")" . "\n";
    $script .= "      {" . "\n";
    $script .= "        case -2:" . "\n";
    // not a JoomGallery page
    $script .= "          jgTreeView" . $modid . ".closeAll();" . "\n";
    // unselect highlighted node
    $script .= "          jgTreeView" . $modid . ".us();" . "\n";
    $script .= "          break;" . "\n";
    $script .= "        case -1:" . "\n";
    // unselect highlighted node
    $script .= "          jgTreeView" . $modid . ".us();" . "\n";
    $script .= "          break;" . "\n";
    $script .= "        case 0:" . "\n";
    // select gallery's home, if root_catid equals zero
    if($root_catid == 0 )
    {
      $script .= "          jgTreeView" . $modid . ".s(0);" . "\n";
    }
    else
    {
      $script .= "          jgTreeView" . $modid . ".us();" . "\n";
    }
    $script .= "          break;" . "\n";
    $script .= "        default:" . "\n";
    // select category
    $script .= "          jgTreeView" . $modid . ".openTo(" . $openToNode . ", true);" . "\n";
    if($root_catid > 0 &&  $openToNode == $root_catid)
    {
      $script .= "          jgTreeView" . $modid . ".s(0);" . "\n";
    }
    $script .= "          break;" . "\n";
    $script .= "      }" . "\n";
    // Show tree always completely expanded
    if($this->getConfig('show_always_expanded'))
    {
      $script .= "      jgTreeView" . $modid . ".openAll()" . "\n";
    }
    return($script);
  }
}
