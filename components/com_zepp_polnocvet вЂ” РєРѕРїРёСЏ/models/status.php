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
class polnocvetModelStatus  extends JModel
{
	/**
	 * Gets the greeting
	 * @return string The greeting to be displayed to the user
	 */
	public function getRecord()
	{
		$table =$this->getTable('polnocvet', '');
		$id = JRequest::getVar('id');
		$user = $this->getGroup_id();

		// привязываем поля формы к таблице
		$table->load($id);
		$record = $table;

		return $record;
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
