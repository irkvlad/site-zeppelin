<?php
/**
 * Polnocvet table class
 * 
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link 
 * @license		irkvlad
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Polnocvet Table class
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class Polnocvet extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;

	/**
	 * @var 
	 */
	var $date_load = null;
	
	/**
	 * @var 
	 */
	var $link = null;

	/**
	 * @var
	 */
	var $name_file = null;

	/**
	 * @var
	 */
	var $file = null;
	
	/**
	 * @var 
	 */
	var $manager_id = null;
	
	/**
	 * @var 
	 */
	var $set_date = null;

	/**
	 * @var
	 */
	var $teh_admin = null;


	/**
	 * @var 
	 */
	var $realis_date = null;
	
	/**
	 * @var 
	 */
	var $set_status = null;
	
	/**
	 * @var brack_text
	 */
	var $brack_text = null;

	/**
	 * @var
	 */
	var $status = null;

	/**
	 * @var
	 */
	var $project_id = null; 	

	/**
	 * @var
	 */
	var $ploschad = null;

	/**
	 * @var
	 */
	var $stanok = null;

	var $complaint = null;
	

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function Polnocvet(& $db) {
		parent::__construct('#__zepp_polnocvet', 'id', $db);
	}
}