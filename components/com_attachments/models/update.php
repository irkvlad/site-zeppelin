<?php
defined('_JEXEC') or die('Restricted access');
/**
* Attachments component
* @package Attachments
* @Copyright (C) 2007, 2008 Jonathan M. Cameron, All Rights Reserved
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link http://joomlacode.org/gf/project/attachments/frs/
* @author Jonathan M. Cameron
**/

jimport( 'joomla.application.component.model' );

class AttachmentsModelUpdate extends JModel
{
    function getArticleTitle($article_id)
    {
        // Get the article name
        $db =& JFactory::getDBO();
        $query = "SELECT * FROM #__content WHERE id='$article_id' LIMIT 1";
        $db->setQuery($query);
        $arows = $db->loadObjectList();
        if ( count($arows) != 1 ) {
            $errmsg = JText::_('ERROR INVALID ARTICLE ID')." ($article_id)";
            JError::raiseError(500, $errmsg);
            }
        return $arows[0]->title;
    }
}
?>
