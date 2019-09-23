<?php
// защита от прямого доступа
defined('_JEXEC') or die('Restricted access');
// подключаем класс JController
jimport('joomla.application.component.controller');

 function _translit($_str)
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

class PortfoliosController extends JController
{
	/**
	 * Конструктор
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Добавление
	 */
	public function add()
	{
		JRequest::setVar('view', 'portfolio');
		$this->display();
	}

	/**
	 * Сохранение данных
	 */
	public function save()
	{
		// проверяем токен
		JRequest::checkToken() or jexit('Invalid Token');

		// получаем значения формы
		$data = JRequest::get('post');
		$model = $this->getModel('Portfolios');
		$link = 'index.php?option=com_portfolios';
		$type = 'message';

		jimport('joomla.filesystem.file');

		//*************// Сохраняем логотип ******************************************
		$file = JRequest::getVar('logo_path', null, 'files', 'array');

		if($file['name']){ // Имя файла ??

        	if (!ctype_alnum($file['name'])) {$file['name']=_translit($file['name']);} //Транслитерация русских символов


			$file['name'] = JFile::makeSafe($file['name']); // чистим символы в имени файла
            $msg = $model->saveFile($file);    // Запись файла

            if($msg[1]){
				$data['logo_path'] =JString::trim($msg[2]);
            }else{
               $message = JText::_('DOC NOT SAVED').' - '.$file.'-'.$msg[2];
               $type = 'notice';
			 }
          }
		//*******************************************************

		// получаем значения формы
		$cid = JRequest::getVar('id');//JRequest::getVar('task')
		$path = "";
		$data['fio'] = JString::trim(JRequest::getVar('fio', '', 'post', 'string', JREQUEST_ALLOWRAW));
		$data['date_rojjdeniy'] = JString::trim(JRequest::getVar('date_rojjdeniy', '', 'post', 'string', JREQUEST_ALLOWRAW));
		$data['telefon'] = JString::trim(JRequest::getVar('telefon', '', 'post', 'string', JREQUEST_ALLOWRAW));
		$data['email'] = JString::trim(JRequest::getVar('email', '', 'post', 'string', JREQUEST_ALLOWRAW));
		$data['date_reg'] = date('Y-m-d');
		$data['student'] = JString::trim(JRequest::getVar('student', '', 'post', 'string', JREQUEST_ALLOWRAW));
		$data['worcked'] = JString::trim(JRequest::getVar('worcked', '', 'post', 'string', JREQUEST_ALLOWRAW));
		$data['notes'] = JString::trim(JRequest::getVar('notes', '', 'post', 'string', JREQUEST_ALLOWRAW));

		//*************// Сохраняем фото ******************************************
        $delF[] = JRequest::getVar('photo_path0');
        $delF[] = JRequest::getVar('photo_path1');
        $delF[] = JRequest::getVar('photo_path2');
        $delF[] = JRequest::getVar('photo_path3');

		$photo[] = JRequest::getVar('photo_path0', null, 'files', 'array');
		$photo[] = JRequest::getVar('photo_path1', null, 'files', 'array');
		$photo[] = JRequest::getVar('photo_path2', null, 'files', 'array');
		$photo[] = JRequest::getVar('photo_path3', null, 'files', 'array');

    	if ($cid){
			$db = JFactory::getDBO();
			$query = 'SELECT photo_path FROM #__portfolios WHERE id = ' . $cid;
			$db->setQuery($query);
			$path = $db->loadResult();
            //path -то что есть на сервере
            //photo-то что поступило
			if ($path){
                $data['photo_path']='';

                $portfolio=explode(";", $path);//Масив файлов которые есть на сервере

                for ($i=0;$i<4;$i++){ // Формируем список на удаление
                    if ($photo[$i]['name']){
                        $photo2[]=$photo[$i];
                        if ($portfolio[$i]){
                            if (JFile::delete($portfolio[$i])) {
					           $message .="Файл портфолио $portfolio[$i]  - удален; Новый Файл портфолио:". $photo[$i]['name']."-загружен <br>";
				            }else{
					           $message .="Ошибка при удалении Файла портфолио $portfolio[$i]  - Не удален<br>";
				            }
                        }
                    }elseif($delF[$i]){
                         if (JFile::delete($portfolio[$i])) {
					           $message .="Файл портфолио $portfolio[$i]  - удален;";
				            }else{
					           $message .="Ошибка при удалении Файла портфолио $portfolio[$i]  - Не удален<br>";
				            }


                    }elseif($portfolio[$i]){
                       $data['photo_path'].=$portfolio[$i].";";
                    }
                 }
             }else{for($i=0;$i<4;$i++){if($photo[$i]['name'])$photo2[]=$photo[$i];}}

        }else{for($i=0;$i<4;$i++){if($photo[$i]['name'])$photo2[]=$photo[$i];}}




		foreach ($photo2 as $p){
 if ($p=="del0") $message.=print_r($photo,true) ;
			if($p['name']){ // Имя файла ??

            	if (!ctype_alnum($p['name'])) {$p['name']=_translit($p['name']);} //Транслитерация русских символов

				$p['name'] = JFile::makeSafe($p['name']); // чистим символы в имени файла
				$msg = $model->saveFile($p);    // Запись файла

				if($msg[1]){
					if ($data['photo_path']) {
						$data['photo_path'] = $data['photo_path'].JString::trim($msg[2].";");
					}else{
						$data['photo_path'] =JString::trim($msg[2].";");
					}

				}else{
				   $message .= " || ".JText::_('DOC NOT SAVED').' - '.$msg[2];
				   $type = 'notice';
				}
			}
		}
		//*******************************************************

		if ($model->save($data))
		{
			$message .= JText::_('SAVE OK');
			$message .= $model->getError();

		} // если нет
		else
		{
			$message = JText::_('SAVE FAILED');
			$message .= ' ['.$model->getError().']'.$data->thri_d; // получаем ошибку из модели
			$type = 'notice';
		}
		// перенаправялем
		$this->setRedirect($link, $message ,$type);
	}

	/**
	 * Редактирование
	 *
	 */
	public function edit()
	{

		JRequest::setVar('view', 'portfolio');

		$this->display();
	}

	/**
	 * Удаляет
	 */
	public function remove()
	{
		$model = $this->getModel('Portfolios');

		if ($model->remove())
		{
			$message = JText::_('CGCA ADMIN DELETE OK')."///".$model->getError();
		}
		else
		{
			$message = JText::_('CGCA ADMIN DELETE FAILED');
			$message .= '['.$model->getError().']'; // получаем ошибку из модели
		}
		$this->setRedirect('index.php?option=com_portfolios', $message);
	}


	/**
	 * Типичный для архитектуры MVC view метод
	 */
	public function display()
	{
		parent::display();
	}
}