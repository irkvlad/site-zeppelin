<?php
// защита от прямого доступа
defined('_JEXEC') or die('Restricted access');
// подключаем класс JView
jimport('joomla.application.component.view');

class PortfoliosViewGalery extends JView
{
	function display($tpl = null)
	{
	// Получаем данные из модели
        $rows =  $this->get('fio');

		// получаем список админов

        $starGroup = JRequest::getVar('starGroup', '', 'post');
       // создаем массив для генерации SELECT списка звездность
		$starGroup = array (
			array('value' => -1, 'text' => 'Не сортировать по звездам' ),
			array('value' => 0, 'text' => 'Сортировать по звездам' ),
			array('value' => 1, 'text' => 'Все с 1 звездой и больше'),
			array('value' => 2, 'text' => 'Все с 2 звездами и больше'),
			array('value' => 3, 'text' => 'Все с 3 звездами и больше'),
			array('value' => 4, 'text' => 'Все с 4 звездами и больше'),
			array('value' => 5, 'text' => 'Все с 5 звездами'),

		);
		// присваиваем значение виду
		$this->assignRef('starGroup', JHTML::_('select.genericlist', $starGroup, 'starGroup', 'class="inputbox"', 'value', 'text', $starGroup));



		// присваиваем список виду
		$this->assignRef('rows', $rows);

		$this->assignRef('thri_d', $row->thri_d);
		$this->assignRef('photoshop', $row->photoshop);
		$this->assignRef('corel', $row->corel);
		$this->assignRef('auto_cad', $row->auto_cad);
		$this->assignRef('web_disign', $row->web_disign);
		$this->assignRef('star_reyting', $row->star_reyting);
		$this->assignRef('photo_path', $row->photo_path);



		 $arr_navyck = explode(";", $row->privut_notes);
		 $navyck=$arr_navyck[0]."<br />".$arr_navyck[1]."<br />".$arr_navyck[2]."<br />".$arr_navyck[3];

		 $this->assignRef('navyck', $navyck);

		 $pagination = &$this->get('Pagination');
		 $this->assignRef('pagination', $pagination);

		// отображаем наш вид
		parent::display($tpl);
	}
}