<?php
/**
* @version $Id$
* @package Joomla! 1.5.x, jsmallfib plugin
* @copyright (c) 2009 Enrico Sandoli
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

/***************************************************************************
 
     This file is part of jsmallfib
 
     This program is free software: you can redistribute it and/or modify
     it under the terms of the GNU General Public License as published by
     the Free Software Foundation, either version 3 of the License, or
     (at your option) any later version.
 
     This program is distributed in the hope that it will be useful,
     but WITHOUT ANY WARRANTY; without even the implied warranty of
     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     GNU General Public License for more details.
  
     A copy of the GNU General Public License is on <http://www.gnu.org/licenses/>.
   
 ***************************************************************************

     This plugin has been written by Enrico Sandoli based on the original
     enCode eXplorer v4 by Marek Rei. Because the code works within the Joomla!
     environment, the original password protection has been replaced with a
     new access rights system. The ability to delete files and folders (if empty)
     has also been added to the original code, together with some extra security
     checks to forbid access to areas outside the intended repositories.
  
     For info on usage, please refer to the plugin configuration page within
     the administrator site in Joomla!, or to jsmallfib homepage, currently
     on http://www.jsmallsoftware.com
  
 ***************************************************************************

     Module extended, corrected and modified in several ways by
       Erik Liljencrantz, erik@eldata.se, http://www.eldata.se
     marked below as /ErikLtz

     One special correction: the module used urldecode on $_GET-variables
     which is a no-no. From Google:
       A reminder: if you are considering using urldecode() on a $_GET
       variable, DON'T!
     Though delfile and delfolder is double urlencoded so these still have
     the urldecode there.
 
 ***************************************************************************/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// import the JPlugin class
jimport('joomla.event.plugin');

class plgContentjsmallfib extends JPlugin
{

	var $baselink;
	var $imgdir = "plugins/content/jsmallfib/";
	var $option;
	var $view;
	var $id;
	var $Itemid;

	var $date_format;
	var $display_seconds;

	var $table_width;
	var $row_height;
	var $highlighted_color;
	var $oddrows_color;
	var $evenrows_color;

	var $framebox_bgcolor;
	var $framebox_border;
	var $framebox_linetype;
	var $framebox_linecolor;

	var $errorbox_bgcolor;
	var $errorbox_border;
	var $errorbox_linetype;
	var $errorbox_linecolor;

	var $uploadbox_bgcolor;
	var $uploadbox_border;
	var $uploadbox_linetype;
	var $uploadbox_linecolor;

	var $inputbox_bgcolor;
	var $inputbox_border;
	var $inputbox_linetype;
	var $inputbox_linecolor;

	var $header_bgcolor;

	var $line_bgcolor;
	var $line_height;

	function plgContentjsmallfib( &$subject )
	{
		parent::__construct( $subject );
		
		// load plugin parameters and language file
		$this->_plugin = JPluginHelper::getPlugin( 'content', 'jsmallfib' );
		$this->_params = new JParameter( $this->_plugin->params );
		JPlugin::loadLanguage('plg_content_jsmallfib', JPATH_ADMINISTRATOR);
	}

	function onPrepareContent(&$article, &$params, $limitstart) {

		global $mainframe;

		$version_number = "1.0.31";

		// return if manually disabled in this article (needed for demo purposes)
		if (strstr($article->text, "jsmallfib_disabled_here")) {
			$article->text = preg_replace("/jsmallfib_disabled_here/", "", $article->text);
			return;
		}

		// check article text; if it is NOT in the form of a jsmallfib command than return
		$regex = '/{(jsmallfib)\s*(.*?)}/i';
		$command_match = array();
		$command_match_found = preg_match($regex, $article->text, $command_match);

		// return if command is not found
		if (!$command_match_found) {
			return;
		}

		// only allow article view (if section or category view, just output a reference to the repository)
		$view = JREQUEST::getVar('view', 0);

		if (strcmp (strtoupper($view), "ARTICLE") && strcmp (strtoupper($view), "DETAILS")) { // added compatibility with component 'EventList'
			$article->text = preg_replace("/{(jsmallfib)\s*(.*?)}/i", JText::_('only_article_view'), $article->text);
			return;
		}

		// GOT HERE SO GO AHEAD AND PROCESS THE COMMAND

		// get default parameters
		$this->date_format 	 	= $this->_params->def('date_format', 'dd_mm_yyyy_slashsep');
		$this->display_seconds 	 	= $this->_params->def('display_seconds', '1');

		$this->table_width 	 	= $this->_params->def('table_width', 680);
		$this->row_height  	 	= $this->_params->def('row_height', 22);
		$this->highlighted_color 	= $this->_params->def('highlighted_color', "FFD");
		$this->oddrows_color 		= $this->_params->def('oddrows_color', "F9F9F9");
		$this->evenrows_color 		= $this->_params->def('evenrows_color', "FFFFFF");

		$this->framebox_bgcolor		= $this->_params->def('framebox_bgcolor', "FFFFFF");
		$this->framebox_border		= $this->_params->def('framebox_border', "1");
		$this->framebox_linetype	= $this->_params->def('framebox_linetype', "solid");
		$this->framebox_linecolor	= $this->_params->def('framebox_linecolor', "CDD2D6");

		$this->errorbox_bgcolor		= $this->_params->def('errorbox_bgcolor', "FFE4E1");
		$this->errorbox_border		= $this->_params->def('errorbox_border', "1");
		$this->errorbox_linetype	= $this->_params->def('errorbox_linetype', "solid");
		$this->errorbox_linecolor	= $this->_params->def('errorbox_linecolor', "CDD2D6");

		$this->uploadbox_bgcolor	= $this->_params->def('uploadbox_bgcolor', "F8F9FA");
		$this->uploadbox_border		= $this->_params->def('uploadbox_border', "1");
		$this->uploadbox_linetype	= $this->_params->def('uploadbox_linetype', "solid");
		$this->uploadbox_linecolor	= $this->_params->def('uploadbox_linecolor', "CDD2D6");

		$this->header_bgcolor		= $this->_params->def('header_bgcolor', "FFFFFF");

		$this->line_bgcolor		= $this->_params->def('line_bgcolor', "CDD2D6");
		$this->line_height		= $this->_params->def('line_height', "1");

		$this->inputbox_bgcolor		= $this->_params->def('inputbox_bgcolor', "FFFFFF");
		$this->inputbox_border		= $this->_params->def('inputbox_border', "1");
		$this->inputbox_linetype	= $this->_params->def('inputbox_linetype', "solid");
		$this->inputbox_linecolor	= $this->_params->def('inputbox_linecolor', "CDD2D6");

		// remove magic quotes if needed
		if (function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) {

    			function stripslashes_deep($value)
    			{
        			$value = is_array($value) ?  array_map('stripslashes_deep', $value) : stripslashes($value);
        			return $value;
    			}

			$_POST = array_map('stripslashes_deep', $_POST);
			$_GET = array_map('stripslashes_deep', $_GET);
			$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
			$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
		}

		// write CSS //
		$this->do_css();

		// write JS //
		$this->do_js();

		// split the article text in two parts (before and after the FIRST occurrence of the command)
		$text_array = array();
		$text_array = preg_split($regex, $article->text, 2);

		// CHECK ACCESS RIGHTS
		
		// get access rights (they are in the format [<optional 'g' or 'G'>userid:permission], but would also work
		// without brackets and/or separated by commas or other chars (excluding ':')
		
		$access_rights_args = array();
		$access_rights_args_found = preg_match_all("/g?\d+:\d+n?/i", $command_match[0], $access_rights_args); 	// introduced undocumented feature: 'n' suffix to permission
															// (needed to disable download ability from level 1 when setting '1n')

		// get current userid
		$user	= $mainframe->getUser();	
		$userid = $user->id;
		$username = $user->name;
		if (!$username)
		{
			$username = JText::_('unregistered_visitor');
		}
		$remote_address = $_SERVER['REMOTE_ADDR'];
		if (!$remote_address)
		{
			$remote_address = JText::_('unavailable');
		}

		// set the default access rights, then check if specific ones do apply
		if ($userid) {
			$access_rights = $this->_params->def('default_reguser_access_rights', 0);
		}
		else {
			$access_rights = $this->_params->def('default_visitor_access_rights', 0);
		}

		if ($access_rights_args_found)
		{
			// get the category of the associated joomla! contact (the group), if there is one
			$db =& JFactory::getDBO();
			$query = "SELECT #__categories.id AS catid "
					."FROM #__contact_details LEFT JOIN #__categories ON #__contact_details.catid=#__categories.id "
					."WHERE #__contact_details.user_id='".$userid."'";

			$db->setQuery($query);
			$row = $db->loadObjectList();
	
			if ($db->getErrorNum()) {
				echo $db->stderr();
				return false;
			}

			if (count($row)) {
				$user_catid = $row[0]->catid;
			}
			else {
				$user_catid = 0;
			}

			// check if any of the specific access rights apply to the current user
			$userid_0_permission = 0;
			foreach ($access_rights_args[0] as $access_rights_pair)
			{
				list ($tmp_userid, $tmp_permission) = explode(":", $access_rights_pair);

				// if userid 0 (visitor) is specified, take note of the relevant permission, and assign it immediately out of this loop
				// if access rights have not been defined, or they are lower than user 0's (this prevents a registered user having lower access than a visitor)
				if (!strcmp($tmp_userid, "0"))
				{
					if (!strcasecmp($tmp_permission, "1n"))
					{
						$userid_0_permission = 1;
						$disable_level_1_downloads = 1;
					}
					else
					{
						$userid_0_permission = $tmp_permission;
						$disable_level_1_downloads = 0;
					}
				}
				if ($tmp_userid[0] == 'g' || $tmp_userid[0] == 'G')
				{
					$tmp_userid[0] = ' ';
					$tmp_userid = ltrim($tmp_userid);

					if ($tmp_userid == $user_catid)
					{
						if (!strcasecmp($tmp_permission, "1n"))
						{
							$access_rights = 1;
							$disable_level_1_downloads = 1;
						}
						else
						{
							$access_rights = $tmp_permission;
							$disable_level_1_downloads = 0;
						}
					}
				}
				else if ($tmp_userid == $userid)
				{
					if (!strcasecmp($tmp_permission, "1n"))
					{
						$access_rights = 1;
						$disable_level_1_downloads = 1;
					}
					else
					{
						$access_rights = $tmp_permission;
						$disable_level_1_downloads = 0;
					}
					break;
				}
			}
			if (!$access_rights || $access_rights < $userid_0_permission)
			{
				$access_rights = $userid_0_permission;
			}
		}

		if (!$access_rights)
	        {
			$text  = "<div id='error'>"
				."<table cellspacing='0' cellpadding='0' border='0' width='100%'>"
				."<tr height='30' valign='middle'>"
				."<td width='60px'><img src=\"".$this->imgdir."warning.png\"></td><td>".JText::_('no_access_rights')."</td>"
				."</tr>"
				."</table>"
				."</div>";

			$article->text = $article->fulltext = $article->introtext = $text;

			$params->set('show_author', '0');
			$params->set('show_create_date', '0');
			$params->set('show_modify_date', '0');
			return;
		}

		// GOT HERE SO GO AHEAD WITH DISPLAY
		
		$error = NULL;

		// get repository from the command
		$repository_match = array();
		$repository_found = preg_match("/\[.*?\]/i", $command_match[0], $repository_match);
		if ($repository_found && !strstr($repository_match[0], ":")) // note: avoid mistaking permission pairs for a missing repository (they contain ':')
		{
			$repository = trim($repository_match[0], "[]");
		}
		else
		{
			$repository = "";
		}

		// get optional description from within the command: must be in the form desc(this is a description)
		$description_args = array();
		$description_args_found = preg_match_all("/desc\(.*?\)/i", $command_match[0], $description_args);
		if ($description_args_found)
		{
			$description = substr_replace($description_args[0][0], "", 0, 5);
			$description = substr_replace($description, "", -1, 1);
		}
		else
		{
			$description = "";
		}
			
		// set the starting directory as an absolute path (if relative add joomla!'s root)
		$is_path_relative = $this->_params->def('is_path_relative', 1);
		if ($is_path_relative)
		{
			$default_absolute_path = JPATH_ROOT.DS.trim($this->_params->def('default_path', 'jsmallfib_top'), "/\\");

			// create the default path folder (only if 1. does not exist already; 2. using relative path)
			if (!file_exists($default_absolute_path))
			{
				if (!($rc = @mkdir ($default_absolute_path)))
				{
					$text  = "<div id='error'>"
						."<table cellspacing='0' cellpadding='0' border='0' width='100%'>"
						."<tr height='30' valign='middle'>"
						."<td width='60px'><img src=\"".$this->imgdir."warning.png\"></td><td>".JText::sprintf('failed_creating_default_dir', $default_absolute_path)."</td>"
						."</tr>"
						."</table>"
						."</div>";
		
					$article->text = $article->fulltext = $article->introtext = $text;
		
					$params->set('show_author', '0');
					$params->set('show_create_date', '0');
					$params->set('show_modify_date', '0');
					return;
				}
			}
		}
		else
		{
			$default_absolute_path = rtrim($this->_params->def('default_path', JPATH_ROOT.DS.'jsmallfib_top'), "/\\");
		}
		if ($repository)
		{
			$starting_dir = $default_absolute_path.DS.$repository;

			// if using default path and starting dir does not exist, attempt to create it
			if ($is_path_relative && !file_exists($starting_dir))
			{
				if (!($rc = @mkdir ($starting_dir)))
				{
					$text  = "<div id='error'>"
						."<table cellspacing='0' cellpadding='0' border='0' width='100%'>"
						."<tr height='30' valign='middle'>"
						."<td width='60px'><img src=\"".$this->imgdir."warning.png\"></td><td>".JText::sprintf('failed_creating_repository', $repository, $default_absolute_path)."</td>"
						."</tr>"
						."</table>"
						."</div>";
		
					$article->text = $article->fulltext = $article->introtext = $text;
		
					$params->set('show_author', '0');
					$params->set('show_create_date', '0');
					$params->set('show_modify_date', '0');
					return;
				}
			}
		}
		else
		{
			$starting_dir = $default_absolute_path;
		}

		// build link base
		$option = JREQUEST::getVar('option', 0);
		$id = JREQUEST::getVar('id', 0);
		$Itemid = JREQUEST::getVar('Itemid', 0);

		$this->baselink = JRoute::_(JURI::base().'index.php?option='.$option.'&view='.$view.'&id='.$id.'&Itemid='.$Itemid);

		// The array of folders that will be hidden from the list.
		$hidden_folders_parameter = $this->_params->def('hidden_folders', 0);
		$hidden_folders = array();
		$hidden_folders = preg_split("/\s*,+\s*/", $hidden_folders_parameter);

		// Manage filenames and extensions that will be hidden from the list.
		$hidden_files_parameter = $this->_params->def('hidden_files', 0);
		
		$hidden_extensions = array();
		$hiden_extensions_found = preg_match_all("/\*{1}\.{1}\w+/", $hidden_files_parameter, $hidden_extensions);

		$hidden_files = array();
		$hidden_files_string = trim(preg_replace("/\*{1}\.{1}\w+/", "", $hidden_files_parameter));
		$hidden_files = preg_split("/\s*,+\s*/", $hidden_files_string);

		// ***********************************************************************************************************************
		// Managing input from user actions
		// ***********************************************************************************************************************

		// set variables for logs
		$log_uploads        = $this->_params->def('log_uploads', 0);
		$log_downloads      = $this->_params->def('log_downloads', 0);
		$log_removedfolders = $this->_params->def('log_removedfolders', 0);
		$log_removedfiles   = $this->_params->def('log_removedfiles', 0);
		$log_newfolders     = $this->_params->def('log_newfolders', 0);
		$log_newfoldernames = $this->_params->def('log_newfoldernames', 0);
		$log_newfilenames   = $this->_params->def('log_newfilenames', 0);
		$logfile_prefix    = JPATH_ROOT.DS."logs".DS."jsmallfib_log_".md5($starting_dir)."_";
		$today = date("Y-m-d H:i:s");

		// Let's see what folder is being opened and react accordingly
		$dir = $starting_dir;
		$upper_dir = "";
		
		if(isset($_GET["dir"]) && strlen($_GET["dir"])) 
		{
			// we had to utf-8 encode delfolders and delfiles for Firefox (special chars are not sent to javascript for delete confirmation)
			if ((isset($_GET["delfile"]) && strlen($_GET["delfile"])) || (isset($_GET["delfolder"]) && strlen($_GET["delfolder"])))
			{
				$get_dir = html_entity_decode(utf8_decode(urldecode($_GET["dir"])));  // NOTE: Here we need urldecode as delfile is double encoded /ErikLtz
			}
			else
			{
				$get_dir = html_entity_decode($_GET["dir"]);   // Removed urldecode on _GET (not delete) /ErikLtz 
				// $get_dir = html_entity_decode(urldecode($_GET["dir"]));
			}

			// This format is forbidden (also check for trying to access folders outside the repository root)
			if(ereg("\.\.(.*)", $get_dir) || (strlen($get_dir) == 1 && $get_dir[0] == DS) || (!stristr(str_replace("/", "\\", $get_dir), str_replace("/", "\\", $starting_dir)))) 
			{
				$dir = $starting_dir;
				$upper_dir = "";
			}
			else
			{
				// if got here then the user is allowed to view the current folder (remove the upper link if this is the starting_dir)
				$dir = rtrim($get_dir, "/\\");
				if(strcmp(str_replace("/", "\\", $starting_dir), str_replace("/", "\\", $get_dir)))
				{
					$upper_dir = $this->upperDir($dir);
				}

				// if asking to delete a folder
				if ($access_rights > 3 && isset($_GET["delfolder"]) && strlen($_GET["delfolder"]))
				{
					// only works with empty folders
					$tmpdir=html_entity_decode($dir.DS.utf8_decode(urldecode($_GET["delfolder"])));  // NOTE: Here we need urldecode as delfolder is double encoded /ErikLtz
					$rc = @rmdir ($tmpdir);
					
					// Check whether directory is gone
					if(file_exists($tmpdir)) {
						
						// Nah, still there, show new error message
						$error = JText::sprintf('delete_folder_failed', urldecode($_GET["delfolder"]));  // NOTE: Here we need urldecode as delfolder is double encoded /ErikLtz
					
					} else {
						
						// for logging purposes
						$removed_folder = "";
						if($log_removedfolders && $rc)
						{
							$removed_folder = utf8_decode(urldecode($_GET["delfolder"]));  // NOTE: Here we need urldecode as delfolder is double encoded /ErikLtz
						}
					}
				}
				// if asking to delete a file
				else if ($access_rights > 2 && isset($_GET["delfile"]) && strlen($_GET["delfile"]))
				{
					$rc = @unlink (html_entity_decode($dir.DS.utf8_decode(urldecode($_GET["delfile"]))));  // NOTE: Here we need urldecode as delfile is double encoded /ErikLtz

					// for logging purposes
					$removed_file = "";
					if($log_removedfiles && $rc)
					{
						$removed_file = utf8_decode(urldecode($_GET["delfile"]));  // NOTE: Here we need urldecode as delfile is double encoded /ErikLtz
					}
				}
			}
		}

		// once dir is defined (with absolute path), define the current position (the sub-path under the main default repository folder)
		// and the relative dir (dir relative to the web root)
		$current_position = substr($dir, strlen($default_absolute_path) + 1, strlen($dir) - strlen($default_absolute_path));
		if (!$current_position)
		{
			$current_position_links = "<a href='".$this->baselink."&dir=".urlencode($starting_dir)."'>".JText::_('toplevel')."</a>";
		} else {

			// Use current_position to build linked list of directories in $current_position_links [ErikLtz]
			$arr = explode(DS, $current_position);
			$current_position_links = "";
			$tmpdir = $dir;
		  
			for($i = count($arr) - 1; $i >= 0; $i--) {
			
				$current_position_links = "<a href='".$this->baselink."&dir=".urlencode($tmpdir)."'>".$arr[$i]."</a>"
					.($i == count($arr) - 1 ? "" : "&nbsp;<img src=\"".$this->imgdir."arrow_right.png\" />&nbsp;").$current_position_links;

			  	$tmpdir=$this->upperDir($tmpdir);
			}

			// if the repository is not reported in the command (using default top repository) then display link to top level (to default top repository)
			if (!$repository)
			{
				$current_position_links = "<a href='".$this->baselink."&dir=".urlencode($starting_dir)."'>".JText::_('toplevel')."</a>"
					."&nbsp;<img src=\"".$this->imgdir."arrow_right.png\" />&nbsp;".$current_position_links;
			}
		}
		$relative_dir = substr($dir, strlen(JPATH_ROOT) + 1, strlen($dir) - strlen(JPATH_ROOT));

		// if the repository is OUTSIDE the web root then use for files the absolute path
		// (you won't be able to display files left-clicking, but you'll be able to download them right-clicking on them)
		if(!stristr(str_replace("/", "\\", $dir), str_replace("/", "\\", $_SERVER["DOCUMENT_ROOT"]))) 
		{
			$relative_dir = $dir;
		}

		// now that the current dir is established, log removals registered above
		if($log_removedfolders && $removed_folder)
		{
			$log_file = $logfile_prefix."removedfolders.txt";
			$log_text = JText::sprintf('removedfolder_log_text', $today, utf8_encode($removed_folder), utf8_encode($relative_dir), $username, $remote_address);
			file_put_contents($log_file, $log_text, FILE_APPEND);

			$removed_folder = "";
		}
		if($log_removedfiles && $removed_file)
		{
			$log_file = $logfile_prefix."removedfiles.txt";
			$log_text = JText::sprintf('removedfile_log_text', $today, utf8_encode($removed_file), utf8_encode($relative_dir), $username, $remote_address);
			file_put_contents($log_file, $log_text, FILE_APPEND);

			$removed_file = "";
		}
		
		// creating the new directory
		if($access_rights > 1 && isset($_POST['userdir']) && strlen($_POST['userdir']) > 0)
		{
			$forbidden = array(".", "/", "\\");
			for($i = 0; $i < count($forbidden); $i++)
			{
				$_POST['userdir'] = str_replace($forbidden[$i], "", $_POST['userdir']);
			}
			$tmpdir = html_entity_decode($dir.DS.utf8_decode($_POST['userdir']));
			if(!@mkdir($tmpdir, 0777))
			{
				// Check for existing file with same name and choose different error message [ErikLtz]
				if(file_exists($tmpdir))
				{
					$error = JText::_('new_folder_failed_exists');
				}
				else
				{
					$error = JText::_('new_folder_failed');
				}
			}
			else if(!@chmod($tmpdir, 0777))
			{
				$error = JText::_('chmod_dir_failed');
			}
			else if($log_newfolders)
			{
				// log
				$log_file = $logfile_prefix."newfolders.txt";
				$log_text = JText::sprintf('newfolder_log_text', $today, $_POST['userdir'], utf8_encode($relative_dir), $username, $remote_address);
				file_put_contents($log_file, $log_text, FILE_APPEND);
			}
		}

		// changing name to a folder
		if($access_rights > 1 && isset($_POST['old_foldername']) && strlen($_POST['old_foldername']) > 0 &&
		       			 isset($_POST['new_foldername']) && strlen($_POST['new_foldername']) > 0)
		{
			$old_foldername = urldecode($_POST['old_foldername']);
			$new_foldername = utf8_decode($_POST['new_foldername']); // this is utf-8 encoded because it comes from the visible text field

			$forbidden = array(".", "/", "\\");
			for($i = 0; $i < count($forbidden); $i++)
			{
				$old_foldername = str_replace($forbidden[$i], "", $old_foldername);
			}
			for($i = 0; $i < count($forbidden); $i++)
			{
				$new_foldername = str_replace($forbidden[$i], "", $new_foldername);
			}
			if(!@rename(html_entity_decode($dir."/".$old_foldername), html_entity_decode($dir."/".$new_foldername)))
			{
				$error = JText::sprintf('folder_rename_failed', utf8_encode($old_foldername), utf8_encode($new_foldername));
			}
			else if($log_newfoldernames)
			{
				// log
				$log_file = $logfile_prefix."newfoldernames.txt";
				$log_text = JText::sprintf('newfoldername_log_text', $today, utf8_encode($old_foldername), utf8_encode($new_foldername), utf8_encode($relative_dir), $username, $remote_address);
				file_put_contents($log_file, $log_text, FILE_APPEND);
			}
		}

		// changing name to a file
		if($access_rights > 1 && isset($_POST['old_filename']) && strlen($_POST['old_filename']) > 0 &&
		       			 isset($_POST['new_filename']) && strlen($_POST['new_filename']) > 0)
		{
			$old_filename = urldecode($_POST['old_filename']);
			$new_filename = utf8_decode($_POST['new_filename']);

			$forbidden = array("/", "\\");
			for($i = 0; $i < count($forbidden); $i++)
			{
				$old_filename = str_replace($forbidden[$i], "", $old_filename);
			}
			for($i = 0; $i < count($forbidden); $i++)
			{
				$new_filename = str_replace($forbidden[$i], "", $new_filename);
			}
			if(!@rename(html_entity_decode($dir."/".$old_filename), html_entity_decode($dir."/".$new_filename)))
			{
				$error = JText::sprintf('file_rename_failed', utf8_encode($old_filename), utf8_encode($new_filename));
			}
			else if($log_newfilenames)
			{
				// log
				$log_file = $logfile_prefix."newfilenames.txt";
				$log_text = JText::sprintf('newfilename_log_text', $today, utf8_encode($old_filename), utf8_encode($new_filename), utf8_encode($relative_dir), $username, $remote_address);
				file_put_contents($log_file, $log_text, FILE_APPEND);
			}
		}

		// moving the uploaded file
		if($access_rights > 1 && isset($_FILES['userfile']['name']) && strlen($_FILES['userfile']['name']) > 0)
		{
			$name = $this->baseName(utf8_decode($_FILES['userfile']['name']));

			$upload_dir = urldecode($_POST['upload_dir']);
			$upload_file = $upload_dir.DS.$name;

			if(!is_uploaded_file(html_entity_decode(utf8_decode($_FILES['userfile']['tmp_name']))))
			{
				$error = JText::_('failed_upload');
			}
			else if(file_exists($uploaded_file))    // Check to avoid overwriting existing file /ErikLtz
			{
				$error = JText::_('failed_upload_exists');
				// Clean up the uploaded file
				@unlink(html_entity_decode(utf8_decode($_FILES['userfile']['tmp_name'])));
			}
			else if(!move_uploaded_file(html_entity_decode(utf8_decode($_FILES['userfile']['tmp_name'])), html_entity_decode($upload_file)))
			{
				$error = JText::_('failed_move');
			}
			else
			{
				@chmod(html_entity_decode($upload_file), 0777);

				// log
				if($log_uploads)
				{
					$log_file = $logfile_prefix."uploads.txt";
					$log_text = JText::sprintf('upload_log_text', $today, utf8_encode($this->baseName($upload_file)), utf8_encode($relative_dir), $username, $remote_address);
					file_put_contents($log_file, $log_text, FILE_APPEND);
				}
			}
		}

		// managing file download
		if($access_rights && isset($_GET['download_file']) && strlen($_GET['download_file']))
		{

			// send requested file
			$download_file = html_entity_decode($_GET['download_file']);   // Removed urldecode on _GET /ErikLtz
			if (file_exists($download_file)) {

				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header("Content-Disposition: attachment; filename=\"".$this->baseName($download_file)."\"");
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . filesize($download_file));
				ob_clean();
				flush();

				// standard PHP function readfile() has documented problems with large files; readfile_chunked() is reported on php.net
				$this->readfile_chunked($download_file);
				//readfile($download_file);

				// log
				if($log_downloads)
				{
					$log_file = $logfile_prefix."downloads.txt";
					$log_text = JText::sprintf('download_log_text', $today, utf8_encode($this->baseName($download_file)), utf8_encode($relative_dir), $username, $remote_address);
					file_put_contents($log_file, $log_text, FILE_APPEND);
				}
				die(); 	// stop execution of further script because we are only outputting the pdf
					// (see readfile() function comment by mark dated 17-Sep-2008 on php.net)
			}
			else
			{
				$text  = "<div id='error'>"
					."<table cellspacing='0' cellpadding='0' border='0' width='100%'>"
					."<tr height='30' valign='middle'>"
					."<td width='60px'><img src=\"".$this->imgdir."warning.png\"></td><td>".JText::sprintf('file_not_found', utf8_encode($this->baseName($download_file)))."</td>"
					."</tr>"
					."</table>"
					."</div>";

				$article->text = $article->fulltext = $article->introtext = $text;
	
				$params->set('show_author', '0');
				$params->set('show_create_date', '0');
				$params->set('show_modify_date', '0');
				return;
			}
		}

		// asking for the actions log
		if($access_rights > 4 && isset($_GET['view_log']) &&
			($log_uploads || $log_downloads || $log_removedfolders || $log_removedfiles || $log_newfolders || $log_newfoldernames || $log_newfilenames))
		{
			$this->view_log($logfile_prefix, $article, $params, $description, $dir, $log_uploads, $log_downloads, $log_removedfolders, $log_removedfiles, $log_newfolders, $log_newfoldernames, $log_newfilenames);
			return;
		}

		// asking for help
		if(isset($_GET['help']))
		{
			$this->do_help($article, $params, $description, $dir);
			return;
		}

		// Reading the data of files and directories
		if($open_dir = @opendir(html_entity_decode(str_replace("\\", "/", $dir."/"))))
		{
			$dirs = array();
			$files = array();
			$i = 0;
			while ($it = @readdir($open_dir)) 
			{
				if($it != "." && $it != "..")
				{
					if(is_dir($dir.DS.$it))
					{
						if(!in_array($it, $hidden_folders))
							$dirs[] = htmlspecialchars($it);
					}
					else if(!in_array($it, $hidden_files) && !in_array("*.".$this->fileExtension($it), $hidden_extensions[0]))
					{
						$files[$i]["name"]	= htmlspecialchars($it);
						$it			= $dir."/".$it;
						$files[$i]["extension"]	= $this->fileExtension($it);
						$files[$i]["size"]	= $this->fileRealSize($it);
						$files[$i]["changed"]	= filemtime($it);
						$i++;
					}
				}
			}
			@closedir($open_dir);
		}
		else
		{
			$text  = "<div id='error'>"
				."<table cellspacing='0' cellpadding='0' border='0' width='100%'>"
				."<tr height='30' valign='middle'>"
				."<td width='60px'><img src=\"".$this->imgdir."warning.png\"></td><td>".JText::sprintf('dir_not_found', utf8_encode($current_position), $default_absolute_path)."</td>"
				."</tr>"
				."</table>"
				."</div>";

			$article->text = $article->fulltext = $article->introtext = $text;

			$params->set('show_author', '0');
			$params->set('show_create_date', '0');
			$params->set('show_modify_date', '0');
			return;
		}

		// Sort files and folders. By default, they are sorted by name
		if($files || $dirs)
		{
			if(isset($_GET["sort_by"]) && isset($_GET["sort_as"]) && $_GET["sort_by"] == "name" && $_GET["sort_as"] != "asc")
			{
				@sort($dirs);
				@usort($files, array($this, "name_cmp_desc"));
			}
			elseif(isset($_GET["sort_by"]) && isset($_GET["sort_as"]) && $_GET["sort_by"] == "name" && $_GET["sort_as"] == "asc")
			{
				@rsort($dirs);
				@usort($files, array($this, "name_cmp_asc"));
			}
			elseif(isset($_GET["sort_by"]) && isset($_GET["sort_as"]) && $_GET["sort_by"] == "size" && $_GET["sort_as"] != "asc" && $files)
			{
				@usort($files, array($this, "size_cmp_desc"));
			}
			elseif(isset($_GET["sort_by"]) && isset($_GET["sort_as"]) && $_GET["sort_by"] == "size" && $_GET["sort_as"] == "asc" && $files)
			{
				@usort($files, array($this, "size_cmp_asc"));
			}
			elseif(isset($_GET["sort_by"]) && isset($_GET["sort_as"]) && $_GET["sort_by"] == "changed" && $_GET["sort_as"] != "asc" && $files)
			{
				@usort($files, array($this, "changed_cmp_desc"));
			}
			elseif(isset($_GET["sort_by"]) && isset($_GET["sort_as"]) && $_GET["sort_by"] == "changed" && $_GET["sort_as"] == "asc" && $files)
			{
				@usort($files, array($this, "changed_cmp_asc"));
			}
			else
			{
				@sort($dirs);
				@usort($files, array($this, "name_cmp_desc"));
			}
		}

		// ***********************************************************************************************************************
		// Start of HTML
		// ***********************************************************************************************************************

		$text = "";
		if ($description) {
			$text = "<b>$description</b>";
		}

		// Print the error (if there is something to print)
		if ($error) {
			$text .= "<div id='error'>"
				."<table cellspacing='0' cellpadding='0' border='0' width='100%'>"
				."<tr height='30' valign='middle'>"
				."<td width='60px'><img src=\"".$this->imgdir."warning.png\"></td><td>".$error."</td>"
				."</tr>"
				."</table>"
				."</div>";
		}

		// refresh/toplevel area
		$show_help_link = $this->_params->def('show_help_link', "1");
		$logs_link = $access_rights > 4 && ($log_uploads || $log_downloads) ? "<a href='".$this->baselink."&dir=".urlencode($dir)."&view_log=1'>".JText::_('view_log')."</a>" : "";
		if ($show_help_link) 
		{
			$help_link = ($logs_link ? "&nbsp;|&nbsp;" : "")."<a href='".$this->baselink."&dir=".urlencode($dir)."&help=1'>".JText::_('help')."</a>";
		}
		else
		{
			$help_link = "";
		}

		// code to display toplevel and refresh links now obsolete with proper navigation introduced by ErikLtz (modified to link to current folder and default top repository if needed)
		$links_string = $logs_link.$help_link;
		/*
		if (strcmp (strtoupper (str_replace("\\", "/", $starting_dir)), strtoupper (str_replace ("\\", "/", $dir)))) {
			$links_string = "<a href='".$this->baselink."&dir=".urlencode($starting_dir)."'>".JText::_('toplevel')
					."</a>&nbsp;|&nbsp;<a href='".$this->baselink."&dir=".urlencode($dir)."'>".JText::_('refresh')."</a>".$logs_link.$help_link;
		}
		else
		{
			$links_string = "<a href='".$this->baselink."&dir=".urlencode($dir)."'>".JText::_('refresh')."</a>".$logs_link.$help_link;
		}
		*/

		$text .= "<div id='topinfo'>"
			."<table cellspacing='0' cellpadding='0' border='0' width='100%'>"
			."<tr valign='bottom'>"
		        // Inserted browsing info here instead of in bottom info /ErikLtz
			//."	<td width='10' valign='middle'><img src=\"".$this->imgdir."arrow_right.png\" /></td>"
			."	<td>".JText::_('browsing').": ".utf8_encode($current_position_links)."</td>"
			."	<td class='links'>".$links_string."</td>"
			."</tr>"
			."</table>"
			."</div>";

		// start frame area
		$text .= "<div id='frame'>";
		
		// start files/folders table
		$text .= "<table class='table' border='0' cellpadding='0' cellspacing='0'>"
			."<tr class='row header'>"
			."	<td class='icon'>";
		
		if($upper_dir)
		{
			$text .= "<a href='".$this->baselink."&dir=".urlencode($upper_dir)."'><img title=\"".JText::_('go_to_previous_folder')."\" src=\"".$this->imgdir."upperdir.png\" /></a>";
		}
		else
		{
			$text .= "<img src=\"".$this->imgdir."null.gif\" />";
		}
		
		$text .= "	</td>"
			."	<td class='filename'>"
				.$this->makeArrow((isset($_GET["sort_by"]) ? $_GET["sort_by"] : ""), (isset($_GET["sort_as"]) ? $_GET["sort_as"] : ""), "name", $dir, JText::_('file_name'))
			."	</td>"
			."	<td class='size'>"
				.$this->makeArrow((isset($_GET["sort_by"]) ? $_GET["sort_by"] : ""), (isset($_GET["sort_as"]) ? $_GET["sort_as"] : ""), "size", $dir, JText::_('size'))	
			."	</td>"
			."	<td class='changed'>"
				.$this->makeArrow((isset($_GET["sort_by"]) ? $_GET["sort_by"] : ""), (isset($_GET["sort_as"]) ? $_GET["sort_as"] : ""), "changed", $dir, JText::_('last_changed'))
			."	</td>";

		if($access_rights > 2)
		{
			$text .= "<td colspan='2' width='60'>&nbsp;</td>";
		}
		else
		{
			$text .= "<td width='60'>&nbsp;</td>";
		}
		$text .= "</tr>";

		// Ready to display folders and files.
		$row = 1;

		// Folders first
		if ($dirs)
		{
			foreach ($dirs as $a_dir)
			{
				$row_style = ($row ? "one" : "two");
				
				if ($this->line_height)
				{
					$text .= "<tr class='line'><td colspan=6><img src=\"".$this->imgdir."null.gif\" /></td></tr>";
				}

				// different line if editing name or not
				if (isset($_GET['old_foldername']) && strlen($_GET['old_foldername']) && !strcmp($_GET['old_foldername'], $a_dir))   // Removed urldecode on _GET /ErikLtz
				{
					$text .= "<form action='".$this->baselink."&dir=".urlencode($dir)."' method='post'>"
						."<tr class='row $row_style'>"
						."	<td class='icon'>"
						."	<img src=\"".$this->imgdir."folder.png\" />"
						."	</td>"
						."	<td>"
						."	<input class='text_input' name='new_foldername' type='text' value=\"".utf8_encode($a_dir)."\" />"
						."	</td>"
						."	<td class='size'><img src=\"".$this->imgdir."null.gif\" /></td>"
						."	<td class='changed'><img src=\"".$this->imgdir."null.gif\" /></td>"
						."	<td class='icon'>"
						."	<input type='image' src=\"".$this->imgdir."tick.png\" title=\"".JText::_('rename_folder_title')."\" />"
						."	</td>"
						."	<td class='icon'><a href='".$this->baselink."&dir=".urlencode($dir)."'>".JText::_('rename_folder_cancel')."</a></td>"
						."</tr>"
						."	<input type='hidden' name='old_foldername' value=\"".urlencode($a_dir)."\" />"
						."</form>";
				}
				else
				{
					$text .= "<tr class='row $row_style' onmouseover='this.className=\"highlighted\"' onmouseout='this.className=\"row $row_style\"'>"
						."	<td class='icon'>"
						."	<img src=\"".$this->imgdir."folder.png\" />"
						."	</td>"
						."	<td>"
						."	<a href='".$this->baselink."&dir=".urlencode($dir).DS.urlencode($a_dir)."'>".utf8_encode($a_dir)."</a>"
						."	</td>"
						."	<td class='size'><img src=\"".$this->imgdir."null.gif\" /></td>"
						."	<td class='changed'><img src=\"".$this->imgdir."null.gif\" /></td>";
					if($access_rights > 1)
					{
						$text .= "<td class='icon'>"
							."<a href='".$this->baselink."&dir=".urlencode($dir)."&old_foldername=".urlencode($a_dir)."'>"
							."<img src=\"".$this->imgdir."rename.png\" border='0' title=\"".JText::sprintf('folder_rename', utf8_encode($a_dir))."\" /></a>"
							."</td>";
					}
					else
					{
						$text .= "<td>&nbsp;</td>";
					}
					if($access_rights > 3)
					{
						// we need to utf-8 encode potential special characters to be passed to javascript, because Firefox does not handle this (it works in IE)
						$text .= "<td class='icon'>"
							."<a href=\"javascript:confirmDelfolder('".addslashes($this->baselink)."','".urlencode(addslashes(utf8_encode($dir)))."','".urlencode(addslashes(utf8_encode($a_dir)))."','".JText::sprintf('about_to_remove_folder', utf8_encode(addslashes($a_dir)))."')\">"
							."<img src=\"".$this->imgdir."delete.png\" border='0' title=\"".JText::sprintf('remove_folder', utf8_encode($a_dir))."\" /></a>"
							."</td>";
					}
					else
					{
						$text .= "<td>&nbsp;</td>";
					}
					$text .= "</tr>";
				}
				$row =! $row;
			}
		}

		// Now the files
		if($files)
		{
			foreach ($files as $a_file)
			{
				$row_style = ($row ? "one" : "two");

				if ($this->line_height)
				{
					$text .= "<tr class='line'><td colspan=6><img src=\"".$this->imgdir."null.gif\" /></td></tr>";
				}

				// different line if editing name or not
				if (isset($_GET['old_filename']) && strlen($_GET['old_filename']) && !strcmp($_GET['old_filename'], $a_file["name"]))   // Removed urldecode on _GET /ErikLtz
				{
					$text .= "<form action='".$this->baselink."&dir=".urlencode($dir)."' method='post'>"
						."<tr class='row $row_style'>"
						."	<td class='icon'>"
						."	<img src=\"".$this->fileIcon($a_file["extension"])."\" />"
						."	</td>"
						."	<td>"
						."	<input class='text_input' name='new_filename' type='text' value=\"".utf8_encode($a_file["name"])."\" />"
						."	</td>"
						."	<td class='size'>"
							.$this->fileSizeF($a_file["size"])
						."	</td>"
						."	<td class='changed'>"
							.$this->fileChanged($a_file["changed"])
						."	</td>"
						."	<td class='icon'>"
						."	<input type='image' src=\"".$this->imgdir."tick.png\" title=\"".JText::_('rename_file_title')."\" />"
						."	</td>"
						."	<td class='icon'><a href='".$this->baselink."&dir=".urlencode($dir)."'>".JText::_('rename_file_cancel')."</a></td>"
						."</tr>"
						."	<input type='hidden' name='old_filename' value=\"".urlencode($a_file["name"])."\" />"
						."</form>";
				}
				else
				{
					if($disable_level_1_downloads)
					{
						$file_link = utf8_encode($a_file["name"]);
					}
					else
					{
						$file_link = "<a href='".$this->baselink."&dir=".urlencode($dir)."&download_file=".urlencode($relative_dir.DS.$a_file["name"])."'>".utf8_encode($a_file["name"])."</a>";
					}

					$text .= "<tr class='row $row_style' onmouseover='this.className=\"highlighted\"' onmouseout='this.className=\"row $row_style\"'>"
						."	<td class='icon'>"
						."	<img src=\"".$this->fileIcon($a_file["extension"])."\" />"
						."	</td>"
						."	<td>"
						.$file_link
						."	</td>"
						."	<td class='size'>"
							.$this->fileSizeF($a_file["size"])
						."	</td>"
						."	<td class='changed'>"
							.$this->fileChanged($a_file["changed"])
						."	</td>";
					if($access_rights > 1)
					{
						$text .= "<td class='icon'>"
							."<a href='".$this->baselink."&dir=".urlencode($dir)."&old_filename=".urlencode($a_file["name"])."'>"
							."<img src=\"".$this->imgdir."rename.png\" border='0' title=\"".JText::sprintf('file_rename', utf8_encode($a_file["name"]))."\" /></a>"
							."</td>";
					}
					else
					{
						$text .= "<td>&nbsp;</td>";
					}
					if($access_rights > 2)
					{
						// we need to utf-8 encode potential special characters to be passed to javascript, because Firefox does not handle this (it works in IE)
						$text .= "<td class='icon'>"
							."<a href=\"javascript:confirmDelfile('".addslashes($this->baselink)."','".urlencode(addslashes(utf8_encode($dir)))."','".urlencode(addslashes(utf8_encode($a_file["name"])))."','".Jtext::sprintf('about_to_remove_file', utf8_encode(addslashes($a_file["name"])))."')\">"
							."<img src=\"".$this->imgdir."delete.png\" border='0' title=\"".JText::sprintf('remove_file', utf8_encode($a_file["name"]))."\" /></a>"
							."</td>";
					}
					else
					{
						$text .= "<td>&nbsp;</td>";
					}
					$text .= "</tr>";
				}
				$row =! $row;
			}
		}

		// Closing files/folders table and frame div
		$text .= "</table>"
			."</div>"
			."<img src=\"".$this->imgdir."null.gif\" height=10 />";

		// upload section
		if ($access_rights > 1)
		{
			$text .= "<div id='upload'>"
				."<form enctype='multipart/form-data' action='".$this->baselink."&dir=".urlencode($dir)."' method='post'>"
				."<table cellspacing='0' cellpadding='0' border='0' width='100%'>"
				."<tr height='20' valign='middle'>"
				."	<td>"
				."	<table cellspacing='0' cellpadding='0' border='0'>"
				."	<tr>"
				."		<td>"
						.JText::_('create_new_folder').":&nbsp;"
				."		</td>"
				."		<td>"
				."		<input class='text' name='userdir' type='text' />"
				."		</td>"
				."		<td class='image'>"
				."		<input type='image' src=\"".$this->imgdir."addfolder.png\" title=\"".JText::_('add_folder')."\" />"
				."		</td>"
				."	</tr>"
				."	</table>"
				."	</td>"
				."	<td align='right'>"
				."	<table cellspacing='0' cellpadding='0' border='0'>"
				."	<tr>"
				."		<td>"
						.JText::_('upload_file').":&nbsp;"
				."		</td>"
				."		<td>"
				."		<input class=\"file\" name=\"userfile\" type=\"file\" />"
				."		</td>"
				."		<td class='image'>"
				."		<input type='image' src=\"".$this->imgdir."addfile.png\" title=\"".JText::_('upload_file')."\" />"
				."		<input type='hidden' name='upload_dir' value=\"".urlencode($dir)."\">"
				."		</td>"
				."	</tr>"
				."	</table>"
				."	</td>"
				."</tr>"
				."</table>"
				."</form>"
				."</div>";
		}

		// current location and small icon with link to site and title containing copyright and version number
		$credits_icon = "<td width='12' align='right'><a href='http://www.jsmallsoftware.com' target='_blank'>"
				."<img src=\"".$this->imgdir."jsmallsoftware.png\" border='0' title=\"".JText::sprintf('short_credits', $version_number)."\" /></a>"
				."</td>";

		// Bottom line
		$text .= "<div id='bottominfo'>"
			."<table cellspacing='0' cellpadding='0' border='0' width='100%'>"
			."<tr>"
		//	."	<td width='10'><img src=\"".$this->imgdir."arrow_right.png\" /></td>"	// moved browsing to the top [ErikLtz]
		//	."	<td>".JText::_('browsing').": ".utf8_encode($current_position)."</td>"	// moved browsing to the top [ErikLtz]
			."	<td>&nbsp;</td>"
			.$credits_icon
			."</tr>"
			."</table>"
			."</div>";

		// ***********************************************************************************************************************
		// End of HTML - Now set article data
		// ***********************************************************************************************************************

		$article->text = $article->fulltext = $article->introtext = $text_array[0].$text.$text_array[1];

		$params->set('show_author', '0');
		$params->set('show_create_date', '0');
		$params->set('show_modify_date', '0');

	} // end of onPrepareContent method

	// ***********************************************************************************************************************
	// Other functions
	// ***********************************************************************************************************************

	function view_log($logfile_prefix, &$article, &$params, $description, $dir, $log_uploads, $log_downloads, $log_removedfolders, $log_removedfiles, $log_newfolders, $log_newfoldernames, $log_newfilenames)
	{
		$color = $this->_params->def('log_highlighted_color', "FF6600");

		$text = "";
		
		if ($description) {
			$text = "<b>$description</b>";
		}

		// title
		$text .= "<br /><br /><br /><div id='info'>"
			."<table cellspacing='0' cellpadding='0' border='0' width='100%'>"
			."<tr height='30' valign='top'>"
			."<td colspan='2'><b>".JText::_('log_title')."</b></td>"
			."</tr>";

		// uploads
		if ($log_uploads) {

			$logfile = $logfile_prefix."uploads.txt";
			$logtext = @file_get_contents($logfile);

			if (!$logtext)
			{
				$logtext = JText::_('no_log_found');
				$icon = $this->imgdir."log_not_found.png";
			}
			else {
				$logtext = preg_replace("/\\n/", "<hr />", $logtext);
				$logtext = preg_replace("/\[/", "<font color='$color'>", $logtext);
				$logtext = preg_replace("/\]/", "</font>", $logtext);
				$icon = $this->imgdir."log_found.png";
			}
		}
		else {
			$logtext = JText::_('not_logging');
			$icon = $this->imgdir."log_disabled.png";
		}
		$text .= "<tr height='30' valign='middle'>"
			."<td colspan='2'><b>".JText::_('log_uploads_title')."</b></td>"
			."</tr>"
			."<tr valign='top'>"
			."<td class='icon'><img src=\"$icon\"></td><td>$logtext</td>"
			."</tr>"
			."<tr height='30' valign='middle'>"
			."<td colspan='2'><img src=\"".$this->imgdir."null.gif\"></td>"
			."</tr>";

		// downloads
		if ($log_downloads) {

			$logfile = $logfile_prefix."downloads.txt";
			$logtext = @file_get_contents($logfile);

			if (!$logtext)
			{
				$logtext = JText::_('no_log_found');
				$icon = $this->imgdir."log_not_found.png";
			}
			else {
				$logtext = preg_replace("/\\n/", "<hr />", $logtext);
				$logtext = preg_replace("/\[/", "<font color='$color'>", $logtext);
				$logtext = preg_replace("/\]/", "</font>", $logtext);
				$icon = $this->imgdir."log_found.png";
			}
		}
		else {
			$logtext = JText::_('not_logging');
			$icon = $this->imgdir."log_disabled.png";
		}
		$text .= "<tr height='30' valign='middle'>"
			."<td colspan='2'><b>".JText::_('log_downloads_title')."</b></td>"
			."</tr>"
			."<tr valign='top'>"
			."<td class='icon'><img src=\"$icon\"></td><td>$logtext</td>"
			."</tr>"
			."<tr height='30' valign='middle'>"
			."<td colspan='2'><img src=\"".$this->imgdir."null.gif\"></td>"
			."</tr>";

		// removed folders
		if ($log_removedfolders) {

			$logfile = $logfile_prefix."removedfolders.txt";
			$logtext = @file_get_contents($logfile);

			if (!$logtext)
			{
				$logtext = JText::_('no_log_found');
				$icon = $this->imgdir."log_not_found.png";
			}
			else {
				$logtext = preg_replace("/\\n/", "<hr />", $logtext);
				$logtext = preg_replace("/\[/", "<font color='$color'>", $logtext);
				$logtext = preg_replace("/\]/", "</font>", $logtext);
				$icon = $this->imgdir."log_found.png";
			}
		}
		else {
			$logtext = JText::_('not_logging');
			$icon = $this->imgdir."log_disabled.png";
		}
		$text .= "<tr height='30' valign='middle'>"
			."<td colspan='2'><b>".JText::_('log_removedfolders_title')."</b></td>"
			."</tr>"
			."<tr valign='top'>"
			."<td class='icon'><img src=\"$icon\"></td><td>$logtext</td>"
			."</tr>"
			."<tr height='30' valign='middle'>"
			."<td colspan='2'><img src=\"".$this->imgdir."null.gif\"></td>"
			."</tr>";

		// removed files
		if ($log_removedfiles) {

			$logfile = $logfile_prefix."removedfiles.txt";
			$logtext = @file_get_contents($logfile);

			if (!$logtext)
			{
				$logtext = JText::_('no_log_found');
				$icon = $this->imgdir."log_not_found.png";
			}
			else {
				$logtext = preg_replace("/\\n/", "<hr />", $logtext);
				$logtext = preg_replace("/\[/", "<font color='$color'>", $logtext);
				$logtext = preg_replace("/\]/", "</font>", $logtext);
				$icon = $this->imgdir."log_found.png";
			}
		}
		else {
			$logtext = JText::_('not_logging');
			$icon = $this->imgdir."log_disabled.png";
		}
		$text .= "<tr height='30' valign='middle'>"
			."<td colspan='2'><b>".JText::_('log_removedfiles_title')."</b></td>"
			."</tr>"
			."<tr valign='top'>"
			."<td class='icon'><img src=\"$icon\"></td><td>$logtext</td>"
			."</tr>"
			."<tr height='30' valign='middle'>"
			."<td colspan='2'><img src=\"".$this->imgdir."null.gif\"></td>"
			."</tr>";

		// new folders
		if ($log_newfolders) {

			$logfile = $logfile_prefix."newfolders.txt";
			$logtext = @file_get_contents($logfile);

			if (!$logtext)
			{
				$logtext = JText::_('no_log_found');
				$icon = $this->imgdir."log_not_found.png";
			}
			else {
				$logtext = preg_replace("/\\n/", "<hr />", $logtext);
				$logtext = preg_replace("/\[/", "<font color='$color'>", $logtext);
				$logtext = preg_replace("/\]/", "</font>", $logtext);
				$icon = $this->imgdir."log_found.png";
			}
		}
		else {
			$logtext = JText::_('not_logging');
			$icon = $this->imgdir."log_disabled.png";
		}
		$text .= "<tr height='30' valign='middle'>"
			."<td colspan='2'><b>".JText::_('log_newfolders_title')."</b></td>"
			."</tr>"
			."<tr valign='top'>"
			."<td class='icon'><img src=\"$icon\"></td><td>$logtext</td>"
			."</tr>"
			."<tr height='30' valign='middle'>"
			."<td colspan='2'><img src=\"".$this->imgdir."null.gif\"></td>"
			."</tr>";

		// renamed folders
		if ($log_newfoldernames) {

			$logfile = $logfile_prefix."newfoldernames.txt";
			$logtext = @file_get_contents($logfile);

			if (!$logtext)
			{
				$logtext = JText::_('no_log_found');
				$icon = $this->imgdir."log_not_found.png";
			}
			else {
				$logtext = preg_replace("/\\n/", "<hr />", $logtext);
				$logtext = preg_replace("/\[/", "<font color='$color'>", $logtext);
				$logtext = preg_replace("/\]/", "</font>", $logtext);
				$icon = $this->imgdir."log_found.png";
			}
		}
		else {
			$logtext = JText::_('not_logging');
			$icon = $this->imgdir."log_disabled.png";
		}
		$text .= "<tr height='30' valign='middle'>"
			."<td colspan='2'><b>".JText::_('log_newfoldernames_title')."</b></td>"
			."</tr>"
			."<tr valign='top'>"
			."<td class='icon'><img src=\"$icon\"></td><td>$logtext</td>"
			."</tr>"
			."<tr height='30' valign='middle'>"
			."<td colspan='2'><img src=\"".$this->imgdir."null.gif\"></td>"
			."</tr>";

		// renamed files
		if ($log_newfilenames) {

			$logfile = $logfile_prefix."newfilenames.txt";
			$logtext = @file_get_contents($logfile);

			if (!$logtext)
			{
				$logtext = JText::_('no_log_found');
				$icon = $this->imgdir."log_not_found.png";
			}
			else {
				$logtext = preg_replace("/\\n/", "<hr />", $logtext);
				$logtext = preg_replace("/\[/", "<font color='$color'>", $logtext);
				$logtext = preg_replace("/\]/", "</font>", $logtext);
				$icon = $this->imgdir."log_found.png";
			}
		}
		else {
			$logtext = JText::_('not_logging');
			$icon = $this->imgdir."log_disabled.png";
		}
		$text .= "<tr height='30' valign='middle'>"
			."<td colspan='2'><b>".JText::_('log_newfilenames_title')."</b></td>"
			."</tr>"
			."<tr valign='top'>"
			."<td class='icon'><img src=\"$icon\"></td><td>$logtext</td>"
			."</tr>";

		// final link
		$text .= "<tr height='60' valign='middle'>"
			."<td class='icon'><img src=\"".$this->imgdir."null.gif\"></td><td><a href='".$this->baselink."&dir=".urlencode($dir)."'>".JText::_('go_back')."</a></td>"
			."</tr>"
			."</table>"
			."</div>";

		$article->text = $article->fulltext = $article->introtext = $text;

		$params->set('show_author', '0');
		$params->set('show_create_date', '0');
		$params->set('show_modify_date', '0');
		return;
	}

	function do_help(&$article, &$params, $description, $dir)
	{
		$text = "";

		if ($description) {
			$text = "<b>$description</b>";
		}

		$helptitle = JText::_('help');
		$helptext  = JText::_('jsmallfibplugindesc');
		$helptext  = preg_replace("/\.\.\/plugins/", "plugins", $helptext);

		$text .= "<br /><br /><br /><div id='info'>"
			."<table cellspacing='0' cellpadding='0' border='0' width='100%'>"
			."<tr height='30' valign='top'>"
			."<td colspan='2'><b>$helptitle</b></td>"
			."</tr>"
			."<tr height='30' valign='middle'>"
			."<td width='60px'><img src=\"".$this->imgdir."null.gif\"></td><td>$helptext</td>"
			."</tr>"
			."<tr height='30' valign='middle'>"
			."<td width='60px'><img src=\"".$this->imgdir."null.gif\"></td><td><a href='".$this->baselink."&dir=".urlencode($dir)."'>".JText::_('go_back')."</a></td>"
			."</tr>"
			."</table>"
			."</div>";

		$article->text = $article->fulltext = $article->introtext = $text;

		$params->set('show_author', '0');
		$params->set('show_create_date', '0');
		$params->set('show_modify_date', '0');
		return;
	}

	// ***********************************************************************************************************************
	// Javascript and Cascading Style Sheets used locally, and other functions
	// ***********************************************************************************************************************

	function do_js()
	{
?>
		<script language='javascript'>

		function addslashes(str) {
			str=str.replace(/\'/g,'\\\'');
			str=str.replace(/\"/g,'\\"');
			str=str.replace(/\\/g,'\\\\');
			str=str.replace(/\0/g,'\\0');
			return str;
		}
		
		function stripslashes(str) {
			str=str.replace(/\\'/g,'\'');
			str=str.replace(/\\"/g,'"');
			str=str.replace(/\\\\/g,'\\');
			str=str.replace(/\\0/g,'\0');
			return str;
		}
		
		function confirmDelfolder(baselink, dir, delfolder, msgString) {

			var browser=navigator.appName;
			var b_version=navigator.appVersion;
			var version=parseFloat(b_version);

			if (confirm(msgString)) {

				if (browser=="Netscape")
				{
					window.location=baselink+'&dir='+escape(encodeURI(dir))+'&delfolder='+escape(encodeURI(delfolder)); /* Firefox */
				}
				else if (browser=="Microsoft Internet Explorer")
				{
					window.location=baselink+'&dir='+escape(dir)+'&delfolder='+escape(delfolder); /* IE */
				}
				else
				{
					window.location=baselink+'&dir='+escape(dir)+'&delfolder='+escape(delfolder); /* treat others like IE */
				}
				return;
			}
		}
		
		function confirmDelfile(baselink, dir, delfile, msgString) {

			var browser=navigator.appName;
			var b_version=navigator.appVersion;
			var version=parseFloat(b_version);

			if (confirm(msgString)) {

				if (browser=="Netscape")
				{
					window.location=baselink+'&dir='+escape(encodeURI(dir))+'&delfile='+escape(encodeURI(delfile)); /* Firefox */
				}
				else if (browser=="Microsoft Internet Explorer")
				{
					window.location=baselink+'&dir='+escape(dir)+'&delfile='+escape(delfile); /* IE */
				}
				else
				{
					window.location=baselink+'&dir='+escape(dir)+'&delfile='+escape(delfile); /* treat others like IE */
				}
				return;
			}
		}
		
		</script>
<?php
	}

	function do_css()
	{
?>

	<style type="text/css">

	#frame {
<?php echo "width:".$this->table_width."px;"; ?>
<?php echo "background-color:#".$this->framebox_bgcolor.";"; ?>
		text-align:left;
		position: relative;
		margin: 0 auto;
		padding:5px;
<?php echo "border: ".$this->framebox_border."px; border-style: ".$this->framebox_linetype."; border-color: #".$this->framebox_linecolor.";"; ?>
	}

	#error {
<?php echo "width:".$this->table_width."px;"; ?>
<?php echo "background-color:#".$this->errorbox_bgcolor.";"; ?>
		font-family:Verdana;
		font-size:11px;
		padding:5px;
		position: relative;
		margin: 10px auto;
		text-align:center;
<?php echo "border: ".$this->errorbox_border."px; border-style: ".$this->errorbox_linetype."; border-color: #".$this->errorbox_linecolor.";"; ?>
	}

	table.table {
<?php echo "width:".($this->table_width - 6)."px;"; ?>
		font-family: Verdana; 
		font-size: 11px;
		margin:3px;
	}

	table.table tr.line {
<?php echo "background-color:#".$this->line_bgcolor.";"; ?>
<?php echo "height:".$this->line_height."px;"; ?>
	}

	table.table tr.highlighted {
<?php echo "background-color:#".($this->highlighted_color).";"; ?>
<?php echo "height:".($this->row_height)."px;"; ?>
	}

	table.table tr.row.header {
<?php echo "background-color:#".($this->header_bgcolor).";"; ?>
<?php echo "height:".($this->row_height)."px;"; ?>
	}

	table.table tr.row.one {
<?php echo "background-color:#".($this->oddrows_color).";"; ?>
<?php echo "height:".($this->row_height)."px;"; ?>
	}

	table.table tr.row.two {
<?php echo "background-color:#".($this->evenrows_color).";"; ?>
<?php echo "height:".($this->row_height)."px;"; ?>
	}

	table.table td.icon {
		text-align:center;
		width:30px;
	}

	table.table tr.row.header td.size {
		width: 100px; 
		text-align:right;
	}

	table.table tr.highlighted td.size {
		width: 100px; 
		text-align:right;
	}

	table.table tr.row.one td.size {
		width: 100px; 
		text-align:right;
	}

	table.table tr.row.two td.size {
		width: 100px; 
		text-align:right;
	}

	table.table tr.row.header td.changed {
		width: 130px; 
		text-align:center;
	}

	table.table tr.highlighted td.changed {
		width: 130px;
		text-align:center;
	}

	table.table tr.row.one td.changed {
		width: 130px;
		text-align:center;
	}
	
	table.table tr.row.two td.changed {
		width: 130px;
		text-align:center;
	}
	
	#upload {
<?php echo "width:".$this->table_width."px;"; ?>
		padding:5px;
<?php echo "background-color:#".$this->uploadbox_bgcolor.";"; ?>
		font-family:Verdana;
		font-size:11px;
		position: relative;
		margin: 0 auto;
		text-align:left;
<?php echo "border: ".$this->uploadbox_border."px; border-style: ".$this->uploadbox_linetype."; border-color: #".$this->uploadbox_linecolor.";"; ?>
	}

	#upload input.text {
<?php echo "background-color:#".$this->inputbox_bgcolor.";"; ?>
		font-family:Verdana;
		font-size:10px;
<?php echo "border: ".$this->inputbox_border."px; border-style: ".$this->inputbox_linetype."; border-color: #".$this->inputbox_linecolor.";"; ?>
	}

	#upload input.file {
<?php echo "background-color:#".$this->inputbox_bgcolor.";"; ?>
		font-family:Verdana;
		font-size:10px;
<?php echo "border: ".$this->inputbox_border."px; border-style: ".$this->inputbox_linetype."; border-color: #".$this->inputbox_linecolor.";"; ?>
	}

	#upload td.image {
		width:35px;
		text-align:center;
	}

	#topinfo {
<?php echo "width:".$this->table_width."px;"; ?>
		margin:3px;
		font-family:Verdana;
		font-size:11px;
		color:#000000;
		padding:5px;
		margin: 0px auto;
	}

	#topinfo td.links {
		text-align:right;
	}

	#frame input.text_input {
<?php echo "width:".($this->table_width - 380)."px;"; ?>
<?php echo "background-color:#".$this->inputbox_bgcolor.";"; ?>
		font-family:Verdana;
		font-size:11px;
<?php echo "border: ".$this->inputbox_border."px; border-style: ".$this->inputbox_linetype."; border-color: #".$this->inputbox_linecolor.";"; ?>
	}

	#bottominfo {
<?php echo "width:".$this->table_width."px;"; ?>
		margin:3px;
		font-family:Verdana;
		font-size:9px;
		color:#888888;
		padding:5px;
		margin: 0px auto;
	}

	</style>
<?php
	}

	//
	// Format the file size
	//
	function fileSizeF($size) 
	{
		$sizes = Array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
		$y = $sizes[0];
		for ($i = 1; (($i < count($sizes)) && ($size >= 1024)); $i++) 
		{
			$size = $size / 1024;
			$y  = $sizes[$i];
		}

		// Erik: Adjusted number format
		$dec = max(0, (3 - strlen(round($size))));
		return number_format($size, $dec, ",", " ")." ".$y;
		// Old code:
		//return round($size, 2)." ".$y;
	}

	function fileRealSize($file)
	{
		$sizeInBytes = filesize($file);
		//
		// If filesize() fails (with larger files), try to get the size from unix command line.
		if (!$sizeInBytes) {
			$sizeInBytes=exec("ls -l '$file' | awk '{print $5}'");
		}
		else
			return $sizeInBytes;
	}

	//
	// Return file extension (the string after the last dot.
	//
	function fileExtension($file)
	{
		$a = explode(".", $file);
		$b = count($a);
		return $a[$b-1];
	}

	//
	// Formatting the changing time
	//
	function fileChanged($time)
	{
		if ($this->display_seconds)
		{
			$timeformat = "H:i:s";
		}
		else {
			$timeformat = "H:i";
		}

		switch ($this->date_format)
		{
		case 'dd_mm_yyyy_dashsep':
			return date("d-m-Y ".$timeformat, $time);
		case 'dd_mm_yyyy_pointsep':
			return date("d.m.Y ".$timeformat, $time);
		case 'dd_mm_yyyy_slashsep':
			return date("d/m/Y ".$timeformat, $time);
		case 'yyyy_mm_dd_dashsep':
			return date("Y-m-d ".$timeformat, $time);
		case 'yyyy_mm_dd_pointsep':
			return date("Y.m.d ".$timeformat, $time);
		case 'yyyy_mm_dd_slashsep':
			return date("Y/m/d ".$timeformat, $time);
		case 'mm_dd_yyyy_dashsep':
			return date("m-d-Y ".$timeformat, $time);
		case 'mm_dd_yyyy_pointsep':
			return date("m.d.Y ".$timeformat, $time);
		case 'mm_dd_yyyy_slashsep':
			return date("m/d/Y ".$timeformat, $time);
		}
	}
	
	//
	// Find the icon for the extension
	//
	function fileIcon($l)
	{
		$l = strtolower($l);
	
		if (file_exists($this->imgdir.$l.".png"))
		{
			return $this->imgdir."$l.png";
		} else {
			return $this->imgdir."unknown.png";
		}
	}

	//
	// Generates the sorting arrows
	//
	function makeArrow($sort_by, $sort_as, $type, $dir, $text)
	{
		if($sort_by == $type && $sort_as == "desc")
		{
			return "<a href=\"".$this->baselink."&dir=".urlencode($dir)."&amp;sort_by=".$type."&amp;sort_as=asc\"> $text <img style=\"border:0;\" title=\"asc\" src=\"".$this->imgdir."arrow_up.png\" /></a>";
		}
		else
			return "<a href=\"".$this->baselink."&dir=".urlencode($dir)."&amp;sort_by=".$type."&amp;sort_as=desc\"> $text <img style=\"border:0;\" title=\"desc\" src=\"".$this->imgdir."arrow_down.png\" /></a>";
	}

	//
	// Functions that help sort the files
	//
	function name_cmp_desc($a, $b)
	{
	   return strcasecmp($a["name"], $b["name"]);
	}

	function size_cmp_desc($a, $b)
	{
		return ($a["size"] - $b["size"]);
	}

	function size_cmp_asc($b, $a)
	{
		return ($a["size"] - $b["size"]);
	}

	function changed_cmp_desc($a, $b)
	{
		return ($a["changed"] - $b["changed"]);
	}

	function changed_cmp_asc($b, $a)
	{
		return ($a["changed"] - $b["changed"]);
	}

	function name_cmp_asc($b, $a)
	{
		return strcasecmp($a["name"], $b["name"]);
	}

	//
	// Find the directory one level up
	//
	function upperDir($dir)
	{
		// Simpler implementation of upperDir method /ErikLtz
		$arr = explode(DS, $dir);
		unset($arr[count($arr) - 1]);
		return implode(DS, $arr);
		
		/*
		$chops = explode(DS, $dir);
		$num = count($chops);
		$chops2 = array();
		for($i = 0; $i < $num - 1; $i++)
		{
			$chops2[$i] = $chops[$i];
		}
		$dir2 = implode(DS, $chops2);
		return $dir2;
		*/
	}

	// Return last part in directory chain (built in basename depends on locale and having an utf8 locale may
        // return wrong characters when they really are iso8859-1)
	// [ErikLtz]
	
	function baseName($dir)
	{
		$arr = explode(DS, $dir);
		return $arr[count($arr) - 1];
	}

	// this function is reported in readfile() php.net page to bypass readfile() documented problems with large files
	function readfile_chunked($filename,$retbytes=true) { 
	
		$chunksize = 1 * (1024 * 1024); // how many bytes per chunk 
		$buffer = ''; 
		$counter = 0; 
     
		$handle = fopen($filename, 'rb'); 
		if ($handle === FALSE)
		{ 
			return FALSE; 
		} 
	
		while (!feof($handle))
		{ 
			$buffer = fread($handle, $chunksize); 
			echo $buffer; 
			ob_flush(); 
			flush(); 

			if ($retbytes)
			{ 
				$counter += strlen($buffer); 
			} 
		}

		$status = fclose($handle); 
	
		if ($retbytes && $status)
		{
			return $counter; // return num. bytes delivered like readfile() does. 
		}

		return $status; 
	} 

} // end of plugin class extension
?>
