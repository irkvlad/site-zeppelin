<?php


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );


class ringclientModelRingclient extends JModel
{
    /**
	 * Hellos data array
	 *
	 * @var array
	 */
	var $_data;
        
    /**
	 * 
	 * @return string 
	 */
	function _buildQuery()
	{
		$query = ' SELECT * '
			. ' FROM #__zepp_ringclient '
                        . ' WHERE manager_id = 0'
		;

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


		return $this->_data;
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
        function getGroup_id()
        {
            //* данные текущего usera
            $rUser =  array();        
            $user = JFactory::getUser();
            $rUser['userid'] = 114;
            $rUser['group_id'] = 0;
            $rUser['userlogin'] = '';
            $rUser['username'] ='';
            $rUser['usergid'] = 0;
            
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
            }
                
            return $rUser;
        }
        
        /***
         * Сохраняет заказ за менеджером
         * 
         * возвращает bool
         */
        function  saveManager($id , $userid)
        {
             //запрос к базе на сохране данных  
            $db     = & JFactory::getDBO();
            $query  = "UPDATE `jos_zepp_ringclient` SET ";
            $query .= " `manager_id`= $userid ";  //Кто менеджер
            $query .= ", `manger_data`= '".date('Y-m-d')."' "; // Дата присоединения менеджера
            $query .= " WHERE `id`= $id"; // id записи
            
            $db->setQuery($query);
            if (!$db->query()) {
                // Вывод ошибки, если запрос не выполняется
                JError::raiseWarning( 403, JText::_("Ошибка при формировании заказа: $db->getErrorMsg()") );
                return false;
            } else {
                // Вывод ошибки, если запрос не выполняется                   
                JFactory::getApplication()->enqueueMessage('OK: ', 'notice');
            }
            $record=$this->getRecord_id($id); 
               /* Array ( 
                [id] =>  
                [manager_id] => 
                [client] => Клиент  
                [creator_id] => кто создал
                [creator_name] => кто создал 
                [telefon] => 12321431 
                [tema] =>  
                [creator_date] => Когда создали 
                [manger_data] => когда взял менеджер 
                [project_id] => ) */
            $body = '<h2>Заказ поступил в работу !</h2>'
                    .'<div>'
                        . '<p>Заказ поступил в работу менеджеру '.ringclientHTML::getUserName($userid).' :'
                        . '<br>клиент: '.$record['client']
                        . '<br>телефон: '.$record['telefon']
                        . '<br>описание: '.$record['tema']
                        . '<br>заказ был сформирован: '.$record['creator_name'] 
                        . '<br>заказ сформирован: '.$record['creator_date'] 
                    . '</div>';
            
            ringclientHTML::sentMailToManager($body , $userid);
            ringclientHTML::sentMailToAdmin($body);
            return true;
        }
		
		 /***
         * Сохраняет запись разговора
         * 
         * возвращает bool
         */
        function  saveMediaFile($id , $media_file)
        {
             //запрос к базе на сохране данных  
            $db     = & JFactory::getDBO();
            $query  = "UPDATE `jos_zepp_ringclient` SET ";
            $query .= " `media_file`= '".$media_file."' ";  //
            //$query .= ", `manger_data`= '".date('Y-m-d')."' "; // Дата присоединения менеджера
            $query .= " WHERE `id`= $id"; // id записи
            
            $db->setQuery($query);
            if (!$db->query()) {
                // Вывод ошибки, если запрос не выполняется
                JError::raiseWarning( 403, JText::_("Ошибка при сохранении записи: $db->getErrorMsg() /$id/$media_file/query") );
                return false;
            } else {
                // Вывод если запрос выполняется                   
                JFactory::getApplication()->enqueueMessage('OK: ', 'notice');
            }
            
            return true;
        }
		
        /***
         * Сохраняет запись разговора2 
         * 
         * возвращает bool
         */
        function  saveMediaFile2($id , $media_file)
        {
             //запрос к базе на сохране данных  
            $db     = & JFactory::getDBO();
            $query  = "UPDATE `jos_zepp_ringclient` SET ";
            $query .= " `media_file2`= '".$media_file."' ";  //
            //$query .= ", `manger_data`= '".date('Y-m-d')."' "; // Дата присоединения менеджера
            $query .= " WHERE `id`= $id"; // id записи
            
            $db->setQuery($query);
            if (!$db->query()) {
                // Вывод ошибки, если запрос не выполняется
                JError::raiseWarning( 403, JText::_("Ошибка при сохранении записи: $db->getErrorMsg() /$id/$media_file/query") );
                return false;
            } else {
                // Вывод если запрос выполняется                   
                JFactory::getApplication()->enqueueMessage('OK: ', 'notice');
            }
            
            return true;
        }
		
		  /***
         * Сохраняет запись рентабельность 
         * 
         * возвращает bool
         */
        function  saveRentabelnost($id , $media_file)
        {
             //запрос к базе на сохране данных  
            $db     = & JFactory::getDBO();
            $query  = "UPDATE `jos_zepp_ringclient` SET ";
            $query .= " `rentabelnost`= '".$media_file."' ";  //
            //$query .= ", `manger_data`= '".date('Y-m-d')."' "; // Дата присоединения менеджера
            $query .= " WHERE `id`= $id"; // id записи
            
            $db->setQuery($query);
            if (!$db->query()) {
                // Вывод ошибки, если запрос не выполняется
                JError::raiseWarning( 403, JText::_("Ошибка при сохранении записи: $db->getErrorMsg() /$id/$media_file/query") );
                return false;
            } else {
                // Вывод если запрос выполняется                   
                JFactory::getApplication()->enqueueMessage('OK: ', 'notice');
            }
            
            return true;
        }
        
        /**
         * Данные конкретной записи
         */
        function getRecord() {
            $id = JRequest::getVar('id');
            $query = " SELECT * "
			. " FROM #__zepp_ringclient "
                        . " WHERE id = $id  LIMIT 1"
		;
            $db     = & JFactory::getDBO();
            $db->setQuery($query);
            
            return  $db->loadAssocList();
        }
        
         /**
         * Данные конкретной записи по id
         */
        function getRecord_id($id) {
           
            $query = " SELECT * "
			. " FROM #__zepp_ringclient "
                        . " WHERE id = $id  LIMIT 1"
		;
            $db     = & JFactory::getDBO();
            $db->setQuery($query);
            $record=$db->loadAssocList();
            return  $record[0];
        }
        
        /**
         * Список менеджеров
         */        
        function getManagerList()
        {
            $session = JFactory::getSession();
            $manager_id =  $session->get('manager', '0');//  JRequest::getVar('manager', 0 , 'int');
            // Получаем объект базы данных
            $db     =& JFactory::getDBO(); // Формируем запрос (OR c.catid=4)
            $user 	= & JFactory::getUser();
            if($manager_id === 0) $manager_id = $user->id;
           
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
                        . " AND c.company <> 2"
                    ;
            
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
                             'manager' /* id и name select`a формы */,
                             'size="1"' /* другие атрибуты элемента select class="inputbox" */,
                             'value' /* название поля в массиве объектов содержащего ключ */,
                             'text' /* название поля в массиве объектов содержащего значение */,
                             $manager_id /* value элемента, который должен быть выбран (selected) по умолчанию */,
                             'manager' /* id select'a формы */,
                             true /* пропускать ли элементы полей text через JText::_(), default = false */
                          );
            
            return $manager_list;
        }



}
