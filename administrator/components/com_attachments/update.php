<?php
defined('_JEXEC') or die('Restricted access');

global $mainframe;
$mainframe->isAdmin() or die('Must be admin to execute!');

/**
* Attachments component
* @package Attachments
* @Copyright (C) 2007, 2008 Jonathan M. Cameron, All Rights Reserved
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link http://joomlacode.org/gf/project/attachments/frs/
* @author Jonathan M. Cameron
**/

/**
 * A class for update functions
 */
class AttachmentsUpdate
{
    function add_icon_filenames()
    {
        require_once(JPATH_COMPONENT_SITE.DS.'file_types.php');

        // Get all the attachment IDs
        $db =& JFactory::getDBO();
        $query = "SELECT id, filename, file_type, icon_filename FROM #__attachments WHERE file_type IS NULL";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ( count($rows) == 0 ) {
            return 'No file_type fields need updating';
            }
        $IDs = array();
        foreach ($rows as $row) {
            $IDs[] = $row->id;
            }

        // Update the icon file_types all the attachments (that do not have one already)
        $row =& JTable::getInstance('Attachments', 'Table');
        $numUpdated = 0;
        foreach ($IDs as $id) {

            $row->load($id);

            // Only update those attachment records that don't already have an icon_filename
            if ( strlen( $row->icon_filename ) == 0 ) {
                $new_icon_filename = AttachmentsFileTypes::icon_filename($row->filename, $row->file_type);
                if ( strlen( $new_icon_filename) > 0 ) {
                    $row->icon_filename = $new_icon_filename;
                    if (!$row->store()) {
                        JError::raiseError(500, $row->getError());
                        }
                    $numUpdated++;
                    }
                }
            }

        return JText::_('ADDED ICON FILENAMES TO') . " $numUpdated " . JText::_('ATTACHMENT S');
    }


    function update_null_dates($redirect=true)
    {
        global $mainframe;
        
        // Get all the attachment IDs
        $db =& JFactory::getDBO();
        $query = "SELECT * FROM #__attachments";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ( count($rows) == 0 ) {
            return 0;
            }

        // Update the dates for all the attachments
        $numUpdated = 0;
        foreach ($rows as $row) {

            // Update the new create and update dates if they are null
            $updated = false;
            $create_date = $row->create_date;
            if ( is_null($create_date) || $create_date == ''  ) {
                jimport( 'joomla.utilities.date' );
                $cdate = new JDate(filemtime($row->filename_sys), $mainframe->getCfg('offset'));
                $create_date = $cdate->toMySQL();
                $updated = true;
                }
            $mod_date = $row->modification_date;
            if ( is_null($mod_date) || $mod_date == '' ) {
                jimport( 'joomla.utilities.date' );
                $mdate = new JDate(filemtime($row->filename_sys), $mainframe->getCfg('offset'));
                $mod_date = $mdate->toMySQL();
                $updated = true;
                }

            // Update the record
            if ( $updated ) {
                $query = "UPDATE #__attachments set modification_date='{$mod_date}', create_date='{$create_date}' WHERE id='{$row->id}'";
                $db->setQuery($query);
                if (!$db->query()) {
                    echo "Error updating {$row->filename}: " . $db->stderr();
                    }
                $numUpdated++;
                }
            }

            return $numUpdated;
    }


    function update_attachments_table()
    {
        // NOTE: It should be harmless to run this function multiple times

        // Get the existing field names
        $db =& JFactory::getDBO();
        $query = "explain #__attachments";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $fields = array();
        foreach ($rows as $row) {
            $fields[] = $row->Field;
            }

        // Define the columns/fields that may need to be added
        $new_fields = Array();
        $new_fields['display_filename'] =
            "ALTER TABLE #__attachments ADD `display_filename` VARCHAR(80) NOT NULL DEFAULT ''";
        $new_fields['user_field_1'] =
            "ALTER TABLE #__attachments ADD `user_field_1` VARCHAR(100) NOT NULL DEFAULT ''";
        $new_fields['user_field_2'] =
            "ALTER TABLE #__attachments ADD `user_field_2` VARCHAR(100) NOT NULL DEFAULT ''";
        $new_fields['user_field_3'] =
            "ALTER TABLE #__attachments ADD `user_field_3` VARCHAR(100) NOT NULL DEFAULT ''";
        $new_fields['create_date'] =
            "ALTER TABLE #__attachments ADD `create_date` DATETIME DEFAULT NULL";
        $new_fields['modification_date'] =
            "ALTER TABLE #__attachments ADD `modification_date` DATETIME DEFAULT NULL";
        $new_fields['download_count'] =
            "ALTER TABLE #__attachments ADD `download_count` INT(11) UNSIGNED DEFAULT '0'";

        // See if we need to add specific fields
        $num_fields_added = 0;
        $dates_added = false;
        foreach ($new_fields as $field => $query) {
            if ( !in_array( $field , $fields ) ) {
                echo "&nbsp;&nbsp;&nbsp; Adding missing column '$field'<br>";
                $db->setQuery($query);
                 if (!$db->query()) {
                    JError::raiseError( 500,
                        "Error installing field " . $field . ": " . $db->stderr() );
                    return false;
                    }
                if ( $field == 'create_date' )
                     $dates_added = true;
                $num_fields_added++;
                }
            }

        if ( $num_fields_added != 0 )
            echo "";

        if ( $dates_added ) {
            echo "&nbsp;&nbsp;&nbsp; Adding missing create/modification dates (using date each attachment file was created). <br>";
            require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_attachments'.DS.'update.php');
            $num = AttachmentsUpdate::update_null_dates();
            if ( $num == 0 )
                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; No attachments needed updating<br>";
            elseif ( $num == 1 )
                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1 attachment updated<br>";
            else
                echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $num attachments updated<br>";
            }

        // Get the existing indexed column names
        $query = "show index from #__attachments";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $indexes = array();
        foreach ($rows as $row) {
            $indexes[] = $row->Key_name;
            }

        // Define the indexes that may need to get added
        $new_indexes = Array();
        $new_indexes['attachment_article_id_index'] =
            "CREATE INDEX attachment_article_id_index ON `#__attachments` (`article_id`)";
        /* These indeces do not add any benefit
           See: http://dev.mysql.com/doc/refman/5.0/en/order-by-optimization.html
        $new_indexes['attachment_filename_index'] =
            "CREATE INDEX attachment_filename_index ON `#__attachments` (`filename`)";
        $new_indexes['attachment_file_size_index'] =
            "CREATE INDEX attachment_file_size_index ON `#__attachments` (`file_size`)";
        $new_indexes['attachment_create_date_index'] =
            "CREATE INDEX attachment_create_date_index ON `#__attachments` (`create_date`)";
        $new_indexes['attachment_modification_date_index'] =
            "CREATE INDEX attachment_modification_date_index ON `#__attachments` (`modification_date`)";
        */

        // Add any missing indexes
        echo "<br>";
        $num_indexes_added = 0;
        foreach ($new_indexes as $index => $query) {
            if ( !in_array( $index , $indexes ) ) {
                echo "&nbsp;&nbsp;&nbsp; Adding index '$index'<br>";
                $db->setQuery($query);
                 if (!$db->query()) {
                    JError::raiseError( 500, "Error installing index " . $index . ": " . $db->stderr() );
                    return false;
                    }
                $num_indexes_added++;
                }
            }
       echo "<h3>The attachments table is now up to date. <h3>\n";
       
       return true;
    }

    
    function disable_sql_uninstall($dbtype = 'mysql')
    {
       jimport('joomla.filesystem.file');

       // Construct the filenames 
       $filename = JPATH_COMPONENT_ADMINISTRATOR.DS."uninstall.$dbtype.sql";
       $tempfilename = $filename.'.tmp';
       $msg = '';
       
       // Read the content of the old version of the unistall sql file
       $contents = JFile::read($filename);
       $lines = explode("\n", $contents);
       $new_lines = Array();
       for ($i=0; $i < count($lines); $i++) {
           $line = trim($lines[$i]);
           if ( strlen($line) != 0 ) {
                if ( $line[0] != '#' ) {
                    $line = '# ' . $line;
                    }
                $new_lines[] = $line;
                }
           }
           
       // Overwrite the old file with a commented out version
       $new_contents = implode("\n", $new_lines);
       JFile::write($tempfilename, $new_contents);
       if ( ! JFile::copy( $tempfilename, $filename) ) {
            $msg = JText::_('ERROR UPDATING FILE') . ": $filename!";
            } 
            
       // Let the user know what happened
       if ( $msg == '' ) {
           $msg = JText::_('DISABLED UNINSTALLING MYSQL ATTACHMENTS TABLE');
           }
       return $msg;
    }


    function regenerate_system_filenames()
    {
        // Get the component parameters
        jimport('joomla.application.component.helper');
        $params = JComponentHelper::getParams('com_attachments');
  
        // Define where the attachments go
        $upload_url = $params->get('attachments_subdir', 'attachments');
        $upload_dir = JPATH_SITE . DS . $upload_url;
        
        // Get all the attachment IDs
        $db =& JFactory::getDBO();
        $query = "SELECT id FROM #__attachments";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ( count($rows) == 0 ) {
            return 'No records to update null dates for.';
            }
        $IDs = array();
        foreach ($rows as $row) {
            $IDs[] = $row->id;
            }

        // Update the system filenames for all the attachments
        $attachment =& JTable::getInstance('Attachments', 'Table');
        $numUpdated = 0;
        foreach ($IDs as $id) {

            $attachment->load($id);

            // Construct the updated system filename
            $filename_info = pathinfo($attachment->filename_sys);
            $basename = $filename_info['basename'];
            $filename_sys = $upload_dir.DS.$basename;
            
            $attachment->filename_sys = $filename_sys;

            // Update the record
            if (!$attachment->store()) {
                JError::raiseError(500, $attachment->getError());
                }
            $numUpdated++;
            }

        return JText::sprintf( 'UPDATED N ATTACHMENTS', $numUpdated );      
    }

}
