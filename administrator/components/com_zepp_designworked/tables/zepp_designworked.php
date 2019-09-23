<?php


// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class TableZepp_designworked extends JTable
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
	var $coment  = null;

    /**
    * userid
    *
	* @var int
	**/
	var $userid   = 114;

    /**
    * Creating date
    *
	* @var timestamp
	**/
	var $date   = null;

    /**
    * path
    *
	* @var varchar(120)
	**/
	var $path  = null;

     /**
     * catid
     *
	 * @var int
	 **/
	var $catid   = null;

	/**
	 * privu
	 *
	 * @var varchar(120)
	 **/
	var $privu  = null;




	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableZepp_designworked(& $db) {
		parent::__construct('#__zepp_designworked', 'id', $db);
	}
}