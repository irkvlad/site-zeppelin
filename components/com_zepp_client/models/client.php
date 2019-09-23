<?php
// защита от прямого доступа
defined('_JEXEC') or die('Restricted access');
// подключаем класс JModel
jimport('joomla.application.component.model');
jimport( 'joomla.database.table' );

class clientModelclient extends JModel
{
	/**
	 * Список
	 *
	 * @var array Список объектов
	 */
	private $_client;
	private $_contact;
	private $_clientContact;
	private $_usersInType;
	private $_send;

	/**
	 * Конструктор
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Загружает список клиентов c контактами
	 *
	 * @return ObjectList Список объектов
	 */
	public function getClientContact()   //searchall
	{   //Добавляем фильтр в запрос выборки
         $order = '';
         $where = '';// AND ( id > 0 ) ';

		if (empty($this->_clientContact))
		{
			$query 	= 'SELECT jos_zepp_client_contact.* ,
					modifer_time, modifer_user, on_start, cast, likes, on_send, name, legal_entity '
					. ' FROM ' . $this->_db->nameQuote('#__zepp_client_contact') .' , '. $this->_db->nameQuote('#__zepp_client')
					. ' WHERE `id_client` = jos_zepp_client.id '.$where
					. ' ORDER BY '.  $order . ' name ';

			$this->_db->setQuery($query);
			$this->_clientContact = $this->_db->loadObjectList();
		}

		return $this->_clientContact;
	}

	public function getSearch()   //
	{
		  //$search = "1";
	 	 $search = JRequest::getVar('searchall');

		if (!empty($search))
		{
			$query 	= ' SELECT * FROM `zepp`.`jos_zepp_client`
							WHERE `id` IN (
								SELECT `id`
								FROM `zepp`.`jos_zepp_client`
								WHERE (
									CONVERT(`id` USING utf8) LIKE \'%'.$search.'%\'
									OR CONVERT(`modifer_time` USING utf8) LIKE \'%'.$search.'%\'
									OR CONVERT(`on_start` USING utf8) LIKE \'%'.$search.'%\'
									OR CONVERT(`cast` USING utf8) LIKE \'%'.$search.'%\'
									OR CONVERT(`likes` USING utf8) LIKE \'%'.$search.'%\'
									OR CONVERT(`on_send` USING utf8) LIKE \'%'.$search.'%\'
									OR CONVERT(`name` USING utf8) LIKE \'%'.$search.'%\'
									OR CONVERT(`legal_entity` USING utf8) LIKE \'%'.$search.'%\'
									)

								UNION

								SELECT `id_client`
								FROM  `zepp`.`jos_zepp_client_contact`
								WHERE (
									CONVERT(  `town` USING utf8 ) LIKE  \'%'.$search.'%\'
									OR CONVERT(  `post` USING utf8 ) LIKE  \'%'.$search.'%\'
									OR CONVERT(  `telefon` USING utf8 ) LIKE  \'%'.$search.'%\'
									OR CONVERT(  `email` USING utf8 ) LIKE  \'%'.$search.'%\'
									OR CONVERT(  `fio` USING utf8 ) LIKE  \'%'.$search.'%\'
									OR CONVERT(  `birthday` USING utf8 ) LIKE \'%'.$search.'%\'
								)
							) ';

			$this->_db->setQuery($query);
			$this->_clientContact = $this->_db->loadObjectList();
		}

		return $this->_clientContact;
	}

	/**
	 * Загружает список клиентов
	 *
	 * @return ObjectList Список объектов
	 */
	public function getClient()
	{
		//Добавляем фильтр в запрос выборки
         $order = '';
         $where = '';// AND ( id > 0 ) ';

		//if (empty($this->_client))
		{
			$query 	= 'SELECT *'
					. ' FROM ' . $this->_db->nameQuote('#__zepp_client')
					. $where
					. ' ORDER BY '.  $order . ' name ';

			$this->_db->setQuery($query);
			$this->_client = $this->_db->loadObjectList();
		}

		return $this->_client;
	}

	/**
	 * Загружает список менеджеров клиентов
	 *
	 * @return ObjectList Список объектов
	 */
	public function getContact()
	{   //Добавляем фильтр в запрос выборки
       $order = '';
         $where = '';// AND ( id > 0 ) ';

		if (empty($this->_contact))
		{
			$query 	= 'SELECT *'
					. ' FROM ' . $this->_db->nameQuote('#__zepp_client_contact')
					. $where
					. ' ORDER BY '.  $order . ' fio ';

			$this->_db->setQuery($query);
			$this->_contact = $this->_db->loadObjectList();
		}

		return $this->_contact;
	}

	public function saveClient($data)
	{
		//$table = JTable::getInstance('ZeppClient', 'Table');
		$table =$this->getTable('zepp_client', '');
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

		return $table->id;
	}


	public function saveContact($data)
	{
		$table =$this->getTable('zepp_client_contact', '');
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

	public function editClient($data,$id,$idc)           //send on_start on_send
	{
		$db 		   = & JFactory::getDBO();
		//$data['modifer_user']='';
		$query 	 = ' UPDATE jos_zepp_client SET modifer_user = ';
        $query 	.=($data['modifer_user']) ? $data['modifer_user'] : 0 ;
        $query 	.= ' , cast = ';
        $query 	.=($data['cast']) ? $data['cast']: 0 ;
        $query 	.= ' , likes = ';
        $query 	.=($data['likes']) ? $data['likes'] : 0 ;
        $query 	.= ' , name = \'';
        $query 	.=($data['name']) ? $data['name'] : '' ;
        $query 	.= '\' , legal_entity = \'';
        $query 	.=($data['legal_entity']) ? $data['legal_entity'] : '' ;

        $query 	.= '\' , send = ';
        $query 	.=($data['send']) ? $data['send'] : 0 ;
        $query 	.= ' , on_start = \'';
        $query 	.=($data['on_start']) ? JHTML::_('date', $data['on_start'], '%Y-%m-%d',NULL ) :  date('Y-m-d') ;
        $query 	.= '\' , on_send = \'';
        $query 	.=($data['on_send']) ? JHTML::_('date', $data['on_send'], '%Y-%m-%d',NULL ) :  date('Y-m-d') ;

        $query 	.= '\' WHERE id = '.$id;

		$db->setQuery($query);
		if(!$db->query()){
				// Вывод ошибки, если запрос не выполняется
				$this->setError($db->stderr());
				return false;
			}

		if($idc)
		{
			$i=0;
			foreach( $idc as $ids)
			{
				$contact['id_client'] = $id;
				$contact['town'] = ($data['town'][$i]) ? $data['town'][$i] : '' ;
				$contact['post'] = ($data['post'][$i]) ? $data['post'][$i] : '' ;
				$contact['telefon'] = ($data['telefon'][$i]) ? $data['telefon'][$i] : '' ;
				$contact['email'] = ($data['email'][$i]) ? $data['email'][$i] : '' ;
				$contact['fio'] = ($data['fio'][$i]) ? $data['fio'][$i] : '' ;
				$contact['birthday'] = ($data['birthday'][$i])? $data['birthday'][$i] : '0000-00-00' ;
				$i++;

				if($ids > 0 ) {

					$query 	= ' UPDATE jos_zepp_client_contact'.
                    ' SET id_client = '.$contact['id_client'].
                    ' , town = \''.$contact['town'].
                    '\' , post = \''.$contact['post'].
                    '\' , telefon = \''.$contact['telefon'].
                    '\' , email = \''.$contact['email'].
                    '\' , fio = \''.$contact['fio'].
                    '\' , birthday = '.$contact['birthday'].
                    ' WHERE id = '.$ids;

                    $db->setQuery($query);
					if(!$db->query()){
							// Вывод ошибки, если запрос не выполняется
							$this->setError($db->stderr());
							return false;
						}
				}

				elseif($ids < 0) {
					$s = $ids * (-1);
					$db = & JFactory::getDBO();
					$query=" DELETE FROM `jos_zepp_client_contact` WHERE id = ".$s;
					// Установка запроса в экземпляр класса работы с БД
					$db->setQuery($query);
					//Выполнение запроса
					if(!$db->query()){
						// Вывод ошибки, если запрос не выполняется
						$this->setError($db->stderr());
						return false;
					}
				}

				else{
					$this->saveContact($contact);
				}
			}
   		}

		return true;
	}

    /**
	 * Удаляет
	 *
	 * @return true on success
	 */
	public function remove()
	{
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		foreach ($cids as $id_client){
			$db = & JFactory::getDBO();
			$query=" DELETE FROM `jos_zepp_client_contact` WHERE id_client = ".$id_client;
			// Установка запроса в экземпляр класса работы с БД
			$db->setQuery($query);
			//Выполнение запроса
			if(!$db->query()){
				// Вывод ошибки, если запрос не выполняется
				$this->setError($db->stderr());
				return false;
			}
		}

		$table = $this->getTable('zepp_client', '');
		foreach ($cids as $cid) {
			if (!$table->delete($cid)) {
				$this->setError($table->getErrorMsg());
				return false;
			}
		}


		return $cids;
	}

	public function getSend() //ContactSend
	{
	     $send = JRequest::getVar('sendCon');
		 $SortManager =  JRequest::getVar('manager');
		 $where = '';
		 if( $send == 1 OR $SortManager > 0){
		 	 $where = ' WHERE ';
		 	 if( $send == 1) $where.=' send = 0 ';
		 	 if( $SortManager > 0 AND $send == 1 ) $where.=' AND modifer_user = '.$SortManager;
		 	 elseif( $SortManager > 0)  $where.=' modifer_user = '.$SortManager;
		}
		//if (empty($this->_client)){
			$query 	= 'SELECT *'
					. ' FROM ' . $this->_db->nameQuote('#__zepp_client')
					.$where  //send = '.$send
					. ' ORDER BY  name ';

			$this->_db->setQuery($query);
			$this->_send = $this->_db->loadObjectList();
			//}
			return $this->_send;
	}
}