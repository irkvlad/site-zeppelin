<?php

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );


class designworkedsViewcategories extends JView
{
	
	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'Дизайнерские работы ДС ЦеППелин - категории' ), 'generic.png' );
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();

		$items		= & $this->get( 'Data');

		$this->assignRef('items',		$items);

		parent::display($tpl);
	}
}