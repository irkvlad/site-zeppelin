<?php
defined('_JEXEC') or die('Restricted access');
/**
* Attachments component
* @package Attachments
* @Copyright (C) 2007, 2008 Jonathan M. Cameron, All Rights Reserved
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link http://joomlacode.org/gf/project/attachments/frs/
* @author Jonathan M. Cameron
**/

jimport( 'joomla.application.component.controller' );

require_once(dirname(__FILE__).DS.'helper.php');

class AttachmentsController extends JController
{
    function __construct( $default = array() )
    {
        parent::__construct( $default );
        // $this->registerTask('apply', 'save');
    }

    function noop()
    {
        echo "<h1>No function selected!</h1>";
        exit();
    }


    function upload()
    {
        require_once(JPATH_COMPONENT_SITE.DS.'helper.php');

        // Setup basic info
        $user =& JFactory::getUser();
        $article_id = JRequest::getVar('artid', null);
        $article_title = AttachmentsHelper::get_article_title($article_id);

        // Make sure this user can add attachments to this article
        require_once(JPATH_COMPONENT_SITE.DS.'permissions.php');
        if ( !AttachmentsPermissions::user_may_add_attachment($user, $article_id) ) {
            $errmsg = JText::_('ERROR NO PERMISSION TO UPLOAD');
            JError::raiseError(500, $errmsg);
            exit();
            }

        // Use a different template for the iframe view
        $from = JRequest::getVar('from', false);
        $Itemid = JRequest::getVar('Itemid', 1);
        if ( $from == 'closeme') {
           JRequest::setVar('tmpl', 'component');
           }

        // Get the component parameters
        jimport('joomla.application.component.helper');
        $params = JComponentHelper::getParams('com_attachments');

        // Make sure the attachments directory exists
        $upload_subdir = $params->get('attachments_subdir', 'attachments');
        if ( $upload_subdir == '' ) {
            $upload_subdir = 'attachments';
            }
        $upload_dir = JPATH_BASE.DS.$upload_subdir;
        $secure = $params->get('secure', false);
        if ( !AttachmentsHelper::setup_upload_directory( $upload_dir, $secure ) ) {
            $errmsg = JText::_('ERROR UNABLE TO SETUP UPLOAD DIR');
            JError::raiseError(500, $errmsg);
            }
        $auto_publish = $params->get('publish_default', false);
        $mod_date_format = $params->get('mod_date_format', 'M-j-Y g:ia');

        // Construct the url to save the form
        $save_url = "index.php?option=com_attachments&task=save&tmpl=component";
        if ( $from == 'closeme') {
             $save_url .= "&from=closeme";
             }

        // Set up the view to redisplay the form with warnings
        require_once(JPATH_COMPONENT_SITE.DS.'views'.DS.'upload'.DS.'view.php');
        $view = new AttachmentsViewUpload();
        $view->assign('save_url', $save_url);
        $view->assign('article_id', $article_id);
        $view->assign('article_title', $article_title);
        $view->assign('filename', '');
        $view->assign('description', '');
        $view->assign('display_filename', '');
        $view->assign('user_field_1', '');
        $view->assign('user_field_2', '');
        $view->assign('user_field_3', '');
        $view->assign('from', JRequest::getVar('from', ''));
        $view->assign('Itemid', JRequest::getVar('Itemid', 1));
        $view->assignRef('params', $params);

         $view->display(null, false, false, false);
    }

    function save()
    {
        // Check for request forgeries
        JRequest::checkToken() or die( 'Invalid Token');

        // Make sure that the caller is logged in
        $user =& JFactory::getUser();
        if ( $user->get('username') == '' ) {
            $errmsg = JText::_('ERROR MUST BE LOGGED IN TO UPLOAD ATTACHMENT');
            JError::raiseError(500, $errmsg);
            }

        // Make sure we have a valid article ID
        $article_id = AttachmentsHelper::valid_article_id(
                          JRequest::getVar('article_id', null, 'POST'));

        // Verify that this user may add attachments to this article
        require_once(JPATH_COMPONENT.DS.'permissions.php');
        if ( !AttachmentsPermissions::user_may_add_attachment($user, $article_id) ) {
            $errmsg = JText::_('ERROR NO PERMISSION TO UPLOAD');
            JError::raiseError(500, $errmsg);
            }

        // Get the Itemid
        $Itemid = JRequest::getVar('Itemid', null, 'POST');
        if ( $Itemid && is_numeric($Itemid) )
            $Itemid = intval($Itemid);
        else
            $Itemid = 1;

        // How to redirect?
        $from = JRequest::getVar('from', false, 'POST');
        if ( $from ) {
            if ( $from == 'frontpage' ) {
                $redirect_to = JURI::base();
                }
            elseif ( $from == 'article' ) {
                $redirect_to = JRoute::_(
                      "index.php?option=com_content&view=article&id=$article_id", False);
                }
            else {
                $redirect_to = JURI::base();
                }
            }
        else {
            $redirect_to = JURI::base();
            }

        // See if we should cancel
        if ( $_POST['submit'] == JText::_('CANCEL') ) {
            $msg = JText::_('UPLOAD CANCELED');
            $this->setRedirect( $redirect_to, $msg );
            return;
            }
 
        // If this is an update, get the attachment id
        $update = JRequest::getVar('update', false, 'POST');
        $attachment_id = false;
        if ( $update ) {
            $attachment_id = JRequest::getVar('id', false, 'POST');
            }

        // Bind the info from the form
        $row =& JTable::getInstance('Attachments', 'Table');
        if ( $attachment_id && !$row->load($attachment_id) ) {
            $errmsg = JText::_('ERROR CANNOT UPDATE ATTACHMENT INVALID ID') . "  ($id)";
            JError::raiseError(500, $errmsg);
            exit();
            }
        if (!$row->bind(JRequest::get('post'))) {
            JError::raiseError(500, $row->getError());
            }
        if ( !$update ) {
            $row->uploader_id = $user->get('id');
            $row->article_id = $article_id;
            }

        // Upload the file
        $tmp_name = $_FILES['upload']['tmp_name'];
        if ( $update ) {
             $update_file = JRequest::getVar('update_file', false, 'POST');
             if ( $update_file ) {
                 $msg = AttachmentsHelper::upload_file($row, $article_id, $update, $attachment_id);
                 // NOTE: store() is not needed if upload_file() is called since it does it
                  }
             else {
                 // Save the updated attachment
                 if (!$row->store()) {
                     JError::raiseError(500, $row->getError());
                     }
                 $msg = "Attachment updated!";
                 }
             }
        else {
             $msg = AttachmentsHelper::upload_file($row, $article_id, $update);
              }

        // If we are supposed to close this iframe, do it now.
        if ( $from == 'closeme' ) {
        
            // Queue the message
            AttachmentsHelper::enqueueSystemMessage($msg);

            // Now do the Javascript to close this pop-up window and reload the parent
            echo "<script language=\"javascript\" type=\"text/javascript\">
            window.parent.document.getElementById('sbox-window').close();
            window.parent.location.reload();
            </script>";
            exit();
            }

        $this->setRedirect( $redirect_to, $msg );
    }

    
    function download()
    {
        // Get the attachment ID
        $id = JRequest::getVar('id', null);
        if ( !is_numeric($id) ) {
            $errmsg = JText::_('ERROR INVALID ATTACHMENT ID') . "  ($id)";
            JError::raiseError(500, $errmsg);
            exit();
            }
        
        AttachmentsHelper::download_attachment($id);
    }

    function request_login()
    {
        // Add CSS for styling
        $document = & JFactory::getDocument();
        $document->addStyleSheet( JURI::base() . 'plugins/content/attachments.css',
                                  'text/css', null, array() );

        // Get the component parameters for the registration URL
        jimport('joomla.application.component.helper');
        $params = JComponentHelper::getParams('com_attachments');
        $url = $params->get('register_url', "index.php?option=com_user&task=register");
        $url = JRoute::_($url);

        // Get a phrase from the login module
        $lang =& JFactory::getLanguage();
        $lang->load('mod_login');
        $register = JText::_('REGISTER');

        // Set up the phrases/refs
        $must_be_logged_in = JText::_('ERROR MUST BE LOGGED IN TO DOWNLOAD ATTACHMENT');
        $ref = "<a href=\"$url\">$register</a>";
        
        // Output the HTML
        echo '<div class="requestLogin">';
        echo "<h1>$must_be_logged_in</h1>";
        echo "<h2>".JText::sprintf('REGISTER HERE', $ref)."</h2>";
        echo '</div>';
    }
    
    function delete()
    {
        global $option;
        $db =& JFactory::getDBO();
        
        // Verify the user is logged in
        $user =& JFactory::getUser();
        if ( $user->get('username') == '' ) {
            $errmsg = JText::_('ERROR MUST BE LOGGED IN TO DELETE ATTACHMENT');
            JError::raiseError(500, $errmsg);
            exit();
            }

        // Make sure we have a valid article ID
        $article_id = JRequest::getVar( 'artid');
        if ( is_numeric($article_id) ) {
            $article_id = intval($article_id);
            }
        else {
            $errmsg = JText::_('ERROR BAD ARTICLE ID');
            JError::raiseError(500, $errmsg);
            exit();
            }
        $query = "SELECT title FROM #__content WHERE id='$article_id'";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if (count($rows) <= 0) {
            $errmsg = JText::_('ERROR CANNOT DELETE INVALID ARTICLE ID') . "  ($article_id)";
            JError::raiseError(500, $errmsg);
            exit();
            }
        $article_title = $rows[0]->title;

        // Make sure we have a valid attachment ID
        $id = JRequest::getVar( 'id');
        if ( is_numeric($id) ) {
            $id = intval($id);
            }
        else {
            $errmsg = JText::_('ERROR CANNOT DELETE INVALID ATTACHMENT ID') . " ($id)";
            JError::raiseError(500, $errmsg);
            exit();
            }
        $query = "SELECT * FROM #__attachments WHERE id='$id'";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if (count($rows) != 1) {
            $errmsg = JText::_('ERROR CANNOT DELETE INVALID ATTACHMENT ID') . "  ($id)";
            JError::raiseError(500, $errmsg);
            exit();
            }
        $filename_sys = $rows[0]->filename_sys;
        $filename = $rows[0]->filename;
        
        // Get the component parameters
        jimport('joomla.application.component.helper');
        $params = JComponentHelper::getParams('com_attachments');

        // See if this user can modify (delete) the attachment
        require_once(JPATH_COMPONENT_SITE.DS.'permissions.php');
        if ( !AttachmentsPermissions::user_may_modify_attachment($user, $rows[0], $article_id, $params) ) {
            $errmsg = JText::_('ERROR NO PERMISSION TO DELETE');
            JError::raiseError(500, $errmsg); 
            }

        // First delete the actual attachment files
        jimport('joomla.filesystem.file');
        JFile::delete($filename_sys);
            
        // Delete the entries in the attachments table
        $query = "DELETE FROM #__attachments WHERE id='$id' LIMIT 1";
        $db->setQuery($query);
        if (!$db->query()) {
            JError::raiseError(500, $db->getErrorMsg());
            }

        // Get the Itemid
        $Itemid = JRequest::getVar( 'Itemid', false);
        if ( is_numeric($Itemid) )
            $Itemid = intval($Itemid);
        else
            $Itemid = 1;

        $msg = JText::_('DELETED ATTACHMENT') . " '$filename'";

        // How to redirect?
        $from = JRequest::getVar( 'from', false);           
        if ( $from ) {
            if ( $from == 'frontpage' ) {
                $redirect_to = JURI::base();
                }
            elseif ( $from == 'article' ) {
                $redirect_to = JRoute::_("index.php?option=com_content&view=article&id=$article_id", False);
                }
            elseif ( $from == 'closeme' ) {
                // If we are supposed to close this iframe, do it now.

                // Queue the message
                require_once(JPATH_COMPONENT_SITE.DS.'helper.php');
                AttachmentsHelper::enqueueSystemMessage($msg);

                // Now do the Javascript to close this pop-up window and reload the parent
                echo "<script language=\"javascript\" type=\"text/javascript\">
                window.parent.document.getElementById('sbox-window').close();
                window.parent.location.reload();
                </script>";
                exit();
                }
            else {
                $redirect_to = JURI::base();
                }
            }
        else {
            $redirect_to = JURI::Base();
            }

        $this->setRedirect( $redirect_to, $msg );
    }



    function delete_warning()
    {
        // Meant to be shown in the iframe popup

        global $mainframe;
        $document = & JFactory::getDocument();
        $document->addStyleSheet( JURI::base() . 'plugins/content/attachments.css',
                                  'text/css', null, array() );
        $document->addStyleDeclaration(
             'div.componentheading { display: none; } * { overflow: hidden; };');

        // Make sure we have a valid attachment ID
        $id = JRequest::getVar('id', false);
        if ( is_numeric($id) ) {
            $id = intval($id);
            }
        else {
            $errmsg = JText::_('ERROR CANNOT DELETE INVALID ATTACHMENT ID') . " ($id)";
            JError::raiseError(500, $errmsg);
            exit();
            }

        // Get the attachment record
        $attachment =& JTable::getInstance('attachments', 'Table');
        if ( !$attachment->load($id) ) {
            $errmsg = JText::_('ERROR CANNOT DELETE INVALID ATTACHMENT ID') . "  ($id)";
            JError::raiseError(500, $errmsg);
            exit();
            }

        // Get the filename for this attachment
        $article_id = JRequest::getVar('artid', false);

        $delete_url = "index.php?option=com_attachments&task=delete&id=$id&artid=$article_id";
        $delete_url .= "&from=closeme";
        $delete_url = JRoute::_($delete_url);
        ?>
        <div class="deleteWarning">
        <h1><?php echo JText::_('WARNING'); ?></h1>
        <h2 id="warning_msg"><?php echo JText::_('REALLY DELETE ATTACHMENT'); ?>
        (<?php echo $attachment->filename; ?>)</h2>
        
        <form action="<?php echo $delete_url; ?>" name="delete_warning_form" method="post">
        <div align="center">
            <input type="submit" name="submit" value="<?php echo JText::_('DELETE'); ?>" />
        </div>
        </form>
        </div>
        <?php
    }



    function update()
    {
        require_once(JPATH_COMPONENT_SITE.DS.'helper.php');
        // Call with: index.php?option=com_attachments&task=update&id=1&tmpl=component
        //        or: component/attachments/update/id/1/tmpl/component

        // Make sure we have a valid attachment ID
        $id = JRequest::getVar( 'id');
        if ( is_numeric($id) ) {
            $id = intval($id);
            }
        else {
            $errmsg = JText::_('ERROR INVALID ATTACHMENT ID') . " ($id)";
            JError::raiseError(500, $errmsg);
            exit();
            }

        // Get the attachment record
        $attachment =& JTable::getInstance('attachments', 'Table');
        if ( !$attachment->load($id) ) {
            $errmsg = JText::_('ERROR CANNOT UPDATE ATTACHMENT INVALID ID') . "  ($id)";
            JError::raiseError(500, $errmsg);
            exit();
            }

        // Get the component parameters
        jimport('joomla.application.component.helper');
        $params = JComponentHelper::getParams('com_attachments');
       
        // Verify that this user may add attachments to this article
        $user =& JFactory::getUser();
        $article_id = $attachment->article_id;
        $article_title = AttachmentsHelper::get_article_title($article_id);
        require_once(JPATH_COMPONENT_SITE.DS.'permissions.php');
        if ( !AttachmentsPermissions::user_may_modify_attachment($user, 
                 $attachment, $article_id, $params ) ) {
            $errmsg = JText::_('ERROR NO PERMISSION TO UPLOAD');
            JError::raiseError(500, $errmsg);
            exit();
            }

        // Make sure the attachments directory exists
        $upload_subdir = $params->get('attachments_subdir', 'attachments');
        if ( $upload_subdir == '' ) {
            $upload_subdir = 'attachments';
            }
        $upload_dir = JPATH_BASE.DS.$upload_subdir;
        $secure = $params->get('secure', false);
        if ( !AttachmentsHelper::setup_upload_directory( $upload_dir, $secure ) ) {
            $errmsg = JText::_('ERROR UNABLE TO SETUP UPLOAD DIR');
            JError::raiseError(500, $errmsg);
            }

        // Set up the view
        require_once(JPATH_COMPONENT_SITE.DS.'views'.DS.'update'.DS.'view.php');
        $view = new AttachmentsViewUpdate();
        $view->assign('update_file', JRequest::getVar('change', false));
        $view->assign('save_url', "index.php?option=com_attachments&task=save&tmpl=component" );
        $view->assign('attachment_id', $id);
        $view->assign('article_id', $article_id);
        $view->assign('article_title', $article_title);
        $view->assign('filename', $attachment->filename);
        $view->assign('description', $attachment->description);
        $view->assign('display_filename', $attachment->display_filename);
        $view->assign('user_field_1', $attachment->user_field_1);
        $view->assign('user_field_2', $attachment->user_field_2);
        $view->assign('user_field_3', $attachment->user_field_3);
        $view->assign('from', JRequest::getVar('from', 'closeme'));
        $view->assign('Itemid', JRequest::getVar('Itemid', 1));
        $view->assignRef('params', $params);
        
        $view->display(null, false, false, false);
    }


    function warning()
    {
        global $mainframe;
        $document = & JFactory::getDocument();
        $document->addStyleSheet( JURI::base() . 'plugins/content/attachments.css',
                                  'text/css', null, array() );
        $document->addStyleDeclaration(
             'div.componentheading { display: none; } * { overflow: hidden; };');

        echo '<div class="warning"><h1>' . JText::_('WARNING') . '</h1>';
        echo '<h2 id="warning_msg">';
        echo '<script>document.write(parent.document.warning_msg);</script>';
        echo '</h2></div>';
    }

}
?>
