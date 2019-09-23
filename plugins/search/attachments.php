<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$mainframe->registerEvent( 'onSearch', 'plgSearchAttachments' );
$mainframe->registerEvent( 'onSearchAreas', 'plgSearchAttachmentAreas' );

JPlugin::loadLanguage( 'plg_search_attachments', JPATH_ADMINISTRATOR);

/**
 * @return array An array of search areas
 */
function &plgSearchAttachmentAreas() {
	  static $areas = array(
		   'attachments' => 'Attachments'
	     );
	  return $areas;
}

/* Attachment Search method
 */
function plgSearchAttachments( $text, $phrase='', $ordering='', $areas=null )
{
	$db		=& JFactory::getDBO();
	$user	=& JFactory::getUser();

    // Exit if the search does not include attachments
    if (is_array( $areas )) {
  		if (!array_intersect( $areas, array_keys( plgSearchAttachmentAreas() ) )) {
  		    return array();
  		    }
	    }

    // Make sure we have something to search for
	$text = trim( $text );
	if ($text == '') {
		return array();
	    }

	// load plugin params info
 	$plugin =& JPluginHelper::getPlugin('search', 'attachments');
 	$pluginParams = new JParameter( $plugin->params );
	$limit = $pluginParams->def( 'search_limit', 50 );

    // Get the component parameters
    jimport('joomla.application.component.helper');
    $attachParams = JComponentHelper::getParams('com_attachments');
    $secure = $attachParams->get('secure', false);
    $user_field_1 = false;
    if ( strlen($attachParams->get('user_field_1_name', '')) > 0 ) {
        $user_field_1 = true;
        $user_field_1_name = $attachParams->get('user_field_1_name');
        }
    $user_field_2 = false;
    if ( strlen($attachParams->get('user_field_2_name', '')) > 0 ) {
        $user_field_2 = true;
        $user_field_2_name = $attachParams->get('user_field_2_name');
        }
    $user_field_3 = false;
    if ( strlen($attachParams->get('user_field_3_name', '')) > 0 ) {
        $user_field_3 = true;
        $user_field_3_name = $attachParams->get('user_field_3_name');
        }

	$wheres 	= array();
	
    switch ($phrase)
	{
		case 'exact':
			$text	= $db->Quote( '%'.$db->getEscaped( $text, true ).'%', false );
            $user_fields_sql = '';
            if ( $user_field_1 )
                $user_fields_sql .= " OR (LOWER(a.user_field_1) LIKE $text)";
            if ( $user_field_2 )
                $user_fields_sql .= " OR (LOWER(a.user_field_2) LIKE $text)";
            if ( $user_field_3 )
                $user_fields_sql .= " OR (LOWER(a.user_field_3) LIKE $text)";

			$where 	= "((LOWER(a.filename) LIKE $text)" .
                      " OR (LOWER(a.display_filename) LIKE $text)" .
                      $user_fields_sql .
                      " OR (LOWER(a.description) LIKE $text))";
			break;

		default:
			$words 	= explode( ' ', $text );
			$wheres = array();
			foreach ($words as $word)
			{
				$word		= $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
				$wheres2 	= array();
				$wheres2[] 	= "LOWER(a.filename) LIKE $word";
				$wheres2[] 	= "LOWER(a.display_filename) LIKE $word";
				if ( $user_field_1 )
                      $wheres2[] 	= "LOWER(a.user_field_1) LIKE $word";
				if ( $user_field_2 )
                      $wheres2[] 	= "LOWER(a.user_field_2) LIKE $word";
				if ( $user_field_3 )
                      $wheres2[] 	= "LOWER(a.user_field_3) LIKE $word";
				$wheres2[] 	= "LOWER(a.description) LIKE $word";
				$wheres[] 	= implode( ' OR ', $wheres2 );
			}
			$where 	= '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
			break;
	}

    // Set up the sorting
	switch ( $ordering )
	{
        case 'oldest':
            $order = 'a.create_date ASC';
            break;
            
        case 'newest':
            $order = 'a.create_date DESC';
            break;
            
        case 'alpha':
		default:
			$order = 'a.filename DESC';
	}
	
    // Load the permissions functions
    require_once(JPATH_SITE.DS.'components'.DS.'com_attachments'.DS.'permissions.php');
    $user =& JFactory::getUser();

    // Construct and execute the query
	$query = 'SELECT *, a.id as attachment_id, c.title as article_title'
	. ' FROM #__attachments AS a' 
	. ' LEFT JOIN #__content as c ON a.article_id = c.id'
	. ' WHERE ('. $where .')'
	. ' AND a.published = 1'
	. ' ORDER BY '. $order;
	$db->setQuery( $query, 0, $limit );
	$rows = $db->loadObjectList();
    
    $count = count( $rows );
    
    $k = 0;
    $results = Array();
    
	for ( $i = 0; $i < $count; $i++ ) {
        
        // Do not add the attachment if the user may not access it
        if ( !AttachmentsPermissions::user_may_access_attachment($user, $rows[$i]->id)) {
            continue;
            }
        
        // Construct the download URL if necessary
        if ( $secure ) {
            $rows[$i]->href = JRoute::_("index.php?option=com_attachments&task=download&id=".$rows[$i]->attachment_id);
            }
        else {
            $rows[$i]->href = $rows[$i]->url;
            }
		if ( $rows[$i]->display_filename && (strlen($rows[$i]->display_filename) > 0) ) {
            $rows[$i]->title = $rows[$i]->display_filename;
            }
        else {
            $rows[$i]->title = $rows[$i]->filename;
            }

        // Set the text to the string containing the search target
        if ( strlen($rows[$i]->display_filename) > 0 ) {
            $text = $rows[$i]->display_filename .
                " (" . JText::_('FILENAME COLON') . " " . $rows[$i]->filename . ") ";
            }
        else {
            $text = JText::_('FILENAME COLON') . " " . $rows[$i]->filename;
            }

        if ( strlen($rows[$i]->description) > 0 ) {
            $text .= " | " . JText::_('DESCRIPTION COLON') . $rows[$i]->description;
            }

        if ( $user_field_1 && (strlen($rows[$i]->user_field_1) > 0) ) {
            $text .= " | " . $user_field_1_name  . ": " . $rows[$i]->user_field_1;
            }
        if ( $user_field_2 && (strlen($rows[$i]->user_field_2) > 0) ) {
            $text .= " | " . $user_field_2_name  . ": " . $rows[$i]->user_field_2;
            }
        if ( $user_field_3 && (strlen($rows[$i]->user_field_3) > 0) ) {
            $text .= " | " . $user_field_3_name  . ": " . $rows[$i]->user_field_3;
            }
        $rows[$i]->text = $text;
        $rows[$i]->created = $rows[$i]->create_date;
        $rows[$i]->browsernav = 2;
        $rows[$i]->section = JText::_('ATTACHED TO ARTICLE').": ".$rows[$i]->article_title; 

        $results[$k] = $rows[$i];        
        $k++;
	}

	return $results;
}