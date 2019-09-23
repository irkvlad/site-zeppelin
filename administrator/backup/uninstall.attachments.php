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

function com_uninstall()
{
    // Get the component parameters
    jimport('joomla.application.component.helper');
    $params = JComponentHelper::getParams('com_attachments');

    // Determine the upload directory
    $upload_subdir = $params->get('attachments_subdir', 'attachments');
    if ( $upload_subdir == '' ) {
        $upload_subdir = "attachments";
        }
    if ( !JFolder::exists( JPATH_BASE . $upload_subdir ) ) {
        $upload_subdir = null;
        }

    ?>
        <div class="header">The article attachments component is now removed from your system. <br>
        <i>Don&rsquo;t forget to remove the attachments plugins too!</i></div>
    <?php
    if ( $upload_subdir ) {
        echo "<h2>You will also need to manually delete the attachments subdirectory: '" . $upload_subdir . "'</h2>\n";
        }
}
?>