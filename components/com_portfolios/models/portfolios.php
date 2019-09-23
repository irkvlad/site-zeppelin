<?php
// защита от прямого доступа
defined('_JEXEC') or die('Restricted access');
// подключаем класс JModel
jimport('joomla.application.component.model');

class PortfoliosModelPortfolios extends JModel
{

	private $_total = null;

	/**
	 * Объект JPagination
	 *
	 * @var object
	 */
	private $_pagination = null;
	/**
	 * Список админов
	 *
	 * @var array Список объектов
	 */
	private $_fio;

	/**
	 * Конструктор
	 */
	function __construct()
	{
		parent::__construct();

		$app = &JFactory::getApplication();
		// какое кол-во записей выводим на одной странице
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');

		// с какой записи начинаем отображение
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		// В случае изменения $limit, подстраиваем $limitstart
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

	}

	/**
	 * Загружает список
	 *
	 * @return array Список объектов
	 */
	public function getFio()
	{   //Добавляем фильтр в запрос выборки

		$thri_d = JRequest::getVar('thri_df');
		$photoshop = JRequest::getVar('photoshopf');
		$corel = JRequest::getVar('corelf');
		$auto_cad = JRequest::getVar('auto_cadf');
		$web_disign = JRequest::getVar('web_disignf');
		$starGroup = JRequest::getVar('starGroupf');

		$where = 'WHERE ( id > 0 ) ';
		$order = '';
		if ($starGroup >= 0 and $starGroup != '') {
		 	$where .= ' AND ( star_reyting >= '.$starGroup.' ) ';
		 	$order = ' star_reyting DESC , ';
		 	}
		if ($thri_d == 1)	$where .= ' AND (thri_d = 1) ';
		if ($photoshop == 1)	$where .= ' AND (photoshop = 1) ';
		if ($corel == 1)	$where .= ' AND (corel = 1) ';
		if ($auto_cad == 1)	$where .= ' AND (auto_cad = 1) ';
		if ($web_disign == 1)	$where .= ' AND (web_disign = 1) ';


		if (empty($this->_fio))
		{
			$query 	= 'SELECT SQL_CALC_FOUND_ROWS *'
					. ' FROM ' . $this->_db->nameQuote('#__portfolios')
					. $where
					. ' ORDER BY '.  $order . ' fio ';

			$this->_db->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
			$this->_fio = $this->_db->loadObjectList();
		}

		return $this->_fio;
	}

	/**
	 * Получает общее кол-во записей
	 *
	 * @return array of objects
	 */
	public function getTotal()
	{
		if (empty($this->_total))
		{
			$this->_db->setQuery('SELECT FOUND_ROWS();');
			$this->_total = $this->_db->loadResult();
		}

		return $this->_total;
	}



	/**
	 * Возвращает объект пагинации
	 *
	 * @return JPagination object
	 */
	public function getPagination()
	{
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}
}