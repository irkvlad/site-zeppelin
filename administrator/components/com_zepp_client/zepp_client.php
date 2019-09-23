<?php
// Защита от прямого доступа
defined('_JEXEC') or die('Restricted access');

// Подключаем базовый контроллер
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controller.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'html.helper.php');
$document = JFactory::getDocument();
$document->addStylesheet(JURI::root(true)."/administrator/components/com_zepp_client/css/com_zepp_client.css");

// Задаем путь для поиска таблиц
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables');

// Создаем контроллер
$controller = new clientController();

// Исполняем задачу
$controller->execute(JRequest::getVar('task', null, 'default', 'cmd'));

// Редирект
$controller->redirect();