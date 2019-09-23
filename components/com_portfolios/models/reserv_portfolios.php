<?php
// защита от прямого доступа
defined('_JEXEC') or die('Restricted access');
// подключаем класс JModel
jimport('joomla.application.component.model');

class PortfoliosModelPortfolios extends JModel
{
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
	}

		/**
	 * Загружает список
	 *
	 * @return array Список объектов
	 */
	public function getFio()
	{   //Добавляем фильтр в запрос выборки

		$thri_d = JRequest::getVar('thri_df', '', 'post');
		$photoshop = JRequest::getVar('photoshopf', '', 'post');
		$corel = JRequest::getVar('corelf', '', 'post');
		$auto_cad = JRequest::getVar('auto_cadf', '', 'post');
		$web_disign = JRequest::getVar('web_disignf', '', 'post');
		$starGroup = JRequest::getVar('starGroupf', '', 'post');

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
			$query 	= 'SELECT *'
					. ' FROM ' . $this->_db->nameQuote('#__portfolios')
					. $where
					. ' ORDER BY '.  $order . ' fio ';

			$this->_db->setQuery($query);
			$this->_fio = $this->_db->loadObjectList();
		}

		return $this->_fio;
	}
}