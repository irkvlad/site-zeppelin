<?php
// защита от прямого доступа
defined('_JEXEC') or die('Restricted access');
// подключаем класс JView
jimport('joomla.application.component.view');

class  clientViewEdit extends JView
{
	function display($tpl = null)
	{
		$id = JRequest::getVar('id','post');
		// получаем список
		//$rows = clientHTML::getClientContact($id);
		$clients = clientHTML::getClient($id);
		$client = $clients[0];
		$contact = clientHTML::getContact($id);
		$IdManagers =clientHTML::getUsersInType(10);

			foreach($IdManagers as $id)
			{
				$user = JFactory::getUser($id);
				$nameManagers =  $user->name;
				$listManager[] = JHTML::_('select.option',$id,  $nameManagers );

			}


		// присваиваем значение виду
		//$this->assignRef('listManager',$listManager);
		//$this->assignRef('rows', $rows);
		$this->assignRef('client', $client);
		$this->assignRef('contact', $contact);
		$this->assignRef('listManager', $listManager);
		/*$this->assignRef('photoshop', $row->photoshop);
		$this->assignRef('corel', $row->corel);
		$this->assignRef('auto_cad', $row->auto_cad);
		$this->assignRef('web_disign', $row->web_disign);
		$this->assignRef('star_reyting', $row->star_reyting);

		 $arr_navyck = explode(";", $row->privut_notes);
		 $navyck=$arr_navyck[0]."<br />".$arr_navyck[1]."<br />".$arr_navyck[2]."<br />".$arr_navyck[3];

		 $this->assignRef('navyck', $navyck);*/

		// отображаем наш вид
		parent::display($tpl);
	}
}