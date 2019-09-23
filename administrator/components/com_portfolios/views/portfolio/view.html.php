<?php
// защита от прямого доступа
defined('_JEXEC') or die('Restricted access');
// подключаем класс JView
jimport('joomla.application.component.view');

class PortfoliosViewPortfolio extends Jview
{
	function display($tpl = null)
	{
		// инстанцируем объект таблицы #__portfolios
		$row = JTable::getInstance('Portfolios', 'Table');
		// получаем доступ к значениям полей таблицы конкретной записи
		$cid = JRequest::getVar('cid', array(0), '', 'array');
		$id = (int)$cid[0];
		if (isset($id)) {
			$row->load($id);
		}
		// присваиваем значение виду
		$this->assignRef('row', $row);

		// создаем массив для генерации SELECT списка (поле agroup)
		$agroup = array (
			array('value' => 0, 		'text' => '- ' . JText::_('CGCA SELECT GROUP') . ' -'),
			array('value' => 'LEVEL 1', 'text' => 'Дизайнеры')

		);
		// присваиваем значение виду
		$this->assignRef('agroup', JHTML::_('select.genericlist', $agroup, 'agroup', 'class="inputbox"', 'value', 'text', $row->agroup));

		// создаем массив для генерации SELECT списка звездность
		$starGroup = array (
			array('value' => 0, 'text' => 'пусто' ),
			array('value' => 1, 'text' => '1'),
			array('value' => 2, 'text' => '2'),
			array('value' => 3, 'text' => '3'),
			array('value' => 4, 'text' => '4'),
			array('value' => 5, 'text' => '5'),

		);
		// присваиваем значение виду
		$this->assignRef('starGroup', JHTML::_('select.genericlist', $starGroup, 'star_reyting', 'class="inputbox"', 'value', 'text', $row->star_reyting));
                               //echo JHTML::_('select.genericlist', $starGroup, 'starGroup', null, 'value', 'text', $currentValue);


		// присваиваем остальные необходимые виду значения
		$this->assignRef('logo_path',  $row->logo_path);
		$this->assignRef('fio', $row->fio);
		$this->assignRef('date_rojjdeniy',  $row->date_rojjdeniy);
		$this->assignRef('student', $row->student);
		$this->assignRef('worcked', $row->worcked);
		$this->assignRef('telefon', $row->telefon);
		$this->assignRef('email', $row->email);
		$this->assignRef('photo_path', $row->photo_path);
		$this->assignRef('date_reg', $row->date_reg );
		$this->assignRef('notes', $row->notes);
        $this->assignRef('privut_notes', $row->privut_notes);
		$this->assignRef('user_id', $row->user_id);

		$this->assignRef('thri_d', $row->thri_d);
		$this->assignRef('photoshop', $row->photoshop);
		$this->assignRef('corel', $row->corel);
		$this->assignRef('auto_cad', $row->auto_cad);
		$this->assignRef('web_disign', $row->web_disign);
		$this->assignRef('star_reyting', $row->star_reyting);

		parent::display($tpl);
	}
}