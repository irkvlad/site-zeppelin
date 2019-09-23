<?php
// защита от прямого доступа
defined('_JEXEC') or die('Restricted access');
// подключаем класс JModel
jimport('joomla.application.component.model');

class PortfoliosModelPortfolios extends JModel
{
	/**
	 * Список
	 *
	 * @var array Список объектов
	 */
	private $_fio;

	/**
	 * Конструктор
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Загружает список
	 *
	 * @return array Список объектов
	 */
	public function getFio()
	{   //Добавляем фильтр в запрос выборки

		$thri_d = JRequest::getVar('thri_df', '', 'post');
		$photoshop = JRequest::getVar('photoshopf', '', 'post');
		$corel = JRequest::getVar('corelf', '', 'post');
		$auto_cad = JRequest::getVar('auto_cadf', '', 'post');
		$web_disign = JRequest::getVar('web_disignf', '', 'post');
		$starGroup = JRequest::getVar('starGroupf', '', 'post');

		$where = 'WHERE ( id > 0 ) ';
		$order = '';
		if ($starGroup >= 0 and $starGroup != '') {
		 	$where .= ' AND ( star_reyting >= '.$starGroup.' ) ';
		 	$order = ' star_reyting DESC , ';
		 	}
		if ($thri_d == 1)	$where .= ' AND (thri_d = 1) ';
		if ($photoshop == 1)	$where .= ' AND (photoshop = 1) ';
		if ($corel == 1)	$where .= ' AND (corel = 1) ';
		if ($auto_cad == 1)	$where .= ' AND (auto_cad = 1) ';
		if ($web_disign == 1)	$where .= ' AND (web_disign = 1) ';


		if (empty($this->_fio))
		{
			$query 	= 'SELECT *'
					. ' FROM ' . $this->_db->nameQuote('#__portfolios')
					. $where
					. ' ORDER BY '.  $order . ' fio ';

			$this->_db->setQuery($query);
			$this->_fio = $this->_db->loadObjectList();
		}

		return $this->_fio;
	}

	/**
	 * Сохраняет данные
	 *
	 * @return true on success
	 */
	public function save($data)
	{
		$cid = JRequest::getVar('id', '', 'post');
		$newFile=$data['logo_path'];
		if ($cid){
			$db = JFactory::getDBO();
			$query = 'SELECT logo_path FROM #__portfolios WHERE id = ' . $cid;
			$db->setQuery($query);
			$file = $db->loadResult();
			if (!$data['logo_path'])$data['logo_path']=$file;

			if ($file and  $newFile and ($file != '../images/porfolio/no_user.png') ){
				//$data['logo_path']=$newFile;
				$msg=$this->deleteFile($cid);
				$msg[2] .=' (Предыдущий логотип)';
				if (!$msg[1]) {

					$this->setError($msg[2]);
					return false;
				}
			}
		}

		$table = $this->getTable();
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

	/**
	 * Удаляет
	 *
	 * @return true on success
	 */
	public function remove()
	{
		$table = $this->getTable();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');

		foreach ($cids as $cid) {

//*********************************************************
			$msg=$this->deleteFile($cid);
//**********************************************************



			if (!$table->delete($cid)) {
				$this->setError($table->getErrorMsg());
				return false;
			}
		}


		return true;
	}

	 public function saveFile($file){
        //Функции работы с файловой системой
        jimport('joomla.filesystem.file');
        //$settings   = & JComponentHelper::getParams( 'com_projectlog' ); // конфиг компанента
        //$allowed = explode(',',trim($settings->get('doc_types'))); // Проверяем расширения файлов
		$filename=$file['name'];
        //Формируем запрос на запись файда
        $src = $file['tmp_name'];///images/porfolio/no_user.png
		$path = '../images/porfolio/';
        $dest = $filename;
        $ext = strtolower(JFile::getExt($filename) );
		$msg[1] = true;
		$msg[2] = $path.$dest;

		$i='';
        while(file_exists($path.$i.$dest)){ // проверяем на существование файла
            $i++;
		}
		$dest=$i.$dest;
		$msg[2] = $path.$dest;
        //Если все нормально закачиваем и записываем на сервер
        if ( JFile::upload($src, $path.$dest) ) {
              //Дальше...
        } else {
			$msg[1] = FALSE;
			$msg[2] = JText::_('FILE NOT UPLOADED');
            return $msg;
        }

		return $msg;
    }

	public function deleteFile($id){
			$db = JFactory::getDBO();
            $query = 'SELECT logo_path FROM #__portfolios WHERE id = ' . $id;
            $db->setQuery($query);
            $path = $db->loadResult();
			jimport('joomla.filesystem.file');
			if ($path !="../images/porfolio/no_user.png"){
				if (JFile::delete($path)) {
					$msg[1]=true;
					$msg[2]=$path." - Удален<br>";
				}else{
					$msg[1]=false;
					$msg[2]=$path." - Не удален<br>";
				}
			}

            $db = JFactory::getDBO();
			$query = 'SELECT photo_path FROM #__portfolios WHERE id = ' . $id;
			$db->setQuery($query);
			$path = $db->loadResult();

			if ($path){
                $portfolio=explode(";", $path);//Масив файлов которые есть на сервере
                foreach ($portfolio as $p){
                    if ($p){
                       if (JFile::delete($p)) {
					       $msg[1]=true;
					       $msg[2]=$p." - Удален<br>";
				       }else{
					       $msg[1]=false;
					       $msg[2]=$p." - Не удален<br>";
                       }
                    }
                }
            }

		      return $msg;
	   }
}