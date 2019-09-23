<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-1.5/Module/JoomTreeview/trunk/tmpl/default.php $
// $Id: default.php 2902 2011-03-13 14:47:32Z erftralle $
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
defined('_JEXEC') or die('Restricted access'); ?>

<?php if(!$jg_installed) : ?>
  <p>
<?php echo JText::_('JG_TREEVIEW_JOOMGALLERY_NOT_INSTALLED_LBL'); ?>
  </p>
<?php elseif($dberror != '') : ?>
  <p>
<?php echo $dberror; ?>
  </p>
<?php else : ?>
  <div class="jgtreeview">
<?php   if($jgTreeviewHelper->getConfig('show_open_close_all') == 1) : ?>
    <div class="jgtreeview_header">
      <a href="javascript: jgTreeView<?php echo $jgTreeviewHelper->getConfig('modid'); ?>.openAll();">
        <?php echo JText::_('JG_TREEVIEW_OPEN_ALL_LBL'); ?>&nbsp;&nbsp;</a>
      <a href="javascript: jgTreeView<?php echo $jgTreeviewHelper->getConfig('modid');?>.closeAll();">
        <?php echo JText::_('JG_TREEVIEW_CLOSE_ALL_LBL'); ?></a>
    </div>
<?php   endif; ?>
    <script type="text/javascript" language="javascript">
    <!--
<?php echo $jgTreeviewHelper->buildTreeview($jgcat_rows); ?>
    // -->
    </script>
<?php   if($jgTreeviewHelper->getConfig('show_credits') == 1) : ?>
    <div class="jgtreeview_footer">
      <?php echo JText::_('JG_TREEVIEW_ICONS_BY_LBL'); ?>&nbsp;
      <a href="http://www.famfamfam.de" target="_blank" title="" >
        www.famfamfam.com</a>
    </div>
<?php   endif; ?>
  </div>
<?php endif; ?>