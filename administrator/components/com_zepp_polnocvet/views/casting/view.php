<?php
/**
 * Polnocvet View for Polnocvet Component
 * 
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link 
 * @license		irkvlad
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

/**
 * Polnocvet View
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class  PolnocvetsViewCasting extends JView
{
	/**
	 * display method of Hello view
	 * @return void
	 **/
	function display($tpl = null)
	{
        $this->_layout = 'form';

        JToolBarHelper::deleteList('', 'removeСasting');
		JToolBarHelper::save('saveСasting');
		JToolBarHelper::cancel( 'cancel', 'Close' );

        $data =  & $this->get( 'Data');
        $model = & JModel::getInstance('Casting','PolnocvetsModel');
        $listMaterial = $model->getListMaterials();
        $this->assignRef('listMaterial',		$listMaterial);
        $listStanok = $model->getListStanok();
        $this->assignRef('listStanok',		$listStanok);
        $listPlotnost = $model->getListPlotnost();
        $this->assignRef('listPlotnost',		$listPlotnost);
        $listColor = $model->getListColor();
        $this->assignRef('listColor',		$listColor);


        $this->assignRef('data',		$data);


		parent::display($tpl);
	}
}