<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/Module/JoomTreeview/trunk/changelog.php $
// $Id: changelog.php 3193 2011-07-14 20:37:00Z erftralle $
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
?>

CHANGELOG MOD_JGTREEVIEW (since Version 1.5 )

Legende / Legend:

* -> Security Fix
# -> Bug Fix
+ -> Addition
^ -> Change
- -> Removed
! -> Note

-------------------------------------------------------------------
Version 1.5.7.1 (for JoomGallery MVC 1.5.7.X)
-------------------------------------------------------------------
20110714
# Too many '/' in some URLs (icon theme, style sheets, scripts)
  , thanks to 'sirscooby'
+ Dutch translation added, thanks to Pieter-Jan de Vries

-------------------------------------------------------------------
Version 1.5.7 (for JoomGallery MVC 1.5.7)
-------------------------------------------------------------------
20110313
^ Minor changes
^ Performance optimization, removed calls of array_push() and some
  calls of in_array()
- Module option to ignore JoomGallery's access rights removed (cfg_access)
+ New module option to show hidden gallery categories
+ New module option to show the treeview always fully expanded
+ Hide empty categories according to JoomGallery's backend
  setting 'jg_hideemptycats'
+ New module option to disable 'New' labeling
+ Check whether the required gallery version is installed

-------------------------------------------------------------------
Version 1.5.5 (for JoomGallery MVC 1.5.5)
-------------------------------------------------------------------
20090928
^ path to JoomGallery's interface.php changed
^ use of JoomHelper::checkNewCatg() to check for new categories
^ use of view instead of func in URL (getTreeOpenToNode())

20100107
^ minor changes
^ removed parameter "false" from JRoute::_(..., false) for xhtml compliance
# &nbsp; instead of &nbsp for positive validation check :-)
^ changed behaviour concerning Itemid

20100330
^ adaption to JoomGallery's new language constants

20100422
^ joined JoomGallery's project team, changes in file headers
# added some missing semicolons in dtree.js

20100424
^ code style adaptions
^ some refactoring to have a smaller default template, switched Code to
  helper class
# minor bug fixes in html code

20100426
# bugfix unknown modid in default.tmpl

20100503
# bugfix for not shown subcategories in treeview , recursive sort
  algo in sortCategoryList() implemented

20100512
^ using JoomHelper:: sortCategoryList(), internal function sortCategoryList() removed

20100822
! Stable

-------------------------------------------------------------------
Version 1.5.1
-------------------------------------------------------------------
20090806 - 20090819
^ complete code refactoring
+ highlight new categories according to JoomGallery backend setting
+ backend parameter to change Itemid used in links
+ default use of JGS_GALLERY for tree view name
+ possibility to sort first level categories and subcategories in different manners
+ possibility to shorten length of category and subcategory names
+ category filter, therefore javascript changes to unselect a node
+ parameter to enter the root category id for the tree view
^ tree view navigation behaviour
+ minor changes
+ additional icon themes provided by Claudia E. , thank you Claudia

-------------------------------------------------------------------------------
Version: 1.5.0
-------------------------------------------------------------------------------
20090222
# removed call-time pass-by-reference
  thanks to mab
# parameter to handle user access was not considered
  thanks to aHa
^ moved inline style definition with addCustomTag to page header
  thanks to aHa

20090224-20090227
- removed paramter setting for caching
  (styles and scripts added by document->addScript() and
  document->addStyleSheet are not loaded when cache is enabled)
+ advanced user access handling according to the JoomGallery's
  configuration settings (category access rights and RM/SM settings),
  therefor slightly changes of dtree javascript
+ now using silk icons from www.famfamfam.com
  thanks for the nice icons
+ moved jgtreeview styles to external style sheet file
+ parameters for
    - showing/hiding treeview grid
    - changing background color of selected node
    - showing/hiding a link for opening/closing complete treeview
    - showing/hiding backlink to famfamfam.com
+ minor other changes

20090228
^ setting file format of all files to dos (LF -> CRLF)