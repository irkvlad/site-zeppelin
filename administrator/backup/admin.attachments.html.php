<?php 
defined( '_JEXEC' ) or die( 'Restricted access' ); 
/**
* Attachments component
* @package Attachments
* @Copyright (C) 2007, 2008 Jonathan M. Cameron, All Rights Reserved
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link http://joomlacode.org/gf/project/attachments/frs/
* @author Jonathan M. Cameron
**/

class HTML_attachments 
{
    function showAttachments( $option, &$rows, &$pageNav ) 
    { 
        global $mainframe;

        // Get the component parameters
        jimport('joomla.application.component.helper');
        $params = JComponentHelper::getParams('com_attachments');

        $secure = $params->get('secure',false);
        $icon_dir = $mainframe->getSiteURL() . 'components/com_attachments/media/icons/';
        $num_columns = 8;
        ?>
        <form action="index.php" method="post" name="adminForm"> 
        <table class="adminlist"> 
        <thead> 
          <tr> 
            <th class="at_published" width="20"> 
              <input type="checkbox" name="toggle" value="" 
                     onclick="checkAll(<?php echo count( $rows ); ?>);" /> 
            </th>
            <th class="at_published" width="5%" nowrap="nowrap"><?php echo JText::_('PUBLISHED'); ?></th> 
            <th class="at_filename"><?php echo JText::_('ATTACHMENT FILENAME'); ?></th>
            <th class="at_description"><?php echo JText::_('DESCRIPTION'); ?></th>
            <?php if ( $params->get('user_field_1_name', '') != '' ):  $num_columns++; ?>
               <th class="at_user_field"><?php echo $params->get('user_field_1_name', '') ?></th>
            <?php endif; ?>
            <?php if ( $params->get('user_field_2_name', '') != '' ):  $num_columns++; ?>
               <th class="at_user_field"><?php echo $params->get('user_field_2_name', '') ?></th>
            <?php endif; ?>
            <?php if ( $params->get('user_field_3_name', '') != '' ):  $num_columns++; ?>
               <th class="at_user_field"><?php echo $params->get('user_field_3_name', '') ?></th>
            <?php endif; ?>
            <th class="at_file_type"><?php echo JText::_('FILE TYPE'); ?></th>
            <th class="at_file_size"><?php echo JText::_('FILE SIZE KB'); ?></th> 
            <th class="at_create_date"><?php echo JText::_('CREATED'); ?></th>
            <th class="at_mod_date"><?php echo JText::_('LAST MODIFIED'); ?></th>
            <?php if ( $secure ):  $num_columns++; ?>
               <th class="at_downloads"><?php echo JText::_('DOWNLOADS'); ?></th>
            <?php endif; ?>
          </tr>
        </thead> 
        <?php
        // jimport('joomla.filter.output');   // Probably not needed on most systems
        $k = 0;
        $last_article_id = -1;
        for ($i=0, $n=count( $rows ); $i < $n; $i++) { 
            $row = &$rows[$i]; 
            $url = $mainframe->getSiteURL() . $row->url;
            if ( $secure ) {
                $url = "index.php?option=com_attachments&task=download&id=" . $row->id;
                $url = JRoute::_($url);
                }
            else
                $url = $mainframe->getSiteURL() . $row->url;
            $checked = JHTML::_('grid.id', $i, $row->id ); 
            $published = JHTML::_('grid.published', $row, $i );
            $size = intval($row->file_size / 1024);
            $link = JFilterOutput::ampReplace( 'index.php?option=' . $option . '&task=edit&cid[]='. $row->id );
            $artLink = JFilterOutput::ampReplace( '../index.php?option=com_content&view=article&id=' . $row->article_id );
            $view_article_title = JText::_('VIEW ARTICLE TITLE');
            if ( strlen($row->icon_filename) > 0 )
                $icon_url = $icon_dir . $row->icon_filename;
            else
                $icon_url = $icon_dir . 'generic.gif';
            $add_attachment_icon = $mainframe->getSiteURL() . 'components/com_attachments/media/add_attachment.gif';
            $add_attachment_title = JText::_('ADD ATTACHMENT TITLE');
            $edit_attachment_title = JText::_('EDIT THIS ATTACHMENT TITLE');
            $access_attachment_title = JText::_('ACCESS THIS ATTACHMENT TITLE');
            
            // Set up the create/modify dates
            jimport( 'joomla.utilities.date' );
                    $cdate = new JDate($row->create_date, -$mainframe->getCfg('offset'));
            $create_date = $cdate->toFormat("%x %H:%M");
                    $mdate = new JDate($row->modification_date, -$mainframe->getCfg('offset'));
            $modification_date = $mdate->toFormat("%x %H:%M");

            $add_attachment_txt = JText::_('ADD ATTACHMENT');
            if ($row->article_id != $last_article_id) {
                $addAttachLink = 'index.php?option=' . $option . '&task=add&article_id='. $row->article_id;
                $addAttachLink = JFilterOutput::ampReplace($addAttachLink);
                $artLine = "<tr><td class=\"at_articlesep\" colspan=\"$num_columns\">";
                $artLine .= "<b>".JText::_('ARTICLE')." </b><a title=\"$view_article_title\" href=\"$artLink\">" . $row->title . "</a>";
                $artLine .= JFilterOutput::ampReplace('&nbsp;&nbsp;&nbsp;&nbsp;');
                $artLine .= "<a class=\"addAttach\" href=\"$addAttachLink\" title=\"$add_attachment_title\"><img src=\"$add_attachment_icon\" alt=\"$add_attachment_txt\" /></a>&nbsp;";
                $artLine .= "<a class=\"addAttach\" href=\"$addAttachLink\" title=\"$add_attachment_title\">$add_attachment_txt</a>";
                $artLine .= "</td></tr>\n";
                echo $artLine;
                $k = 0;
                }
            $last_article_id = $row->article_id;
            $download_verb = JText::_('DOWNLOAD VERB');
          ?>
              <tr class="<?php echo "row$k"; ?>"> 
                <td><?php echo $checked; ?></td> 
                <td class="at_published" align="center"><?php echo $published;?></td> 
                <td class="at_filename">
                   <a href="<?php echo $link; ?>" title="<?php echo $edit_attachment_title; ?>"
                       ><img src="<?php echo $icon_url; ?>" alt="<?php echo $download_verb; ?>" /></a>&nbsp;<a
                       href="<?php echo $link; ?>" title="<?php echo $edit_attachment_title; ?>"
                       ><?php echo $row->filename; ?></a>&nbsp;&nbsp;<a class="downloadAttach" href="<?php echo $url; ?>"
                       title="<?php echo $access_attachment_title; ?>"><?php echo $download_verb;
                    ?></a><a class="downloadAttach" href="<?php echo $url; ?>"
                       title="<?php echo $access_attachment_title; ?>"
                       ><img src="<?php echo $icon_dir . 'download.gif'; ?>" alt="<?php echo $download_verb; ?>" /></a>
                </td> 
                <td class="at_description"><?php echo $row->description; ?></td>
                <?php if ( $params->get('user_field_1_name', '') != '' ): ?>
                   <td class="at_user_field"><?php echo $row->user_field_1; ?></td>
                <?php endif; ?>
                <?php if ( $params->get('user_field_2_name', '') != '' ): ?>
                   <td class="at_user_field"><?php echo $row->user_field_2; ?></td>
                <?php endif; ?>
                <?php if ( $params->get('user_field_3_name', '') != '' ): ?>
                   <td class="at_user_field"><?php echo $row->user_field_3; ?></td>
                <?php endif; ?>
                <td class="at_file_type"><?php echo $row->file_type; ?></td>
                <td class="at_file_size"><?php echo $size; ?></td>
                <td class="at_create_date"><?php echo $create_date; ?></td>
                <td class="at_mod_date"><?php echo $modification_date ?></td>
                <?php if ( $secure ): ?>
                   <td class="at_downloads"><?php echo $row->download_count; ?></td>
                <?php endif; ?>
              </tr>
              <?php 
              $k = 1 - $k; 
            }
        ?>
        <tfoot>
         <tr>
         <td colspan="<?php echo $num_columns; ?>"><?php echo $pageNav->getListFooter(); ?></td>
         </tr> 
        </tfoot>
      </table> 
      <input type="hidden" name="option" value="<?php echo $option;?>" /> 
      <input type="hidden" name="task" value="" /> 
      <input type="hidden" name="boxchecked" value="0" />
      </form>       
        <?php
    }

    function editAttachment( $row, $lists, $option, $change_article=false, $update_file=false )
    {
        global $mainframe;

        // Get the component parameters
        jimport('joomla.application.component.helper');
        $params = JComponentHelper::getParams('com_attachments');
        $secure = $params->get('secure',false);

        // Set up the create/modify dates
        jimport( 'joomla.utilities.date' );
            $cdate = new JDate($row->create_date, -$mainframe->getCfg('offset'));
        $create_date = $cdate->toFormat("%x %H:%M");
            $mdate = new JDate($row->modification_date, -$mainframe->getCfg('offset'));
        $modification_date = $mdate->toFormat("%x %H:%M");

        $change_article_url = "index.php?option=com_attachments&amp;task=edit&amp;cid[]={$row->id}&amp;change=article";
        $update_file_url = "index.php?option=com_attachments&amp;task=edit&amp;cid[]={$row->id}&amp;change=file";

        if ( $update_file )
            $enctype = "enctype=\"multipart/form-data\"";
        else
            $enctype = '';

        ?>
        <form action="index.php" method="post" <?php echo $enctype ?> name="adminForm" id="adminForm">
        <fieldset class="adminform"> 
        <legend><?php echo JText::_('DETAILS'); ?></legend> 
        <table class="admintable"> 
          <tr><td class="key"><?php echo JText::_('PUBLISHED COLON'); ?></td>
            <td><?php echo $lists['published']; ?></td>
          </tr>
          <tr><?php if ( $change_article ): ?>
                    <td class="key"><label for="article_id"><b><?php echo JText::_('SELECT ARTICLE COLON'); ?></b></label></td>
                    <td><input id="article_title" value="<?php echo $row->article_title; ?>"
                           disabled="disabled" type="text" size="60" />&nbsp;
                    <a class="modal-button" type="button"
                                           title="<?php echo JText::_('SELECT ARTICLE') ?>"
                       href="index.php?option=com_content&amp;task=element&amp;tmpl=component"
                       rel="{handler: 'iframe', size: {x: 650, y: 375}}"><?php echo JText::_('SELECT ARTICLE') ?></a>
                       <input type="hidden" name="old_article_id" value="<?php echo $row->article_id; ?>" />
                    </td>
                <?php else: ?>
                    <td class="key"><?php echo JText::_('ATTACHED TO ARTICLE'); ?></td>
                    <td><?php echo $row->article_title; ?>
                    <a class="changeButton" href="<?php echo $change_article_url; ?>"
                                           title="<?php echo JText::_('CHANGE ARTICLE TOOLTIP'); ?>"
                                           ><?php echo JText::_('CHANGE ARTICLE') ?></a>
                    </td>
                <?php endif; ?>
          </tr>
          <tr><?php if ( $update_file ): ?>
                 <td class="key"><label for="upload"><b><?php
                     echo JText::_('SELECT NEW FILE'); ?></b></label></td>
                                 <td><input type="file" name="upload" id="upload" size="68" maxlength="512" />
                                     <input type="hidden" name="update_file" value="TRUE" />
                                     <input type="hidden" name="filename" value="<?php echo $row->filename; ?>" />
                                     <input type="hidden" name="filename_sys" value="<?php echo $row->filename_sys; ?>" />
                                 &nbsp;<?php echo JText::_('OPTIONAL') ?></td>
                          <?php else: ?>
                         <td class="key"><?php echo JText::_('FILENAME COLON'); ?></td>
                 <td><?php echo $row->filename; ?>
                 <a class="changeButton" href="<?php echo $update_file_url; ?>"
                                    title="<?php echo JText::_('CHANGE FILE TOOLTIP'); ?>"
                                        ><?php echo JText::_('CHANGE FILE') ?></a>
                                 </td>
                          <?php endif; ?>
          </tr>
          <tr><td class="key"><?php echo JText::_('SYSTEM FILENAME'); ?></td>
              <td><?php echo $row->filename_sys; ?></td>
          </tr>
          <tr><td class="key"><label for="display_filename"
                                     title="<?php echo JText::_('DISPLAY FILENAME TOOLTIP'); ?>"
                                     ><?php echo JText::_('DISPLAY FILENAME COLON'); ?></label></td>
              <td><input class="text" type="text" name="display_filename" 
                         id="display_filename" size="80" maxlength="80" 
                         title="<?php echo JText::_('DISPLAY FILENAME TOOLTIP'); ?>"
                         value="<?php echo $row->display_filename;?>"
                                                 />&nbsp;&nbsp;<?php echo JText::_('OPTIONAL'); ?></td>
          </tr>
          <tr><td class="key"><label for="description"><?php echo JText::_('DESCRIPTION COLON'); ?></label></td>
              <td><input class="text" type="text" name="description" 
                 id="description" size="80" maxlength="255" 
                 value="<?php echo $row->description;?>" /></td>
          </tr>
          <?php if ( $params->get('user_field_1_name', '') != '' ): ?>
          <tr><td class="key"><label for="user_field_1"><?php echo $params->get('user_field_1_name'); ?>:</label></td>
              <td><input class="text" type="text" name="user_field_1"
                 id="user_field_1" size="80" maxlength="100"
                 value="<?php echo $row->user_field_1;?>" /></td>
          </tr>
          <?php endif; ?>
          <?php if ( $params->get('user_field_2_name', '') != '' ): ?>
          <tr><td class="key"><label for="user_field_2"><?php echo $params->get('user_field_2_name'); ?>:</label></td>
              <td><input class="text" type="text" name="user_field_2"
                 id="user_field_2" size="80" maxlength="100"
                 value="<?php echo $row->user_field_2;?>" /></td>
          </tr>
          <?php endif; ?>
          <?php if ( $params->get('user_field_3_name', '') != '' ): ?>
          <tr><td class="key"><label for="user_field_3"><?php echo $params->get('user_field_3_name'); ?>:</label></td>
              <td><input class="text" type="text" name="user_field_3"
                 id="user_field_3" size="80" maxlength="100"
                 value="<?php echo $row->user_field_3;?>" /></td>
          </tr>
          <?php endif; ?>
          <tr><td class="key"><?php echo JText::_('UPLOADER NAME'); ?></td>
              <td><?php echo $row->uploader_name;?></td>
          </tr>
          <tr><td class="key"><label for="icon_filename"><?php echo JText::_('ICON FILENAME COLON'); ?></label></td>
              <td><?php echo $lists['icon_filenames']; ?></td>
          </tr>
          <tr><td class="key"><?php echo JText::_('URL'); ?></td>
              <td><?php echo $row->url; ?></td>
          </tr>
          <tr><td class="key"><?php echo JText::_('FILE TYPE COLON'); ?></td>
              <td><?php echo $row->file_type; ?></td>
          </tr>
          <tr><td class="key"><?php echo JText::_('FILE SIZE COLON'); ?></td>
              <td><?php echo $row->size; ?> <?php echo JText::_('KB'); ?></td>
          </tr>
          <tr><td class="key"><?php echo JText::_('DATE CREATED'); ?>:</td>
            <td><?php echo $create_date; ?></td>
          </tr>
          <tr><td class="key"><?php echo JText::_('DATE LAST UPDATED'); ?>:</td>
            <td><?php echo $modification_date; ?></td>
          </tr>
          <?php if ( $secure ):
             $tooltip = JText::_('NUMBER OF DOWNLOADS TOOLTIP');
          ?>
          <tr><td class="key" title="<?php echo $tooltip; ?>"><?php echo JText::_('NUMBER OF DOWNLOADS'); ?>:</td>
            <td title="<?php echo $tooltip; ?>"><?php echo $row->download_count; ?></td>
          </tr>
          <?php endif; ?>


        </table> 
        </fieldset> 
        <input type="hidden" name="id" value="<?php echo $row->id; ?>" /> 
        <input type="hidden" id="article_id" name="article_id" value="<?php echo $row->article_id; ?>" />
        <input type="hidden" name="option" value="<?php echo $option;?>" /> 
        <input type="hidden" name="task" value="edit" />
        <?php echo JHTML::_( 'form.token' ); ?>
        </form
        
        <?php
    }


    function newAttachment( $article_id, $lists, $option, $from=false ) 
    {
        global $mainframe;
        $db =& JFactory::getDBO();

        // Add the plugins stylesheet to style the list of attachments
        $document = & JFactory::getDocument();
        $document->addStyleSheet( $mainframe->getSiteURL() . 'plugins/content/attachments.css', 
                  'text/css', null, array() );

        // Get the article title (if any)
        $article_title = false;
        if ( $article_id ) {
            // Get the article names
            $query = "SELECT * FROM #__content WHERE id='$article_id' LIMIT 1";
            $db->setQuery($query);
            $rows = $db->loadObjectList();
            if ( count($rows) != 1 ) {
                $errmsg = JText::_('ERROR INVALID ARTICLE ID') . " ($article_id)";
                JError::raiseError(500, $errmsg);
                }
            $article_title = $rows[0]->title;
            }
        if ( $article_title ) {
            echo "<h1><b>" . JText::_('ARTICLE') . "</b> &ldquo;$article_title&rdquo;</h1>";
            }

        // Show the existing attachments
        require_once(JPATH_COMPONENT_SITE.DS.'helper.php');
        echo AttachmentsHelper::attachmentsTableHTML($article_id,
                  'EXISTING ATTACHMENTS', false,false,$from);

        // Handle the from option
        if ( $from ) 
            $fromval = $from;
        else
            $fromval = '';

        $save_url = JRoute::_(JURI::root() . "administrator/index.php?option=com_attachments&task=save");
        ?>
    
        <form class="attachmentsBackend" enctype="multipart/form-data" 
              name="adminForm" id="adminForm"
              action="<?php echo $save_url; ?>" method="post">
            <fieldset class="adminform">
                <legend><?php echo JText::_('ADD ATTACHMENT'); ?></legend>
                <?php if ( $article_id ): ?>
                    <p><input type="hidden" name="article_id" value="<?php echo $article_id; ?>" /></p>
                <?php else: ?>
                    <p><label for="article_id"><b><?php echo JText::_('SELECT ARTICLE COLON'); ?></b></label>
                    <input id="article_title" value=""
                           disabled="disabled" type="text" size="60" />&nbsp;
                    <a class="modal-button" type="button" title="<?php echo JText::_('SELECT ARTICLE REQUIRED') ?>"
                       href="index.php?option=com_content&amp;task=element&amp;tmpl=component"
                       rel="{handler: 'iframe', size: {x: 650, y: 375}}"><?php echo JText::_('SELECT ARTICLE') ?></a>
                       <input id="article_id" name="article_id" value="-1" type="hidden">
                    </p>
                <?php endif; ?>
                <p><label for="upload"><b><?php echo JText::_('ATTACH FILE COLON'); ?></b></label>
                   <input type="file" name="upload" id="upload" size="68" maxlength="512" /></p>
                <p><label for="display_filename"
                          title="<?php echo JText::_('DISPLAY FILENAME TOOLTIP'); ?>"
                          ><b><?php echo JText::_('DISPLAY FILENAME COLON'); ?></b></label> 
                   <input type="text" name="display_filename" id="display_filename" size="70" maxlength="80" 
                          title="<?php echo JText::_('DISPLAY FILENAME TOOLTIP'); ?>"
                          value="" />&nbsp;<?php echo JText::_('OPTIONAL'); ?></p>
                <p><label for="description"><b><?php echo JText::_('DESCRIPTION COLON'); ?></b></label> 
                   <input type="text" name="description" id="description" size="70" maxlength="255" value="" /></p>
            </fieldset>
            <input type="hidden" name="MAX_FILE_SIZE" value="524288" />
            <input type="hidden" name="option" value="<?php echo $option;?>" /> 
            <input type="hidden" name="task" value="new" />
            <input type="hidden" name="from" value="<?php echo $fromval; ?>" />
            <?php if ( $from == 'closeme' ): ?>
            <div align="center">
               <input type="submit" name="Submit" class="button" 
                      onclick="javascript: submitbutton('saveNew')"
                      value="<?php echo JText::_('UPLOAD'); ?>" />
            </div>
            <?php endif; ?>
            <?php echo JHTML::_( 'form.token' ); ?>
        </form>
        <?php
    }
}
?>
