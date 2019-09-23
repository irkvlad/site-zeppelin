<?php
/************************************************************************************
 mod_allnews (up to v2.0) for Joomla v1.5.0 by Thierry S,                          
 mod_allnews (from 2.0 up to now) for Joomla v1.5 by Olinad       				    
                                                                                 
 @author: Thierry S.                                                              
 @author: Olinad - dan@cdh.it                                                    	

 ----- This file is part of the AllNews Module. -----

    AllNews Module is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    AllNews is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this module.  If not, see <http://www.gnu.org/licenses/>.
************************************************************************************/
// no direct access
defined('_JEXEC') or die('Restricted access');
require_once (dirname(__FILE__).DS.'helper.php');
$item_id = mod_allnews_ItemHelper::getContent($params);
require(JModuleHelper::getLayoutPath('mod_allnews'));
?>