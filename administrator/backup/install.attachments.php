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

function com_install()
{
    // First make sure that this version of Joomla is 1.5 or greater
    $version = new JVersion();
    if ( (real)$version->RELEASE < 1.5 ) {
        echo "<h1 style=\"color: red;\">The 'Attachments' package will only work on Joomla version 1.5 or later!</h1>";
        return false;
        }

    echo "<h3>Updating Attachments Table...</h3>\n";
    require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_attachments'.DS.'update.php');
    AttachmentsUpdate::update_attachments_table();

    // Check to see if the plugin has been installed yet
    $plugin_installed = false;
    $plugin_published = false;
    $db =& JFactory::getDBO();
    $query = "SELECT published FROM #__plugins WHERE element='attachments' AND folder='content' LIMIT 1";
    $db->setQuery($query);
    $rows = $db->loadObjectList();
    if ( count($rows) > 0 ) {
        $plugin_installed = true;
        $plugin_published = (intval($rows[0]->published) == 1);
        }

    echo '<div class="header">Attachments for articles component succesfully installed! <br>';

    if ( $plugin_installed ) {
        if ( !$plugin_published )
            echo "<i>Don&rsquo;t forget to enable the plugins!</i>";
        }
    else {
        echo "<i>Don&rsquo;t forget to install the plugins too!</i>";
        }
    ?>
    </div>
    <h2>NOTES:</h2>

    <p>
        Once the attachment plugins and component have been installed and the plugins are enabled,
        the attachments should work.  The default after installation is for the attachments to be
        visible to anyone that is logged in and for the link to add attachments to visible only
        to the author of the article.   Both of these options can be changed via the attachments
        parameters which can be changed in the component manager (go to the administrative back end,
        select "Article&nbsp;Attachments" under the "Components" menu, and click on the Parameters
        item on the toolbar).
    </p>

    <p>
        Once an attachment is uploaded, it is not visible until it is published.  To do this go to the
        administrative backend and select "Article&nbsp;Attachments" under the "Components" menu.  This will
        show a list of attachments and has controls to publish the attachments.  The option to make
        attachments automatically be published after they are uploaded can be changed via the Attachments
        component manager.
    </p>

    <p>
        This extension respects the options in the Media Manager regarding what types of files can 
        be uploaded.  If you can&rsquo;t attach certain file types (such as zip files), go to the
        "Global Configuration" item under the "Site" menu in the administrative backend.  Click
        on the "System" tab and look for the "Media Settings" section.  You can add appropriate
        file extensions or mime types there.
    </p>

    <p>
        Please see the help page for further details, including known limitations of this extension
        and suggestions on how to upgrade this extension.  To get to the help page, select
        "Article&nbsp;Attachments" under the "Components" menu in the adminstrative    backend.
        Click on the help icon on the toolbar to bring up the help page.  Or you may use this URL:
        <a href="<?php echo JURI::base(); ?>components/com_attachments/help/en-GB/help.html"
           target="_blank">Help File (in English)</a>.
    </p>

    <p>
        Please report bugs and suggestions to <a href="mailto:jmcameron@jmcameron.net">jmcameron@jmcameron.net</a>.
    </p>
    <?php

    return true;
}
?>