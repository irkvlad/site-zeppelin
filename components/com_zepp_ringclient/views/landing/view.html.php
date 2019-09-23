<?php


jimport( 'joomla.application.component.view');


class ringclientViewLanding extends JView
{
	function display($tpl = null)
	{
        //$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
        //print_r($cids,false);

        //$status     =   JRequest::getVar('status',1,'int');
        $itemid = JRequest::getVar('Itemid','0','int');
        $this->assignRef('itemid',$itemid);
        $session = JFactory::getSession();
        $status = $session->get('status', '0');
        $this->assignRef('status',$status);

        $date   = JFactory::getDate();
        $startdate = $session->get('startdate', $date->toFormat ('01.%m.%Y'));
        $enddate = $session->get('enddate', $date->toFormat ('%d.%m.%Y'));
        $this->assignRef('startdate',$startdate);
        $this->assignRef('enddate',$enddate);
            
        $model = & JModel::getInstance('ringclient','ringclientModel');

        $userGroup = $model->getGroup_id();
        $this->assignRef('userGroup',$userGroup);

        $managerList = $model->getManagerList();
        $this->assignRef('managerList',$managerList);

        $search = JRequest::getVar('searchall','','string');
        $this->assignRef('search',$search);

        //$task = JRequest::getVar('task');
        $layout = JRequest::getVar('layout');

        $release_id = $this->get('listRelease');
        $this->assignRef('release_id',$release_id);


        if ($layout==='form') {
            $record = $model->getRecord();
            $this->assignRef('Record',$record[0]);
        } else  {
             // Получаем данные из модели
            $items =& $this->get('Data');
            $pagination =& $this->get('Pagination');


             // Назначаем переменные для работы с данными из шаблона
            $this->assignRef('items', $items);
            $this->assignRef('pagination', $pagination);

        }

		parent::display($tpl);
	}
}
?>
