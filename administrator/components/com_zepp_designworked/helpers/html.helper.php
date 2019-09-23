<?php
defined('_JEXEC') or die('Restricted access');
//jimport( 'joomla.database.table' );
jimport('joomla.filesystem.file');

class designworkedHTML{

	public function getCategoriName($id){
		$database = JFactory::getDBO();
		$database->setQuery( "SELECT name FROM  #__zepp_designworked_cat WHERE id = ".$id );
		return $database->loadResult();
	}

	public function getCatList($catid)
	{
		// Получаем объект базы данных
		$db     =& JFactory::getDBO(); // Формируем запрос (OR c.catid=4)
		$query = " SELECT "
			. " id AS value, "
			. " name AS text  "
			." FROM "
			. " jos_zepp_designworked_cat "
		;

		$db->setQuery($query);
		$categorylist = $db->loadObjectList();
		// Создаём первый элемент выпадающего списка (<option value="0">Выберите категорию</option>)
		$categories[] = JHTML::_('select.option',  '0', "Выберите категорию", 'value', 'text' );
		// Добавляем массив данных из базы данных
		$categories = array_merge( $categories, $categorylist);
		// Получаем выпадающий список
		$categories_list = JHTML::_(
			'select.genericlist' /* тип элемента формы */,
			$categories /* массив, каждый элемент которого содержит value и текст */,
			'catid' /* id и name select`a формы */,
			' size="1" autocomplete="off" ' /* другие атрибуты элемента select class="inputbox" */,
			'value' /* название поля в массиве объектов содержащего ключ */,
			'text' /* название поля в массиве объектов содержащего значение */,
			$catid /* value элемента, который должен быть выбран (selected) по умолчанию */,
			'catid' /* id select'a формы */,
			true /* пропускать ли элементы полей text через JText::_(), default = false */
		);

		return $categories_list;

	}

	/**
	 * Возвращает Список кататегорий работ (id, name)
	 * @return mixed
	 */
	public function getCatIds()
	{
		// Получаем объект базы данных
		$db =& JFactory::getDBO(); // Формируем запрос (OR c.catid=4)
		$query = " SELECT "
			. " id , "
			. " name  "
			. " FROM "
			. " jos_zepp_designworked_cat "
			. " ORDER BY name";

		$db->setQuery($query);
		$getCatIds = $db->loadObjectList();
		return $getCatIds;
	}

	public function delFiles($p,$ch=''){
		jimport('joomla.filesystem.file');
		$row =& $this->getTable('zepp_designworked');
		$row->load($p['id']);
		$message='';
//echo '$p[\'id\']:>'.$p['id'].'<br>';
//echo '<br> $row:> <br>';print_R($row);echo '<br>';
		$path = explode(";", $row->path);
		$privu = explode(";", $row->privu);
//echo '<br> $path:> <br>';print_R($path,false);echo '<br>';
//echo '<br> $prevu:> <br>';print_R($privu,false);echo '<br>';
		$k=0;
		foreach ($p['del'] as $del) {
			$pr = str_replace('.jpg', '_privu.jpg', $del);
			if (file_exists($del)) {
				if (JFile::delete($del)) {
					//$key=array_search($del, $path);
					//if ($key) unset($path[ $key ]);
					$index = array();
					$path = irkvladHTML::difArray($path, $ch.$del, $index);
//$message['DD']=$del;
//$message['PP']=$path;


//echo '$index[0]'.$index[0];
					unset($privu[$index[0]+$k]);
					if (file_exists($pr)) {
						JFile::delete($pr);
					}
					$k++;
				}

			}/*else if($del === '' OR $del === null ){
				$index = array();
				$path = irkvladHTML::difArray($path, $del, $index);
echo '$index[0]'.$index[0];
				unset($privu[$index[0]]);
			}*/

//echo "del===<br>";
		}
//echo '<br> $path:> <br>';print_R($path,false);echo '<br>';
//echo '<br> $prevu:> <br>';print_R($privu,false);echo '<br>';
		$message['path']=implode(";",$path);
		$message['privu']=implode(";",$privu);
//echo '<br> $message[\'path\']:> <br>';print_R($message['path'],false);echo '<br>';
//echo '<br> $message[\'path\']:> <br>';print_R($message['privu'],false);echo '<br>';

		return $message;
	}

}