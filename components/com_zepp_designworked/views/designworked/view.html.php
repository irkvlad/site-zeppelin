<?php

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
//require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS .'html.helper.php');
//require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS .'irkvladhelper.php');
require_once( JPATH_ROOT . DS . 'dumphper.php' );

jimport( 'joomla.application.component.view' );


class designworkedsViewDesignworked extends JView
{
	/**
	 * display method of Hello view
	 * @return void
	 **/
	function display($tpl = null)
	{
//echo "class designworkedsViewDesignworked extends JView11111111111<br>";

		$itemid = JRequest::getVar('Itemid','0','int');
		$this->assignRef('itemid',$itemid);

		$dis	= JRequest::getVar( 'dis' ,'0','int');
		$this->assignRef('dis',$dis);

		$cat	= JRequest::getVar( 'cat','0','int' );
		$this->assignRef('cat',$cat);
		
		 $user = JFactory::getUser();
		 $this->assignRef('user',$user);

		$item = $this->get('Item');
		$this->assignRef('item',$item);


//print_R($item,false);
		$data		=& $this->get('Data');
//echo '$item[0]->id='.$item[0]->id;
//echo '$data->id до='.$data->id;

		if($data->id < 1)  {
			$model = & JModel::getInstance('designworked','designworkedsModel');
			$data = $model->getDataId($item[0]->id);


		}

		$this->assignRef('data',$data);
		$isNew		= ($data->id < 1);
				
		$catList = designworkedHTML::getCatList($data->catid);
		$this->assignRef('catList',$catList);

		$designerList = irkvladHTML::getDesignerList($data->userid);
		$this->assignRef('designerList',$designerList);

		$designer_name = irkvladHTML::getUserName($data->userid);
		$this->assignRef('designer_name',$designer_name);

		$cat_name =  designworkedHTML::getCategoriName($data->catid);
		$this->assignRef('cat_name',$cat_name);


		if($data->path) $path = explode(";",$data->path);
		if($data->privu) $privus = explode(";",$data->privu);
		$images = array();
//print_r($path,false);
//echo 'count($path)='.count($path).'<br>$path='.$data->path.'<br>';
		for($i=0 ; $i < count($path)-1; $i++) {
//echo 'JPATH_ROOT .substr($privus[$i], 2)'.JPATH_ROOT .substr($privus[$i], 2);echo '<br>';
			if (file_exists(JPATH_ROOT .substr($privus[$i], 2))) { //!!!!!!!!!!!!!!!!!!
				$altprivu = explode("/", $path[$i]);
				$img['alt'] = $altprivu[count($altprivu) - 1];
				$img['privu'] = '<img src="'.substr($privus[$i], 1) . '" alt="' . $img['alt'] . '">';// alt= имя файла
				$img['path'] = substr($path[$i], 1);  //!!!!!!!!!!!!!!!!!!
				$images[] = $img;
//echo '$img=';print_R($img,false);echo '<br>';
			} elseif (file_exists(JPATH_COMPONENT_ADMINISTRATOR . DS . 'img' . DS . $privus[$i])) {
				$altprivu = explode("/", $path[$i]);
				$img['alt'] = $altprivu[count($altprivu) - 1];
				//$img['privu'] = '<img src="'.JPATH_COMPONENT_ADMINISTRATOR . DS . 'img' . DS . $privus[$i].'" alt="' . $altprivu[count($altprivu) - 1] . '">';
				$img['privu'] = '<img src="'.JPATH_COMPONENT_ADMINISTRATOR . DS .'img' . DS . $privus[$i] . '" alt="' . $img['alt'] . '">';
				$img['path'] = $path[$i];
				$images[] = $img;
//echo '2$img=';print_R($img,false);echo '<br>';
			}
//echo '$i'.$i;
		}
		$this->assignRef('images',$images);

		parent::display($tpl);
	}
}