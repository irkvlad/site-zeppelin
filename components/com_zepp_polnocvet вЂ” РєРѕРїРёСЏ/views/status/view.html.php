<?php
/**
 * PolnocvetView for Polnocvet Component
 * 
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link 
 * @license		irkvlad
 */

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Polnocvet Component
 *
 * @package		Joomla.Tutorials
 * @subpackage	Components
 */
class polnocvetViewStatus extends JView
{
	function display($tpl = null)
	{
		$record =$this->get('record');
		$this->assignRef( 'record',	$record );
		$user = $this->get('Group_id');
		$this->assignRef( 'user',	$user );
		

		parent::display($tpl);
	}
}

