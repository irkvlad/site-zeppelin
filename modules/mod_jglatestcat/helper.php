<?php
/**
* JoomGallery Latest categories
* Copyright (C) 2009 Erftralle
* file: mod_jglatestcat/helper.php
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
defined('_JEXEC') or die('Restricted access');

class modJgLatestCatHelper extends joominterface {

  /**
   * entry function 
   *
   * @param   object  $params - backend parameters
   * @param   string  $dberror - database error
   * @param   int     $module_id - the id of the module
   */
  function fillObject(&$params, &$dberror, $module_id){
    // read the parameters
    $this->getParams($params, $module_id);

    // create and include the dynamic css for default view
    // according to backend settings
    $this->renderCSS();

    // get the newest categories
    return($this->getJgCats($dberror));
  }

  /**
   * get the newest categories from JoomGallery
   *
   * @param   string  $dberror - database error
   * @return  object  $jgcat_rows - category objects 
   */
  function getJgCats(&$dberror){

    $dberror = ''; 
    $db      = &JFactory::getDBO();
    $user    = &JFactory::getUser(); 

    $query = "SELECT ca.cid, ca.name, ca.description, ca.catpath, ca.catimage, @catimgid:=0 AS imgid"
          . " FROM #__joomgallery_catg AS ca"
          . " INNER JOIN #__joomgallery AS jg"
          . " ON ca.cid = jg.catid"
          . " WHERE ca.published = 1"
          . " AND ( isnull( jg.published ) OR jg.published = 1 )"
          . " AND ( isnull( jg.approved ) OR jg.approved = 1 )";

    if($this->getConfig('access') == 1)
    {
      $query .= " AND ca.access <= " . $user->aid;
    }
    $query .= " GROUP BY ca.cid DESC";
    $query = $query . " LIMIT 0," . $this->getConfig('categorycount');

    $db->setQuery($query);
    $jgcat_rows = $db->loadObjectList();

    $dberror="";
    if($jgcat_rows == null)
    {
      if($db->getErrorNum())
      {
        $dberror = JText::_('JG_LATESTCAT_DB_ERROR_LBL') . ": " . $db->getErrorMsg();
      }
      else
      {
        $dberror = JText::_('JG_LATESTCAT_NO_CATEGORIES_FOUND_LBL');
      }
    }

    if($dberror == "")
    {
      if($this->getConfig('showthumb') == 1)
      {
        // we need the image id's of the category images to show manual selected category thumbnails
        foreach($jgcat_rows AS $row)
        {
          // debug output
          // $arr = get_object_vars( $row );
          // while ( list( $prop, $val ) = each( $arr ) )
          //   echo "\t$prop = $val\n";

          $query = "SELECT id, imgfilename"
                    . " FROM #__joomgallery"
                    . " WHERE TRIM(imgfilename)=" . "'" . trim($row->catimage) . "'"
                    . " LIMIT 0,1";

          $db->setQuery($query);
          $jgimg_rows = $db->loadObjectList();

          if($jgimg_rows != null)
          {
            $img_row = &$jgimg_rows[0];
            $row->imgid = $img_row->id;
          }
          else
          {
            if($db->getErrorNum())
            {
              $dberror = JText::_('JG_LATESTCAT_DB_ERROR_LBL') . ": " . $db->getErrorMsg();
              break;
            }
          }
        }
      } 
    }
    return $jgcat_rows;
  }

  /**
   * get the params setted in module backend
   *
   * @param object $params - backend parameters
   * @param int    $module_id - module id 
   */
  function getParams(&$params, $module_id){
 
    //get the parameters and add them to the config
    $this->addConfig('shownumcomments', 0);
    $this->addConfig('showlastcomment', 0);
    $this->addConfig('showtitle', 0);
    $this->addConfig('showpicasnew', 0);
    $this->addConfig('showauthor', 0);
    $this->addConfig('showcategory', intval($params->get('cfg_showimgcat', 1)));
    $this->addConfig('showcatlink', intval($params->get('cfg_showimgcatlink', 1)));
    $this->addConfig('showhits', 0);
    $this->addConfig('showrate', 0);
    $this->addConfig('showdescription', intval($params->get('cfg_showthumb', 2)) == 2 ? 0 : $params->get('cfg_showimgdescr', 0));
    $this->addConfig('categoryfilter', '');
    $this->addConfig('hidebackend', '');
    $this->addConfig('piclink', intval($params->get('cfg_showimglink', 1)) == 2 ? 1 : 0);
    $this->addConfig('access', intval($params->get('cfg_access', 1)));
    $catcount = intval($params->get('cfg_count', 4) );
    $this->addConfig('categorycount', ($catcount <= 0 || $catcount >= 20) ? 4 : $catcount);
    $this->addConfig('showthumb', intval($params->get('cfg_showthumb', 2)));
    $this->addConfig('showimglink', intval( $params->get('cfg_showimglink', 1 ) ) > 0 ? true : false );
    $this->addConfig('thumbnaildim', intval($params->get('cfg_thumbnaildim', 0)));
    $this->addConfig('thumbnaildimvalue', intval( $params->get('cfg_thumbnaildimvalue', 150 )));
    $this->addConfig('rows', intval( $params->get('cfg_rows', 1 )));
    $this->addConfig('imgposition', intval( $params->get('cfg_imgposition', 1 )));
    $this->addConfig('horalign', $params->get('cfg_horalign', 'center' ));
    $this->addConfig('showtext', ($this->getConfig('showcategory') == 0 && $this->getConfig('showdescription') == 0) ? 0 : 1);
    $this->addConfig('showhorruler', $params->get('cfg_showhorruler', 1 ));
    $this->addConfig('txtresetliststyle', $params->get('cfg_txtresetliststyle', 0 ));
    $this->addConfig('showshorttext', $params->get('cfg_showshorttext', 0 ));
    $this->addConfig('imgshowborder', $params->get('cfg_imgshowborder', 1 ));
    $this->addConfig('imgborderwidth', $params->get('cfg_imgborderwidth', '1px' ));
    $this->addConfig('imgborderstyle', $params->get('cfg_imgborderstyle', 'solid' ));
    $this->addConfig('imgbordercolor', $params->get('cfg_imgbordercolor', '#C3C3C3' ));
    $this->addConfig('imgpadding', $params->get('cfg_imgpadding', '3px' ));
    $this->addConfig('imgbgcolor', $params->get('cfg_imgbgcolor', '#FFFFFF' ));
    $this->addConfig('csstag',"jglatestcat".$module_id."_");
  }

  /**
   * create and include the dynamic css for default view
   * according to backend settings
   *
   */
  function renderCSS(){
    $containerwidth=floor(100/$this->getConfig("rows"));
    $csstag=$this->getConfig("csstag");

    $horalign="text-align:".$this->getConfig("horalign")."!important;\n";
    $csscont="float:left;\n";
    $cssimgborder="";
    
    if($this->getConfig('showthumb') != 2 )
    {
      switch ($this->getConfig("imgposition")) {
      	default:
      	case 1:
          // image above text
          $cssimg .= $horalign;
          $csstxt = $horalign."padding-top:0.5em;\n";
          break;
        case 2:
          // image left from text
          if( $this->getConfig('showtext') == 0 ) {
            $cssimg .= $horalign;
          }
          else {
            $cssimg = "float:left;\n";
          	$csstxt = "float:left;\npadding-left:0.5em;\n";
          }
          break;
        case 3:
          // image right from text
          if( $this->getConfig('showtext') == 0 ) {
            $cssimg .= $horalign;
          }
          else {
        	  $cssimg = "float:right;\n";
            $csstxt = "float:right;\npadding-right:0.5em;\n";
          }
          break;
        case 4:
          // image below text
        	$cssimg .= $horalign;
          $csstxt = $horalign."padding-bottom:0.5em;\n";
          break;
      }
      if( $this->getConfig('imgshowborder') ) {
        $cssimgborder .= "border: "
                      .$this->getConfig("imgborderwidth")
                      ." "
                      .$this->getConfig("imgborderstyle")
                      ." "
                      .$this->getConfig("imgbordercolor")
                      .";\npadding: "
                      .$this->getConfig("imgpadding")
                      .";\n"
                      ."background-color: "
                      . $this->getConfig("imgbgcolor")
                      .";\n";        
      }
    }
    else
    {
      // thumb should not be displayed
      $cssimg ="";
      $csstxt = $horalign;
    }

    $css="";

    // class to clear floats 
    $css .= ".".$csstag."clr {\n"
        . "clear:both;\n"
        ."}\n";
    // row
    $css .= ".".$csstag."row {\n"
        . "overflow:hidden;\n"
        . "padding:0.5em 0;\n"
        // . "border: 1px solid red;\n"
        ."}\n";
    
    // container for image and text
    $css .= ".".$csstag."imgct {\n"
         . "width:".$containerwidth."% !important;\n"
         // . "border: 1px solid green;\n"
         . $csscont
    	 ."}\n";

    // image
    if (!empty($cssimg)){
      $css .= ".".$csstag."img {\n"
        . $cssimg
        ."}\n";
    }

    // image border
    if ( !empty( $cssimgborder ) ) {
      $css .= ".".$csstag."img img{\n"
        . $cssimgborder
        ."}\n";        
    }    
    
    // text
    if (!empty($csstxt)){
      $css .= ".".$csstag."txt {\n"
           . $csstxt
           ."}\n";
    }
    
    // reset list style for text
    if($this->getConfig('txtresetliststyle') == 1)
    {
    	$css .= ".".$csstag."txt ul {\n"
    	         ."line-height: 100% !important;\n"
                ."margin:0 !important;\n"
                ."padding:0 !important;\n"
    	         ."}\n";
    	
    	
      $css .= ".".$csstag."txt li {\n"
          ."background-image:none !important;\n"
          ."list-style-type:none !important;\n"
          ."list-style-image:none !important;\n"
          ."margin:0 !important;\n"
          ."padding:0 !important;\n"
          ."line-height: 100% !important;\n"
          ."}\n";
    }

    $document = &JFactory::getDocument();
    $document->addStyleDeclaration($css);

  }
}
?>
