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
class Filials extends JTable
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
    var $webpage = null;

    /**
     * @var
     */
    var $company = null;

    /**
     * @var
     */
    var $filial = null;

    /**
     * @var
     */
    var $adress = null;



    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function Filials(& $db) {
        parent::__construct('#__zepp_filials', 'id', $db);
    }
}