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
class Material extends JTable
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
    var $set = null;

    /**
     * @var
     */
    var $plotnost= null;

    /**
     * @var
     */
    var $texture = null;

    /**
     * @var
     */
    var $color = null;



    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct(& $db) {
        parent::__construct('#__zepp_polnocvet_material', 'id', $db);
    }
}