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

/**
 * The controller for special requests
 * (adapted from administrator/components/com_config/controllers/component.php) 
 */
class AttachmentsControllerSpecial extends JController
{
    /**
     * Custom Constructor
     */
    function __construct( $default = array())
    {
        $default['default_task'] = 'noop';
        parent::__construct( $default );
    }

    function noop()
    {
        echo "<h1>Special function not specified!</h1>";
        exit();
    }
    
    function editParams()
    {   
        // This function allows editing the Attachments parameters
        // with a form that has a regular submit button (not Javascript).
        //
        // This function is for automated testing of the Attachments 
        // extension and as exactly the same functionality as the regular
        // Attachments parameter editor in the component manager, except
        // that it is not a pop-up window.
        //
        // Due to the implementation of the component parameter editor for
        // pop up frames, this save form pops back into edit mode after saving.

        $model = $this->getModel('Special');
        if ( !$model ) {
            echo "Unable to find Special model! <br>";
            exit();
            }
            
        $table =& JTable::getInstance('component');
        if (!$table->loadByOption( 'com_attachments' ))
        {
                JError::raiseWarning( 500, 'Unable to load Attachments component' );
                return false;
        }
                
        require_once(JPATH_COMPONENT.DS.'views'.DS.'special'.DS.'view.php');
        $view = new AttachmentsViewSpecial( );
        $view->assignRef('component', $table);
        $view->setModel( $model, true );
        $view->display();
    }
    

    function showSEF()
    {
        global $mainframe;
        echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
        echo "<html><head><title>SEF Status</title></head><body>";
        echo "SEF: " . $mainframe->getCfg('sef') . "<br>";
        echo "</body></html>";
        exit();
    }

    function listAttachmentIDs()
    {
        $db =& JFactory::getDBO();
        $query = "SELECT attach.id,article_id,art.sectionid,art.catid FROM #__attachments as attach ";
        $query .= "LEFT JOIN #__content as art ON attach.article_id = art.id ORDER BY art.id";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
        echo "\n<html><head><title>Attachment IDs</title></head><body>\n";
        echo "Attachment IDS:";
        foreach ($rows as $row) {
            echo " " . $row->id . "/" . $row->article_id . "/" . $row->sectionid . "/" . $row->catid;
            }
        echo "\n</body></html>";
        exit();  
    }


    // Define some functions for URLs to invoke the udpate functions
    //   (We could move these to an update controller...)
    //

    function add_icon_filenames()
    {
        global $option;
        require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_attachments'.DS.'update.php');
        $msg = AttachmentsUpdate::add_icon_filenames();
        $this->setRedirect('index.php?option=' . $option, $msg);
    }
    
    
    function update_null_dates($redirect=true)
    {
        global $option;
        require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_attachments'.DS.'update.php');
        
        $numUpdated = AttachmentsUpdate::update_null_dates();
        $msg = JText::sprintf( 'UPDATED N ATTACHMENTS', $numUpdated );      
        $this->setRedirect('index.php?option=' . $option, $msg);
    }


    function update_attachments_table()
    {
        global $option;
        require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_attachments'.DS.'update.php');

        echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
        echo "\n<html><head><title>Updating Attachments Tables</title></head><body>\n";
        echo "<h2>Updating Attachments Tables</h2>\n";

        AttachmentsUpdate::update_attachments_table();

        $return_url = JURI::base();
        echo "<br>&nbsp;<br><a href=\"$return_url\">Return to Admin page</a>\n";
        echo "</body>\n</html>";

        exit();
     }
     
    function disable_sql_uninstall()
    {
        require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_attachments'.DS.'update.php');
        
        $close =JRequest::getVar('close', false);
        $msg = AttachmentsUpdate::disable_sql_uninstall();

        if ( $close ) {
            require_once(JPATH_COMPONENT_SITE.DS.'helper.php');
            AttachmentsHelper::enqueueSystemMessage($msg);

            // Close this window and refesh the parent window
            echo "<script>window.parent.document.getElementById('sbox-window').close();
                  window.parent.location.reload();</script>";
            }
        else {
            global $option;
            $this->setRedirect('index.php?option=' . $option, $msg); 
            }
    }
    
    
    function regenerate_system_filenames()
    {
        require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_attachments'.DS.'update.php');
        
        $close =JRequest::getVar('close', false);
        $msg = AttachmentsUpdate::regenerate_system_filenames();

        if ( $close ) {
            require_once(JPATH_SITE.DS.'components'.DS.'com_attachments'.DS.'helper.php');
            AttachmentsHelper::enqueueSystemMessage($msg);
            echo "<script>window.parent.document.getElementById('sbox-window').close();
                  window.parent.location.reload();</script>";
            }
        else {
            global $option;
            $this->setRedirect('index.php?option=' . $option, $msg); 
            }
    }


}
