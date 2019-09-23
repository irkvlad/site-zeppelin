<?php
/**
* JoomGallery Latest categories
* Copyright (C) 2009 Erftralle
* file: mod_jglatestcat/tmpl/default.php
* version: 1.5.1
* contact:
* license http://www.gnu.org/copyleft/gpl.html GNU/GPL or have a look at mod_jglatestcat/LICENSE.TXT
*
* This program is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the Free Software
* Foundation; either version 2 of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along with this program;
* if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
*/

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

if(!$jg_installed)
{
  // JoomGallery is not installed, show a message to the frontend
  echo "<p>" . JText::_('JG_LATESTCAT_JOOMGALLERY_NOT_INSTALLED_LBL') . "</p>";
}
else
{  
  // get JoomGallery styles
  $jgcat_obj->getPageHeader();

  // evaluate access rights 
  $user = &JFactory::getUser();
  $aid = 99;
  if($jgcat_obj->getConfig('access') == 1 )
  {
    $aid = $user->aid;
  }
  
  // CSS classes for row and clear
  $csstag = $jgcat_obj->getConfig('csstag');
  $rowclass = $csstag."row";
  $clearclass = $csstag."clr";  

  // thumbnail dimensions
  $dim = "";
  switch($jgcat_obj->getConfig('thumbnaildim'))
  {
    case 1:
        $dim = "height=" . "\"" . $jgcat_obj->getConfig('thumbnaildimvalue') . "\"";
      break;
    case 2:
        $dim =  "width=" . "\"" . $jgcat_obj->getConfig('thumbnaildimvalue') . "\"" ;
      break;
    default:
      break;
  }

  $count_img_per_row = 0;
  $count_img = 0;

  // start output
  if ( $jgcat_rows == null || $dberror != "" ) {
      echo "<p>" . $dberror . "</p>"; 
  }
  else {
  ?>
  <!-- open jglatestcat main div -->
  <div class="<?php echo $jgcat_obj->getConfig('csstag'); ?>main">
    <!-- open first jglatestcat row div -->
    <div class="<?php echo $rowclass; ?>">
  <?php 
    foreach($jgcat_rows as $row)
    {
      if($count_img_per_row >= $jgcat_obj->getConfig('rows'))
      {
  ?>
    <!-- close jglatestcat row div -->
    </div>
    <div class="<?php echo $clearclass; ?>"></div>
  <?php
        if($jgcat_obj->getConfig('showhorruler'))
        {
  ?>
    <hr/>
  <?php
        }
        $count_img_per_row = 0;
  ?>
    <!-- open jglatestcat row div -->
    <div class="<?php echo $rowclass; ?>">
  <?php
      }
      if($jgcat_obj->getConfig('showthumb') == 1 && $row->imgid != 0)
      {
        // show thumbnail configured in JoomGallery category manager
        $jgimg_row = $jgcat_obj->getPicture( $row->imgid, $aid );
      }
      else
      {
        $jgcatimg_rows = $jgcat_obj->getPicsByCategory( $row->cid, $aid, "rand()", 1, 0 );
        $jgimg_row = &$jgcatimg_rows[ 0 ];
      }          
  ?>
   	  <!-- open jglatestcat image container div -->
      <div class="<?php echo $csstag;?>imgct">
  <?php

      $textelement = '';
      if($jgcat_obj->getConfig('showtext') == 1 )
      {
        $text = $jgcat_obj->displayDesc($jgimg_row);
        if($jgcat_obj->getConfig('showshorttext') == 1)
        {
          // remove the descriptive part of the strings
          $search = JText::_('JGS_CATEGORY') . ': ';
          $text = str_replace( $search, "", $text);
          $search = JText::_('JGS_DESCRIPTION') . ': ';
          $text = str_replace( $search, "", $text);
        }

        $textelement = "<div class=\"".$csstag."txt\">\n"
                          .$text."\n"
                          ."</div>\n";
      }
      if($jgcat_obj->getConfig('showthumb') == 2 )
      {
        // do not show a thumbnail
        echo $textelement;
      }
      else
      {
        $imgelement = $jgcat_obj->displayThumb( $jgimg_row, $jgcat_obj->getConfig('showimglink'), null, $csstag.'img', $dim );
        
        // remove some JoomGallery's CSS styles
        $imgelement = str_replace( " class=\"jg_catelem_photo\"", "", $imgelement );        
        $imgelement = str_replace( " class=\"jg_photo\"", "", $imgelement );

        switch ($jgcat_obj->getConfig('imgposition')) {
          case 1:
          case 2:
          case 3:
            //image above (1) or left (2) or right(3) to text
            echo $imgelement;
            echo $textelement;
            break;
          case 4:
            //image below text
            echo $textelement;
            echo $imgelement;
            break;
        }
      }
  ?>
      <!-- close jglatestcat image container div -->
      </div>
  <?php
      $count_img_per_row++;
    }
  ?>
    <!-- close last jglatestcat row div -->
    </div>
    <div class="<?php echo $clearclass; ?>"></div>
  <!-- close jglatestcat main div -->
  </div>
  <?php
  }
}
?>
