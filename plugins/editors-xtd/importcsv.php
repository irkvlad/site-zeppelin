<?php
/**
* Add import CSV button in editor
* @package importCSV
* @Copyright (C) 2008, 2009 Nicolas GRILLET, All Rights Reserved
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @author Nicolas GRILLET
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');


class plgButtonImportCSV extends JPlugin
{
    /**
     * Constructor
     *
     * For php4 compatability we must not use the __constructor as a constructor for plugins
     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
     * This causes problems with cross-referencing necessary for the observer design pattern.
     *
     * @param       object $subject The object to observe
     * @param       array  $config  An array that holds the plugin configuration
     * @since 1.5
     */
    function plgButtonImportCSV(& $subject, $config) 
    {
        parent::__construct($subject, $config);
 	$this->loadLanguage( );
   }

    /**
     * Add Import CSV button
     *
     * @return a button
     */
    function onDisplay($name)
    {          
        global $mainframe;
        $doc =& JFactory::getDocument();
	$template = $mainframe->getTemplate();
        if ( $mainframe->isAdmin() ) 
        {
         	$doc->addStyleSheet($mainframe->getSiteURL() . 'plugins/editors-xtd/importcsv/importCSV.css', 'text/css', null, array() );
         	$base='../plugins/editors-xtd/';
         	$baseSite=$mainframe->getSiteURL();
        }
        else
        {
         	$doc->addStyleSheet( JURI::Base() . 'plugins/editors-xtd/importcsv/importCSV.css', 'text/css', null, array() );
          	$base='/plugins/editors-xtd/';
          	$baseSite=JURI::Base();

       }
        $getContent = $this->_subject->getContent($name);

	//$link = $base."importCSV-process.php?base=".$baseSite;
	$link="index.php?option=com_importcsv&view=process&tmpl=component";
	JHTML::_('behavior.modal');
        $button = new JObject();
        $button->set('modal', true);
        $button->set('class', 'modal');
        $button->set('text', JText::_('INSERTCSV'));
 	$button->set('options', "{handler: 'iframe', size: {x: 500, y: 310}}");
        $button->set('name', 'importCSV');
        $button->set('link', $link);
        $button->set('image', 'importcsv/logo-csv.png');

        return $button;
    }
}
?>
