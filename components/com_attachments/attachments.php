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

require_once( JPATH_COMPONENT.DS.'controller.php' );

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_attachments'.DS.'tables');

// Use default controller
$controller = new AttachmentsController( array('default_task' => 'noop') );
$controller->execute( JRequest::getVar('task') );
$controller->redirect();
?>