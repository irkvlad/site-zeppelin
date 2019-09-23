<?php defined('_JEXEC') or die('Direct Access to this location is not allowed.');
echo $this->loadTemplate('header'); ?>
  <div class="jg_userpanelview">
<?php if($this->params->get('show_categories_notice')): ?>
    <div class="jg_uploadquotas">
      <span class="jg_quotatitle">
        <?php echo JText::_('JGS_USERCATEGORIES_NEW_CATEGORY_NOTE'); ?>
      </span><br />
      <?php echo JText::sprintf('JGS_USERCATEGORIES_NEW_CATEGORY_MAXCOUNT', $this->_config->get('jg_maxusercat')); ?><br />
      <?php echo JText::sprintf('JGS_USERCATEGORIES_NEW_CATEGORY_YOURCOUNT', count($this->rows)); ?><br />
      <?php echo JText::sprintf('JGS_USERCATEGORIES_NEW_CATEGORY_REMAINDER', ($this->_config->get('jg_maxusercat') - count($this->rows))); ?><br />
    </div>
<?php endif;
      if($this->params->get('show_category_button')): ?>
    <div class="jg_up_head">
      <input type="button" name="button" value="<?php echo JText::_('JGS_COMMON_NEW_CATEGORY');?>" class="button"
        onclick = "javascript:location.href='<?php echo JRoute::_('index.php?view=editcategory'.$this->slimitstart, false); ?>';" />
<?php endif; ?>
      <form action="<?php echo JRoute::_('index.php?view=usercategories'); ?>" method="post" name="form">
        <p>
<?php if(!is_null($this->pagination)): ?>
          <?php echo $this->pagination->getListFooter(); ?>
<?php endif; ?>
          <?php echo $this->lists['filter']; ?>
        </p>
      </form>
    </div>
    <div class="sectiontableheader">
      <div class="jg_up_entry">
        <div class="jg_up_ename">
          <?php echo JText::_('JGS_COMMON_CATEGORY'); ?>
        </div>
        <div class="jg_up_ehits">
          <?php echo JText::_('JGS_USERCATEGORIES_IMAGES'); ?>
        </div>
        <div class="jg_up_ecat">
          <?php echo JText::_('JGS_COMMON_PARENT_CATEGORY');?>
        </div>
        <div class="jg_up_eact">
          <?php echo JText::_('JGS_COMMON_ACTION');?>
        </div>
        <div class="jg_up_epubl">
          <?php echo JText::_('JGS_COMMON_PUBLISHED');?>
        </div>
      </div>
    </div>
<?php if(!count($this->rows)): ?>
    <div class="jg_txtrow">
      <div class="sectiontableentry1">
        <?php echo JHTML::_('joomgallery.icon', 'arrow.png', 'arrow'); ?>
        <?php echo JText::_('JGS_USERCATEGORIES_YOU_NOT_HAVE_CATEGORY'); ?>
      </div>
    </div>
<?php endif;
      $this->i = 0;
      $display_hidden_asterisk = false;
      foreach($this->rows as $row): ?>
    <div class="sectiontableentry<?php $this->i++; echo ($this->i%2)+1; ?>">
      <div class="jg_up_entry">
        <div class="jg_up_ename">
<?php   if($this->_config->get('jg_showminithumbs')):
          if($row->catimage): ?>
            <?php echo JHTML::_('joomgallery.minithumbcat', $row, 'jg_up_eminithumb', $row->published, true); ?>
<?php     endif;
          if(!$row->catimage): ?>
          <div class="jg_floatleft">
            <?php echo JHTML::_('joomgallery.icon', 'arrow.png', 'arrow'); ?>
          </div>
<?php     endif;
        endif;
        if($row->published): ?>
          <a href="<?php echo JRoute::_('index.php?view=category&catid='.$row->cid); ?>">
<?php   endif; ?>
            <?php echo $row->name; ?>
<?php   if($row->published): ?>
          </a>
<?php   endif; ?>
        </div>
        <div class="jg_up_ehits">
          <?php echo $row->images; ?>
        </div>
        <div class="jg_up_ecat">
<?php   if(!$row->parent): ?>
          <?php echo '-'; ?>
<?php   endif;
        if($row->parent): ?>
          <?php echo JHTML::_('joomgallery.categorypath', $row->parent, ' &raquo; ', false, false, true); ?>
<?php   endif; ?>
        </div>
        <div class="jg_up_esub1<?php echo JHTML::_('joomgallery.tip', 'JGS_COMMON_EDIT_CATEGORY_TIPTEXT', 'JGS_COMMON_EDIT_CATEGORY_TIPCAPTION'); ?>">
          <a href="<?php echo JRoute::_('index.php?view=editcategory&catid='.$row->cid.$this->slimitstart); ?>">
            <?php echo JHTML::_('joomgallery.icon', 'edit.png', 'JGS_COMMON_EDIT'); ?></a>
        </div>
        <div class="jg_up_esub2<?php echo JHTML::_('joomgallery.tip', 'JGS_COMMON_DELETE_CATEGORY_TIPTEXT', 'JGS_COMMON_DELETE_CATEGORY_TIPCAPTION'); ?>">
<?php   if(!$row->children && !$row->images): ?>
          <a href="javascript:if (confirm('<?php echo JText::_('JGS_COMMON_ALERT_SURE_DELETE_SELECTED_ITEM',true); ?>')){ location.href='<?php echo JRoute::_('index.php?task=deletecategory&catid='.$row->cid.$this->slimitstart, false); ?>';}">
            <?php echo JHTML::_('joomgallery.icon', 'edit_trash.png', 'JGS_COMMON_DELETE'); ?></a>
<?php   else: ?>
          <?php echo JHTML::_('joomgallery.icon', 'transparentpixel.gif'); ?>
<?php   endif; ?>
        </div>
<?php   $p_img = 'cross';
        $p_title  = JText::_('JGS_COMMON_PUBLISH_CATEGORY_TIPCAPTION');
        $p_text   = JText::_('JGS_COMMON_PUBLISH_CATEGORY_TIPTEXT');
        if($row->published):
          $p_img = 'tick';
          $p_title = JText::_('JGS_COMMON_UNPUBLISH_CATEGORY_TIPCAPTION');
          $p_text  = JText::_('JGS_COMMON_UNPUBLISH_CATEGORY_TIPTEXT');
        endif; ?>
        <div class="jg_up_epubl">
          <a href="<?php echo JRoute::_('index.php?task=publishcategory&catid='.$row->cid.$this->slimitstart); ?>"<?php echo JHTML::_('joomgallery.tip', $p_text, $p_title, true, false); ?>>
            <?php echo JHTML::_('joomgallery.icon', $p_img.'.png', $p_img); ?></a><?php
        if($row->published && $row->hidden):
          $h_title = JText::_('JGS_COMMON_HIDDEN_ASTERISK');
          $h_text  = JText::_('JGS_COMMON_PUBLISHED_BUT_HIDDEN');
          echo '<span'.JHTML::_('joomgallery.tip', $h_text, $h_title, true, false).'>'.JText::_('JGS_COMMON_HIDDEN_ASTERISK').'</span>';
          $display_hidden_asterisk = true;
        endif; ?>
        </div>
      </div>
    </div>
<?php endforeach; ?>
  </div>
  <div class="jg_txtrow">
    <input type="button" name="button" value="<?php echo JText::_('JGS_COMMON_BACK_TO_USER_PANEL');?>" class="button"
      onclick = "javascript:location.href='<?php echo JRoute::_('index.php?view=userpanel', false); ?>';" />
  </div>
<?php if($display_hidden_asterisk): ?>
  <div align="right">
    <?php echo JText::_('JGS_COMMON_HIDDEN_ASTERISK'); ?> <?php echo JText::_('JGS_COMMON_PUBLISHED_BUT_HIDDEN'); ?>
  </div>
<?php endif;
      echo $this->loadTemplate('footer');