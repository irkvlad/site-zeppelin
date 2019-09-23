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

class TableAttachments extends JTable
{
     var $id = null;
     var $filename = null;
     var $filename_sys = null;
     var $file_type = null;
     var $file_size = null;
     var $display_filename = null;
     var $description = null;
     var $icon_filename = null;
     var $url = null;
     var $uploader_id = null;
     var $article_id = null;
     var $published = null;
     var $user_field_1 = null;
     var $user_field_2 = null;
     var $user_field_3 = null;
     var $create_date = null;
     var $modification_date = null;
     var $download_count = null;

     function __construct(&$db)
     {
             parent::__construct('#__attachments', 'id', $db);
     }
}
?>
