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
class  PolnocvetsViewMaterial extends JView
{
	/**
	 * display method of Hello view
	 * @return void
	 **/
	function display($tpl = null)
	{
        $this->_layout = 'form';

        JToolBarHelper::deleteList('', 'removeMaterial');
		JToolBarHelper::save('saveMaterial');
		JToolBarHelper::cancel( 'cancel', 'Close' );

        $data =  & $this->get( 'Data');

		$this->assignRef('data',		$data);


		parent::display($tpl);
	}
}