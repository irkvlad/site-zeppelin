<?php

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );


class designworkedsViewDesignworkeds extends JView
{
	
	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'Дизайнерские работы ДС ЦеППелин' ), 'generic.png' );
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();

		// Get data from the model
		$items		= & $this->get( 'Data');
                
//echo 'start--ringclients\view.html.php<br>';
//print_R($items,false);
//echo 'end--ringclients\view.html.php<br>';

		$this->assignRef('items',		$items);

		parent::display($tpl);
	}
}