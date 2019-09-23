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

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

switch($task)
{

    case 'add':
        TOOLBAR_attachments::_EDIT(false);
        break;

    case 'edit':
        TOOLBAR_attachments::_EDIT(true);
        break;

    default:
        TOOLBAR_attachments::_DEFAULT();
        break;
}
?>