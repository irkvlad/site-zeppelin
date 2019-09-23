<?php
/**
 * Polnocvet for Polnocvet Component
 * 
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link 
 * @license		irkvlad
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

/**
 * Polnocvet Model
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class polnocvetModelMain  extends JModel
{
	var $_query = null;

	/**
	 * Объявляем переменную $_total;
	 * Она будет возвращена функцией getTotal()
	 * Items total
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Объявляем переменную $_pagination в модели;
	 * Она будет возвращена функцией getPagination().
	 * Pagination object
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Hellos data array
	 *
	 * @var array
	 */
	var $_data;
	//var $_look;

	/**
	 * Добавляем в конструктор (или создаем его)
	 * две переменные — $limitstart и $limit.
	 * Они будут нужны для класса JPagination.
	 * @global type $mainframe
	 * @global type $option
	 * @setState('limit', $limit);
	 * @setState('limitstart', $limitstart);
	 */
	function __construct()
	{
		parent::__construct();

		global $mainframe, $option;
        jimport('joomla.html.pagination');
        $total = $this->getTotal();
		// Получаем переменные для постраничной навигации
		$limit = 	$mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');

		$limitstart = JRequest::getVar('limitstart', $total, '', 'int');
		// В был изменен предел, отрегулируйте его
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

//echo "limitstart".$limitstart;

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$session = JFactory::getSession();

		$manager_id =   JRequest::getVar('managerP', -1 , 'int');
        $company =   JRequest::getVar('company', -1 , 'int');
		$status     =   JRequest::getVar('status',-1,'int');
		$startDate  =   JRequest::getVar('startDate',-1 );
		$endDate    =   JRequest::getVar('endDate', -1 );

		//$look	=	JRequest::getVar('look',-1);

		if ($manager_id >= 0) $session->set( 'managerP', $manager_id );
        if ($company >= 0) $session->set( 'company', $company );
		if ($status >= 0) $session->set( 'status', $status );
		if ($startDate  != -1) $session->set( 'startDate', $startDate );
		if ($endDate != -1 ) $session->set( 'endDate', $endDate );

		//if ($look != -1) $session->set( 'look', $look );
	}

	/**
	 *
	 * @return string
	 */
	function _buildQuery()
	{
		$session = JFactory::getSession();
		$date   = JFactory::getDate();
		$startDate = $session->get('startDate', $date->toFormat ('01.%m.%Y'));
		$endDate = $session->get('endDate', $date->toFormat ('%d.%m.%Y'));
		$manager_id = $session->get('managerP', '0');
        $company = $session->get('company', '0');
		$look	=	JRequest::getVar('look',-1);

		//$look			= $session->get('look', '');

		$WHERE = " WHERE ( id != 0 ) ";
		if ( ($manager_id > 0) ) { // ($manager_id <> 114) AND

			$WHERE .=" AND ( manager_id = $manager_id ) ";
		}

		if ( $company > 0 ){
            $WHERE .=" AND ( company = $company ) ";
        }

		if ($startDate <> 0){
			$date = JFactory::getDate($startDate);
			$startDate = $date->toFormat ('%Y-%m-%d 00:00:00');
		} //Если есть выборка повремени (по умолчанию певый день месяца
		if ($endDate <> 0){
			$date = JFactory::getDate($endDate);
			$endDate = $date->toFormat ('%Y-%m-%d 23:59:59');
		} //Если есть выборка повремени (по умолчанию текущий день

		if ($startDate <> 0){ // Если есть начальная дата
			$WHERE .=  " AND ( DATE(date_load) BETWEEN '$startDate' AND ";

			if($endDate == 0 ){ // И нет конечной
				$WHERE .= "  DATE(NOW()) ) ";
			}
			else { //И есть конечная
				$WHERE .= " '$endDate' ) ";
			}
		} else if($endDate <> 0){ //Если нет начальной даты но есть конечная

			if($startDate == 0 ){ // Это тут всегда так
				$WHERE .= " AND (`date_load` <=  '$endDate') ";
			} else { // это условие ни когда не выполнится ))
				$WHERE .= " AND ( DATE(date_load) BETWEEN '$startDate' AND '$endDate' ) ";
			}
		}
		switch( $look ) {// анализируем запрос
			case 'Новые':
				$WHERE .= " AND ( set_date IS NULL ) ";
                //$WHERE .= " AND ( status = 0 ) ";
				break;
			case 'Сегодня':
				$WHERE .= " AND ( set_date = DATE(NOW()) ) ";
				break;
			case 'Просроченные':
				$WHERE .= " AND (  (set_date < DATE(NOW())) AND ((status != 2) AND (status != 4))  ) ";

				break;
			case 'Брак':
				$WHERE .= " AND ( status = 3 ) ";
				break;
			case 'Готовые':
				$WHERE .= " AND ( (status = 2) OR (status = 4) ) ";
				break;
		}
		
		$query = 'SELECT * FROM #__zepp_polnocvet'
		. $WHERE
		;
		return $query;
	}

	/**
	 * Пересмотрим функцию getData() в компоненте.
	 * Добавим значения переменных $limitstart и $limit в метод _getList().
	 * Это позволит выбирать нужные строки, а не все строки.
	 * @return array Array of objects containing the data from the database
	 */
	function getData()
	{
		// if data hasn't already been obtained, load it
		if (empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_data;
	}

	/**
	 * Создадим новую функцию getTotal().
	 * В этой функции будем использовать метод _getListCount() класса JModel.
	 * Эта функция будет возвращать общее количество строк в запросе.
	 * Значение, которое она будет возвращать будет использоваться в
	 * следующей функции getPagination().
	 *
	 * @return type int
	 */
	function getTotal()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}
		return $this->_total;
	}

	/**
	 * Создадим функцию getPagination().
	 * Эта функция будет создавать и возвращать новый объект Pagination,
	 * который будет передаваться в Вид.
	 * @return type
	 */
	function getPagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
	
	
	
	/**
	 * Gets the greeting
	 * @return string The greeting to be displayed to the user
	 */
	public function getPolnocvet()
	{
		$db =& JFactory::getDBO();

		$query = 'SELECT * FROM #__zepp_polnocvet'; //
		$db->setQuery( $query );
		$polnocvet = $db->loadObjectList();

		return $polnocvet;
	}


	/**
	 * Список компаний
	 */
	function getListCompany()
	{
        $session = JFactory::getSession();
        $company = $session->get('company', '0');

        $db     =& JFactory::getDBO();

		$query = " SELECT id AS value , name AS text "

			." FROM "
			. " jos_zepp_company "

		;

		$db->setQuery($query);
		$companylist = $db->loadObjectList();
		// Создаём первый элемент выпадающего списка (<option value="0">Выберите категорию</option>)
		$companyes[] = JHTML::_('select.option',  '0', "Выберите фирму", 'value', 'text' );
		// Добавляем массив данных из базы данных
        $companyes = array_merge( $companyes, $companylist);

		//$categories[] = JHTML::_('select.option',  '114', "Без менеджера", 'value', 'text' );
		// Получаем выпадающий список
		$company_list = JHTML::_(
			'select.genericlist' /* тип элемента формы */,
            $companyes /* массив, каждый элемент которого содержит value и текст */,
			'company' /* id и name select`a формы */,
			'size="1"  onchange="changeCompany()"' /* другие атрибуты элемента select class="inputbox" */,
			'value' /* название поля в массиве объектов содержащего ключ */,
			'text' /* название поля в массиве объектов содержащего значение */,
            $company /* value элемента, который должен быть выбран (selected) по умолчанию */,
			'company' /* id select'a формы */,
			true /* пропускать ли элементы полей text через JText::_(), default = false */
		);

		return $company_list;
	}

    function getCompanyColor(){

        $db     =& JFactory::getDBO();

        $query = " SELECT * "

            ." FROM "
            . " jos_zepp_company "

        ;

        $db->setQuery($query);
        $companylist = $db->loadObjectList();
        $companyColor = array();
            foreach ($companylist as $com){
                $color = $com->color ;
                $key = $com->id;
                $companyColor[$key] = $color;
            }

        return $companyColor;
    }


    /**
     * Список менеджеров
     */
    function getManagerList()
    {
        $session = JFactory::getSession();
        $manager_id = $manager_id = $session->get('managerP', '0');//  JRequest::getVar('manager', 0 , 'int');
        $session = JFactory::getSession();
        $company = $session->get('company', '0');
        // Получаем объект базы данных
        $db     =& JFactory::getDBO(); // Формируем запрос (OR c.catid=4)
        $user 	= & JFactory::getUser();
        if($manager_id === 0) $manager_id = $user->id;

        $query = " SELECT "
            . " user_id AS value, "
            . " name AS text  "
            ." FROM "
            . " jos_contact_details "

            ." WHERE "
            . " catid = 3 "
            . " AND published = 1 "
        ;

        if ($company > 0 ){
            $query .= " AND  company = $company " ;
        }

        $db->setQuery($query);
        $categorylist = $db->loadObjectList();
        // Создаём первый элемент выпадающего списка (<option value="0">Выберите категорию</option>)
        $categories[] = JHTML::_('select.option',  '0', "Выберите менеджера", 'value', 'text' );
        // Добавляем массив данных из базы данных
        $categories = array_merge( $categories, $categorylist);

        //$categories[] = JHTML::_('select.option',  '114', "Без менеджера", 'value', 'text' );
        // Получаем выпадающий список
        $manager_list = JHTML::_(
            'select.genericlist' /* тип элемента формы */,
            $categories /* массив, каждый элемент которого содержит value и текст */,
            'managerP' /* id и name select`a формы */,
            'size="1"' /* другие атрибуты элемента select class="inputbox" */,
            'value' /* название поля в массиве объектов содержащего ключ */,
            'text' /* название поля в массиве объектов содержащего значение */,
            $manager_id /* value элемента, который должен быть выбран (selected) по умолчанию */,
            'managerP' /* id select'a формы */,
            true /* пропускать ли элементы полей text через JText::_(), default = false */
        );

        return $manager_list;
    }

	
	
	
	/**
	 * Нужные групповые принадлежность текущего пользователя
	 *
	 * возвращет ассоциативный массив:
	 *  userid - id пользователя joomlы;
	 *  group_id - группа пользователя в проектах;
	 *  userlogin - логин пользователя в joomlе;
	 *  username - имя пользователя в joomlеuserid;
	 *  - группа пользователя в joomle
	 */
	public function getGroup_id()
	{
		//* данные текущего usera
		$rUser =  array();
		$user = JFactory::getUser();
		$rUser['userid'] = 114;
		$rUser['group_id'] = 0;
		$rUser['userlogin'] = '';
		$rUser['username'] ='';
		$rUser['usergid'] = 0;
		$rUser['polnocvet'] = 0;

		if (!$user->guest) {
			$rUser['userlogin'] = $user->username;
			$rUser['username'] = $user->name;
			$rUser['userid'] = $user->id;
			$rUser['usergid'] = $user->gid;
			//Является ли user менеджером
			$db     = & JFactory::getDBO();
			$query = 'SELECT group_id FROM jos_projectlog_groups_mid WHERE user_id =' . $user->id;
			$db->setQuery($query);
			$rUser['group_id'] = $db->loadResult();
			$query = 'SELECT polnocvet FROM jos_users WHERE id =' . $user->id;
			$db->setQuery($query);
			$rUser['polnocvet'] = $db->loadResult();
			$query = 'SELECT pr_user_id FROM jos_users WHERE id =' . $user->id;
			$db->setQuery($query);
			$rUser['pr_user'] = $db->loadResult();
            $query = 'SELECT company FROM jos_contact_details WHERE  	user_id =' . $user->id;
            $db->setQuery($query);
            $rUser['company'] = $db->loadResult();

		}

		
		return $rUser;
	}

	/**
	 * Сохранение записи
	 */
	 public function saveRecord()
	{
		$table =$this->getTable('polnocvet', '');
		$link = JRequest::getVar('link');
		$file = JRequest::getVar('file');
		if (!$link){
			$link = "\\\\Cehcom\\хранилище 1\\";
		}

		$data = array();
		$data['link'] = $link;
		$data['file'] = $file;
		$data['manager_id'] = $this->getGroup_id();


		// привязываем поля формы к таблице
		if (!$table->bind($data))
		{
			$this->setError($table->getError());
			return false;
		}
		// проверяем данные
		if ($table->check($data))
		{
			// сохраняем данные
			if (!$table->store($data))
			{
				$this->setError($table->getError());
				return false;
			}
		}
		else
		{
			$this->setError($table->getError());
			return false;
		}


		return true;
	}	
	
}
