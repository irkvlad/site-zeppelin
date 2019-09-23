<?php
defined('_JEXEC') or die('Restricted access');
//jimport( 'joomla.database.table' );
jimport('joomla.filesystem.file');

class irkvladHTML{

	public function translit($_str)
	{
		$tr = array(
			'А'=>'A','Б'=>'B','В'=>'V','Г'=>'G',
			'Д'=>'D','Е'=>'E','Ж'=>'J','З'=>'Z','И'=>'I',
			'Й'=>'Y','К'=>'K','Л'=>'L','М'=>'M','Н'=>'N',
			'О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T',
			'У'=>'U','Ф'=>'F','Х'=>'H','Ц'=>'TS','Ч'=>'CH',
			'Ш'=>'SH','Щ'=>'SCH','Ъ'=>'','Ы'=>'YI','Ь'=>'',
			'Э'=>'E','Ю'=>'YU','Я'=>'YA','а'=>'a','б'=>'b',
			'в'=>'v','г'=>'g','д'=>'d','е'=>'e','ж'=>'j',
			'з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l',
			'м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r',
			'с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h',
			'ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sch','ъ'=>'y',
			'ы'=>'yi','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya', ' ' => '_');

		return strtr($_str,$tr);
	}

	public function getUserName($user_id){
		$database = JFactory::getDBO();
		$database->setQuery( "SELECT name FROM #__users WHERE id = ".$user_id );
		return $database->loadResult();
	}

	public function getContactName($user_id){
		$database = JFactory::getDBO();
		$database->setQuery( "SELECT name FROM  #__contact_details WHERE id = ".$user_id );
		return $database->loadResult();
	}

	/**
	* $src - Исходное изображение (.gif, .png, .jpg, .jpeg)
	* $dest - Сохраняемое изображение (.jpg)
	* $sidepx - Размер
	* $quality - Качество
	 **/

	function imgResize($src, $dest, $sidepx, $quality=90) {
		if (!file_exists($src)) return false;
		$size = getimagesize($src);
		if ($size === false) return false;
		$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
		$icfunc = "imagecreatefrom".$format;
		if (!function_exists($icfunc)) return false;
		$isrc = $icfunc($src);
		$img['r_foto'] = (($size[0]>$size[1])?$size[0]/$sidepx:$size[1]/$sidepx);
		$img['sizex'] = round($size[0]/$img['r_foto']);
		$img['sizey'] = round($size[1]/$img['r_foto']);
		$idest = imagecreatetruecolor($img['sizex'], $img['sizey']);
		imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $img['sizex'], $img['sizey'], $size[0], $size[1]);
		imagejpeg($idest, $dest, $quality);
		imagedestroy($isrc);
		imagedestroy($idest);
		return true;
	}

	/**
	 * Функция загрузки файла (аплоадер)
	 * @param  int    $max_file_size    максимальный размер файла в мегабайтах
	 * @param  array  $valid_extensions массив допустимых расширений
	 * @param  string $upload_dir       директория загрузки
	 * @return array                    сообщение о ходе выполнения
	 */
	public function uploadHandle($max_file_size = 1024, $valid_extensions = array(), $upload_dir = '.',$file_id = 0, $i = 0)
	{
		$file_name='';
		$pathfile='';
		$error = null;
		$info  = null;
		$max_file_size *= 1048576;  // размер файла в Mb
		if ($_FILES['file']['error'][$i] === UPLOAD_ERR_OK)
		{
			// проверяем расширение файла
			$file_extension = pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION);
			if (in_array(strtolower($file_extension), $valid_extensions))
			{
				// проверяем размер файла
				if ($_FILES['file']['size'][$i] < $max_file_size)
				{
					$file = $_FILES['file']['name'][$i];
					$file =strtolower(irkvladHTML::translit($file)); //Транслитерация русских символов
					$file = JFile::makeSafe($file); // чистим символы в имени файла

					if ($file_id == 0) $file_id = time() ;
					$file_name = $file_id.'-'.$file;  // к имени файла добавляем метку (времени), чтобы исключить одинаковые имена
					$destination = $upload_dir .'/' . $file_name;

					if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $destination)){
						$info = "Файл $destination успешно загружен";
						$pathfile = $destination;}
					else
						$error = "Не удалось загрузить файл $file_name";
				}
				else
					$error = "Размер файла". $_FILES['file']['name'][$i]." больше допустимого: $max_file_size";
			}
			else
				$error = "У файла ". $_FILES['file']['name'][$i]." недопустимое расширение";
		}
		else
		{
			// массив ошибок
			$error_values = array(

				UPLOAD_ERR_INI_SIZE   => 'Размер файла больше разрешенного директивой upload_max_filesize в php.ini',
				UPLOAD_ERR_FORM_SIZE  => 'Размер файла превышает указанное значение в MAX_FILE_SIZE',
				UPLOAD_ERR_PARTIAL    => 'Файл был загружен только частично',
				UPLOAD_ERR_NO_FILE    => 'Не был выбран файл для загрузки',
				UPLOAD_ERR_NO_TMP_DIR => 'Не найдена папка для временных файлов',
				UPLOAD_ERR_CANT_WRITE => 'Ошибка записи файла на диск'

			);

			$error_code = $_FILES['file']['error'][$i];

			if (!empty($error_values[$error_code]))
				$error = $error_values[$error_code];
			else
				$error = 'Случилось что-то непонятное';
		}

		return array('info' => $info, 'error' => $error, 'pathfile'=>$pathfile);
	}

	public function getManagerList()
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

	public function getDesignerList($user=0)
	{
		// Получаем объект базы данных
		$db     =& JFactory::getDBO(); // Формируем запрос (OR c.catid=4)
		//$user 	= & JFactory::getUser();
		$query = " SELECT "
			. " id "
			." FROM "
			. " jos_categories "
			." WHERE "
			. " title LIKE 'Дизайнер' "
			. " AND published=1 "
		;

		$db->setQuery($query);
		$catid = $db->loadResult();

		$query = " SELECT "
			. " user_id AS value, "
			. " name AS text  "
			. " FROM "
			. " jos_contact_details "
			. " WHERE "
			. " published=1 "
			. " AND catid = "
			. $catid
		;

		$db->setQuery($query);
		$categorylist = $db->loadObjectList();
		// Создаём первый элемент выпадающего списка (<option value="0">Выберите категорию</option>)
		$categories[] = JHTML::_('select.option',  '0', "Выберите дизайнера", 'value', 'text' );
		// Добавляем массив данных из базы данных
		$categories = array_merge( $categories, $categorylist);
		// Получаем выпадающий список
		$designer_list = JHTML::_(
			'select.genericlist' /* тип элемента формы */,
			$categories /* массив, каждый элемент которого содержит value и текст */,
			'userid' /* id и name select`a формы */,
			'size="1"' /* другие атрибуты элемента select class="inputbox" */,
			'value' /* название поля в массиве объектов содержащего ключ */,
			'text' /* название поля в массиве объектов содержащего значение */,
			$user /* value элемента, который должен быть выбран (selected) по умолчанию */,
			'userid' /* id select'a формы */,
			true /* пропускать ли элементы полей text через JText::_(), default = false */
		);

		return $designer_list;
	}

	/**
	 * Возвращает список  дизайнеров (user_id,name)
	 * @return mixed
	 */
	public function getDesignerIds()
	{
		// Получаем объект базы данных
		$db =& JFactory::getDBO(); // Формируем запрос (OR c.catid=4)
		//$user 	= & JFactory::getUser();
		$query = " SELECT "
			. " id "
			. " FROM "
			. " jos_categories "
			. " WHERE "
			. " title LIKE 'Дизайнер' "
			. " AND published=1 ";

		$db->setQuery($query);
		$catid = $db->loadResult();

		$query = " SELECT "
			. " user_id, "
			. " name  "
			. " FROM "
			. " jos_contact_details "
			. " WHERE "
			. " published=1 "
			. " AND catid = "
			. $catid;

		$db->setQuery($query);
		$designerList = $db->loadObjectList();
		return $designerList;
	}

	public function difArray($arr1,$arr2, &$index){
		if (!$arr1 AND is_array($arr1)) return false;

		for($i=0; $i<count($arr1) ; $i++){
			if (is_array($arr2)) {
				foreach ($arr2 as $dif) {
					if ($arr1[$i] === $dif) {
//echo "$arr1[$i] === $dif <br>";
						unset($arr1[$i]);
						$index[] = $i;
						break;
					}
//echo "$arr1[$i] !== $dif  <br>";
				}
			} else{
				if ($arr1[$i] === $arr2) {
//echo "$arr1[$i] === $arr2 <br>";
					unset($arr1[$i]);
					$index[] = $i;
					break;
				}
//echo "$arr1[$i] !== $arr2  <br>";
			}


//echo "arr1 Конец итерации $i<br>";
		}

		$retr = array();
		foreach($arr1 as $r){
			$retr[] = $r;
		}
		return $retr;
	}
}