<?php defined('_JEXEC') or die('Direct Access to this location is not allowed.'); ?>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
  <tr valign="middle">
    <th align="center" colspan="2">
      <?php echo JText::_(count($this->files) ? 'JGA_MIGMAN_NOTE' : 'JGA_MIGMAN_NOTE_NO_SCRIPTS'); ?>
    </th>
  </tr>
<?php $show_jmtablerow = true;
      foreach($this->files as $file):
        require_once($file);
      endforeach; ?>
</table>
<?php JHTML::_('joomgallery.credits');