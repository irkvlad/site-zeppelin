<?php
/**
 * @version       1.5.3 2009-10-12
 * @package       Joomla
 * @subpackage    Project Log
 * @copyright (C) 2009 the Thinkery
 * @link          http://thethinkery.net
 * @license       GNU/GPL see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class projectlogViewcat extends JView
{
	function display($tpl = null)
	{
		global $option, $mainframe;
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.modal', 'a.modal');

		$db   = &JFactory::getDBO();
		$user = &JFactory::getUser();

		$query = 'SELECT * FROM #__users WHERE id = ' . $user->id;
		$db->setQuery($query);
		$usercolor = $db->loadObject();

		$this->baseurl = JURI::base();
		$document      = &JFactory::getDocument();
		$settings      = &JComponentHelper::getParams('com_projectlog');
		$pathway       = &$mainframe->getPathway();
		$document->addStyleSheet($this->baseurl . '/components/com_projectlog/assets/css/projectlog.css');

		$model      = &$this->getModel();
		$catinfo    = &$this->get('data');
		$projects   = &$this->get('projects');
		$pagination = &$this->get('Pagination');
		$logo       = &$this->get('Logo');
		$doc_path   = 'media/com_projectlog/docs/';
		$brak       = &$this->get('Brak');


		$lists = array();

		$filter_order     = $mainframe->getUserStateFromRequest($option . '.cat.filter_order', 'filter_order', 'p.release_date', 'cmd');
		$filter_order_dir = $mainframe->getUserStateFromRequest($option . '.cat.filter_order_dir', 'filter_order_Dir', 'DESC', 'word');
		$filter           = $mainframe->getUserStateFromRequest($option . '.cat.filter', 'filter', '', 'int');
		$search           = $mainframe->getUserStateFromRequest($option . '.cat.search', 'search', '', 'string');
		$search           = $db->getEscaped(trim(JString::strtolower($search)));

		$filters   = array();
		$filters[] = JHTML::_('select.option', '1', JText::_('PROJECT NAME'));
		$filters[] = JHTML::_('select.option', '2', JText::_('RELEASE NUM'));
		$filters[] = JHTML::_('select.option', '3', JText::_('Бригадир'));
		if ($user->id <> 0) $filters[] = JHTML::_('select.option', '4', JText::_('RELEASE MANEGER'));

		$lists['filter']    = JHTML::_('select.genericlist', $filters, 'filter', 'size="1" class="inputbox"', 'value', 'text', $filter);
		$lists['search']    = $search;
		$lists['order']     = $filter_order;
		$lists['order_Dir'] = $filter_order_dir;
		//==========================================================================
		//Список  Менеджер
		// Получаем объект базы данных
		$db =& JFactory::getDBO();
		// Формируем запрос (OR c.catid=4)
		$query = "SELECT c.user_id AS value, c.name AS text FROM #__contact_details AS c WHERE (c.catid=3 ) AND c.published=1 AND c.company <> 2";
		// Выполняем запрос
		$db->setQuery($query);
		// Получаем массив объектов
		$categorylist = $db->loadObjectList();
		// Создаём первый элемент выпадающего списка (<option value="0">Выберите категорию</option>)
		$categories[] = JHTML::_('select.option', '0', "Выберите менеджера", 'value', 'text');
		// Добавляем массив данных из базы данных
		$categories = array_merge($categories, $categorylist);
		// Получаем выпадающий список
		$manager_list = JHTML::_(
			'select.genericlist' /* тип элемента формы */,
			$categories /* массив, каждый элемент которого содержит value и текст */,
			'manager' /* id и name select`a формы */,
			'size="1"' /* другие атрибуты элемента select class="inputbox" */,
			'value' /* название поля в массиве объектов содержащего ключ */,
			'text' /* название поля в массиве объектов содержащего значение */,
			$user->id /* value элемента, который должен быть выбран (selected) по умолчанию */,
			'manager' /* id select'a формы */,
			true /* пропускать ли элементы полей text через JText::_(), default = false */
		);
		//список  технолог
		$db    =& JFactory::getDBO();
		$query = "SELECT c.user_id AS value, c.name AS text FROM #__contact_details AS c WHERE (c.catid=13 ) AND c.published=1";
		$db->setQuery($query);
		$categorylist     = $db->loadObjectList();
		$categories       = $categorie = array();
		$categories[]     = JHTML::_('select.option', '0', "Выберите технолога", 'value', 'text');
		$categories       = array_merge($categories, $categorylist);
		$technicians_list = $categories;

		//список  дизайнер
		$db    =& JFactory::getDBO();
		$query = "SELECT c.user_id AS value, c.name AS text FROM #__contact_details AS c WHERE (c.catid=12 ) AND c.published = 1 ";
		$db->setQuery($query);
		$categorylist = $db->loadObjectList();
		$categories   = $categorie = array();
		$categories[] = JHTML::_('select.option', '0', "Выберите дизайнера", 'value', 'text');
		$categories   = array_merge($categories, $categorylist);
		$chief_list   = $categories;

//======================================================================
		$this->assignRef('catinfo', $catinfo);
		$this->assignRef('projects', $projects);
		$this->assignRef('lists', $lists);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('settings', $settings);
		$this->assignRef('user', $user);
		$this->assignRef('logo', $logo);
		$this->assignRef('document', $document);
		$this->assignRef('doc_path', $doc_path);
		$this->assignRef('usercolor', $usercolor);

		$this->assignRef('manager_list', $manager_list);
		$this->assignRef('technicians_list', $technicians_list);
		$this->assignRef('chief_list', $chief_list);

		$this->assignRef('brak', $brak);

		parent::display($tpl);
	}
}

?>
