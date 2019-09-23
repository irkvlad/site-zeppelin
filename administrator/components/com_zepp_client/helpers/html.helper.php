<?php
defined('_JEXEC') or die('Restricted access');
//jimport( 'joomla.database.table' );


class clientHTML{
	public function getUsersInType($type)
	{
		$db 		   = & JFactory::getDBO();
		$query 	= 'SELECT user_id  FROM jos_projectlog_groups_mid '
					. ' WHERE `group_id` = '.$type;

		$db->setQuery($query);
		$usersInType = $db->loadResultArray();
		return  $usersInType;
	}

	/**
	 * Загружает список менеджеров клиентов
	 *
	 * @return ObjectList Список объектов
	 */
	public function getContact($id_client)
	{   //Добавляем фильтр в запрос выборки
       $order = '';
       $where = '';// AND ( id > 0 ) ';
       if($id_client) $where=' WHERE id_client = '.$id_client;
		$db 		   = & JFactory::getDBO();
			$query 	= 'SELECT *'
					. ' FROM ' . $db->nameQuote('#__zepp_client_contact')
					. $where
					. ' ORDER BY '.  $order . ' fio ';

			$db->setQuery($query);
			$contact = $db->loadObjectList();


		return $contact;
	}

		/**
	 * Загружает список клиентов
	 *
	 * @return ObjectList Список объектов
	 */
	public function getClient($id)
	{   //Добавляем фильтр в запрос выборки
         $order = '';
         $where = '';// AND ( id > 0 ) ';
         if($id) $where=' WHERE id = '.$id;
         $db 		   = & JFactory::getDBO();

			$query 	= 'SELECT *'
					. ' FROM ' . $db->nameQuote('#__zepp_client')
					. $where
					. ' ORDER BY '.  $order . ' name ';

			$db->setQuery($query);
			$client = $db->loadObjectList();


		return $client;
	}


	/**
	 * Загружает список клиентов c контактами
	 *
	 * @return ObjectList Список объектов
	 */
	 /*
	public function getClientContact($id_client)
	{   //Добавляем фильтр в запрос выборки
         $order = '';
         $where = '';// AND ( id > 0 ) ';
        $db 		   = & JFactory::getDBO();

			$query 	= 'SELECT jos_zepp_client_contact.* ,
					modifer_time, modifer_user, on_start, cast, likes, on_send, name, legal_entity '
					. ' FROM ' . $db->nameQuote('#__zepp_client_contact') .' , '. $db->nameQuote('#__zepp_client')
					. ' WHERE `id_client` = jos_zepp_client.id '.$where
					. ' ORDER BY '.  $order . ' name ';

			$db->setQuery($query);
			$clientContact = $db->loadObjectList();


		return $clientContact;
	}
*/
}