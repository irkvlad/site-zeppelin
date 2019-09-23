<?php

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS .'html.helper.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS .'irkvladhelper.php');

jimport( 'joomla.application.component.view' );


class designworkedsViewDesignworked extends JView
{
	/**
	 * display method of Hello view
	 * @return void
	 **/
	function display($tpl = null)
	{
//echo "class designworkedsViewDesignworked extends JView<br>";


		$data		=& $this->get('Data');
		$isNew		= ($data->id < 1);

		$catList = designworkedHTML::getCatList($data->catid);
		$this->assignRef('catList',$catList);

		$designerList = irkvladHTML::getDesignerList($data->userid);
		$this->assignRef('designerList',$designerList);

		$this->assignRef('path',$data->path);
		$this->assignRef('privu',$data->privu);

		/*$designer= irkvladHTML::getUserName($data->userid);
		$this->assignRef('designert',$designer);

		$cat= designworkedHTML::getCategoriName($data->catid);
		$this->assignRef('cat',$cat);*/


		if($data->path) $path = explode(";",$data->path);
		if($data->privu) $privus = explode(";",$data->privu);
		$images = array();
//print_r($path,false);
//echo 'count($path)='.count($path).'<br>$path='.$data->path.'<br>';
		for($i=0 ; $i < count($path)-1; $i++) {
			if (file_exists($privus[$i])) {
				$altprivu = explode("/", $path[$i]);
				$img['alt'] = $altprivu[count($altprivu) - 1];
				$img['privu'] = '<img src="' . $privus[$i] . '" alt="' . $img['alt'] . '">';// alt= имя файла
				$img['path'] = $path[$i];
				$images[] = $img;
			} elseif (file_exists(JPATH_COMPONENT_ADMINISTRATOR . DS . 'img' . DS . $privus[$i])) {
				$altprivu = explode("/", $path[$i]);
				$img['alt'] = $altprivu[count($altprivu) - 1];
				//$img['privu'] = '<img src="'.JPATH_COMPONENT_ADMINISTRATOR . DS . 'img' . DS . $privus[$i].'" alt="' . $altprivu[count($altprivu) - 1] . '">';
				$img['privu'] = '<img src="../administrator/components/com_zepp_designworked/img/' . $privus[$i] . '" alt="' . $img['alt'] . '">';
				$img['path'] = $path[$i];
				$images[] = $img;
			}
//echo '$i'.$i;
		}
		$this->assignRef('images',$images);

		$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );

		JToolBarHelper::title(   JText::_( 'Категории дизайнерских работ' ).': <small><small>[ ' . $text.' ]</small></small>' );
		JToolBarHelper::save();
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}

		$this->assignRef('data',		$data);
		/*print_r($data, false);echo '<br>===<br>';
		print_r($isNew, false);echo '<br>===<br>';
		print_r($text, false);echo '<br>===<br>';
var_dump ($this);*/

		parent::display($tpl);
	}
}