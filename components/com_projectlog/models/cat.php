<?php
/**
 * @version       1.5.3 2009-10-12
 * @package       Joomla
 * @subpackage    Project Log
 * @copyright (C) 2009 the Thinkery
 * @link          http://thethinkery.net
 * @license       GNU/GPL see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class projectlogModelCat extends JModel
{
	var $_data = null;
	var $_id = null;
	var $_where = null;
	var $_total = null;
	var $_logo = null;


	function __construct()
	{
		parent::__construct();

		global $mainframe, $option;
		$settings = &$mainframe->getParams('com_projectlog');
		// Get the pagination request variables
		$limit      = $mainframe->getUserStateFromRequest($option . '.cat.limit', 'limit', $settings->def('perpage', 0), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		$this->setState('filter_order', JRequest::getCmd('filter_order', 'p.release_date'));
		$this->setState('filter_order_dir', JRequest::getCmd('filter_order_Dir', 'DESC'));

		// Set id for project type
		$id = JRequest::getVar('id', 0, '', 'int');
		$this->setId($id);
	}

	function setId($id)
	{
		$this->_id   = $id;
		$this->_data = null;
	}

	function getData()
	{
		global $mainframe, $option;
		$settings = &$mainframe->getParams('com_projectlog');

		if (empty($this->_data))
		{
			$query = "SELECT * FROM #__projectlog_categories WHERE id = " . $this->_id;

			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
		}

		return $this->_data;
	}

	function getLogo()
	{
		$query = 'SELECT * FROM #__projectlog_logo ' . ' ORDER BY date DESC';//path WHERE project_id = 46
		$this->_db->setQuery($query);
		$this->_logo = $this->_db->loadObjectList();

		return $this->_logo;

	}

	function getLogos($id)
	{
		$query = 'SELECT * FROM #__projectlog_logo WHERE project_id = ' . $id;//path WHERE project_id = 46
		$db    = JFactory::getDBO();
		$db->setQuery($query);
		$logos = $db->loadObject();

		return $logos;

	}

	function getProject($id)
	{
		$db    = JFactory::getDBO();
		$query = 'SELECT * FROM #__projectlog_projects WHERE id = ' . $id;
		$db->setQuery($query);
		$pitem = $db->loadObject();

		return $pitem;
	}

	function getProjects()
	{
		global $mainframe, $option;
		$debug = 0;

		if (empty($this->_projects))
		{
			$query           = $this->_buildQuery();
			$this->_projects = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_projects;
	}

	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query        = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	function _buildQuery()
	{
		$where   = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();
		$user    = &JFactory::getUser();

		$query = 'SELECT p.*, p.id AS id, p.title AS title, p.description as description, c.title as cattitle'
			. ' FROM #__projectlog_projects AS p'
			. ' LEFT JOIN #__projectlog_groups AS g ON p.group_access = g.id'
			. ' LEFT JOIN #__projectlog_groups_mid AS gm ON gm.group_id = g.id'
			. ' LEFT JOIN #__projectlog_categories AS c ON c.id = p.category'
			. ' WHERE p.published = 1'
			. ' AND p.approved = 1';

		//if user is not admin, make sure they have group access if any applied
		if ($user->get('gid') < 24)
		{
			$query .= ' AND'
				. ' CASE'
				. ' WHEN p.group_access != 0 THEN'
				. ' ' . $user->id . ' IN (SELECT user_id FROM #__projectlog_groups_mid WHERE group_id = gm.group_id)'
				. ' WHEN p.group_access = 0 THEN'
				. ' 1 = 1'
				. ' END';
		}
		$query .= $where
			. ' GROUP BY p.id'
			. $orderby;

		return $query;

	}

	function _buildContentWhere()
	{
		global $mainframe, $option;
		$where = array();
		if ($this->_id <> '0') $where = ' AND p.category = ' . $this->_id;
		if ($this->_id == '0')
		{
			$where = ' AND ( p.category = 6 OR p.category = 7 OR p.category = 8 OR p.category = 9 OR p.category = 12 OR p.category = 13)';
		}


		$user = &JFactory::getUser();

		$filter = $mainframe->getUserStateFromRequest($option . '.cat.filter', 'filter', '', 'int');
		$search = $mainframe->getUserStateFromRequest($option . '.cat.search', 'search', '', 'string');

		if ($search && $filter == 1)
		{
			$where .= ' AND p.title LIKE \'%' . $search . '%\' ';
		}
		elseif ($search && $filter == 2)
		{
			$where .= ' AND p.release_id LIKE \'%' . $search . '%\' ';
		}
		elseif ($search && $filter == 3)
		{
			$dbz = JFactory::getDBO();
			$q   = 'SELECT id FROM #__contact_details WHERE name LIKE \'%' . $search . '%\' ';
			$dbz->setQuery($q);
			$n_search = $dbz->loadResult();
			$where    .= ' AND p.brigadir = ' . $n_search . ' ';
		}
		elseif ($filter == 4 AND $user->id <> 0)
		{
			$where .= ' AND ( p.manager = ' . $user->id . ' OR p.chief = ' . $user->id . ' OR p.technicians = ' . $user->id . ')';
		}

		return $where;
	}

	function _buildContentOrderBy()
	{
		$filter_order     = $this->getState('filter_order');
		$filter_order_dir = $this->getState('filter_order_dir');

		$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_dir;

		return $orderby;
	}

	function projectSitestatus($id, $status = 0)
	{
		$db =& JFactory::getDBO();
		if (!$id)
		{
			$action = $status ? 'onsite' : 'offsite';
			echo "<script>alert('Select an item to set $action'); window.history.go(-1);</script>";
			exit;
		}
		$db->setQuery("UPDATE #__projectlog_projects SET onsite = '$status' WHERE id =" . $id);
		if (!$db->query())
		{
			echo "<script>alert('" . JText::_('ERROR SAVING') . "'); window.history.go(-1);</script>";
			exit();
		}
	}

	function changeStatus($post)
	{
		global $my, $mainframe, $itemid;
		$db =& JFactory::getDBO();
		if (!$post['mat']) $post['mat'] = 0;
		if (!$post['pipl']) $post['pipl'] = 0;
		if (!$post['plan']) $post['plan'] = 0;

		$db->setQuery("UPDATE #__projectlog_projects SET mat_on = " . $post['mat'] . " , pipl_on = " . $post['pipl'] . " , plan_on = " . $post['plan'] . " WHERE id = " . $post['id']);
		if (!$db->query())
		{
			echo "<script>alert('Не удалось обновить статус'); window.history.go(-1);</script>";
			exit();
		}

		return true;
	}

	/**
	 * Запись проекта в базу
	 *
	 * @param $post
	 *
	 * @return bool|int
	 */
	function saveProject($post)
	{

		global $mainframe;
		$settings = &$mainframe->getParams('com_projectlog');
		$user     = &JFactory::getUser();
		$row      =& $this->getTable('projectlog_projects', '');

		if (!$row->bind($post))
		{
			JError::raiseError(500, $this->_db->getErrorMsg());

			return false;
		}

		$row->id         = (int) $row->id;
		$row->created_by = (int) $user->get('id');
		$row->approved   = ($settings->get('approval') && !$post['id']) ? 0 : 1;
		$row->published  = ($settings->get('approval') && !$post['id']) ? 0 : 1;
		$manager         = $row->manager;
		$release_date    = $row->release_date;

		// Проверка корректности
		if (!$row->check($settings))
		{
			$this->setError($row->getError());

			return false;
		}

		// Сохраняем в базе
		if (!$row->store())
		{
			JError::raiseError(500, $this->_db->getErrorMsg());

			return false;
		}


		// Создаем каталог для проекта

		$patch = JPATH_SITE . DS . 'media' . DS . 'com_projectlog' . DS . 'docs' . DS . $row->id;
		if (!file_exists($patch))
		{
			if (!mkdir($patch))
			{
				$this->setError('Не удалось создать папку, по адресу: ' . $patch .
					'<br> Обратись к администратору! Сохранение файлов в проекте не возможно!');
				//return false;
			}
		}

		// отправляем на почту
		if ($row->category > 6)
		{
			$onLoadM = $post['onloadm'];
			$onLoadD = $post['onloadd'];

			$ONL_podrydchik = $post['ONL_podrydchik'];

			$ONL_title = $post['ONL_title'];
			$this->project->title;        //Заказчик
			$ONL_job_id = $post['ONL_job_id'];
			$this->project->job_id;       //Заказ
			$ONL_description = $post['ONL_description'];
			$this->project->description;  //Материалы
			$ONL_technicians = $post['ONL_technicians'];
			$this->project->technicians;  //Технолог
			$ONL_client = $post['ONL_client'];
			$this->project->client;       //Контакт
			$ONL_location_gen = $post['ONL_location_gen'];
			$this->project->location_gen; //Доставка\Монтаж


			if ($onLoadM <> '' and $onLoadM <> $manager)
			{
				projectlogHTML::notifyAdmin('manager', $user, $row->id, $onLoadM);
			}
			if ($onLoadD <> '' and $onLoadD <> $release_date)
			{
				projectlogHTML::notifyAdmin('release_date', $user, $row->id, $onLoadD);
			}

			if ($ONL_podrydchik <> $row->podrydchik)
			{
				projectlogHTML::notifyAdmin('podrydchik', $user, $row->id, $ONL_podrydchik);
			}

			if (
				($ONL_title <> '' and $ONL_title <> $row->title) or
				($ONL_job_id <> '' and $ONL_job_id <> $row->job_id) or
				($ONL_description <> '' and $ONL_description <> $row->description) or
				($ONL_technicians <> '' and $ONL_technicians <> $row->technicians) or
				($ONL_client <> '' and $ONL_client <> $row->client) or
				($ONL_location_gen <> '' and $ONL_location_gen <> $row->location_gen)
			)
			{
				$teh_list = "";
				if ($ONL_title <> '' and $ONL_title <> $row->title) $teh_list = "Заказчик с <i>" . $ONL_title . "</i> на <i>" . $row->title . "</i><br />";
				if ($ONL_job_id <> '' and $ONL_job_id <> $row->job_id) $teh_list .= "Заказ с <i>" . $ONL_job_id . "</i> на <i>" . $row->job_id . "</i><br />";
				if ($ONL_description <> '' and $ONL_description <> $row->description) $teh_list .= "Материалы с <i>" . $ONL_description . "</i> на <i>" . $row->description . "</i><br />";
				if ($ONL_technicians <> '' and $ONL_technicians <> $row->technicians) $teh_list .= "Технолог с <i>" . $ONL_technicians . "</i> на <i>" . $row->technicians . "</i><br />";
				if ($ONL_client <> '' and $ONL_client <> $row->client) $teh_list .= "Контакт  с <i>" . $ONL_client . "</i> на <i>" . $row->client . "</i><br />";
				if ($ONL_location_gen <> '' and $ONL_location_gen <> $row->location_gen) $teh_list .= "Монтаж  с <i>" . $ONL_location_gen . "</i> на <i>" . $row->location_gen . "</i><br />";

				projectlogHTML::notifyDoc('teh_list', $user, $row->id, $teh_list);
			}
		}
		if ($row->ringclient_ids)
		{
			$this->setStatusYS($row->ringclient_ids, $row->release_id, $row->id); // Сообщаем в базу заказов о созданом проекте $project_ids
			}

		return $row->id;
	}

	function setStatusYS($id, $release_id, $project_ids)
	{
		$link_project = JRoute::_('index.php?option=com_projectlog&view=project&id=');

		$db =& JFactory::getDBO();
		// Получаем строку с заказом
		$query = "SELECT * FROM jos_zepp_ringclient WHERE id = " . $id;
		$db->setQuery($query);
		$ringclient = $db->loadObjectlist();


		$query = "UPDATE `jos_zepp_ringclient` SET ";
		$query .= " `status`= 1 ";  //Статус заявки


		if ($project_ids)
		{
			$query .= ", `statustext`='" . $ringclient[0]->statustext . " <a href=\"" . $link_project . $project_ids . "\" >Проект № $release_id </a>;' ";  //Пояснение к статусу
			$query .= ", `project_ids` ='" . $ringclient[0]->project_ids . $project_ids . ";'"; //ID проекта
		}
		else
		{
			$query .= ", `statustext`= 'При записи данных проекта в заказе произощла ошибка' ";  //Пояснение к статусу
		}

		$query .= ", `statusdata`='" . date('Y-m-d') . "'"; // Дата установки статуса}
		$query .= " WHERE `id`= $id "; // id записи


		$db->setQuery($query);
		if (!$db->query()) return false;

		return true;
	}

	function deleteProject($id)
	{
		$user   = &JFactory::getUser();
		$result = false;

		$db    = JFactory::getDBO();
		$query = 'SELECT * FROM #__projectlog_projects WHERE id =' . $id;
		$db->setQuery($query);
		$project = $db->loadObjectlist();

		$query = 'DELETE FROM #__projectlog_projects'
			. ' WHERE id =' . $id;

		$this->_db->setQuery($query);

		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());

			return false;
		}

		$this->deleteLogs($id);
		$this->deleteDocs($id);
		projectlogHTML::notifyDoc('del_proj', $user, '', $project);

		return true;
	}

	function deleteLogs($id)
	{
		$query = 'DELETE FROM #__projectlog_logs WHERE project_id = ' . $id;
		$this->_db->setQuery($query);
		$this->_db->query();
	}

	function deleteDocs($id)
	{
		$query = 'SELECT path FROM #__projectlog_docs WHERE project_id = ' . $id;
		$this->_db->setQuery($query);
		$docs = $this->_db->loadObjectList();

		foreach ($docs as $d)
		{
			$pathF = $id . '/' . $d->path;
			$this->deleteFile($pathF);
		}

		rmdir(JPATH_SITE . DS . 'media' . DS . 'com_projectlog' . DS . 'docs' . DS . $id);

		$query = 'DELETE FROM #__projectlog_docs WHERE project_id =' . $id;
		$this->_db->setQuery($query);
		$this->_db->query();
	}

	function deleteFile($file)
	{
		jimport('joomla.filesystem.file');
		$path = JPATH_SITE . DS . 'media' . DS . 'com_projectlog' . DS . 'docs' . DS;
		JFile::delete($path . $file);

	}

	function brak($post)
	{
		global $my, $mainframe, $itemid;
		$user = &JFactory::getUser();
		// Запись сообщения в базу
		$db =& JFactory::getDBO();
		if ($post['id'])
		{
			if ($post['brak_msg'] == '')
			{
				$db->setQuery("UPDATE #__projectlog_projects SET location_spec = NULL WHERE id = " . $post['id']);
			}
			else
			{
				$db->setQuery("UPDATE #__projectlog_projects SET location_spec = '" . $post['brak_msg'] . "' , deployment_from = NOW( ) WHERE id = " . $post['id']);
			}


			if (!$db->query())
			{
				echo "<script>alert('Не удалось обновить статус'); window.history.go(-1);</script>";
				exit();
			}

			projectlogHTML::notifyAdmin('brak', $user, $post['id']);

			return true;
		}
		else
		{
			echo "<script>alert('Ошибка обновления статуса'); window.history.go(-1);</script>";
			exit();
		}

	}

	function changePolnocvet($post)
	{
		global $my, $mainframe, $itemid;
		$db =& JFactory::getDBO();
		$db->setQuery("UPDATE #__projectlog_docs SET polnocvet = 0  WHERE project_id = " . $post['id']);
		if (!$db->query())
		{
			$this->setError($db->getErrorMsg());

			return false;
		}

		$query = 'SELECT id FROM #__projectlog_docs WHERE project_id = ' . $post['id'];
		$this->_db->setQuery($query);
		$docs = $this->_db->loadObjectList();

		$i = 0;
		foreach ($docs as $d)
		{

			if (isset($post['file' . $i]))
			{

				$db->setQuery("UPDATE #__projectlog_docs SET polnocvet = 1  WHERE id = " . $post['file' . $i]);
				if (!$db->query())
				{
					$this->setError($db->getErrorMsg());

					return false;
				}


			}
			$i = $i + 1;
		}

		return true;
	}

//=====================================================
	function copyProject($post)
	{
		global $mainframe;
		$settings = &$mainframe->getParams('com_projectlog');
		$user     = &JFactory::getUser();
		//Нужно получить всю инфу по проекту.
		$_project = $this->getProject($post['id']);
		//почистить поля принадлежащие тольк этому проекту [category] => 8 [release_id] => КО 3239
		$_project->category   = '6';
		$_project->release_id = $_project->release_id . '-копия';
		//изменить id ПРОЕКТА присвоев ему НОЛЬ??
		$_project->id = '0';
		//сохранить проект
		{
			$row =& $this->getTable('projectlog_projects', '');

			if (!$row->bind($_project))
			{
				JError::raiseError(500, $this->_db->getErrorMsg());

				return false;
			}
			$row->id         = (int) $row->id;
			$row->created_by = (int) $user->get('id');
			$row->approved   = ($settings->get('approval') && !$_project['id']) ? 0 : 1;
			$row->published  = ($settings->get('approval') && !$_project['id']) ? 0 : 1;

			// Проверка корректности
			if (!$row->check($settings))
			{
				$this->setError($row->getError());

				return false;
			}

			// Сохраняем в базе
			if (!$row->store())
			{
				JError::raiseError(500, $this->_db->getErrorMsg());

				return false;
			}

			// Создаем каталог для проекта

			$patch = JPATH_SITE . DS . 'media' . DS . 'com_projectlog' . DS . 'docs' . DS . $row->id . DS;
			if (!file_exists($patch))
			{
				if (!mkdir($patch))
				{
					$this->setError($row->getError());

					return false;
				}
			}
		}

		//скопировать файлы и календарик
		{
			jimport('joomla.filesystem.file');
			$path_last = JPATH_SITE . DS . 'media' . DS . 'com_projectlog' . DS . 'docs' . DS . $post['id'];
			$files     = JFolder::files($path_last, $filter = '', $recurse = false, $full = true, $exclude = '');
			$files_new = Array();
			foreach ($files as $src)
			{
				$files_new[] = $dest = $patch . JFile::getName($src);
				$msg         = JFile::copy($src, $dest);
			}
		}
		//скопировать записи в базе о файлах и календарике
		{
			//таблица с файлами
			$query = 'SELECT * FROM #__projectlog_docs WHERE project_id = ' . $post['id'];
			$db    = JFactory::getDBO();
			$db->setQuery($query);
			$d = $db->loadObjectList();
			foreach ($d as $data)
			{
				$data->id         = '';
				$data->project_id = $row->id;
				$rowf             =& $this->getTable('projectlog_docs', ''); //подключение таблицы

				if (!$rowf->bind($data))
				{ // привязка значений к объекту таблицы
					$this->setError($row->getError());

					return false;
				}
				$rowf->id          = (int) $rowf->id;   // передаем в объкт значения
				$rowf->date        = date('Y-m-d');
				$rowf->submittedby = $user->get('id');    // Проверка на корректность
				if (!$rowf->check($settings))
				{
					$this->setError($rowf->getError());

					return false;
				}

				// Запись в базу
				if (!$rowf->store())
				{
					$this->setError($rowf->getError());

					return false;
				}
			}
			//таблица с логотипом
			$data             = $this->getLogos($post['id']);
			$data->id         = '';
			$data->project_id = $row->id;
			$rowl             =& $this->getTable('projectlog_logo', ''); //подключение таблицы

			if (!$rowl->bind($data))
			{ // привязка значений к объекту таблицы
				$this->setError($rowl->getError());

				return false;
			}
			$rowl->id          = (int) $rowl->id;   // передаем в объкт значения
			$rowl->date        = date('Y-m-d');
			$rowl->submittedby = $user->get('id');

			// Проверка на корректность
			if (!$rowl->check($settings))
			{
				$this->setError($rowl->getError());

				return false;
			}

			// Запись в базу
			if (!$rowl->store())
			{
				$this->setError($rowl->getError());

				return false;
			}

			/**/
		}

		//$this->setError(print_R($post,true).'<br>переход в модель<br>'.print_R($rowl,true));
		return $row->id;


	}

	function getBrak()
	{     // Все записи "В работе"
		$db    = JFactory::getDBO();
		$query = 'SELECT id, release_id, location_spec FROM #__projectlog_projects WHERE (length(location_spec) > 1) ORDER BY release_date'; //(category = 12) AND
		$db->setQuery($query);
		$pitem = $db->loadObjectList();

		return $pitem;
	}
}

//=====================================================
?>