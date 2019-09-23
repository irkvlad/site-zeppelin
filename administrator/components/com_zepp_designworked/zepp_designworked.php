<?php

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Require the base controller
require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'controller.php' );
//require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'html.helper.php');
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables');
//$document = JFactory::getDocument();
//$document->addStylesheet(JURI::root(true)."/administrator/components/com_zepp_ringclient/css/ringclient.css");



// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

// Create the controller
$classname	= 'designworkedsController'.$controller;
$controller	= new $classname( );

//echo 'start--zepp_ringclient.php<br>';
//print_R($classname, false);echo ' - $classname <br>';print_R($controller,false);echo ' - $controller <br>';
//echo 'start--zepp_ringclient.php<br>'; 

// Perform the Request task
$controller->execute( JRequest::getVar( 'task', null, 'default', 'cmd' ) );

// Redirect if set by the controller
$controller->redirect();