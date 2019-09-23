<?php 
defined('_JEXEC') or die('Restricted access');
/**
* Attachments plugin
* @package Attachments
* @Copyright (C) 2007, 2008 Jonathan M. Cameron, All Rights Reserved
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link http://joomlacode.org/gf/project/attachments/frs/
* @author Jonathan M. Cameron
**/

$mainframe->registerEvent('onAfterDispatch', 'addAttachmentsStyleSheet');
$mainframe->registerEvent('onPrepareContent', 'addAttachments');
// was: $mainframe->registerEvent('onAfterDisplayContent', 'addAttachments');


function addAttachmentsStyleSheet()
{
    $document = & JFactory::getDocument();
    $document->addStyleSheet( JURI::base() . 'plugins/content/attachments.css', 
                              'text/css', null, array() );
}


function attachments_attachmentListHTML($article_id, $user_can_add, $Itemid, $from)
{
    require_once(JPATH_SITE.DS.'components'.DS.'com_attachments'.DS.'helper.php');

    // Generate the HTML for the attachments for the specified article
    $alist = "";
    $db =& JFactory::getDBO();
    $query = "SELECT count(*) FROM #__attachments WHERE article_id='" . $article_id . "' AND published='1'";
    $db->setQuery($query);
    $total = $db->loadResult();
    if ( $total > 0 ) {

        // Get the component parameters
        jimport('joomla.application.component.helper');
        $params = JComponentHelper::getParams('com_attachments');

        // Check the security status
        $attach_dir = JPATH_SITE.DS.$params->get('upload_dir', 'attachments');
        $secure = $params->get('secure', false);
        $hta_filename = $attach_dir.DS.'.htaccess';
        if ( ($secure && !file_exists($hta_filename)) ||
             (!$secure && file_exists($hta_filename)) ) {
            AttachmentsHelper::setup_upload_directory($attach_dir, $secure);
            }

        $alist = AttachmentsHelper::attachmentsTableHTML($article_id, false, true, true, $from);
        }

    return $alist;
}


function attachments_attachmentButtonsHTML($article_id, $Itemid, $from)
{
    $document = & JFactory::getDocument();
    $document->addScript( JURI::root(true).'/media/system/js/modal.js' );
    JHTML::_('behavior.modal', 'a.modal-button');

    // Generate the HTML for a  button for the user to click to get to a form to add an attachment
    $url = "index.php?option=com_attachments&task=upload&artid=$article_id&tmpl=component";
    if ( $from ) {
        // Add a var to give a hint of where to return to
        // $url .= "&from=$from";
        $url .= "&from=closeme";
        }
    $url = JRoute::_($url);
    $icon_url = JURI::Base() . 'components/com_attachments/media/add_attachment.gif';

    $add_attachment_txt = JText::_('ADD ATTACHMENT');
    $ahead = '<a class="modal-button" type="button" href="' . $url . '" ';
    $ahead .= "rel=\"{handler: 'iframe', size: {x: 900, y: 550}}\">";
    $links = "$ahead<img src=\"$icon_url\" alt=\"$add_attachment_txt\" /></a>";
    $links .= $ahead.$add_attachment_txt."</a>";
    return "\n<div class=\"addattach\">$links</div>\n";

    // The old way (save; may need for <noscript>)
    // return "\n<div class=\"addattach\"><a href=\"$url\"><img src=\"$icon_url\" alt=\"\" /></a>&nbsp;<a href=\"$url\">" . JText::_('ADD ATTACHMENT') . "</a></div>\n";
}


function addAttachments( &$row, &$params, $page=0 )
{
    // Only display attachments for content (articles)
    global $option;
    if ( $option != 'com_content' )
        return;

    // Apparently this is called before articles are displayed (ignore those calls)
    if ( !isset($row->id) )
        return;
        
    // Get the component parameters
    jimport('joomla.application.component.helper');
    $attachParams = JComponentHelper::getParams('com_attachments');

    // Get some of the options
    $user =& JFactory::getUser();
    $logged_in = $user->get('username') <> '';
    $user_type = $user->get('usertype', false);

    // Load the language files from the backend
    $lang = & JFactory::getLanguage();
    $lang->load('plg_frontend_attachments', JPATH_ADMINISTRATOR);

    // See whether we can display the links to add attachments
    require_once(JPATH_SITE.DS.'components'.DS.'com_attachments'.DS.'permissions.php');
    if ( AttachmentsPermissions::attachments_hidden_for_article($row->id, $attachParams) ) {
        return;
        }
    $user_can_add = AttachmentsPermissions::user_may_add_attachment($user, $row->id);

    // Determine where we are
    global $option;
    $from = JRequest::getVar( 'view', false);
    $Itemid = JRequest::getVar( 'Itemid', false);
    if ( is_numeric($Itemid) )
        $Itemid = intval($Itemid);
    else
        $Itemid = 1;
                
    // Show the attachment list (if appropriate)
    $who_can_see = $attachParams->get('who_can_see', 'logged_in');
    
    if ( ( $who_can_see == 'anyone' ) ||
         ( ($who_can_see == 'logged_in') && $logged_in ) ) {
        $row->text .= attachments_attachmentListHtml($row->id, $user_can_add, $Itemid, $from);
        }

    if ( $user_can_add ) {
        $row->text .= attachments_attachmentButtonsHTML($row->id, $Itemid, $from);  
        }
}

?>
