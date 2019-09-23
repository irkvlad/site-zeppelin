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
class Stanok extends JTable
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
    var $name = null;
    /**
     * @var
     */
    var $key = null;

    /**
     * @var
     */
    var $mats = null;

    /**
     * @var
     */
    var $ploshad = null;

    /**
     * @var
     */
    var $ispraven = null;

    /**
     * @var
     */
    var $set = null;


    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct(& $db) {
        parent::__construct('#__zepp_polnocvet_stanok', 'id', $db);
    }
}