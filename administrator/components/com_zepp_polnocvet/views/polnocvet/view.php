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
class PolnocvetsViewPolnocvet extends JView
{
	/**
	 * display method of Hello view
	 * @return void
	 **/
	function display($tpl = null)
	{
		//get the hello
		$polnocvet		=& $this->get('Data');
		$isNew		= ($ringсlient->id < 1);

		$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
		JToolBarHelper::title(   JText::_( 'Hello' ).': <small><small>[ ' . $text.' ]</small></small>' );
		JToolBarHelper::save();
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}

		$this->assignRef('polnocvet',		$polnocvet);

		parent::display($tpl);
	}
}