<?php
defined('_JEXEC') or die('Restricted access');

class AttachmentsPermissions
{

    /* Return true if this user may add an attachment to this article
     * (Note that all of the arguments are assumed to be valid; no sanity checking is done.  
     *  It is up to the caller to validate these objects before calling this function.)       
     * @param JUser $user The user object
     * @param int $article_id The ID of the article the attachment is attached to     
     * @return true if this user add attachments to this article
     */
    function user_may_add_attachment($user, $article_id)
    {
        // A user must be logged in to add attachments
        if ( $user->get('username') == '' ) {
            return false;
            }

        // If the user generally has permissions to add content, they qualify.
        // (editor, publisher, admin, etc)
        // NOTE: Exclude authors since they need to be handled separately.
        $user_type = $user->get('usertype', false);
        if ( ($user_type != 'Author') && 
             $user->authorize('com_content', 'add', 'content', 'all') ) {
            return true;
            }

        // Make sure the article is valid and load its info
        if ( $article_id == null || $article_id == '' || !is_numeric($article_id) ) {
            return false;
            }
        $db =& JFactory::getDBO();
        $query = "SELECT created_by from #__content WHERE id='" . $article_id . "'";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ( count($rows) == 0 ) {
            return false;
            }
        $created_by = $rows[0]->created_by;

        // Get the component parameters
        jimport('joomla.application.component.helper');
        $params = JComponentHelper::getParams('com_attachments');

        // Check who may add attachments
        $who_can_add = $params->get('who_can_add', 'author');
            
        // Verify that this user can upload and attach to this article
        if ( ($who_can_add == 'author') && ( $user->get('id') == $created_by ) ) {
            // The author of the article can add attachments.  (In this mode, 
            // authors may not add attachments to articles they do not own)
            return true;
            }
        if ( $who_can_add == 'logged_in' ) {
            // Anyone who is logged in can add attachments 
            // (Can't get here unless the user is logged in)
            return true;
            }
            
        // No one else is allowed to add articles
        return false;
    }


    /* Return true if this user may modify (edit/delete/update) this attachment for this article
     * (Note that all of the arguments are assumed to be valid; no sanity checking is done.
     *  It is up to the caller to validate these objects before calling this function.)
     * @param JUser $user The user object
     * @param record $attachment database reocrd for the attachment
     * @param int $article_id The ID of the article the attachment is attached to
     * @param $params The Attachments component parameters object
     * @return true if this user may modify this attachment
     */
    function user_may_modify_attachment($user, $attachment, $article_id, &$params)
    {
        // If the user generally has permissions to edit all content, they 
        // may modify this attachment (editor, publisher, admin, etc)
        if ( $user->authorize('com_content', 'edit', 'content', 'all') ) {
            return true;
            }
        else {
            $user_id = $user->get('id');
            $who_can_add = $params->get('who_can_add');
            $attachment_id = $attachment->id;
            
            // Load info about the article
            $db =& JFactory::getDBO();
            $query = "SELECT created_by from #__content WHERE id='" . $article_id . "'";
            $db->setQuery($query);
            $rows = $db->loadObjectList();
            if ( count($rows) == 0 ) {
                return false;
                }
            $created_by = $rows[0]->created_by;

            // Verify that this user can delete/modify this attachment for this article
            if ( $who_can_add == 'logged_in' ) {
                if ( ($user_id == $attachment->uploader_id) ||
                     ($user_id == $created_by) ) {
                     // Registered users and authors can only modify attachments if
                     //   they added the attachment or they own the article
                     return true;
                     }
                }
            elseif ( $who_can_add == 'author' ) {
                if ( $user_id == $created_by ) {
                    // Authors can modify ANY attachments that belong to their article
                    return true;
                    }
                }
            }
                
        return false;
    }


    /** Check to see if the user may access (see/download) the attachments
     * @param JUser $user : the current user object
     * @param int $id : The id of the attachment
     * @return true if access is okay (false if not)
     */                   
    function user_may_access_attachment( $user, $id )
    {
        // NOTE: This implementation is pretty dumb...
        
        // Get the component parameters
        jimport('joomla.application.component.helper');
        $params = JComponentHelper::getParams('com_attachments');
        $who_can_see = $params->get('who_can_see', 'logged_in');
        $logged_in = $user->get('username') <> '';
        
        if ( ( $who_can_see == 'anyone' ) ||
             ( ($who_can_see == 'logged_in') && $logged_in ) ) {
           return true;
           }
      
        return false;
    }


    /** Return true if the attachments should be hidden for this article
     * @param int $article_id The ID of the article the attachment is attached to
     * @param $params The Attachments component parameters object
     * @return true if the attachments should be hidden for this article
     */
    function attachments_hidden_for_article($article_id, &$params)
    {
        // Make sure we have a valid article ID
        if ( $article_id == null || $article_id == '' || !is_numeric($article_id) ) {
            $errmsg = JText::_('ERROR BAD ARTICLE ID');
            JError::raiseError(500, $errmsg);
            }

        // Make sure the article is valid and get info about it
        $db =& JFactory::getDBO();
        $query = "SELECT created_by, sectionid, catid from #__content WHERE id='" . $article_id . "'";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ( count($rows) == 0 ) {
            $errmsg = JText::_('ERROR INVALID ATTACHMENT ID') . " ($article_id)";
            JError::raiseError(500, $errmsg);
            }
        $created_by = intval($rows[0]->created_by);
        $sectionid = intval($rows[0]->sectionid);
        $catid = intval($rows[0]->catid);

        // First, check to see whether the attachments should be hidden for this article
        $hide_attachments_for = str_replace('-', '_', trim($params->get('hide_attachments_for', '')));
        if ( $hide_attachments_for <> '' ) {
            $hide_specs = split(',', $hide_attachments_for);
            foreach ( $hide_specs as $hide ) {
                if ( trim($hide) == 'frontpage' ) {
                    if ( JRequest::getVar('view', false) == 'frontpage' ) {
                        return true;
                        }
                    }
                elseif ( trim($hide) == 'all_but_article_views' ) {
                    if ( !(JRequest::getVar('view') == 'article') ) {
                        return true;
                        }
                    }
                else {
                    // We assume it must be section/category specs
                    $sect_cat = split('/', $hide);
                    $hide_sect_id = intval($sect_cat[0]);
                    $hide_cat_id = -1;
                    if ( count($sect_cat) > 1 )
                        $hide_cat_id = intval($sect_cat[1]);
                    if ( ($hide_cat_id == -1) and ($sectionid == $hide_sect_id) ) {
                        return true;
                        }
                    if ( ($sectionid == $hide_sect_id) and ($catid == $hide_cat_id) ) {
                        return true;
                        }
                    }
                }
            }

        return false;
    }


    /* Add the permissions to the array of attachments data
     * @param &array &$attachments : An array of attachments for an article from a DB query.
     * @param JUser $user : The current user object
     * @param int $article_id : the id of the article
     * @return true if some attachments should be visible, false if none should be visible
     *
     * This function adds the following boolean fields to each attachment row:
     *     - 'user_may_see'
     *     - 'user_may_modify'
     */
    function add_permissions( &$attachments, $user, $article_id )
    {
        // Make sure we have a valid article ID
        if ( $article_id == null || $article_id == '' || !is_numeric($article_id) ) {
            $errmsg = JText::_('ERROR BAD ARTICLE ID');
            JError::raiseError(500, $errmsg);
            }

        // If there are no attachments, don't do anything
        if ( count($attachments) == 0 ) {
             return false;
             }

        // Get the component parameters
        jimport('joomla.application.component.helper');
        $params = JComponentHelper::getParams('com_attachments');

        // Process each attachment
        $logged_in = $user->get('username') <> '';
        $who_can_add = $params->get('who_can_add');
        $some_visible = false;
        for ($i=0, $n=count($attachments); $i < $n; $i++) {
           $attach = &$attachments[$i];
                   
           $attach->user_may_see = false;
           $attach->user_may_modify = false;
           
           // Determine if the user may modify this attachment
           //  (Nobody may modify attachments without being logged in)
           if ( $logged_in ) {
                $attach->user_may_modify =
                    AttachmentsPermissions::user_may_modify_attachment($user, $attach, $article_id, $params);
                }

           // Determine if the user may see the attachment
           $who_can_see = $params->get('who_can_see', 'logged_in');
           if ( ( $who_can_see == 'anyone' ) ||
                ( ($who_can_see == 'logged_in') && $logged_in ) ) {
               $attach->user_may_see = true;
               $some_visible = true;
               }
           }

        return $some_visible;
    }

}
