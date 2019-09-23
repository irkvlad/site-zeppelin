<?php


// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );


class ringclientsModelRingclients extends JModel
{
	/**
	 * Hellos data array
	 *
	 * @var array
	 */
	var $_data;


	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	 function _buildQuery()
   {
        $M_WHERE = ' manager_id <> 0 ';
        $date = JFactory::getDate();
        $search = JRequest::getVar('searchall','','string');        
        $manager_id = JRequest::getVar('manager', 0 , 'int');        
        $startdate = JRequest::getVar('startdate', $date->toFormat ('01.%m.%Y') );
        $enddate= JRequest::getVar('enddate', $date->toFormat ('%d.%m.%Y') );
        $creator=JRequest::getVar('creator',0,'int');
         
 // Формирую WHERE
        if ($startdate <> 0){
            $date = JFactory::getDate($startdate);
            $startdate = $date->toFormat ('%Y-%m-%d 00:00:00');
        } //Если есть выборка повремени (по умолчанию певый день месяца
        if ($enddate <> 0){
            $date = JFactory::getDate($enddate);
            $enddate = $date->toFormat ('%Y-%m-%d 23:59:59');
        } //Если есть выборка повремени (по умолчанию текущий день
        
        if (($manager_id <> 114) AND ($manager_id > 0) AND ($creator == 0) ) {
            
            $M_WHERE ="( manager_id = $manager_id )";
        } //Если есть выборка по менеджеру
        
        if ($startdate <> 0){ // Если есть начальная дата
            $M_WHERE .=  " AND ( DATE(creator_date) BETWEEN '$startdate' AND ";
            
            if($enddate == 0 ){ // И нет конечной
                $M_WHERE .= "  DATE(NOW()) )";
            }             
            else { //И есть конечная
                $M_WHERE .= " '$enddate' )";
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
                    . "GROUP BY creator_name";            
            
        } else if (
                    ($creator <> 1) 
                    AND (!empty($search) 
                            OR ($M_WHERE <> ' manager_id <> 0 ')
                        ) 
                )	{ // Выборка записей в интервале и по менеджерам
            
            $query 	= ' SELECT * 
                                FROM #__zepp_ringclient
				WHERE `id` IN (
					SELECT `id`
                                            FROM #__zepp_ringclient
						WHERE '.$M_WHERE;
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
                       //. ' WHERE manager_id <> 114'
           ;
        }
        //echo 'SQL='.$query.'-ENDSQL';
     return $query;
   }

	/**
	 * Retrieves the hello data
	 * @return array Array of objects containing the data from the database
	 */
	function getData()
	{
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data ))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList( $query );
		}
//echo 'start--models\ringclient.php<br>';
//print_R($this->_data,false);
//echo 'end--models\ringclient.php<br>';

		return $this->_data;
	}
        
        function getManagerList()
        {
            // Получаем объект базы данных
            $db     =& JFactory::getDBO(); // Формируем запрос (OR c.catid=4)
            $user 	= & JFactory::getUser();
           
            $query = " SELECT " 
                        . " c.user_id AS value, "
                        . " c.name AS text  "
                    ." FROM "
                        . " jos_contact_details AS c, "
                        . " jos_projectlog_groups_mid AS m "	
                    ." WHERE "
                        . " (m.group_id=10 ) "
                        . " AND c.published=1 "
                        . " AND m.user_id = c.user_id " 
                    ;
            
            $db->setQuery($query);           
            $categorylist = $db->loadObjectList();
            // Создаём первый элемент выпадающего списка (<option value="0">Выберите категорию</option>)
            $categories[] = JHTML::_('select.option',  '0', "Выберите менеджера", 'value', 'text' );
            // Добавляем массив данных из базы данных
            $categories = array_merge( $categories, $categorylist);
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
            
            return $manager_list;
        }
}