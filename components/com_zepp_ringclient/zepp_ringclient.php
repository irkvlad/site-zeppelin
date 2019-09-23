<?php


// no direct access
defined('_JEXEC') or die('Restricted access');

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'html.helper.php');
// Указываем URL-путь к компоненту
$url_path = JURI::root(true) . '/components/com_zepp_ringclient/';     
// Получаем объект документа
$document = JFactory::getDocument();
// Подключаем файл стилей
$document->addStyleSheet($url_path . '/css/zepp_ringclient.css');
$document->addScript($url='/includes/js/joomla.javascript.js', $type = "text/javascript");

// Задаем путь для поиска таблиц
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables');

/*/ Require specific controller if requested
if($controller = JRequest::getVar('controller')) {
	require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
}

// Create the controller
$classname	= 'ringclientController'.$controller;
$controller = new $classname();*/

$controller = new ringclientController();
//$controller->execute(JRequest::getVar('view'));

// Perform the Request task
$controller->execute( JRequest::getVar('task'));

// Redirect if set by the controller
$controller->redirect();

?>
