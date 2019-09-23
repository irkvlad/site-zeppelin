<?php

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

//require_once( './dumphper.php' );


class designworkedsViewDesignworkeds extends JView
{
	
	function display($tpl = null)
	{
//echo "designworkedsViewDesignworkeds display()<br>";

		// Список дизайнеров (столбцы)
		$designerIds=irkvladHTML::getDesignerIds();
		$this->assignRef('designerIds',		$designerIds);
		
		$dis	= JRequest::getVar( 'dis','0','int' );
		$this->assignRef('dis',$dis);

		$cat	= JRequest::getVar( 'cat','0','int' );
		$this->assignRef('cat',$cat);

		// Список категорий (строки)
		$catIds = designworkedHTML::getCatIds();
		$this->assignRef('catIds',		$catIds);

		// Масив с количесвом  (тело таблицы)
		$data = $this->get( 'Data' );
		$this->assignRef('data',$data);
		
		$dataS = $this->get( 'DataS' );
		$this->assignRef('dataS',$dataS);
		
		parent::display($tpl);
	}
}