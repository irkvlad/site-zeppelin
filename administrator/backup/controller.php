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

jimport('joomla.application.component.controller');

class AttachmentsController extends JController
{
    function __construct( $default = array() )
    {
        parent::__construct( $default );
        $this->registerTask('apply', 'save');
        $this->registerTask('applyNew', 'saveNew');
        $this->registerTask('unpublish', 'publish');
    }
    
    function showAttachments()
    {
        global $option, $mainframe;
        $limit = JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
        $limitstart = JRequest::getVar('limitstart', 0);
        $db =& JFactory::getDBO();
        $query = "SELECT count(*) FROM #__attachments";
        $db->setQuery($query);
        $total = $db->loadResult();
        $query = "SELECT *, #__attachments.id as id, c.title as title FROM #__attachments ";
        $query .= "LEFT JOIN #__content as c ON #__attachments.article_id = c.id ORDER BY c.id";
        $db->setQuery($query, $limitstart, $limit);
        $rows = $db->loadObjectList();
        if ($db->getErrorNum()) {
            echo $db->stderr();
            return false;
            }
        jimport('joomla.html.pagination');
        $pageNav = new JPagination($total, $limitstart, $limit);
        HTML_attachments::showAttachments($option, $rows, $pageNav);
    }

    function add()
    {
        global $mainframe, $option;
        
        // See if an article is specified
        $article_id = JRequest::getVar('article_id', false);
        $from = JRequest::getVar('from', false);

        // Use a different template for the iframe view
        if ( $from == 'closeme') {
           JRequest::setVar('tmpl', 'component');
           }

        // Add the published selection
        $lists['published'] = JHTML::_('select.booleanlist',
            'published', 'class="inputbox"', false);
        
        if ( !$article_id ) {
            // Set up the necessary javascript
            $document = & JFactory::getDocument();
            $document->addScript( JURI::root(true).'/media/system/js/modal.js' );
            $js = "
            function jSelectArticle(id, title) {
                  document.getElementById('article_id').value = id;
                  document.getElementById('article_title').value = title;
                  document.getElementById('sbox-window').close();
                  }";
            $document->addScriptDeclaration($js);
            JHTML::_('behavior.modal', 'a.modal-button');
            }
        else {
            if ( !is_numeric($article_id) ) {
                $errmsg = JText::_('ERROR INVALID ARTICLE ID') . " ($article_id)";
                JError::raiseError(500, $errmsg);
                }
            }
            
        JRequest::setVar( 'hidemainmenu', 1 );
        
        HTML_attachments::newAttachment($article_id, $lists, $option, $from);
    }

    function edit()
    {
        global $option, $mainframe;

        $db =& JFactory::getDBO();

        $row =& JTable::getInstance('Attachments', 'Table');
        $cid = JRequest::getVar( 'cid', array(0), '', 'array');
        $change_article = JRequest::getVar('change', False) == 'article';
        $update_file = JRequest::getVar('change', False) == 'file';
        $id = $cid[0];
        $row->load($id);
        $lists = array();
        $lists['published'] = JHTML::_('select.booleanlist', 'published',
                                       'class="inputbox"', $row->published);

        // Construct the drop-down list for legal icon filenames
        $icon_filenames = array();
        require_once(JPATH_COMPONENT_SITE.DS.'file_types.php');
        foreach ( AttachmentsFileTypes::unique_icon_filenames() as $ifname) {
            $icon_filenames[] = JHTML::_('select.option', $ifname);
            }
        $lists['icon_filenames'] = JHTML::_('select.genericlist',  $icon_filenames, 
               'icon_filename', 'class="inputbox" size="1"', 'value', 'text', $row->icon_filename);

        // Get the uploaders name
        $query = "SELECT name FROM #__users WHERE id='" . $row->uploader_id . "' LIMIT 1";
        $db->setQuery($query);
        $row->uploader_name = $db->loadResult();

        // Massage the data
        $row->size = intval( $row->file_size / 1024.0 );
        $row->url = $mainframe->getSiteURL() . $row->url;

        // Get the article name
        $article_id = $row->article_id;
        $query = "SELECT * FROM #__content WHERE id='$article_id' LIMIT 1";
        $db->setQuery($query);
        $arows = $db->loadObjectList();
        if ( count($arows) != 1 ) {
            $errmsg = JText::_('ERROR INVALID ARTICLE ID') . " ($article_id)";
            JError::raiseError(500, $errmsg);
            }
        $row->article_title = $arows[0]->title;
        
        if ( $change_article ) {
            $document = & JFactory::getDocument();
            $document->addScript( JURI::root(true).'/media/system/js/modal.js' );
            $js = "
            function jSelectArticle(id, title) {
                    document.getElementById('article_id').value = id;
                    document.getElementById('article_title').value = title;
                    document.getElementById('sbox-window').close();
                    }";
            $document->addScriptDeclaration($js);
            JHTML::_('behavior.modal', 'a.modal-button');
            }

        JRequest::setVar( 'hidemainmenu', 1 );
        
        HTML_attachments::editAttachment($row, $lists, $option, $change_article, $update_file);
    }

    function save()
    {
        // Check for request forgeries
        JRequest::checkToken() or die( 'Invalid Token');

        global $option;
        $row =& JTable::getInstance('Attachments', 'Table');
        if (!$row->bind(JRequest::get('post'))) {
            JError::raiseError(500, $errmsg);
            }

        // See if the article has been changed, rename files if necessary
        $old_article_id = JRequest::getVar('old_article_id', false);
        if ( $old_article_id && is_numeric($old_article_id) ) {
            $old_article_id = intval($old_article_id);
            }
        $article_id = intval($row->article_id);
        if ( $old_article_id && ( $article_id != $old_article_id ) ) {
            require_once(JPATH_BASE.DS.'..'.DS.'components'.DS.'com_attachments'.DS.'helper.php');
            $error_msg = AttachmentsHelper::switch_article($row, $old_article_id, $article_id);
            if ( $error_msg != '' ) {
                $warning = JText::_($error_msg);
                JError::raiseError(500, $warning);
                }
            }
            
        // Compute the update time
        jimport( 'joomla.utilities.date' );
        $now = new JDate();
        $row->modification_date = $now->toMySQL();

        // Update the record
        $update_file = JRequest::getVar('update_file', False);
        $msg = "";
        if ( $update_file && ( ($_FILES['upload']['size'] > 0) ||
                               ($_FILES['upload']['tmp_name'] != '') ) ) {
             // Process the uploaded file (if one was uploaded)
             // (Note that this also save the record)
             require_once(JPATH_BASE.DS.'..'.DS.'components'.DS.'com_attachments'.DS.'helper.php');
             $msg = AttachmentsHelper::upload_file($row, $article_id, true);
             }
        else {
             // Save the updated attachment
             if (!$row->store()) {
                 JError::raiseError(500, $row->getError());
                 }
             }

        switch ($this->_task)
        {
            case 'apply':
                $msg = JText::_('CHANGES TO ATTACHMENT SAVED');
                $link = 'index.php?option=' . $option . '&task=edit&cid[]=' . $row->id;
                break;
                
            case 'save':
            default:
                $msg = JText::_('ATTACHMENT SAVED');
                $link = 'index.php?option=' . $option;
                break;
        }
        
        $this->setRedirect($link, $msg);
    }


    function saveNew()
    {
        // Check for request forgeries
        JRequest::checkToken() or die( 'Invalid Token');

        // Make sure we have a user
        $user =& JFactory::getUser();
        if ( $user->get('username') == '' ) {
            $errmsg = JText::_('ERROR MUST BE LOGGED IN TO UPLOAD ATTACHMENT');
            JError::raiseError(500, $errmsg);
            }
            
        // Make sure we have a valid article ID
        require_once(JPATH_BASE.DS.'..'.DS.'components'.DS.'com_attachments'.DS.'helper.php');
        $article_id = AttachmentsHelper::valid_article_id($_POST['article_id']);
        if ( $article_id == -1 ) {

           // Save the warning message for the pop-up window
           
           // ???
           // echo "<script>SqueezeBox.fromElement('<a href=\"index.php\"></a>')</script>";
           // echo "<script>document.getElementById('sbox-window').open()</script>";

//             require_once(JPATH_BASE.DS.'..'.DS.'components'.DS.'com_attachments'.DS.'helper.php');
//             $msg = JText::_('ERROR MUST SELECT ARTICLE');
//             AttachmentsHelper::save_warning_message($msg);
//             $button->set('options', "{handler: 'iframe', size: {x: 400, y: 300}}");
//             $link = "index.php?option=com_attachments&task=warning&tmpl=component";

             $errmsg = JText::_('ERROR MUST SELECT ARTICLE');
             echo "<script> alert('$errmsg'); window.history.go(-1); </script>\n";
//            exit();
            }
            
        // Make sure this user has permission to upload (should never fail with admin?)
        require_once(JPATH_COMPONENT_SITE.DS.'permissions.php');
        if ( !AttachmentsPermissions::user_may_add_attachment($user, $article_id) ) {
            $errmsg = JText::_('ERROR NO PERMISSION TO UPLOAD');
            JError::raiseError(500, $errmsg);
            exit();
            }

        // Set up the new record
        $row =& JTable::getInstance('Attachments', 'Table');
        if (!$row->bind(JRequest::get('post'))) {
            JError::raiseError(500, $row->getError());
            }
        $row->uploader_id = $user->get('id');
        $row->article_id = $article_id;
                        
        // Handle 'from' clause
        $from = JRequest::getVar('from', ' (no from)');

        $msg = AttachmentsHelper::upload_file($row, $article_id);

        // See where to go to next
        global $option;
        switch ($this->_task)
        {
            case 'applyNew':
                $link = 'index.php?option=' . $option . '&task=edit&cid[]=' . $row->id;
                break;
                
            case 'saveNew':
            default:
                $link = 'index.php?option=' . $option;
                break;
        }
        
        // If called from the editor, go back to it
        if ($from == 'editor') {
            $link = 'index.php?option=com_content&task=edit&cid[]=' . $article_id;
            }
                
        // If we are supposed to close this iframe, do it now.
        if ( $from == 'closeme' ) {
            echo "<script language=\"javascript\" type=\"text/javascript\">window.parent.document.getElementById('sbox-window').close()</script>";
            exit();
            }
        
        $this->setRedirect($link, $msg);
    }


    function myCancel()
    {
        // See if we have a special 'from' to handle
        $from = JRequest::getVar('from', false);
        if ( $from == 'editor' ) {
                
            // Make sure we have a valid article ID
            require_once(JPATH_BASE.DS.'..'.DS.'components'.DS.'com_attachments'.DS.'helper.php');
            $article_id = AttachmentsHelper::valid_article_id($_POST['article_id']);
            if ( $article_id == -1 ) {
                $this->execute('cancel'); // Give up   
                }
                                
            $link = 'index.php?option=com_content&task=edit&cid[]=' . $article_id;
            $this->setRedirect($link, JText::_('UPLOAD CANCELED'));
            }

        $this->execute('cancel');
    }
        

    function download()
    {
        global $mainframe;
        if ( ! $mainframe->isAdmin() ) {
            $errmsg = JText::_('ERROR MUST BE LOGGED IN AS ADMIN');
            JError::raiseError(500, $errmsg);
            }
        
        // Get the attachment ID
        $id = JRequest::getVar('id', null);
        if ( !is_numeric($id) ) {
            JError::raiseError(500, $errmsg);
            }

        require_once(JPATH_BASE.DS.'..'.DS.'components'.DS.'com_attachments'.DS.'helper.php');
        
        AttachmentsHelper::download_attachment($id);
    }



    function remove()
    {
        jimport('joomla.filesystem.file');

        global $option;
        $cid = JRequest::getVar('cid', array(), '', 'array');
        $db =& JFactory::getDBO();
        if (count($cid)) {
            $cids = implode(',', $cid);
            $query = "SELECT filename_sys, id FROM #__attachments WHERE id IN ( $cids )";
            $db->setQuery($query);
            $rows = $db->loadObjectList();
            
            // First delete the actual attachment files
            foreach ($rows as $row) {
                JFile::delete($row->filename_sys);
                }
                
            // Delete the entries in the attachments table
            $query = "DELETE FROM #__attachments WHERE id IN ( $cids )";
            $db->setQuery($query);
            if (!$db->query()) {
                JError::raiseError(500, $db->getErrorMsg());
                }
            }
            
        $this->setRedirect( 'index.php?option=' . $option);
    }

    
    function publish()
    {
        global $option;
        $cid = JRequest::getVar('cid', array(), '', 'array');
        if ($this->_task == 'publish') {
            $publish = 1;
            }
        else {
            $publish = 0;
            }
        $attachmentTable =& JTable::getInstance('attachments', 'Table');
        $attachmentTable->publish($cid, $publish);
        $this->setRedirect('index.php?option=' . $option);
    }


    function admin()
    {
        // Set up the tooltip behavior
        $opts = Array( 'hideDelay' => 0, 'showDelay' => 0 );
        JHTML::_('behavior.tooltip', '.hasTip', $opts);

        // Set up url/link/tooltip for each command
        $url_top = JURI::base() . "index.php?option=com_attachments";
        
        // Set up the HTML for each command
        $disable_mysql_uninstall_url = "$url_top&amp;controller=special&amp;task=disable_sql_uninstall&tmpl=component&close=true";
        // $disable_mysql_uninstall = JText::_('DISABLE MYSQL UNINSTALLATION');
        $disable_mysql_uninstall_tooltip = JText::_('DISABLE MYSQL UNINSTALLATION TOOLTIP');
        $disable_mysql_uninstall_html =
           JHTML::_('tooltip', $disable_mysql_uninstall_tooltip, null, null,
                    'DISABLE MYSQL UNINSTALLATION' , $disable_mysql_uninstall_url );
        
        $regenerate_system_filenames_url = "$url_top&amp;controller=special&amp;task=regenerate_system_filenames&tmpl=component&close=true";
        // $regenerate_system_filenames = JText::_('REGENERATE ATTACHMENT SYSTEM FILENAMES');
        $regenerate_system_filenames_tooltip = JText::_('REGENERATE ATTACHMENT SYSTEM FILENAMES TOOLTIP');
        $regenerate_system_filenames_html =
           JHTML::_('tooltip', $regenerate_system_filenames_tooltip, null, null,
                    'REGENERATE ATTACHMENT SYSTEM FILENAMES', $regenerate_system_filenames_url);
        ?>
        <div class="attachmentsAdmin">
        <h1><?php echo JText::_('EXTRA ADMINISTRATIVE COMMANDS'); ?></h1>
        <ul>
            <li><h2><?php echo $disable_mysql_uninstall_html; ?></h2></li>

            <li><h2><?php echo $regenerate_system_filenames_html ?></h2></li>
        </ul>
        </div>
        <?php
    }

    
    function warning()
    {
        global $mainframe;
        $document = & JFactory::getDocument();
        $document->addStyleSheet( $mainframe->getSiteURL() . 'plugins/content/attachments.css',
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
