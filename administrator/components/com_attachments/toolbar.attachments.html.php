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

class TOOLBAR_attachments 
{
    function _EDIT($edit)
    {
        $text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );

        JToolBarHelper::title( JText::_( 'ATTACHMENT' ).': <small>[ '. $text.' ]</small>', 'attachments.png' );

        if ( $edit ) {
            JToolBarHelper::save();
            JToolBarHelper::apply();
            // for existing attachments the button is renamed `close`
            JToolBarHelper::cancel( 'cancel', 'Close' );
            }
        else {
            JToolBarHelper::save('saveNew', 'Save');
            JToolBarHelper::apply('applyNew', 'Apply');
            JToolBarHelper::cancel('myCancel', 'Cancel');
            }
    }

    function _DEFAULT()
    {
        JToolBarHelper::title( JText::_( 'ARTICLE ATTACHMENTS' ), 'attachments');

        JToolBarHelper::help('help', true);
        JToolBarHelper::publishList();
        JToolBarHelper::unpublishList();
        JToolBarHelper::editListX();
        JToolBarHelper::addNewX();
        JToolBarHelper::deleteList();
        JToolBarHelper::preferences( 'com_attachments', $height='500', $width='800' );

        // Add a button for extra admin commands
        $bar = & JToolBar::getInstance('toolbar');
        $bar->appendButton( 'Popup', 'admin', $alt='ADMIN',
             'index.php?option=com_attachments&task=admin&tmpl=component', 
             $width='600', $height='450' );
    }
}
?>