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
class polnocvetModelPolnocvet extends JModel
{
	/**
	 * Gets the greeting
	 * @return string The greeting to be displayed to the user
	 */
	public function getPolnocvet()
	{
		$db =& JFactory::getDBO();

		$query = '';//SELECT name FROM #__polnocvet';
		$db->setQuery( $query );
		$polnocvet = $db->loadResult();

		return $polnocvet;
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
		$files = JRequest::getVar('file');
		$ploschad = JRequest::getVar('ploschad');
		$stanok = JRequest::getVar('stanok');
		$name_file = JRequest::getVar('name_file');
		$user = $this->getGroup_id();
		$data = array();

		foreach ($files as $file) {
			if (!$link) {
				$data['link'] = "Смотри файл на сервере: \\\\Cehcom\\хранилище 1\\";
			} else {
				$data['link'] = '<a href="' . $link . '" >Сcылка на файл</a>';
			}

			$data['file'] = $file;
			$data['manager_id'] = $user['userid'];
			$data['name_file'] = $name_file;
			$data['ploschad'] = $ploschad;
			$data['stanok'] = $stanok;

			// привязываем поля формы к таблице
			if (!$table->bind($data)) {
				$this->setError($table->getError());
				return false;
			}
			// проверяем данные
			if ($table->check($data)) {
				// сохраняем данные
				if (!$table->store($data)) {
					$this->setError($table->getError());
					return false;
				}
			} else {
				$this->setError($table->getError());
				return false;
			}
		}

		$msg = "Файл $name_file отправлен на печать";

		return $msg;
	}

	/**
	 * Сохранение даты
	 */

	public function saveSetDite()
	{
		$table =$this->getTable('polnocvet', '');
		$id = JRequest::getVar('text');
		$setsetdite = JRequest::getVar('setsetdite');
		$user = $this->getGroup_id();

		// привязываем поля формы к таблице
		$table->load($id);
		$table->set_date = $setsetdite;
		$table->teh_admin = $user['userid'];

		// проверяем данные
		if ($table->check())
		{
			// сохраняем данные
			if (!$table->store())
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

		$msg[] = "Дата готовности печати Файла $table->name_file установлена на:$setsetdite ";
		$msg[] = $table->manager_id;
		$msg[] = $id;
		return $msg;
	}

	/**
	 * Заказ готов
	 */
	public function setComplekt(){
		$table =$this->getTable('polnocvet', '');
		$id = JRequest::getVar('id');
		$user = $this->getGroup_id();

		// привязываем поля формы к таблице
		$table->load($id);
		$table->set_status = date("Y.m.d");

		if ($table->status == 3) {
			$table->status = 4;
			$table->brack_text .= ' |';
		}else $table->status = 2;

		// проверяем данные
		if ($table->check())
		{
			// сохраняем данные
			if (!$table->store())
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

		$msg[] = "Работы приняты: файл $table->name_file, - продукция получена";
		$msg[] = $id;
		return $msg;
	}

	/**
	 * Ставим предварительную дату готовности
	 */
	public function saveStatus(){
		$table =$this->getTable('polnocvet', '');
		$id = JRequest::getVar('text');
		$user = $this->getGroup_id();

		// привязываем поля формы к таблице
		$table->load($id);
		$table->realis_date = date("Y.m.d");
		$table->teh_admin = $user['userid'];

		// проверяем данные
		if ($table->check())
		{
			// сохраняем данные
			if (!$table->store())
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

		$msg[] = "Файл $table->name_file все работы выполнены в полном объеме";
		$msg[] = $table->manager_id;
		$msg[] = $id;
		return $msg;
	}

	/**
	 * Допущен брак
	 */
	public function setBrack(){
		$table =$this->getTable('polnocvet', '');
		$text = JRequest::getVar('text');
		$id = JRequest::getVar('id');
		$user = $this->getGroup_id();

		// привязываем поля формы к таблице
		$table->load($id);
		$table->set_status = date("Y.m.d");
		$table->brack_text .= $text.'; ';
		$table->status = 3;

		// проверяем данные
		if ($table->check())
		{
			// сохраняем данные
			if (!$table->store())
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

		$msg[] = "Сообщение о браке отправленно ";
		$msg[] = $text;
		$msg[] = $id;
		return $msg;
	}

	/**
	 * Уведомление о готовности
	 */
	public function sentComplect(){
		$table =$this->getTable('polnocvet', '');
		$id = JRequest::getVar('text');
		$user = $this->getGroup_id();

		// привязываем поля формы к таблице
		$table->load($id);
		$table->realis_date = date("Y.m.d");
		$table->teh_admin = $user['userid'];
		$table->set_status = date("Y.m.d");
		$table->status = 2;
		
		

		// проверяем данные
		if ($table->check())
		{
			// сохраняем данные
			if (!$table->store())
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

		$msg[] = "Файл $table->name_file все работы выполнены в полном объеме";
		$msg[] = $table->manager_id;
		return $msg;
	}

	public function setComplaint(){
		$table =$this->getTable('polnocvet', '');
		$text = JRequest::getVar('text');
		$id = JRequest::getVar('id');
		$user = $this->getGroup_id();

		// привязываем поля формы к таблице
		$table->load($id);
		$table->complaint .= " <Дата: ".date("Y.m.d").">".$text.'; ';
		$table->status = 3;

		// проверяем данные
		if ($table->check())
		{
			// сохраняем данные
			if (!$table->store())
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

		$msg[] = "Жалоба отправленна ";
		$msg[] = $text;
		$msg[] = $id;
		return $msg;
	}
}
