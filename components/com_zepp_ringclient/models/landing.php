<?php


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );


class ringclientModelLanding extends JModel
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
 
      // Получаем переменные для постраничной навигации
      $limit = 0; //$mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
      $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
 
      // В был изменен предел, отрегулируйте его
      $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
	  
//echo "limit".$limit;
 
      $this->setState('limit', $limit);
      $this->setState('limitstart', $limitstart);
// 888888
       $session = JFactory::getSession();
       //$date   = JFactory::getDate();
       $manager_id =   JRequest::getVar('manager', -1 , 'int');
       $status     =   JRequest::getVar('status',-1,'int');
       $startdate  =   JRequest::getVar('startdate',-1 );
       $enddate    =   JRequest::getVar('enddate', -1 );
        if ($manager_id >= 0) {
            $session->set( 'manager', $manager_id );
        }
        if ($status >= 0) {
            $session->set( 'status', $status );
        }
       //print_R($startdate,false); print_R($enddate,false);
       if ($startdate  != -1) {
           $session->set( 'startdate', $startdate );
       }
       if ($enddate != -1 ) {
           $session->set( 'enddate', $enddate );
       }
//8888888
    }

    /**
    * 
    * @return string 
    */
   function _buildQuery()
   {
        $M_WHERE = '( manager_id <> 0 ) '; // '( manager_id <> 114 ) ';
        $checkstatus = '';

        $session = JFactory::getSession();
        $date   = JFactory::getDate();

        $search     =   JRequest::getVar('searchall','','string');
        //$startdate  =   JRequest::getVar('startdate', $date->toFormat ('01.%m.%Y') );
        //$enddate    =   JRequest::getVar('enddate', $date->toFormat ('%d.%m.%Y') );
        $creator    =   JRequest::getVar('creator',0,'int');
        //$status     =   JRequest::getVar('status',-1,'int');


       $startdate = $session->get('startdate', $date->toFormat ('01.%m.%Y'));
       $enddate = $session->get('enddate', $date->toFormat ('%d.%m.%Y'));
       $manager_id = $session->get('manager', '0');
       $status = $session->get('status', '0');

 // Формирую WHERE
        switch( $status ) {
            case '1': $checkstatus = ' (`status` = 0 ) '; break;   // движений нет
            case '2': $checkstatus = ' (`status` = 1 ) '; break;   // есть проект
            case '3': $checkstatus = ' (`status` = 2 ) '; break;   // работа не получилась
            case '4': $checkstatus = ' (`status` = 1 OR `status` = 2 ) '; break;
            case '5': $checkstatus = ' (`status` = 5 ) '; break;    // ошибка
            case '0': $checkstatus = ' (`status` < 5 ) '; break;
        }

        if ($startdate <> 0){
            $date = JFactory::getDate($startdate);
            $startdate = $date->toFormat ('%Y-%m-%d 00:00:00');
        } //Если есть выборка повремени (по умолчанию певый день месяца
        if ($enddate <> 0){
            $date = JFactory::getDate($enddate);
            $enddate = $date->toFormat ('%Y-%m-%d 23:59:59');
        } //Если есть выборка повремени (по умолчанию текущий день
        
        if ( ($manager_id > 0) AND ($creator == 0) ) { // ($manager_id <> 114) AND
            
            $M_WHERE ="( manager_id = $manager_id ) ";
        } //Если есть выборка по менеджеру
        
        if ($startdate <> 0){ // Если есть начальная дата
            $M_WHERE .=  " AND ( DATE(creator_date) BETWEEN '$startdate' AND ";
            
            if($enddate == 0 ){ // И нет конечной
                $M_WHERE .= "  DATE(NOW()) ) ";
            }             
            else { //И есть конечная
                $M_WHERE .= " '$enddate' ) ";
            }
        } else if($enddate <> 0){ //Если нет начальной даты но есть конечная
            
            if($startdate == 0 ){ // Это тут всегда так
                $M_WHERE .= " AND (`creator_date` <=  '$enddate') ";
            } else { // это условие ни когда не выполнится ))
                $M_WHERE .= " AND ( DATE(creator_date) BETWEEN '$startdate' AND '$enddate' ) ";
            }            
        }
        
 // Формирую SQL запрос       
        if (($creator == 1)){ // Выборка создателей в интервале
            $query= " SELECT *, COUNT(*) AS col FROM #__zepp_ringclient  "
                    . " WHERE ".$M_WHERE
                    . " AND (status <> 5 ) "
                    . " GROUP BY creator_name ";
            
        } else if (
                    ($creator <> 1) 
                    AND (!empty($search)
                        OR ($M_WHERE <> '( manager_id <> 0 ) ')//OR ($M_WHERE <> '( manager_id <> 114 ) ')
                        ) 
                )	{ // Выборка записей в интервале и по менеджерам
            
            $query 	= ' SELECT * 
                        FROM #__zepp_ringclient
				        WHERE `id` IN (
					                    SELECT `id`
                                        FROM #__zepp_ringclient
						                WHERE '.$M_WHERE.' AND '.$checkstatus;
            if (!empty($search)){ // И если есть строка поиска
                                        $query 	.=' AND ( 
                                                        CONVERT(`client` USING utf8) LIKE \'%'.$search.'%\'
                                                        OR CONVERT(`creator_name` USING utf8) LIKE \'%'.$search.'%\'
                                                        OR CONVERT(`telefon` USING utf8) LIKE \'%'.$search.'%\'
                                                        OR CONVERT(`tema` USING utf8) LIKE \'%'.$search.'%\'                                                
                                                    )
                                ) 
                                                ';
            }else { 
                                        $query 	.=' )';
                
            }
        }else{ // Если нет фильтра
           $query = ' SELECT * '
                   . ' FROM #__zepp_ringclient '
                       . ' WHERE ( manager_id <> 114) AND ( manager_id <> 0) AND '. $checkstatus //
           ;
        }
        //echo 'SQL='.$query.'-ENDSQL';
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

    /***
     * Сохраняет статус заказа
     *
     * возвращает bool
     */
    function  setStatusNO($id , $text)
    {
        //запрос к базе на сохране данных
        $db     = & JFactory::getDBO();
        $query  = "UPDATE `jos_zepp_ringclient` SET ";
        $query .= " `status`= 2 ";  //Статус заявки
        $query .= ", `statustext`= '$text' ";  //Пояснение к статусу
        $query .= ", `statusdata`= '".date('Y-m-d')."' "; // Дата установки статуса
        $query .= " WHERE `id`= $id "; // id записи

        $db->setQuery($query);
        if (!$db->query())  return false;
        return true;
    }

    /***
     * Сохраняет статус заказа
     *
     * возвращает bool
     */
    function  setStatusER($id , $text)
    {
        //запрос к базе на сохране данных
        $db     = & JFactory::getDBO();
        $query  = "UPDATE `jos_zepp_ringclient` SET ";
        $query .= " `status`= 5 ";  //Статус заявки
        $query .= ", `manager_id`= 114 ";  //Нет менеджера
        $query .= ", `statustext`= '$text' ";  //Пояснение к статусу
        $query .= ", `statusdata`= '".date('Y-m-d')."' "; // Дата установки статуса
        $query .= " WHERE `id`= $id "; // id записи

        $db->setQuery($query);
        if (!$db->query())  return false;
        return true;
    }

    /***
     * Сохраняет статус заказа
     *
     * возвращает bool
     */
    function  setStatusYS($id , $text)
    {
        //запрос к базе на сохране данных
        $db     = & JFactory::getDBO();
        $query = "SELECT `id` FROM `jos_projectlog_projects` WHERE `release_id` LIKE '$text' ";
        $db->setQuery($query);
        $projekt= $db->loadresult();

        $query  = "UPDATE `jos_zepp_ringclient` SET ";
        $query .= " `status`= 1 ";  //Статус заявки

        if ($projekt){
            $query .= ", `statustext`= 'Проект № $text' ";  //Пояснение к статусу
            $query .= ", `project_id` = $projekt"; //ID проекта
        }else{
            $query .= ", `statustext`= 'Проект с номером: $text,- не найден' ";  //Пояснение к статусу
        }

        $query .= ", `statusdata`= '".date('Y-m-d')."' "; // Дата установки статуса
        $query .= " WHERE `id`= $id "; // id записи

        $db->setQuery($query);
        if (!$db->query())  return false;
        return true;
    }

    /***
     * Список пректов
     *
     * возвращает строку <input list><datalist>
     */
    function  getlistRelease()
    {
        //запрос к базе на сохране данных
        $db     = & JFactory::getDBO();
        $query = "SELECT `release_id` FROM `jos_projectlog_projects`"
        ." WHERE `category` = 6 OR `category` = 7 OR `category` = 8 OR `category` = 13 ";

        $db->setQuery($query);
        $listRelease_id = $db->loadObjectList();
        $list =  '<datalist id="releases">';
        foreach($listRelease_id as $r ){

            $list .=  '<option value="'.$r->release_id.'" />';
        }

        $list .='</datalist>';


        //print_r($list,false);
        return $list;
    }
   
      
}
