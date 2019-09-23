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
class  PolnocvetsViewstanok extends JView
{
	/**
	 * display method of Hello view
	 * @return void
	 **/
	function display($tpl = null)
	{
        $this->_layout = 'form';
        JToolBarHelper::deleteList('', 'removeStanok');
		JToolBarHelper::save('saveStanok');
		JToolBarHelper::cancel( 'cancel', 'Close' );

        $stanok =  & $this->get( 'Data');

		$this->assignRef('stanok',		$stanok);


		parent::display($tpl);
	}
}