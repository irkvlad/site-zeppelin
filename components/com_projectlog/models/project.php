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
defined('_JEXEC') or die('No access');
jimport('joomla.application.component.model');

function chmod_R($path, $perm)
{
	$handle = opendir($path);
	while (false !== ($file = readdir($handle)))
	{
		if (($file !== ".") && ($file !== ".."))
		{
			if (is_file($path . "/" . $file))
			{
				chmod($path . "/" . $file, $perm);
			}
			else
			{
				chmod($path . "/" . $file, $perm);
				chmod_R($path . "/" . $file, $perm);
			}
		}
	}

	closedir($handle);
}


class ProjectlogModelProject extends JModel
{
	var $_id = null;
	var $_project = null;
	var $_data = null;
	var $_logs = null;
	var $_docs = null;
	var $_akts  = null;
	var $_logo = null;

	function __construct()
	{
		parent::__construct();
		global $option;

		$mainframe =& JFactory::getApplication();
		$this->setId(JRequest::getInt('id', '0'));
	}

	function setId($id)
	{
		$this->_id = $id;
	}


	function &getData()
	{
		if ($this->loadData())
		{

		}
		else  $this->_initData();

		return $this->_data;
	}
	//endregion

	function loadData()
	{
		if (empty($this->_data))
		{
			$query = 'SELECT *'
				. ' FROM #__projectlog_projects'
				. ' WHERE id = ' . $this->_id;

			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();

			return (boolean) $this->_data;
		}

		return true;
	}

	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$project                  = new stdClass();
			$project->id              = 0;
			$project->category        = null;
			$project->group_access    = null;
			$project->release_id      = null;
			$project->job_id          = null;
			$project->task_id         = null;
			$project->workorder_id    = null;
			$project->title           = null;
			$project->description     = null;
			$project->release_date    = null;
			$project->contract_from   = null;
			$project->contract_to     = null;
			$project->location_gen    = null;
			$project->location_spec   = null;
			$project->manager         = null;
			$project->chief           = null;
			$project->technicians     = null;
			$project->brigadir        = null;
			$project->deployment_from = null;
			$project->deployment_to   = null;
			$project->onsite          = null;
			$project->projecttype     = null;
			$project->client          = null;
			$project->status          = null;
			$project->approved        = null;
			$project->published       = 1;

			$this->_data = $project;

			return (boolean) $this->_data;
		}
	}

	function getLogs()
	{
		$query = 'SELECT * FROM #__projectlog_logs WHERE project_id = ' . $this->_id . ' AND published = 1 ORDER BY date DESC';
		$this->_db->setQuery($query);
		$this->_logs = $this->_db->loadObjectlist();

		return $this->_logs;
	}


	function getLog($id)
	{
		$db    = JFactory::getDBO();
		$query = 'SELECT * FROM #__projectlog_logs WHERE id = ' . $id;
		$db->setQuery($query);
		$logitem = $db->loadObject();

		return $logitem;
	}


	function getDocs()
	{
		$query = 'SELECT * FROM #__projectlog_docs WHERE project_id = ' . $this->_id . ' ORDER BY time DESC';
		$this->_db->setQuery($query);
		$this->_docs = $this->_db->loadObjectlist();

		return $this->_docs;
	}

	function getAkts()
	{
		$query = 'SELECT * FROM #__zepp_project_orderakts WHERE project_id = ' . $this->_id . ' ORDER BY save DESC';
		$this->_db->setQuery($query);
		$this->_akts = $this->_db->loadObjectlist();

		return $this->_akts;
	}


	function getLogo()
	{
		$query = 'SELECT * FROM #__projectlog_logo WHERE project_id = ' . $this->_id . ' ORDER BY date DESC';
		$this->_db->setQuery($query);
		$this->_logo = $this->_db->loadObjectlist();

		return $this->_logo;
	}

	function saveLog($data)
	{
		$settings = &JComponentHelper::getParams('com_projectlog');
		$user     = &JFactory::getUser();

		$row =& $this->getTable('projectlog_logs', '');

		if (!$row->bind($data))
		{
			$this->setError($row->getError());

			return false;
		}

		$row->id = (int) $row->id;
		if (!$data['id'])
		{
			$row->loggedby = $data['userid'];
			$row->date     = date('Y-m-d H:i:s');
		}
		else
		{
			$row->modified    = date('Y-m-d H:i:s');
			$row->modified_by = $data['userid'];
		}

		$nullDate = $this->_db->getNullDate();

		// Make sure the data is valid
		if (!$row->check($settings))
		{
			$this->setError($row->getError());

			return false;
		}

		// Store it in the db
		if (!$row->store())
		{
			$this->setError($row->getError());

			return false;
		}

		projectlogHTML::notifyDoc('log', $user, $row->project_id);

		return $row->id;
	}

	function deleteLog($id)
	{
		$query = 'DELETE FROM #__projectlog_logs WHERE id = ' . (int) $id . ' LIMIT 1';
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($row->getError());

			return false;
		}

		return true;
	}

	function saveDoc($data)
	{
		$settings = &JComponentHelper::getParams('com_projectlog'); //настройки конфига
		$user     = &JFactory::getUser();// список пользователей
		$row      =& $this->getTable('projectlog_docs', ''); //подключение таблицы

		if (!$row->bind($data))
		{ // привязка значений к объекту таблицы
			$this->setError($row->getError());

			return false;
		}
		$row->id          = (int) $row->id;   // передаем в объкт значения
		$row->date        = date('Y-m-d');
		$row->submittedby = $user->get('id');

		// Проверка на корректность
		if (!$row->check($settings))
		{
			$this->setError($row->getError());

			return false;
		}

		// Запись в базу
		if (!$row->store())
		{
			$this->setError($row->getError());

			return false;
		}

		// Почта
		projectlogHTML::notifyDoc('doc', $user, $row->project_id);



		return $row->id;
	}

	function deleteDoc($id, $project_id)
	{
		if ($id)
		{
			$query = 'SELECT path FROM #__projectlog_docs WHERE id = ' . $id;
			$this->_db->setQuery($query);
			$file = $project_id . '/' . $this->_db->loadResult();
			$this->deleteFile($file);
			$user = &JFactory::getUser();// список пользователей

			$query = 'DELETE FROM #__projectlog_docs WHERE id =' . $id;
			$this->_db->setQuery($query);

			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());

				return false;
			}
		}
		else
		{
			$this->setError(JText::_('NO DOCS SELECTED'));

			return false;
		}
		projectlogHTML::notifyDoc('del_doc', $user, $project_id, $file);

		return true;
	}

	function saveFile($file, $id)
	{
		//Функции работы с файловой системой
		jimport('joomla.filesystem.file');
		$settings = &JComponentHelper::getParams('com_projectlog'); // конфиг компанента
		$allowed  = explode(',', trim($settings->get('doc_types'))); // Проверяем расширения файлов
		$filename = $file['name'];
		//Формируем запрос на запись файда
		$src  = $file['tmp_name'];
		$dest = JPATH_SITE . DS . 'media' . DS . 'com_projectlog' . DS . 'docs' . DS . $id . DS ;//. $filename;
		//$ext  = strtolower(JFile::getExt($filename));
        $ext  = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

		//$name =  JFile::stripExt($filename);
        $name =  strtolower( basename( $filename, ".".$ext ) );
		$trueName = $name;

		$i=0;
		while(file_exists($dest.$trueName.".".$ext)){
			$i++;
			$trueName = $name."".$i;
		}
		$dest .= $trueName.".".$ext;

		if ( file_exists( $dest ) )
		{ // проверяем на существование файла
            $this->setError(JText::_('FILE EXISTS') . ' - ' . $filename);
			return 'ERR';
		}

		//Если все нормально закачиваем и записываем на сервер
		if ( in_array( $ext, $allowed ) )
		{
			if ( JFile::upload( $src, $dest ) )
			{
				//Дальше...
				return $trueName.".".$ext;
			}
			else
			{
                $this->setError(JText::_('FILE NOT UPLOADED'));
				return 'ERR';
			}
		}
		else
		{
            $this->setError(sprintf(JText::_('WRONG FILE TYPE'), $ext));
			return 'ERR';
		}
	}

	function deleteFile($file)
	{
		jimport('joomla.filesystem.file');
		$path = JPATH_SITE . DS . 'media' . DS . 'com_projectlog' . DS . 'docs' . DS;
		JFile::delete($path . $file);
	}

	function saveLogo($data)
	{ // Записывыем логотип проекта
		$settings = &JComponentHelper::getParams('com_projectlog'); //настройки конфига
		$user     = &JFactory::getUser();// список пользователей
		$row      =& $this->getTable('projectlog_logo', ''); //подключение таблицы saveLogo($post)

		if (!$row->bind($data))
		{ // привязка значений к объекту таблицы
			$this->setError($row->getError());

			return false;
		}
		$row->id          = (int) $row->id;   // передаем в объкт значения
		$row->date        = date('Y-m-d');
		$row->submittedby = $user->get('id');

		// Проверка на корректность
		if (!$row->check($settings))
		{
			$this->setError($row->getError());

			return false;
		}

		// Запись в базу
		if (!$row->store())
		{
			$this->setError($row->getError());

			return false;
		}

		if ($settings->get('notify_admin_doc') == 1)
		{//почта
			projectlogHTML::notifyAdmin('doc', $user, projectlogHTML::getProjectName($row->project_id));
		}

		return $row->id;
	}

	function deleteLogo($id, $project_id)
	{ // Удаляем логотип

		if ($id)
		{
			$query = 'SELECT path FROM #__projectlog_logo WHERE id = ' . $id;
			$this->_db->setQuery($query);
			$f     = $this->_db->loadResult();
			$file  = $project_id . DS . $f;
			$file2 = $project_id . DS . '80x80_' . $f;
			$file3 = $project_id . DS . '227x219_' . $f;
			$this->deleteFile($file);
			$this->deleteFile($file2);
			$this->deleteFile($file3);

			$query = 'DELETE FROM #__projectlog_logo WHERE id =' . $id;
			$this->_db->setQuery($query);

			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());

				return false;
			}
		}
		else
		{
			$this->setError(JText::_('NO DOCS SELECTED'));

			return false;
		}

		return true;
	}

	function existLogo($id)
	{//

		if ($id)
		{
			$query = 'SELECT id FROM #__projectlog_logo WHERE project_id = ' . $id;
			$this->_db->setQuery($query);
			$logo = $this->_db->loadResult();

			if ($logo)
			{

				return $logo;
			}
			else
			{

				$this->setError(JText::_('exist') . $id . $logo);
			}

			return false;
		}
	}

	function moveProject($mov, $project_id, $msg)
	{

		$users = &JFactory::getUser();// список пользователей
		$user  = $users->get('id');

		if ($project_id)
		{
			$deployment = JHTML::_('date', $date = null, $format = '%Y-%m-%d', $offset = null);


			$query = "UPDATE #__projectlog_projects SET deployment_to = '" . $deployment . "' , category = " . $mov . " WHERE id= " . $project_id;
			$this->_db->setQuery($query);

			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg() . $moveDate . $category + 1);

				return false;
			}



			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg() . $moveDate . $category + 1);

				return false;
			}


			//Почта
			// Отправлен
			// адрес, Менеджер, контрагент, номер заказа, описание, файлы, ссылка на сайте, ссылка на календарь
			projectlogHTML::notifyUsers($project_id, $mov, $msg);


		}
		else
		{
			$this->setError(JText::_('NO MOVED'));

			return false;
		}

		return true;
	}

	function s_f_on_serv($post)
	{
		$retr = '';

		$db    = JFactory::getDBO();
		$query = 'SELECT * FROM #__projectlog_projects WHERE id = ' . $post['project_id'];
		if ($db->setQuery($query))
		{
			$retr .= JText::_('MYSQL ERROR SELECT * FROM #__projectlog_projects');

			return $retr;
		}
		$project = $db->loadObject();

		$query = 'SELECT path FROM #__projectlog_logo WHERE project_id = ' . $post['project_id'];
		if ($db->setQuery($query))
		{
			$retr .= JText::_('MYSQL ERROR SELECT path FROM #__projectlog_logo');

			return $retr;
		}
		$logo = $db->loadResult();

		$query = 'SELECT path FROM #__projectlog_docs WHERE project_id = ' . $post['project_id'];
		if ($db->setQuery($query))
		{
			$retr .= JText::_('MYSQL ERROR SELECT path FROM #__projectlog_docs');

			return $retr;
		}
		$docs = $db->loadObjectlist();
		if (stripos($project->release_id, "со") === false
			and stripos($project->release_id, "СО") === false
			and stripos($project->release_id, "CO") === false
		)
		{
			$manager = projectlogHTML::getusername($project->manager);
			$manager = strtok($manager, " ");

		}
		else
		{
			$manager = 'Сервис';

		}

		$Yar = JHTML::_('date', $date = null, $format = '%Y', $offset = null);


		$local_path = DS . 'project' . DS . $Yar . DS . $manager . DS . $project->release_id . DS;

		$server_path = JPATH_BASE . DS . 'media' . DS . 'com_projectlog' . DS . 'docs' . DS . iconv("UTF-8", "cp1251", $post['project_id']) . DS;
		$retr        .= $Yar . '\\' . $manager . '\\' . $project->release_id . '\\';

		jimport('joomla.filesystem.file');

		if (!JFolder::create($local_path, 0777))
		{
			$retr .= '<br>ERROR ошибка при создании папки проекта ' . $local_path . '<br>Файла не скопированы!!';//iconv("cp1251", "UTF-8", '

			return $retr;
		}

		foreach ($docs as $d):
			$cop = '';
			$i   = 0;
			WHILE (JFile::exists($local_path . $cop . $d->path))
			{

				$i++;
				$cop = 'Копия(' . $i . ')';
			}
			if (!JFile::copy($server_path . $d->path, $local_path . $cop . $d->path))
			{
				$retr .= '<br>ERROR ошибка копирования файла: ' . $d->path . ", ";
			}
			else
			{
				$retr .= '<br><span style="font-size:10px;" >Файл: ' . $d->path . ' - скопирован как:&nbsp;&nbsp;&nbsp;' . $cop . $d->path . '</span>,';
			}
		endforeach;
		if ($logo)
		{
			$cop = '';
			$i   = 0;
			WHILE (JFile::exists($local_path . $cop . $logo))
			{

				$i++;
				$cop = 'Копия(' . $i . ')';
			}
			if (!JFile::copy($server_path . $logo, $local_path . $cop . $logo))
			{
				$retr .= '<br>ERROR ошибка копирования картинки календарика и тех.задания: ' . $logo . ", ";
			}
		}
		else
		{
			$retr .= '<br>ERROR Картинка календарика и тех.задания отсутствует: ';
		}
		$logotip    = $local_path . 'Календарик.doc';
		$shot_title = $project->shot_title;
		if ($shot_title == '') $shot_title = strtok($project->title, ' ');
		$kalendarik = projectlogHTML::kalendarik('227', $shot_title, $project->release_date, $project->job_id, $project->release_id, strtok(projectlogHTML::getusername($project->technicians), " "), $project->projecttype, $project->workorder_id, $logo, '227', '219', $project->podrydchik);


		$fp = fopen($logotip, 'w+');

		$fstr = header("Content-type: application/vnd.ms-word");
		$fstr .= header("Content-Disposition: attachment;Filename=$logotip");
		$fstr .= "<html>";

		$fstr .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
		$fstr .= '<body>' . $kalendarik . "</body>";
		$fstr .= "</html>";

		fwrite($fp, $fstr);
		fclose($fp);

		$tehform     = $local_path . 'ТехЗадание.doc';//iconv("UTF-8", "cp1251",
		$tehform_prn = projectlogHTML::tehform_prn($project, $logo);//$local_path.
		$fp          = fopen($tehform, 'w+');

		$fstr = header("Content-type: application/vnd.ms-word");
		$fstr .= header("Content-Disposition: attachment;Filename=ТехЗадание.doc");
		$fstr .= "<html>";
		$fstr .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

		$fstr .= '<body>' . $tehform_prn . '</body>';
		$fstr .= "</html>";

		fwrite($fp, $fstr);
		fclose($fp);

		chmod($local_path, 0777);

		chmod_R($local_path, 0777);


		return $retr;
	}

	function brigadir($post)
	{
		$brigadir = $post['brigadir'];
		$query    = "UPDATE  #__projectlog_projects SET brigadir = " . $brigadir . " WHERE id = " . $post['id'];
		$this->_db->setQuery($query);
		if (!$this->_db->query()) return $this->setError($this->_db->getErrorMsg());
		else $this->project->brigadir = $brigadir;

		return $this->project->brigadir;
	}

	function saveAkt($data)
	{ // Записывыем логотип проекта
		$settings = &JComponentHelper::getParams('com_projectlog'); //настройки конфига
		$user     = &JFactory::getUser();// список пользователей
		$row      =& $this->getTable('zepp_project_orderakts', ''); //подключение таблицы saveLogo($post)

		if (!$row->bind($data))
		{ // привязка значений к объекту таблицы
			$this->setError($row->getError());

			return false;
		}
		// передаем в объкт значения
		$row->date        = date('Y-m-d');
		$row->submittedby = $user->get('id');
		$row->puth = $data['path']['name'];

		// Проверка на корректность
		if (!$row->check($settings))
		{
			$this->setError($row->getError());

			return false;
		}

		// Запись в базу
		if (!$row->store())
		{
			$this->setError($row->getError());

			return false;
		}


		projectlogHTML::notifyAdmin('akt', $user, $row->project_id );



		return $row->id;
	}


}
