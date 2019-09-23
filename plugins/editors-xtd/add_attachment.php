<?php
/**
* Add Attachments Button plugin
* @package Attachments
* @Copyright (C) 2007, 2008 Jonathan M. Cameron, All Rights Reserved
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link http://joomlacode.org/gf/project/attachments/frs/
* @author Jonathan M. Cameron
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.event.plugin');


class plgButtonAdd_attachment extends JPlugin
{
    /**
     * Constructor
     *
     * For php4 compatability we must not use the __constructor as a constructor for plugins
     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
     * This causes problems with cross-referencing necessary for the observer design pattern.
     *
     * @param       object $subject The object to observe
     * @param       array  $config  An array that holds the plugin configuration
     * @since 1.5
     */
    function plgAdd_attachment(& $subject, $config) 
    {
        parent::__construct($subject, $config);
    }

    /**
     * Add Attachment button
     *
     * @return a button
     */
    function onDisplay($name)
    {   
        // Avoid displaying the button for anything except content articles
        global $option;
        if ( $option != 'com_content' ) {
            return new JObject();
            }
 
        // Get the article ID
        $cid = JRequest::getVar( 'cid', array(0), '', 'array');
        $id = 0;
        if ( count($cid) > 0 ) {
            $id = intval($cid[0]);
            }
        if ( $id == 0) {
            $nid = JRequest::getVar( 'id', null);
            if ( !is_null($nid) ) {
                $id = intval($nid);
                }
            }

        // Create the button object
        $button = new JObject();
            
        // Load the language file from the backend
        $lang = & JFactory::getLanguage();
        $lang->load('plg_frontend_attachments', JPATH_ADMINISTRATOR);
        
        // ??? Where is the tooltip coming from?
        // (Would like to use different tip for create article button)

        // Figure out where we are and construct the right link and set
        // up the style sheet (to get the visual for the button working)
        global $mainframe;
        $doc =& JFactory::getDocument();
        if ( $mainframe->isAdmin() ) {
            if ( $id == 0 ) {
                // New article, explain that we can't add attachments until it is saved
                
                // Load the language file from the frontend
                $lang = & JFactory::getLanguage();
                $lang->load('com_attachments', JPATH_SITE);
                
                // Save the warning message for the pop-up window
                require_once(JPATH_BASE.DS.'..'.DS.'components'.DS.'com_attachments'.DS.'helper.php');
                $msg = JText::_('SAVE ARTICLE BEFORE ATTACHING')."  ".JText::_('TRY APPLY BUTTON FIRST');
                AttachmentsHelper::save_warning_message($msg);

                $button->set('options', "{handler: 'iframe', size: {x: 400, y: 300}}");
                $link = "index.php?option=com_attachments&task=warning&tmpl=component";
                }
            else {
                $button->set('options', "{handler: 'iframe', size: {x: 800, y: 530}}");
                $link = "index.php?option=com_attachments&task=add&article_id=$id&from=closeme";
                }
            $doc->addStyleSheet( $mainframe->getSiteURL() . 'plugins/editors-xtd/add_attachment.css', 
                                 'text/css', null, array() );
            }
        else {
            if ( $id == 0 ) {
                // Load the language file from the frontend
                $lang = & JFactory::getLanguage();
                $lang->load('com_attachments', JPATH_SITE);

                // Save the warning message for the pop-up window
                require_once(JPATH_BASE.DS.'components'.DS.'com_attachments'.DS.'helper.php');
                $msg = JText::_('SAVE ARTICLE BEFORE ATTACHING');
                AttachmentsHelper::save_warning_message($msg);

                // New article, explain we can't add attachments until it is saved
                $button->set('options', "{handler: 'iframe', size: {x: 400, y: 300}}");
                $link = "index.php?option=com_attachments&task=warning&tmpl=component";
                }
            else {
                $button->set('options', "{handler: 'iframe', size: {x: 700, y: 530}}");
                $link = "index.php?option=com_attachments&task=upload&artid=$id&from=closeme";
                }
            $doc->addStyleSheet( JURI::Base() . 'plugins/editors-xtd/add_attachment.css', 
                                 'text/css', null, array() );
            }

        $button->set('modal', true);
        $button->set('class', 'modal');
        $button->set('text', JText::_('ADD ATTACHMENT'));
        $button->set('name', 'add_attachment');
        $button->set('link', $link);
        $button->set('image', 'add_attachment.png');

        return $button;
    }
}
?>
