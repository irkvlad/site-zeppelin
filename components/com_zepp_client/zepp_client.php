<?php
// Защита от прямого доступа
defined('_JEXEC') or die('Restricted access');

// Подключаем базовый контроллер
require_once(JPATH_COMPONENT . DS . 'controller.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'html.helper.php');
// Указываем URL-путь к компоненту
$url_path = JURI::root(true) . '/components/com_zepp_client/';     ///Z:/home/zepp.loc/www/components/com_zepp_client/css/com_zepp_client.css
// Получаем объект документа
$document = JFactory::getDocument();
// Подключаем файл стилей
$document->addStyleSheet($url_path . '/css/com_zepp_client.css');
$document->addScript($url='/includes/js/joomla.javascript.js', $type = "text/javascript");


// Задаем путь для поиска таблиц
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables');

// Создаем контроллер
$controller = new clientController();

// Исполняем задачу
$controller->execute(JRequest::getVar('task', null, 'default', 'cmd'));

// Редирект
$controller->redirect();