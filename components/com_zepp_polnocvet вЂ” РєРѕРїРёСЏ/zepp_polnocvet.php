<?php
/**
 * Polnocvet entry point file for Polnocvet Component
 * 
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link 
 * @license		irkvlad
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'html.helper.php');

// Указываем URL-путь к компоненту
$url_path = JURI::root(true) . '/components/com_zepp_polnocvet/';
$document = JFactory::getDocument();
// Подключаем файл стилей
$document->addStyleSheet($url_path . '/css/zepp_polnocvet.css');
$document->addScript($url='/includes/js/joomla.javascript.js', $type = "text/javascript");
$document->addScript($url='/includes/js/jquery-3.1.0.min.js', $type = "text/javascript");

// Задаем путь для поиска таблиц
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables');

// Require specific controller if requested
if($controller = JRequest::getVar('controller')) {
	require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
}

// Create the controller
$classname	= 'PolnocvetController'.$controller;
$controller = new $classname();

// Perform the Request task
$controller->execute( JRequest::getVar('task'));

// Redirect if set by the controller
$controller->redirect();


