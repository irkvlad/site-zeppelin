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

$filename = 'components/com_flexicontent/index.html';
			if (file_exists($filename)) {
				require_once (JPATH_SITE.DS.'components'.DS.'com_flexicontent'.DS.'helpers'.DS.'route.php');
			}else{
				require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
			}

class mod_allnews_ItemHelper {
	function getContent(&$params) {
		$item_id = intval( $params->get( 'item_id') );
		return $item_id;
	}
}

//Choice between number of words or characters
function affichage_intro($intro,$nombre_mots,$choix_type_longueur){
  if ($choix_type_longueur){ // However in number of characters
      $intro_temp = $intro;
      // Take the first characters of the intro (nombre of words, in fact number of letters)
      if(strlen($intro)>=$nombre_mots){$intro=substr($intro,0,$nombre_mots);}
      // The number of word in the intro
      $tabl_temp = array_slice(preg_split('/ /', $intro, -1, PREG_SPLIT_NO_EMPTY),0,$nombre_mots);
      $compteur = 0;
      foreach ($tabl_temp  as $value){
          $compteur = $compteur + 1 ;
      }
      // To cut the intro former(ancient) with the number of found words (avoids cutting the intro in the middle of a word)
      $tabl_temp = array_slice(preg_split('/ /', $intro_temp, -1, PREG_SPLIT_NO_EMPTY),0,$compteur);
      $intro = implode(" ",$tabl_temp) .' ...';
  }else {     // If number of words
      $tabl_temp = array_slice(preg_split('/ /', $intro, -1, PREG_SPLIT_NO_EMPTY),0,$nombre_mots);
      $intro = implode(" ",$tabl_temp) .' ...';
  }
  return $intro;
}
//Get first or article's images
function getFirstImg($article) {
$regex = "#<img[^>]+src=['|\"](.*?)['|\"][^>]*>#i";
$img = "";
if (preg_match($regex, $article, $matches)){$img = $matches[1];}
return $img;
}

// Output Functions - New in v 2.5.3

//Title
function OutputTitle($css, $link, $titolo, $title, $blank, $linktitle) {
	if ($blank == 1) {
		$target = ' target="_blank"';
	}else{
		$target = '';
	}
	if ($linktitle == 1) {
		$outputtitle = '<span style="' . $css . '"><a href="' . $link . '"' . $target . ' title="' . $titolo . '">' . $title . '</a></span> ';
	}else{
		$outputtitle = '<span style="' . $css . '">' . $title . '</span> ';
	}
return $outputtitle;
}

//Date
function OutputDate($uselang, $dprefix, $date, $css) {
	if ($uselang == 1) {
		$pr = JText::_('F_DATEPREFIX');
	}else{
		$pr = $dprefix;
	}
$outputdate = '<span style="' . $css . '">' . $pr . ' ' . $date . '</span> ';
return $outputdate;
}

//Author
function OutputAuthor($uselang, $aprefix, $profilesystem, $profilelink, $creator, $author, $css) {
	if ($uselang == 1) {
		$pr = JText::_('F_AUTHORPREFIX');
	}else{
		$pr = $aprefix;
	}
	if ($profilesystem != 0) {
		$outputauthor = '<span style="' . $css . '">' . $pr . ' <a href="' . $profilelink . $creator . '">' . $author . '</a></span> '; 
	}else{
		$outputauthor = '<span style="' . $css . '">' . $pr . ' ' . $author . '</span> ';
	}
return $outputauthor;
}

//Category
function OutputCategory($uselang, $catprefix, $showcat, $css) {
	if ($uselang == 1) {
		$pr = JText::_('F_CATHPREFIX');
	}else{
		$pr = $catprefix;
	}
$outputcategory = '<span style="' . $css . '">' . $pr . ' ' . $showcat . '</span> ';
return $outputcategory;
}

//Comments
function OutputComments($uselang, $comprefix, $commenti, $comtitle, $comimg, $css) {
	if ($uselang == 1) {
		$pr = JText::_('F_COMMPREFIX');
		if ($commenti == 1) {
			$tit = JText::_('F_COMMTITLE_S');
		}else{
			$tit = JText::_('F_COMMTITLE_P');
		}
	}else{
		$pr = $comprefix;
		$tit = $comtitle;
	}
	if ($comimg == 1) {
		$outputcomments = '<span style="' . $css . '">' . $pr . ' ' . $commenti . ' <img src="modules/mod_allnews/comment.png" title="' . $tit . '"/></span> ';
	}else{
		$outputcomments = '<span style="' . $css . '">' . $pr . ' ' . $commenti . ' ' . $tit . '</span> ';
	}
return $outputcomments;
}

//Hits
function OutputHits($hitimg, $uselang, $hitprefix, $visite, $hittitle, $css) {
	if ($uselang == 1) {
		$pr = JText::_('F_HITPREFIX');
		if ($visite == 1) {
			$tit = JText::_('F_HITTITLE_S');
		}else{
			$tit = JText::_('F_HITTITLE_P');
		}
	}else{
		$pr = $hitprefix;
		$tit = $hittitle;
	}	
	if ($hitimg == 1) {
		$outputhits = '<span style="' . $css . '">' . $pr . ' ' . $visite . ' <img src="modules/mod_allnews/hit.png" title="' . $tit . '"/></span> ';
	}else{
		$outputhits = '<span style="' . $css . '">' . $pr . ' ' . $visite . ' ' . $tit . '</span> ';
	}
return $outputhits;
}

//Rating
function OutputRating($ratimg, $uselang, $ratprefix, $voti, $rattitle, $css) {
	if ($uselang == 1) {
		$pr = JText::_('F_RATINGPREFIX');
		if ($voti == 1) {
			$tit = JText::_('F_RATINGTITLE_S');
		}else{
			$tit = JText::_('F_RATINGTITLE_P');
		}
	}else{
		$pr = $ratprefix;
		$tit = $rattitle;
	}	
	if ($ratimg == 1) {
		$outputrating = '<span style="' . $css . '">' . $pr . ' ' . $voti . ' <img src="modules/mod_allnews/rating.png" title="' . $tit . '"/></span> ';
	}else{
		$outputrating = '<span style="' . $css . '">' . $pr . ' ' . $voti . ' ' . $tit . '</span> ';
	}
return $outputrating;
}

//Readmore
function OutputRM($uselang, $link, $keepon, $lire_suite, $titolo, $tit, $css){
	if ($uselang == 1) {
		$readmore = JText::_('F_READMORE');
	}else{
		$readmore = $lire_suite; 
	}
	if ($keepon == 1) {
		$outputreadmore = '<span style="' . $css . '"><a href="' . $link . '">' . $readmore . '</a></span> ';
	}else{
		$outputreadmore = '<span style="' . $css . '">' . $readmore . ' <a href="' . $link . '" title="' . $titolo . '">' . $tit . '</a></span> ';
	}
return $outputreadmore;
}

//Add Comment
function Outputaddcomm($uselang, $link, $addcomm, $css, $ct){
	if ($uselang == 1) {
		$c = JText::_('F_ADDCOMMENTS');
	}else{
		$c = $addcomm; 
	}
	if ($ct == 1) {
		$link .= "#addcomments";
	}
return '<span style="' . $css . '"><a href="' . $link . '">' . $c . '</a></span> ';
}

//Image
function OutputImage($float, $css, $link, $blank, $affichage_image) {
	if ($blank == 1) {
		$artblank = 'target="_blank"';
	}else{
		$artblank = '';
	}
	$outputimage = '<span style="float:' . $float . '; ' . $css . '"><a href="' . $link . '" ' . $artblank . ' >' . $affichage_image . '</a></span>';
return $outputimage;
}

//Flexi Field
function OutputField($css, $l_css, $label, $field) {
	$outputfield = '<div style="' . $css . '"><span style="' . $l_css . '">' . $label . '</span> ' . $field . '</div>';
return $outputfield;
}
?>