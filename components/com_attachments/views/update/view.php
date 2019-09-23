<?php
defined('_JEXEC') or die();

/**
* Attachments component
* @package Attachments
* @Copyright (C) 2007, 2008 Jonathan M. Cameron, All Rights Reserved
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link http://joomlacode.org/gf/project/attachments/frs/
* @author Jonathan M. Cameron
**/

jimport( 'joomla.application.component.view' );

/**
 * View for the uploads
 */
class AttachmentsViewUpdate extends JView
{
    /**
     * Display the view
     */
    function display($tpl=null, $error=false, $error_msg=false, $full_html=false)
    {
        $params = $this->params;
        $save_url = JRoute::_($this->save_url);
        $update_url = "index.php?option=com_attachments&task=update&id={$this->attachment_id}";
        $update_url .= "&amp;tmpl=component&amp;change=file";
        $update_url = JRoute::_($update_url);
        $base_url = JURI::Base();

        $document = & JFactory::getDocument();
        $document->addStyleSheet( JURI::base() . 'plugins/content/attachments.css',
                                  'text/css', null, array() );

        if ( $this->update_file ) {
             $enctype = "enctype=\"multipart/form-data\"";
             }
        else {
             $enctype = '';
             }

        // Prepare for error displays
        $upload_id = 'upload';
        $filename = $this->filename;
        switch ( $error ) {
        case 'no_file':
               $upload_id = 'upload_warning';
               $filename = '';
               break;

        case 'file_too_big':
               $upload_id = 'upload_warning';
               break;

        case 'file_not_on_server':
               $upload_id = 'upload_warning';
               break;
           }
        // If this is a stand-alone page, generate the full html wrapper
        if ( $full_html ): ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <title></title>
   <link href="<?php echo $base_url; ?>plugins/content/attachments.css" 
         rel="stylesheet" type="text/css" />
   <link href="<?php echo $base_url; ?>plugins/content/attachments2.css" 
         rel="stylesheet" type="text/css" />
   <link href="<?php echo $base_url; ?>templates/rhuk_milkyway/css/template.css" 
         rel="stylesheet" type="text/css" />
</head>
<body>
    <?php
        endif;

           echo "<div class=\"uploadAttachmentsPage\">\n";
        echo "<h1>" . JText::_('UPDATE ATTACHMENT COLON') . " $this->filename</h1>\n";
        echo "<h2><strong>" . JText::_('FOR ARTICLE COLON') . "</strong> {$this->article_title}</h2>\n";

        ?>
        <form class="attachments" <?php echo $enctype ?> name="upload_form"
              action="<?php echo $save_url; ?>" method="post">
            <fieldset>
                <?php if ( $this->update_file ): ?>
                <legend><?php echo JText::_('UPDATE ATTACHMENT'); ?></legend>
                <?php if ( $error_msg ): ?>
                <div class="formWarning" id="formWarning"><?php echo $error_msg; ?></div>
                <?php endif; ?>
                <p><label for="<?php echo $upload_id; ?>"><b><?php 
                      echo JText::_('SELECT NEW FILE IF YOU WANT TO UPDATE ATTACHMENT FILE'); ?></b></label>
                   <input type="file" name="upload" id="<?php echo $upload_id; ?>" size="68" maxlength="512" />
                   <input type="hidden" name="update_file" value="TRUE" /></p>
                <?php else: ?>
                <p><label><b><?php echo JText::_('FILENAME COLON'); ?></b></label> <?php echo $this->filename; ?>
                <a class="changeButton" href="<?php echo $update_url ?>"
                   title="<?php echo JText::_('CHANGE FILE TOOLTIP'); ?>"
                   ><?php echo JText::_('CHANGE FILE') ?></a>
                </p>
                <?php endif; ?>
                <p><label for="display_filename"
                          title="<?php echo JText::_('DISPLAY FILENAME TOOLTIP'); ?>"
                          ><b><?php echo JText::_('DISPLAY FILENAME COLON'); ?></b></label>
                   <input type="text" name="display_filename" id="display_filename" 
                          size="70" maxlength="80"
                          title="<?php echo JText::_('DISPLAY FILENAME TOOLTIP'); ?>"
                          value="<?php echo $this->display_filename; ?>" />&nbsp;<?php echo JText::_('OPTIONAL'); ?></p>
                <p><label for="description"><b><?php echo JText::_('DESCRIPTION COLON'); ?></b></label>
                   <input type="text" name="description" id="description" 
                          size="70" maxlength="100" value="<?php echo $this->description; ?>" /></p>
                <?php if ( $params->get('user_field_1_name', false) ): ?>
                <p><label for="user_field_1"><b><?php echo $params->get('user_field_1_name'); ?>:</b></label>
                   <input type="text" name="user_field_1" id="user_field_1" size="70" maxlength="100"
                          value="<?php echo $this->user_field_1; ?>" /></p>
                <?php endif; ?>
                <?php if ( $params->get('user_field_2_name', false) ): ?>
                <p><label for="user_field_2"><b><?php echo $params->get('user_field_2_name'); ?>:</b></label>
                   <input type="text" name="user_field_2" id="user_field_2" size="70" maxlength="100"
                          value="<?php echo $this->user_field_2; ?>" /></p>
                <?php endif; ?>
                <?php if ( $params->get('user_field_3_name', false) ): ?>
                <p><label for="user_field_3"><b><?php echo $params->get('user_field_3_name'); ?>:</b></label>
                   <input type="text" name="user_field_3" id="user_field_3" size="70" maxlength="100"
                          value="<?php echo $this->user_field_3; ?>" /></p>
                <?php endif; ?>

            </fieldset>
            <input type="hidden" name="MAX_FILE_SIZE" value="524288" />
            <input type="hidden" name="submitted" value="TRUE" />
            <input type="hidden" name="update" value="TRUE" />
            <input type="hidden" name="id" value="<?php echo $this->attachment_id; ?>" />
            <input type="hidden" name="article_id" value="<?php echo $this->article_id; ?>" />
            <input type="hidden" name="from" value="<?php echo $this->from; ?>" />
            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
            <?php echo JHTML::_( 'form.token' ); ?>
            <div align="center">
                <input type="submit" name="submit" value="<?php echo JText::_('UPDATE'); ?>" />
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="button" name="cancel" value="<?php echo JText::_('CANCEL'); ?>"
                 onClick="window.parent.document.getElementById('sbox-window').close();" />
            </div>
        </form>
        </div>
        <?php

        // Close the html wrapper, if appropriate
        if ( $full_html ) {
            echo "</body>\n</html>\n";
            }
    }
}
?>