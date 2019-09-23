<?php
// Защита от прямого доступа
defined('_JEXEC') or die('Restricted access');
 
// Подключаем базовый контроллер
require_once(JPATH_COMPONENT . DS . 'controller.php');

// Задаем путь для поиска таблиц
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables');

// Указываем URL-путь к компоненту
$url_path = JURI::root(true) . '/components/com_portfolios/';
// Получаем объект документа
$document = JFactory::getDocument();
// Подключаем файл стилей
$document->addStyleSheet($url_path . 'assets/style.css');

// Создаем контроллер
$controller = new PortfoliosController();
// Исполняем задачу
$controller->execute(JRequest::getVar('task', null, 'default', 'cmd'));
// Редирект
$controller->redirect();