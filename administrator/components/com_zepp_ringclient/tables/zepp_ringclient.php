<?php


// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class TableZepp_ringclient extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;

	/**
         * id manager
         * 
	 * @var int
	 */
	var $manager_id  = 114;
        
        /**
         * Client
         * 
	 * @var varchar(30)
	 */
	var $client  = null;

        /**
         * Creator id 
         * 
	 * @var int
	 */
	var $creator_id   = 114;

        /**
         * Creator name 
         * 
	 * @var varchar(18)
	 */
	var $creator_name   = null;

        /**
         * telefon 
         * 
	 * @var varchar(18)
	 */
	var $telefon   = null;

        /**
         * Tema
         * 
	 * @var text
	 */
	var $tema  = null;

        /**
         * Creating date
         * 
	 * @var timestamp
	 */
	var $creator_date   = 0;

        /**
         * Mangering data
         * 
	 * @var date
	 */
	var $manger_data  = null;

        /**
         * Project id 
         * 
	 * @var int
	 */
	var $project_id   = null;


	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableZepp_ringclient(& $db) {
		parent::__construct('#__zepp_ringclient', 'id', $db);
	}
}