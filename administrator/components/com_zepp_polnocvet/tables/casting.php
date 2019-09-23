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
class Casting extends JTable
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
    var $stanok = null;

    /**
     * @var
     */
    var $material = null;

    /**
     * @var
     */
    var $plotnost = null;

    /**
     * @var
     */
    var $color = null;

    /**
     * @var
     */
    var $cast = null;

    /**
     * @var
     */
    var $set_user = null;

    /**
     * @var
     */
    var $set_date = null;

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
        parent::__construct('#__zepp_polnocvet_casting', 'id', $db);
    }
}