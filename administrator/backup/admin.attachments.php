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

require_once( JApplicationHelper::getPath( 'admin_html' ) ); 

require_once( JPATH_COMPONENT.DS.'controller.php' ); 

JTable::addIncludePath(JPATH_COMPONENT.DS.'tables'); 

$document = &JFactory::getDocument();
$document->addStyleSheet( $mainframe->getSiteURL() . 'administrator/components/com_attachments/attachments.css', 
                          'text/css', null, array() );

// Check for requests for named controller
$controller = JRequest::getWord('controller', False);
if ( $controller ) {
    // Invoke the named controller, if it exists
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
    jimport('joomla.filesystem.file');
    if ( JFile::exists($path) ) {
        require_once( $path );
        $classname      = 'AttachmentsController'.ucfirst($controller);
        $controller     = new $classname( );
        $controller->execute( JRequest::getCmd( 'task' ) );
        $controller->redirect();
        }
    else {
        echo "<h1>Error! Unable to find controller '$controller'!</h1><br>";
        exit();
        }
    }

// Use default controller
$controller = new AttachmentsController( array('default_task' => 'showAttachments') ); 
$controller->execute( JRequest::getVar( 'task' ) ); 
$controller->redirect();

?>
