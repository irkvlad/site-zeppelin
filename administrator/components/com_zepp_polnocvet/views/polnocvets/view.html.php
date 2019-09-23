<?php
/**
 * Polnocvet View for Polnocvet Component
 * 
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link h
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
class PolnocvetsViewPolnocvets extends JView
{
	/**
	 * Ringсlients view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'Полноцветная печать' ), 'generic.png' );
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX('addFilial');
        JToolbarHelper::custom('addCompany', 'company', '', 'Фирмы', false);
        JToolbarHelper::custom('addStanok', 'print', '', 'Станки', false);
        //JToolbarHelper::custom('removeTexture', 'texture', '', 'Текстуры', false);
        JToolbarHelper::custom('addPlotnost', 'plotnost', '', 'Плотности', false);
        JToolbarHelper::custom('addColor', 'color', '', 'Цвета', false);
        JToolbarHelper::custom('addMaterial', 'papirus', '', 'Материалы', false);
        JToolbarHelper::custom('addСasting', 'casting', '', 'Цены', false);

        $filials		=& $this->get('Filials');
        $companyList		=& $this->get('CompanyList');

        //JToolbarHelper::custom('posts.addfilial', 'filial', '', 'Добавить филиал');

		// Get data from the model
		$items		= & $this->get( 'Data');
        //$company		= & $this->model->getCompany();

		$this->assignRef('items',		$items);
        $this->assignRef('filials',		$filials);
        $this->assignRef('companyList',		$companyList);
        $this->assignRef('company',		$company);

		parent::display($tpl);
	}
}