<?php
// Защита от прямого доступа
defined('_JEXEC') or die('Restricted access');
 
// Подключаем базовый контроллер
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controller.php');

// Задаем путь для поиска таблиц
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables');

// Создаем контроллер
$controller = new PortfoliosController();
 
// Исполняем задачу
$controller->execute(JRequest::getVar('task', null, 'default', 'cmd'));
 
// Редирект
$controller->redirect();