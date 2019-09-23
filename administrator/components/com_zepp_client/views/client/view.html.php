<?php
// защита от прямого доступа
defined('_JEXEC') or die('Restricted access');
// подключаем класс JView
jimport('joomla.application.component.view');

class  clientViewClient extends JView
{
	function display($tpl = null)
	{
		// получаем список
		//$rows = $this->get('ClientContact');
		 $search = JRequest::getVar('searchall');
		 $send = JRequest::getVar('sendCon');
		 $SortManager =  JRequest::getVar('manager');

		 if( ($send==1) OR ($SortManager > 0 ) ) $client = $this->get('send');
		 else {
			 if(!empty($search)){
				$rows = $this->get('Search');
					if( !empty($rows) ) {$search.=" - ".count($rows)." записей"; $client = $rows;}
					else { $client = $this->get('Client'); $search.=" - не найдено";}
			} else  { $client = $this->get('Client'); $search.="";}
		}
		$contact = $this->get('Contact');
		$IdManagers = clientHTML::getUsersInType(10);
        $listManager[] = JHTML::_('select.option',0,  "Все менеджеры" );

			foreach($IdManagers as $id)
			{
				$user = JFactory::getUser($id);
				$nameManagers =  $user->name;
				$listManager[] = JHTML::_('select.option',$id,  $nameManagers ); //value="'.$id.'"  onclick="alarm(this.name);"

			}



		// присваиваем значение виду
		$this->assignRef('listManager',$listManager);
		$this->assignRef('rows', $rows);
		$this->assignRef('client', $client);
		$this->assignRef('contact', $contact);
		$this->assignRef('IdManager', $SortManager);
		$this->assignRef('search', $search);
		$this->assignRef('send', $send);
        /*
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