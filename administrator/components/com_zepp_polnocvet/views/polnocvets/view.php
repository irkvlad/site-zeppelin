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
        JToolbarHelper::custom('posts.addCompany', 'company', '', 'Фирмы');

		// Get data from the model
		$items		= & $this->get( 'Data');
        $filial		= & $this->get( 'Filial');

		$this->assignRef('items',		$items);
        $this->assignRef('filial',		$filial);

		parent::display($tpl);
	}
}