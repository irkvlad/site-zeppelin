<?php


jimport( 'joomla.application.component.view');


class ringclientViewRingclient extends JView
{
	function display($tpl = null)
	{ 
                //$document = JFactory::getDocument();
                //$document->addScript($url='/includes/js/joomla.javascript.js', $type = "text/javascript");
                $itemid = JRequest::getVar('Itemid','0','int');
                $this->assignRef('itemid',$itemid);

                $userGroup = $this->get('Group_id');
                $this->assignRef('userGroup',$userGroup);
                
                $managerList = $this->get('ManagerList');
                $this->assignRef('managerList',$managerList);                
                
                //$task = JRequest::getVar('task');
                $layout = JRequest::getVar('layout');
                if ($layout==='form') { //$task === 'getzakaz' OR $task === 'editzakaz'){                    
                    $record = $this->get('Record');
                    $this->assignRef('Record',$record[0]);
                } else  {           
                    $Data = $this->get( 'Data' );
                    $this->assignRef('Data', $Data);
                }

		parent::display($tpl);
	}
}

