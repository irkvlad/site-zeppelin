<?php


// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class TableZepp_designworked_cat extends JTable
{
	/**
	* Primary Key
	*
	* @var int
	**/
	var $id = null;

	/**
    * name
    *
	* @var varchar(65)
	**/
	var $name  = '';
        
    /**
    * Coment
    *
	* @var text
	**/
	var $comment  = null;


	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableZepp_designworked_cat(& $db) {
		parent::__construct('#__zepp_designworked_cat', 'id', $db);
	}
}