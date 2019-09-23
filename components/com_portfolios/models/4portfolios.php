<?php
// защита от прямого доступа
defined('_JEXEC') or die('Restricted access');
// подключаем класс JModel
jimport('joomla.application.component.model');

class PortfoliosModelPortfolios extends JModel
{
var		$thri_d = null;//JRequest::getVar('thri_df', '', 'post');
var		$photoshop = null;//JRequest::getVar('photoshopf', '', 'post');
var		$corel = null;//JRequest::getVar('corelf', '', 'post');
var		$auto_cad = null;//JRequest::getVar('auto_cadf', '', 'post');
var		$web_disign = null;//JRequest::getVar('web_disignf', '', 'post');
var		$starGroup = null;//JRequest::getVar('starGroupf', '', 'post');

 /**
   * Items total
   * @var integer
   */
  var $_total = null;

  /**
   * Pagination object
   * @var object
   */
  var $_pagination = null;
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


        global $mainframe, $option;

        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
	}

	public function buildQuery()
	{
	 //Добавляем фильтр в запрос выборки

		/*$thri_d = JRequest::getVar('thri_df', '', 'post');
		$photoshop = JRequest::getVar('photoshopf', '', 'post');
		$corel = JRequest::getVar('corelf', '', 'post');
		$auto_cad = JRequest::getVar('auto_cadf', '', 'post');
		$web_disign = JRequest::getVar('web_disignf', '', 'post');
		$starGroup = JRequest::getVar('starGroupf', '', 'post');*/

		$where = 'WHERE ( id > 0 ) ';
		$order = '';
		if ($this->starGroup >= 0 and $this->starGroup != '') {
		 	$where .= ' AND ( star_reyting >= '.$this->starGroup.' ) ';
		 	$order = ' star_reyting DESC , ';
		 	}
		if ($this->thri_d == 1)	$where .= ' AND (thri_d = 1) ';
		if ($this->getState('photoshop') == 1)	$where .= ' AND (photoshop = 1) ';
		if ($this->corel == 1)	$where .= ' AND (corel = 1) ';
		if ($this->auto_cad == 1)	$where .= ' AND (auto_cad = 1) ';
		if ($this->web_disign == 1)	$where .= ' AND (web_disign = 1) ';



			$query 	= 'SELECT *'
					. ' FROM ' . $this->_db->nameQuote('#__portfolios')
					. $where
					. ' ORDER BY '.  $order . ' fio ';
		return  $query;
		}

		/**
	 * Загружает список
	 *
	 * @return array Список объектов
	 */
	public function getFio()
	{

	 //Добавляем фильтр в запрос выборки

		$this->thri_d = JRequest::getVar('thri_df', '', 'post');
		$this->photoshop = JRequest::getVar('photoshopf', '', 'post');
		$this->corel = JRequest::getVar('corelf', '', 'post');
		$this->auto_cad = JRequest::getVar('auto_cadf', '', 'post');
		$this->web_disign = JRequest::getVar('web_disignf', '', 'post');
		$this->starGroup = JRequest::getVar('starGroupf', '', 'post');
        /*
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



			$query 	= 'SELECT *'
					. ' FROM ' . $this->_db->nameQuote('#__portfolios')
					. $where
					. ' ORDER BY '.  $order . ' fio ';*/

			//$this->_db->setQuery($query);
			//$this->_fio = $this->_db->loadObjectList();

			 // if data hasn't already been obtained, load it
	 if (empty($this->_fio))
		{
             $query = $this->buildQuery();
            $this->_fio = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		  return $this->_fio;
	}

	function getTotal()
  {
  		$this->thri_d = JRequest::getVar('thri_df', '', 'post');
		$this->photoshop = JRequest::getVar('photoshopf', '', 'post');
		$this->corel = JRequest::getVar('corelf', '', 'post');
		$this->auto_cad = JRequest::getVar('auto_cadf', '', 'post');
		$this->web_disign = JRequest::getVar('web_disignf', '', 'post');
		$this->starGroup = JRequest::getVar('starGroupf', '', 'post');

        // Load the content if it doesn't already exist
        if (empty($this->_total)) {
            $query = $this->buildQuery();
            $this->_total = $this->_getListCount($query);
        }
        return $this->_total;
  }

  function getPagination()
  {
  		$this->thri_d = JRequest::getVar('thri_df', '', 'post');
		$this->photoshop = JRequest::getVar('photoshopf', '', 'post');
		$this->corel = JRequest::getVar('corelf', '', 'post');
		$this->auto_cad = JRequest::getVar('auto_cadf', '', 'post');
		$this->web_disign = JRequest::getVar('web_disignf', '', 'post');
		$this->starGroup = JRequest::getVar('starGroupf', '', 'post');

        // Load the content if it doesn't already exist
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'),$this->photoshop );
        }
        return $this->_pagination;
  }
}