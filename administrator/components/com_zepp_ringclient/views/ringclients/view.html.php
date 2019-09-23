<?php

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );


class ringclientsViewRingclients extends JView
{
	
	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'Zeppelin Ring Client Manager' ), 'generic.png' );
		JToolBarHelper::deleteList();
		//JToolBarHelper::editListX();
		//JToolBarHelper::addNewX();
                
                $managerList = $this->get('ManagerList');
                $this->assignRef('managerList',$managerList);
                
                $search = JRequest::getVar('searchall','','string');
                $this->assignRef('search',$search);
                
                

		// Get data from the model
		$items		= & $this->get( 'Data');
                
//echo 'start--ringclients\view.html.php<br>';
//print_R($items,false);
//echo 'end--ringclients\view.html.php<br>';

		$this->assignRef('items',		$items);

		parent::display($tpl);
	}
}