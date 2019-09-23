<?php
defined('_JEXEC') or die('Restricted access');

class AttachmentsHelper
{
    function truncate_filename($raw_filename, $maxlen)
    {
        $filename_info = pathinfo($raw_filename);
        $basename = $filename_info['basename'];
        $extension = $filename_info['extension'];

        // Construct the filename without extension (since pathinfo doesn't
        // support directly this for PHP pre 5.2.0)                                                      
        $filename = substr($basename, 0, strlen($basename) - strlen($extension) - 1);
        
        if ( strlen($extension) > 0 ) {
            $maxlen = max( $maxlen - (strlen($extension) + 2), 1);
            return substr($filename, 0, $maxlen) . '~.' . $extension;
            }
        else {
            return substr($filename, 0, $maxlen) . '~';
            }
    }

    
    function valid_article_id($article_id)
    {
        if ( is_numeric($article_id) ) {
            $article_id = intval($article_id);
            }
        else {
            $errmsg = JText::_('ERROR BAD ARTICLE ID');
            JError::raiseError(500, $errmsg);
            }
        return $article_id;
    }
    
    
    function get_article_title($article_id)
    {
        // Get the article name
        $db =& JFactory::getDBO();
        $query = "SELECT title FROM #__content WHERE id='$article_id' LIMIT 1";
        $db->setQuery($query);
        $arows = $db->loadObjectList();
        if ( count($arows) != 1 ) {
            return false;
            }
        return $arows[0]->title;
    }


    function setup_upload_directory($upload_dir, $secure)
    {
        jimport('joomla.filesystem.file');

        $subdir_ok = false;
    
        // Create the subdirectory (if necessary)
        jimport( 'joomla.filesystem.folder' );
        if ( JFolder::exists( $upload_dir ) ) {
            $subdir_ok = true;
            }
        else {
            if ( JFolder::create( $upload_dir )) {
                // ??? Change to 2775 if files are owned by you but webserver runs as group
                chmod($upload_dir, 0775);
                $subdir_ok = true;
                }
            }

        if ( !$subdir_ok || !JFolder::exists($upload_dir) ) {
            $errmsg = JText::_('ERROR UNABLE TO SETUP UPLOAD DIR');
            JError::raiseError(500, $errmsg . " ($upload_dir)"); 
            return false;
            }
        
        // Add a simple index.html file to the upload directory to prevent browsing
        $index_ok = false;
        $index_fname = $upload_dir.DS.'index.html';
        JFile::write($index_fname, "<html><body><br><h2 align=\"center\">Access denied.</h2></body></html>"); 
        if ( JFile::exists($index_fname) ) {
            $index_ok = true;
            }
        if ( ! $index_ok ) {
            $errmsg = JText::_('ERROR ADD INDEX HTML');
            JError::raiseError(500, "<p>" . $errmsg . " ($upload_dir)</p>"); 
            return false;
            }       
    
        // If this is secure, create the .htindex file, if necessary
        $hta_fname = $upload_dir.DS.'.htaccess';
        if ( $secure ) {
            $hta_ok = false;

            JFile::write($hta_fname, "order deny,allow\ndeny from all\n");
            if ( JFile::exists($hta_fname) ) {
                $hta_ok = true;
                }
            if ( ! $hta_ok ) {
                $errmsg = JText::_('ERROR ADD HTACCESS');
                JError::raiseError(500, "<p>" . $errmsg . " ($upload_dir)</p>"); 
                return false;
                }
            }
        else {
            if ( JFile::exists( $hta_fname ) ) {
                // If the htaccess file exists, delete it so normal access can occur
                JFile::delete($hta_fname);
                }
            }

        return true;
    }


    function upload_file(&$row, $article_id, $update=false, $attachment_id=false)
    {
        global $mainframe;

        // Get the component parameters
        jimport('joomla.application.component.helper');
        $params = JComponentHelper::getParams('com_attachments');

        // Get the auto-publish setting
        $auto_publish = $params->get('publish_default', false);

        // Make sure the attachments directory exists
        $upload_subdir = $params->get('attachments_subdir', 'attachments');
        if ( $upload_subdir == '' ) {
            $upload_subdir = 'attachments';
            }
        $upload_dir = JPATH_SITE.DS.$upload_subdir;
        $secure = $params->get('secure', false);
        if ( !AttachmentsHelper::setup_upload_directory( $upload_dir, $secure ) ) {
            $errmsg = JText::_('ERROR UNABLE TO SETUP UPLOAD DIR');
            JError::raiseError(500, "<p>" . $errmsg . " ($upload_dir)</p>");
            }

        // If we are updating, note the name of the old filename
        $old_filename = null;
        $old_filename_sys = null;
        if ( $update ) {
            $old_filename = $row->filename;
            $old_filename_sys = $row->filename_sys;
            }

        // Get the new filename
        $filename  = $_FILES['upload']['name'];
        $ftype = $_FILES['upload']['type'];

        // Make sure a file was successfully uploaded
        if ( $update )
            $update_file = JRequest::getVar('change', true);
        else
            $update_file = true;

        if ( $update_file && ($_FILES['upload']['size'] == 0) && 
                             ($_FILES['upload']['tmp_name'] == '') ) {
        
            // Guess the type of error
            if ( $filename == '' ) {
                $error = 'no_file';
                $error_msg = JText::_('ERROR UPLOADING FILE') . ' ' . $filename;
                $error_msg .= ' (' . JText::_('YOU MUST SELECT A FILE TO UPLOAD') . ')';
                if ( $mainframe->isAdmin() )
                    return $error_msg;
                }
            else {
                $error = 'file_too_big';
                $error_msg = JText::_('ERROR UPLOADING FILE') . ' ' . $filename;
                $error_msg .= ' <br>(' . JText::_('ERROR MAY BE LARGER THAN LIMIT') . ' ';
                $error_msg .= get_cfg_var('upload_max_filesize') . ')';
                if ( $mainframe->isAdmin() )
                    return $error_msg;
                }

            // Set up the view to redisplay the form with warnings
            if ( $update ) {
                require_once(JPATH_COMPONENT_SITE.DS.'views'.DS.'update'.DS.'view.php');
                $view = new AttachmentsViewUpdate();
                $view->assign('update_file', $update_file);
                $view->assign('attachment_id', $attachment_id);
                }
            else {
                require_once(JPATH_COMPONENT_SITE.DS.'views'.DS.'upload'.DS.'view.php');
                $view = new AttachmentsViewUpload();
                }
            $view->assign('save_url', JRoute::_("index.php?option=com_attachments&task=save&tmpl=component"));
            $view->assign('filename', $filename);
            $view->assign('article_id', $article_id);
            $view->assign('article_title', AttachmentsHelper::get_article_title($article_id));
            $view->assign('description', $row->description);
            $view->assign('display_filename', $row->display_filename);
            $view->assign('user_field_1', $row->user_field_1);
            $view->assign('user_field_2', $row->user_field_2);
            $view->assign('user_field_3', $row->user_field_3);
            $view->assign('from', JRequest::getVar('from', ''));
            $view->assign('Itemid', JRequest::getVar('Itemid', 1));
            $view->assignRef('params', $params);

            $view->display(null, $error, $error_msg, true);
            exit();
            }

        // Make sure the file type is okay (respect restrictions imposed by media manager)
        jimport('joomla.filesystem.file');
        $cmparams = &JComponentHelper::getParams( 'com_media' );

        // First check to make sure the extension is allowed
        $allowable = explode( ',', $cmparams->get( 'upload_extensions' ));
        $ignored = explode(',', $cmparams->get( 'ignore_extensions' ));
        $format = strtolower(JFile::getExt($filename));
        $error = false;
        $error_msg = false;
        if (!in_array($format, $allowable) && !in_array($format,$ignored)) {
            $error = 'illegal_file_extension';
            $error_msg = JText::_('ERROR UPLOADING FILE') . ': ' . $filename;
            $error_msg .= "<br>" . JText::_('ERROR ILLEGAL FILE EXTENSION') . " $format";
            $error_msg .= "<br>" . JText::_('ERROR CHANGE IN MEDIA MANAGER');
            }

        // Check to make sure the mime type is okay
        if ( $cmparams->get('restrict_uploads',true) ) {
            if ( $cmparams->get('check_mime', true) ) {
                $allowed_mime = explode(',', $cmparams->get('upload_mime'));
                $illegal_mime = explode(',', $cmparams->get('upload_mime_illegal'));
                if( strlen($ftype) && !in_array($ftype, $allowed_mime) &&
                       in_array($ftype, $illegal_mime)) {
                    $error = 'illegal_mime_type';
                    $error_msg = JText::_('ERROR UPLOADING FILE') . ' ' . $filename;
                    $error_msg .= ', ' . JText::_('ERROR ILLEGAL FILE MIME TYPE') . " $ftype";
                    $error_msg .= "  <br>" . JText::_('ERROR CHANGE IN MEDIA MANAGER');
                    }
                }
            }

        // If there was an error, refresh the form with a warning
        if ( $error ) {

            if ( $mainframe->isAdmin() )
                return $error_msg;

            // Set up the view to redisplay the form with warnings
            if ( $update ) {
                require_once(JPATH_COMPONENT_SITE.DS.'views'.DS.'update'.DS.'view.php');
                $view = new AttachmentsViewUpdate();
                $view->assign('update_file', JRequest::getVar('change', false));
                $view->assign('attachment_id', $attachment_id);
                }
            else {
                require_once(JPATH_COMPONENT_SITE.DS.'views'.DS.'upload'.DS.'view.php');
                $view = new AttachmentsViewUpload();
                }
             $view->assign('save_url', JRoute::_("index.php?option=com_attachments&task=save&tmpl=component"));
            $view->assign('filename', $filename);
             $view->assign('article_id', $article_id);
             $view->assign('article_title', AttachmentsHelper::get_article_title($article_id));
             $view->assign('description', $row->description);
             $view->assign('display_filename', $row->display_filename);
             $view->assign('user_field_1', $row->user_field_1);
             $view->assign('user_field_2', $row->user_field_2);
             $view->assign('user_field_3', $row->user_field_3);
             $view->assign('from', JRequest::getVar('from', ''));
             $view->assign('Itemid', JRequest::getVar('Itemid', 1));
             $view->assignRef('params', $params);

             $view->display(null, $error, $error_msg, true);
            exit();
            }

        // Define where the attachments go
        $upload_url = $params->get('attachments_subdir', 'attachments');
        $upload_dir = JPATH_SITE . DS . $upload_url;

        // Figure out the system filename
        $filename_sys = null;
        $url = null;
        $prepend = $params->get('prepend', 'article_id');
        switch ($prepend) {
        
            case 'article_id': 
                $prefix = sprintf("%03d_", $article_id);
                $filename_sys = $upload_dir . DS . $prefix . $filename;
                $url = $upload_url . "/" . $prefix . $filename;
                break;
                
            // NOTE: for attachment_id, save normally and make a second pass 
            //       to rename the file after we know the attachment ID
            default:
                $filename_sys = $upload_dir . DS . $filename;
                $url = $upload_url . "/" . $filename;           
            }
        
        // If not updating, make sure the system filename doesn't already exist
        $error = false;
        if ( !$update && JFile::exists($filename_sys) && ($prepend != 'attachment_id') ) {
            $error = 'file_not_on_server';
            $error_msg = JText::_('ERROR FILE ALREADY ON SERVER');
            $error_msg .= "   ($filename)";

            if ( $mainframe->isAdmin() )
                return $error_msg;

            // Set up the view to redisplay the form with warnings
            require_once(JPATH_COMPONENT_SITE.DS.'views'.DS.'upload'.DS.'view.php');
            $view = new AttachmentsViewUpload();
            $view->assign('filename', $filename);
             $view->assign('save_url', JRoute::_("index.php?option=com_attachments&task=save&tmpl=component"));
             $view->assign('article_id', $article_id);
             $view->assign('article_title', AttachmentsHelper::get_article_title($article_id));
             $view->assign('description', $row->description);
             $view->assign('display_filename', $row->display_filename);
             $view->assign('user_field_1', $row->user_field_1);
             $view->assign('user_field_2', $row->user_field_2);
             $view->assign('user_field_3', $row->user_field_3);
             $view->assign('from', JRequest::getVar('from', ''));
             $view->assign('Itemid', JRequest::getVar('Itemid', 1));
             $view->assignRef('params', $params);

             $view->display(null, $error, $error_msg, true);
            exit();
            }
        
        // Get the maximum allowed filename length (for the filename display)
        $max_filename_length =$params->get('max_filename_length', 0);
        if ( is_numeric($max_filename_length) ) 
            $max_filename_length = intval($max_filename_length);
        else
            $max_filename_length = 0;
            
        // Create a display filename, if needed (for long filenames)
        if ( ($max_filename_length > 0) and 
             ( strlen($row->display_filename) == 0 ) and
             ( strlen($filename) > $max_filename_length ) ) {
            $row->display_filename = AttachmentsHelper::truncate_filename($filename,
                                                                $max_filename_length);
            }

        // Copy the info about the uploaded file into the new record
        $row->filename = $filename;
        $row->filename_sys = $filename_sys;
        $row->url = $url;
        $row->file_type = $ftype;
        $row->file_size = $_FILES['upload']['size'];
        $row->published = $auto_publish;
        
        // Set the create/modify dates
        jimport('joomla.utilities.date');
        $now = new JDate();
        $row->create_date = $now->toMySQL();
        $row->modification_date = $row->create_date;
        
        // Add the icon file type
        require_once(JPATH_COMPONENT_SITE.DS.'file_types.php');
        $row->icon_filename = AttachmentsFileTypes::icon_filename($filename, $ftype);

        // Save the updated attachment
        if (!$row->store()) {
            JError::raiseError(500, $row->getError());
            }
            
        // Get the upload id
        $db =& JFactory::getDBO();
        $attachment_id = $db->insertid();

        // If we're prepending attachment IDs, fix the system filename and URL and
        // update the attachment record (now that we know the attachment ID)
        if ( $prepend == 'attachment_id' ) {
            $prefix = sprintf("%03d_", $attachment_id);
            $filename_sys = $upload_dir . DS . $prefix . $filename;
            $url = $upload_url . "/" . $prefix . $filename;
            $row->id = $attachment_id;
            $row->filename_sys = $filename_sys;
            $row->url = $url;
            $row->store();
            }

        // Move the file
        $msg = "";
        if (JFile::upload($_FILES['upload']['tmp_name'], $filename_sys)) {
            $size = intval( $row->file_size / 1024.0 );
            chmod($filename_sys, 0644);
            if ( $update )
                $msg = JText::_('UPDATED ATTACHMENT') . ' ' . $filename . " (" . $size . " Kb)!";
            else
                $msg = JText::_('UPLOADED ATTACHMENT') . ' ' . $filename . " (" . $size . " Kb)!";
            }
        else {
            $query ="DELETE FROM #__attachments WHERE id=$attachment_id";
            $db->setQuery($query);
            $result = $db->query();
            $msg = JText::_('ERROR MOVING FILE') . " {$_FILES['upload']['tmp_name']} -> {$filename_sys})";
            }

        // If we are updating, we may need to delete the old file
        if ( $update ) {
            if ( $filename != $old_filename ) {
               JFile::delete($old_filename_sys);
               }
            }
        
        return $msg;
    }


    function download_attachment($id)
    {
        // Verify the user is logged in
        $user =& JFactory::getUser();
        if ( $user->get('username') == '' ) {
            $redirect_to = JRoute::_('index.php?option=com_attachments&task=request_login');
            $this->setRedirect( $redirect_to );
            $this->redirect();
            }
            
        // Get the article ID
        $db =& JFactory::getDBO();
        $query = "SELECT * FROM #__attachments WHERE id='$id' LIMIT 1";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ( count($rows) != 1 ) {
            $errmsg = JText::_('ERROR INVALID ATTACHMENT ID') . " ($id)";
            JError::raiseError(500, $errmsg);
            }
        $article_id = $rows[0]->article_id;

        // NOTE: When 'who_can_see' mode is generalized for secure mode,
        //       we will probably need to add further checking here.

        // Get the component parameters
        jimport('joomla.application.component.helper');
        $params = JComponentHelper::getParams('com_attachments');

        // Get the other info about the attachment
        $download_mode = $params->get('download_mode', 'attachment');
        $content_type = $rows[0]->file_type;
        $filename = $rows[0]->filename;
        $filename_sys = $rows[0]->filename_sys;
        
        // Make sure the file exists
        jimport('joomla.filesystem.file');
        if ( !JFile::exists($filename_sys) ) {
             $errmsg = JText::_('ERROR FILE NOT FOUND ON SERVER') . " ($filename)";
             JError::raiseError(500, $errmsg);
             }
        $len = filesize($filename_sys);

        // Update the download count
        JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_attachments'.DS.'tables');
        $attachment =& JTable::getInstance('attachments', 'Table');
        $attachment->load($id);
        $dl_count = intval($attachment->download_count);
        $attachment->download_count = $dl_count + 1;
        if ( !$attachment->store() ) {
             JError::raiseError(500, $row->getError() );
             }

        // Begin writing headers
        ob_clean(); // Clear any previously written headers in the output buffer
        header("Pragma: public");
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: pre-check=0, post-check=0, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
       
        // Use the desired Content-Type
        header("Content-Type: $content_type");

        // Force the download
        header("Content-Disposition: $download_mode; filename=\"$filename\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".$len);
        @readfile($filename_sys);
        exit;
    }


    function attachmentsTableHTML($article_id, $title, $show_file_links, $allow_modify, $from)
    {
        global $mainframe;
    
        // Load the language files from the backend
        $lang = & JFactory::getLanguage();
        $lang->load('plg_frontend_attachments', JPATH_ADMINISTRATOR);

        // Get the component parameters
        jimport('joomla.application.component.helper');
        $params = JComponentHelper::getParams('com_attachments');

        // Set up to list the attachments for this artticle
        $sort_order = $params->get('sort_order', 'filename');
        if ( $sort_order == 'filename' )
            $order_by = "filename";
        else if ( $sort_order == 'file_size' )
            $order_by = "file_size";
        else if ( $sort_order == 'file_size_desc' )
            $order_by = "file_size DESC";
        else if ( $sort_order == 'description' )
            $order_by = "description";
        else if ( $sort_order == 'create_date' )
            $order_by = "create_date";
        else if ( $sort_order == 'create_date_desc' )
            $order_by = "create_date DESC";
        else if ( $sort_order == 'modification_date' )
            $order_by = "modification_date";
        else if ( $sort_order == 'modification_date_desc' )
            $order_by = "modification_date DESC";
        else if ( $sort_order == 'user_field_1' )
            $order_by = "user_field_1";
        else if ( $sort_order == 'user_field_2' )
            $order_by = "user_field_2";
        else if ( $sort_order == 'user_field_3' )
            $order_by = "user_field_3";
        else if ( $sort_order == 'id' )
            $order_by = "id";
        else
            $order_by = "filename";

        $db =& JFactory::getDBO();
        $query = "SELECT * FROM #__attachments WHERE article_id='$article_id' AND published='1' ORDER BY $order_by";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ( count($rows) == 0 )
            return '';
            
        // Load the permissions functions
        require_once(JPATH_SITE.DS.'components'.DS.'com_attachments'.DS.'permissions.php');

        // Get the permissions for the attachments for this article
        $user =& JFactory::getUser();
        if ( !AttachmentsPermissions::add_permissions($rows, $user, $article_id) ) {
            return '';
            }

        // Scan through the results and see if any of them may be modified
        $some_attachments_modifiable = false;
        if ( $allow_modify ) {
            foreach ( $rows as $row ) {
                if ( $row->user_may_modify ) {
                    $some_attachments_modifiable = true;
                    break;
                    }
                }
            }
            
        // If modifiable, add necessary Javascript for iframe
        if ( $some_attachments_modifiable ) {
            $document = & JFactory::getDocument();
            $document->addScript( JURI::root(true).'/media/system/js/modal.js' );
            JHTML::_('behavior.modal', 'a.modal-button');
            }

        // Get the plugin options
        $style = $params->get('attachments_table_style', 'attachmentsList');
        $secure = $params->get('secure', false);
        $show_column_titles = $params->get('show_column_titles', false);
        $show_description = $params->get('show_description', true);
        $show_file_size = $params->get('show_file_size', true);
        $show_downloads = $params->get('show_downloads', false);
        $show_mod_date = $params->get('show_modification_date', false);
        $file_link_open_mode = $params->get('file_link_open_mode', 'in_same_window');
        if ( $show_mod_date ) {
            $mod_date_format = $params->get('mod_date_format', 'M-j-Y g:ia');
            }

        // Construct the title first
        $rtitle_str = $params->get('attachments_titles', '');
        if ( !$title || (strlen($title) == 0) ) 
            $title = 'ATTACHMENTS TITLE';
        if ( $rtitle_str != '' ) {
            $rtitle_list = split("[\n|\r]", $rtitle_str);
            foreach ($rtitle_list as $rtitle) {
                $rchunks = split(' ', $rtitle, 2);
                if ( $rtitle == '' ) {
                    continue;
                    }
                if ( count($rchunks) == 1 ) {
                    $title = $rtitle;
                    }
                else {
                     if ( is_numeric($rchunks[0]) ) {
                         if ( intval($rchunks[0]) == intval($article_id) ) {
                             $title = trim($rchunks[1]);
                             break;
                             }
                         }
                    else {
                         $title = $rtitle;
                          }
                    }
                }
            }
        $title = JText::_($title);

        // Massage some of the attachments info
        if ( $mainframe->isAdmin() ) {
            $base_url = $mainframe->getSiteURL();
            }
        else {
            $base_url = JURI::Base();
            }
        $icon_url_base = $base_url . 'components/com_attachments/media/icons/';
            
        // Construct the starting HTML
        $html = "\n<div class=\"$style\">\n";
        $html .= "<table>\n";
        $html .= "<caption>$title</caption>\n";
        
        // Add the column titles, if requested
        if ( $show_column_titles ) {
            $html .= "<thead>\n<tr>";
            $html .= "<th class=\"at_filename\">" . JText::_('FILE') . "</th>";
            if ( $show_description )
                $html .= "<th class=\"at_description\">" . JText::_('DESCRIPTION') . "</th>";
            if ( $params->get('user_field_1_name', '') != '' ) {
                $html .= "<th class=\"at_user_field\">" . $params->get('user_field_1_name', '') . "</th>";
               }
            if ( $params->get('user_field_2_name', '') != '' ) {
                $html .= "<th class=\"at_user_field\">" . $params->get('user_field_2_name', '') . "</th>";
               }
            if ( $params->get('user_field_3_name', '') != '' ) {
                $html .= "<th class=\"at_user_field\">" . $params->get('user_field_3_name', '') . "</th>";
               }
            if ( $show_file_size )
                $html .= "<th class=\"at_file_size\">" . JText::_('FILE SIZE') . "</th>";
            if ( $secure && $show_downloads )
                $html .= "<th class=\"at_downloads\">" . JText::_('DOWNLOADS') . "</th>";
            if ( $show_mod_date )
                $html .= "<th class=\"at_mod_date\">" . JText::_('LAST MODIFIED') . "</th>";
             if ( $some_attachments_modifiable )
                $html .= "<th class=\"at_modify\">&nbsp;</th>";
           $html .= "</tr>\n</thead>\n";
            }

        $html .= "<tbody>\n";

        // Construct the lines for the attachments
        $row_num = 0;
        for ($i=0, $n=count($rows); $i < $n; $i++) {
            $row = &$rows[$i];
        
            // Skip this one if it should not be visible
            if ( !$row->user_may_see )
                continue;

            $row_num++;
            if ( $row_num & 1 == 1)
                $html .= '<tr class="odd">';
            else
                $html .= '<tr class="even">';

            // Construct some display items
            if ( strlen($row->icon_filename) > 0 )
                $icon_url = $icon_url_base . $row->icon_filename;
            else
                $icon_url = $icon_url_base . 'generic.gif';
            if ( $show_file_size)
                $file_size = intval( $row->file_size / 1024.0 );
            if ( $show_mod_date ) {
                  jimport( 'joomla.utilities.date' );
                $date = new JDate($row->modification_date, -$mainframe->getCfg('offset'));
                $last_modified = $date->toFormat($mod_date_format);
                }
                      
            // Add the filename
            $target = '';
            if ( $file_link_open_mode == 'new_window')
                $target = ' target="_blank"';
            $html .= '<td class="at_filename">';
            if ( strlen($row->display_filename) == 0 )
                $filename = $row->filename;
            else
                $filename = $row->display_filename;
            if ( $show_file_links ) {
                if ( $secure ) {
                    $url = "index.php?option=com_attachments&task=download&id=" . $row->id;
                    $url = JRoute::_($url);        
                    }
                else {
                    $url = $base_url . $row->url;
                    }
                $tooltip = JText::_('DOWNLOAD THIS FILE') . ' (' . $row->filename . ')';
                $html .= "<a class=\"at_icon\" href=\"$url\"$target title=\"$tooltip\"><img src=\"$icon_url\" alt=\"$tooltip\" /></a>";
                $html .= "<a class=\"at_url\" href=\"$url\"$target title=\"$tooltip\">$filename</a>";
                }
            else {
                $tooltip = JText::_('DOWNLOAD THIS FILE') . ' (' . $row->filename . ')';
                $html .= "<img src=\"$icon_url\" alt=\"$tooltip\" />&nbsp;";
                $html .= $filename;
                }
            $html .= "</td>";
            
            // Add description (maybe)
            if ( $show_description ) {
                $description = $row->description;
                if ( strlen($description) == 0)
                    $description = '&nbsp;';
                if ( $show_column_titles )
                    $html .= "<td class=\"at_description\">$description</td>";
                else
                    $html .= "<td class=\"at_description\">[$description]</td>";
                }

            // Show the USER DEFINED FIELDs (maybe)
            if ( $params->get('user_field_1_name', '') != '' ) {
                $user_field = $row->user_field_1;
                if ( strlen($user_field) == 0 )
                     $user_field = '&nbsp;';
                if ( $show_column_titles )
                    $html .= "<td class=\"at_user_field\">" . $user_field . "</td>";
                else
                    $html .= "<td class=\"at_user_field\">[" . $user_field . "]</td>";
                }
            if ( $params->get('user_field_2_name', '') != '' ) {
                $user_field = $row->user_field_2;
                if ( strlen($user_field) == 0 )
                     $user_field = '&nbsp;';
                if ( $show_column_titles )
                    $html .= "<td class=\"at_user_field\">" . $user_field . "</td>";
                else
                    $html .= "<td class=\"at_user_field\">[" . $user_field . "]</td>";
                }
            if ( $params->get('user_field_3_name', '') != '' ) {
                $user_field = $row->user_field_3;
                if ( strlen($user_field) == 0 )
                     $user_field = '&nbsp;';
                if ( $show_column_titles )
                    $html .= "<td class=\"at_user_field\">" . $user_field . "</td>";
                else
                    $html .= "<td class=\"at_user_field\">[" . $user_field . "]</td>";
                }

            // Add file size (maybe)
            if ( $show_file_size ) {
                $html .= "<td class=\"at_file_size\">$file_size Kb</td>";
                }

            // Show number of downloads (maybe)
            if ( $secure && $show_downloads ) {
                $num_downloads = intval($row->download_count);
                $label = '';
                if ( ! $show_column_titles ) {
                     if ( $num_downloads == 1 )
                         $label = '&nbsp;' . JText::_('DOWNLOAD NOUN');
                     else
                         $label = '&nbsp;' . JText::_('DOWNLOADS');
                     }
                $html .= '<td class="at_downloads">'. $num_downloads.$label.'</td>';
                }

            // Add the modification date (maybe)
            if ( $show_mod_date ) {
                $html .= "<td class=\"at_mod_date\">$last_modified</td>";
                }
                
            // Add the link to delete the article, if requested
            if ( $some_attachments_modifiable && $row->user_may_modify ) {

                // Create the delete link
                $url = "index.php?option=com_attachments&task=update&id={$row->id}";
                $url .= "&from=closeme&tmpl=component";
                $url = JRoute::_($url);
                $update_img = $base_url . 'components/com_attachments/media/pencil.gif';
                $tooltip = JText::_('UPDATE THIS FILE') . ' (' . $row->filename . ')';
                $update_link = '<a class="modal-button" type="button" href="' . $url . '"';
                $update_link .= " rel=\"{handler: 'iframe', size: {x: 800, y: 530}}\"";
                $update_link .= " title=\"$tooltip\"><img src=\"$update_img\" alt=\"$tooltip\" /></a>";

                // Create the delete link
                $url = "index.php?option=com_attachments&task=delete_warning&id={$row->id}&artid=$article_id";
                if ( $from ) {
                    // Add a var to give a hint of where to return to
                    $url .= "&from=$from";
                    }
                else {
                    $url .= "&from=closeme";
                    }
                $url .= "&tmpl=component";
                $url = JRoute::_($url);
                $delete_img = $base_url . 'components/com_attachments/media/delete.gif';
                $tooltip = JText::_('DELETE THIS FILE') . ' (' . $row->filename . ')';
                $del_link = '<a class="modal-button" type="button" href="' . $url . '"';
                $del_link .= " rel=\"{handler: 'iframe', size: {x: 600, y: 300}}\"";
                $del_link .= " title=\"$tooltip\"><img src=\"$delete_img\" alt=\"$tooltip\" /></a>";
                $html .= "<td class=\"at_modify\">$update_link $del_link</td>";
                }
                
            $html .= "</tr>\n";
            }

        // Close the HTML
        $html .= "</tbody></table></div>\n";
        
        return $html;
    }


    function switch_article($row, $old_article_id, $new_article_id)
    {
        // Switch the article as specified, renaming the file as necessary
        // Return success status

        // Get the component parameters
        jimport('joomla.application.component.helper');
        $params = JComponentHelper::getParams('com_attachments');

        // Figure out how filenames are currently constructed
        $prepend = $params->get('prepend', 'article_id');
        
        // Construct the new filename, if we are prepending the article ID
        if ( $prepend == 'article_id' ) {
            $id = $row->id;

            // Get the old filename
            JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_attachments'.DS.'tables');
            $atrow =& JTable::getInstance('attachments', 'Table');
            $atrow->load($id);
            $old_filename_sys = $atrow->filename_sys;

            // Construct the new filename
            $filename = $atrow->filename;
            $upload_url = $params->get('attachments_subdir', 'attachments');
            $upload_dir = JPATH_SITE . DS . $upload_url;
            $prefix = sprintf("%03d_", $new_article_id);
            $new_filename_sys = $upload_dir . DS . $prefix . $filename;
            $new_url = $upload_url . "/" . $prefix . $filename;

            // Rename the file
            jimport('joomla.filesystem.file');
            if ( JFile::exists($new_filename_sys) ) {
                 return 'ERROR CANNOT SWITCH ARTICLE NEW FILENAME ALREADY EXISTS';
                 }
            if ( !rename($old_filename_sys, $new_filename_sys) ) {
                 return 'ERROR CANNOT SWITCH ARTICLE FILE RENAME FAILED';
                 }
                 
            // Save the changes to the attachment record immediatley
            $atrow->filename_sys = $new_filename_sys;
            $atrow->url = $new_url;
            if ( !$atrow->store() ) {
                 JError::raiseError(500, $row->getError() );
                 }
               
            return '';
            }
        else {
            // If $prepend is 'nothing' or 'attachment_id' there is
            // no need to rename the file, so do nothing
            return '';
            }
    }

    function save_warning_message($msg)
    {
        $doc =& JFactory::getDocument();
        $doc->addScriptDeclaration("function save_warning_msg(str) { document.warning_msg = str; };");
        echo "<script>save_warning_msg('$msg');</script>";
    }
    
    function enqueueSystemMessage($msg)
    {
        global $mainframe;
        $mainframe->enqueueMessage($msg, 'message');

        // Persist the message, borrowed from redirect() function in:
        //   libraries/joomla/application/application.php
        $session =& JFactory::getSession();
        $session->set('application.queue', $mainframe->getMessageQueue());
    }
}
