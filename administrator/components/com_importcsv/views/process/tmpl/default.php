<?php
include_once("xmlToArray.class.php");
function do_offset($level){
    $offset = "";             // offset for subarry 
    for ($i=1; $i<$level;$i++){
    $offset = $offset . "<td></td>";
    }
    return $offset;
}
function autoMakeLink($text){
  $text = ereg_replace("((www.)([a-zA-Z0-9@:%_.~#-\?&]+[a-zA-Z0-9@:%_~#\?&/]))","http://\\1", $text);
  $text = ereg_replace("((ftp://|http://|https://){2})([a-zA-Z0-9@:%_.~#-\?&]+[a-zA-Z0-9@:%_~#\?&/])", "http://\\3", $text);
  $text = ereg_replace("(((ftp://|http://|https://){1})[a-zA-Z0-9@:%_.~#-\?&]+[a-zA-Z0-9@:%_~#\?&/])", "<A HREF=\"\\1\" TARGET=\"_blank\">\\1</A>", $text);
  $text = ereg_replace("([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})","<A HREF=\"mailto:\\1\">\\1</A>", $text);
  return $text;
}

function show_array($array, $level, $sub,$code){
    if (is_array($array) == 1){          // check if input is an array
       foreach($array as $key_val => $value) {
           $offset = "";
           if (is_array($value) == 1){   // array is multidimensional
           $code.="<tr>";
           $offset = do_offset($level);
           $code.=$offset;
           $code=show_array($value, $level+1, 1,$code);
           }
           else{                        // (sub)array is not multidim
           if ($sub != 1){          // first entry for subarray
               $code.="<tr nosub>\n";
               $offset = do_offset($level);
           }
           $sub = 0;
           $code.=$offset . "<td main ".$sub." width=\"120\">" . $key_val . 
               "</td><td width=\"120\">" . $value . "</td>\n"; 
           $code.="</tr>\n";
           }
       } //foreach $array
    }  
    return $code;
}

function html_show_array($array){
	if(!empty($_POST['taille']))
				$code="<table width=\"".$_POST['taille']."\" border='0'>";
			else
				$code="<table border='0'>";  
	$code.=show_array($array, 1, 0,"");
    $code.="</table>\n";
	return $code;
}
function affCSVXML($contenu,$type,$autodetect="n",$params)
{
    $tab=array();
    $allowedExt = array("csv","xml");

    if(in_array($type,array("csv")))
    {
		if($params['EOL_DELIMITER']=='\n')
            $res=explode("\n",$contenu);
        else
             $res=explode($params['EOL_DELIMITER'],$contenu);
		for($i=0;$i<sizeof($res);$i++)
		{
			$res2=explode($params['CELL_DELIMITER'],$res[$i]);
			foreach($res2 as $r)
				$tab[$i][]=str_replace($params['TEXT_DELIMITER'],'',$r);
		}
		if(!empty($_POST['taille']))
			$code="<table width=\"".$_POST['taille']."\">";
		else
			$code="<table>";
		for($i=0;$i<sizeof($tab);$i++)
		{
			if($i==0&&$_POST['entete']=="o")
			{
				$code.="<tr>";
				foreach($tab[$i] as $t)
				{
					// $code.="<th>".str_replace(array('"',"'"),"\'",$t)."</th>";
					// correction proposé par lmconseils
					$code.="<th>".utf8_encode(str_replace(array('"',"'"),"\'",$t))."</th>";
				}	
				$code.="</tr>";
			}
			else
			{
				$code.="<tr>";
				foreach($tab[$i] as $t)
				{
					//$code.="<td>".str_replace(array('"',"'"),"\'",$t)."</td>";
					// correction proposé par lmconseils
					$code.="<td>".utf8_encode(str_replace(array('"',"'"),"\'",$t))."</td>";
				}	
				$code.="</tr>";
			}
		}
		$code.="</table>";
	}
	else
	{
		$xml2a = new XMLToArray();
		$contenuXML=$xml2a->parse($contenu); 
		$code=html_show_array($contenuXML);
	}
	if($autodetect=="o")
	{
		$code=autoMakeLink($code);
	}
	echo "<br><div id=\"codeCSV1\" style=\"display:none\">$code</div><center></center><button onclick=\"window.parent.jInsertEditorText(document.getElementById('codeCSV1').innerHTML, 'text');\">".JText::_('INSERT_CONTENT')."</button> <a href=\"".JRoute::_('index.php?option=com_importcsv&view=process&tmpl=component')."\">".JText::_('Back')."</a></center>";
}
function readFileContent($filename)
{
	$fp=fopen($filename,"r");
    $content="";
    while(!feof($fp))
    	$content.=fread($fp,1024);
    fclose($fp);
	return $content;
}
function findExt($filename)
{
	return end(explode('.',$filename));
}
if( isset($_POST['submit']) ) // si formulaire soumis
{
        if ( $mainframe->isAdmin() ) 
        {
         	$baseSite=$mainframe->getSiteURL();
        }
        else
        {
          	$baseSite=JURI::Base();
      	}
    $params=array('EOL_DELIMITER'=>$_POST['EOL_DELIMITER'],'CELL_DELIMITER'=>$_POST['CELL_DELIMITER'],'TEXT_DELIMITER'=>$_POST['TEXT_DELIMITER']);
    if($_POST['type']=="file")
	{
	$content_dir=str_replace(array('administrator/components/com_importcsv/views/process/tmpl/','administrator\\components\\com_importcsv\\views\\process\\tmpl\\'),'',dirname(__FILE__).DS."tmp".DS);
    $tmp_file = $_FILES['fichier']['tmp_name'];

    if( !is_uploaded_file($tmp_file) )
    {
        exit(JText::_('ERROR_FILE1'));
    }

    // on vérifie maintenant l'extension
    $type_file = $_FILES['fichier']['type'];
    $name_file = $_FILES['fichier']['name'];
    $allowedExt = array("csv","xml");

    if(!in_array(end(explode(".", $name_file)),$allowedExt))
    {
        exit(JText::_('ERROR_FILE2')." <a href=\"".JRoute::_('index.php?option=com_importcsv&view=process&tmpl=component')."\">".JText::_('Back')."</a>");
    }

    // on copie le fichier dans le dossier de destination
    $name_file = $_FILES['fichier']['name'];

    if( !move_uploaded_file($tmp_file,$content_dir . $name_file) )
    {
        exit(JText::_('ERROR_FILE3')." $content_dir <a href=\"".JRoute::_('index.php?option=com_importcsv&view=process&tmpl=component')."\">".JText::_('Back')."</a>");
    }
	echo "<br><br><br>".JText::_('FILE_SUCCESS');
	$filename=$content_dir.$name_file;
	affCSVXML(readFileContent($filename),findExt($filename),$_POST['autodetect'],$params);
	}
	elseif($_POST['type']=="url")
		affCSVXML(str_replace('"','',file_get_contents($_POST['fichier'])),findExt($_POST['fichier']),$_POST['autodetect'],$params);
	else
		affCSVXML($_POST['fichier'],$_POST['typeFile'],$_POST['autodetect'],$params);

}
else
{
	 if ( $mainframe->isAdmin() ) 
        {
         	$baseSite=$mainframe->getSiteURL();
        }
        else
        {
          	$baseSite=JURI::Base();
      	}
	?>
<script language="javascript" type="application/javascript">
function view(ID)
{
	document.getElementById('tab1').style.display='none';
	document.getElementById('tab2').style.display='none';
	document.getElementById('tab3').style.display='none';
	document.getElementById(ID).style.display='block';
}
</script>
<style>
.buttonCSV
{
	margin-left:5px;
	margin-right:5px;
	border:1px solid blue;
	padding:5px;
	text-decoration:none;
}
</style>
<div style="clear:both">
<ul style="list-style-type:none;clear:both">
<li style="float:left"><a href="#" onclick="view('tab1');return false;"><div class="buttonCSV"><?php echo JText::_('UPLOAD_CSV')?></div></a></li>
<li style="float:left"><a href="#" onclick="view('tab2');return false;"><div class="buttonCSV"><?php echo JText::_('FROMURL_CSV')?></div></a></li>
<li style="float:left"><a href="#" onclick="view('tab3');return false;"><div class="buttonCSV"><?php echo JText::_('PASTE_CSV')?></div></a></li>
</ul>
</div>
<div style="clear:both"></div>
<br />
<br />
<div id="tab1">
<h1><?php echo JText::_('UPLOAD_CSV')?></h1>
<form action="<?php echo JRoute::_('index.php?option=com_importcsv&view=process&tmpl=component')?>" method="POST" enctype="multipart/form-data">
<input type="hidden" name="type" value="file" />
  <table>
    <tr>
      <td><?php echo JText::_('CSV_FILE')?> :</td>
      <td><input type="file" name="fichier" /></td>
    </tr>
    <tr>
      <td><?php echo JText::_('TABLE_WIDTH')?> :</td>
      <td><input type="text" name="taille" value="100%" /></td>
    </tr>
    <tr>
      <td><?php echo JText::_('DISPLAY_COLS')?> :</td>
      <td><label for="enteteOui"><?php echo JText::_('YES')?></label>
        <input type="radio" value="o" name="entete" id="enteteOui" />
        <label for="enteteNon"><?php echo JText::_('NO')?></label>
        <input type="radio" value="n" name="entete" id="enteteNon" checked="checked" /></td>
    </tr>
    <tr>
      <td><?php echo JText::_('AUTODETECT')?> :</td>
      <td><label for="autoOui"><?php echo JText::_('YES')?></label>
        <input type="radio" value="o" name="autodetect" id="autoOui" />
        <label for="autoNon"><?php echo JText::_('NO')?></label>
        <input type="radio" value="n" name="autodetect" id="autoNon" checked="checked" /></td>
    </tr>
    <tr>
        <td>
        <h3><?php echo JText::_('DELIMITERS')?></h3>
            <table width="100%">
                <tr>
                    <td><?php echo JText::_('CELL_DELIMITER')?></td><td><?php echo JText::_('TEXT_DELIMITER')?></td><td><?php echo JText::_('EOL_DELIMITER')?></td>
                </tr>
                <tr>
                    <td><input type="text" name="CELL_DELIMITER" value=";" style="width:15px;" /></td>
                    <td><input type="text" name="TEXT_DELIMITER" value="" style="width:15px;" /></td>
                    <td><input type="text" name="EOL_DELIMITER" value="\n" style="width:15px;" /></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
      <td colspan="2" align="center"><input type="submit" name="submit" /></td>
    </tr>
  </table>
  <input type="hidden" name="baseSite" value="<?php echo "/".str_replace('//','/',$baseSite)?>" />
</form>
</div>
<div id="tab2" style="display:none">
<h1><?php echo JText::_('FROMURL_CSV')?></h1>
<form action="<?php echo JRoute::_('index.php?option=com_importcsv&view=process&tmpl=component')?>" method="POST" enctype="multipart/form-data">
<input type="hidden" name="type" value="url" />
  <table>
    <tr>
      <td><?php echo JText::_('URL')?> :</td>
      <td><input type="text" name="fichier" /></td>
    </tr>
    <tr>
      <td><?php echo JText::_('TABLE_WIDTH')?> :</td>
      <td><input type="text" name="taille" value="100%" /></td>
    </tr>
    <tr>
      <td><?php echo JText::_('DISPLAY_COLS')?> :</td>
      <td><label for="enteteOui"><?php echo JText::_('YES')?></label>
        <input type="radio" value="o" name="entete" id="enteteOui" />
        <label for="enteteNon"><?php echo JText::_('NO')?></label>
        <input type="radio" value="n" name="entete" id="enteteNon" checked="checked" /></td>
    </tr>
    <tr>
      <td><?php echo JText::_('AUTODETECT')?> :</td>
      <td><label for="autoOui"><?php echo JText::_('YES')?></label>
        <input type="radio" value="o" name="autodetect" id="autoOui" />
        <label for="autoNon"><?php echo JText::_('NO')?></label>
        <input type="radio" value="n" name="autodetect" id="autoNon" checked="checked" /></td>
    </tr>
    <tr>
        <td>
        <h3><?php echo JText::_('DELIMITERS')?></h3>
            <table width="100%">
                <tr>
                    <td><?php echo JText::_('CELL_DELIMITER')?></td><td><?php echo JText::_('TEXT_DELIMITER')?></td><td><?php echo JText::_('EOL_DELIMITER')?></td>
                </tr>
                <tr>
                    <td><input type="text" name="CELL_DELIMITER" value=";" style="width:15px;" /></td>
                    <td><input type="text" name="TEXT_DELIMITER" value="" style="width:15px;" /></td>
                    <td><input type="text" name="EOL_DELIMITER" value="\n" style="width:15px;" /></td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
      <td colspan="2" align="center"><input type="submit" name="submit" /></td>
    </tr>
  </table>
  <input type="hidden" name="baseSite" value="<?php echo "/".str_replace('//','/',$baseSite)?>" />
</form>
</div>
<div id="tab3" style="display:none">
<h1><?php echo JText::_('PASTE_CSV')?></h1>
<form action="<?php echo JRoute::_('index.php?option=com_importcsv&view=process&tmpl=component')?>" method="POST" enctype="multipart/form-data">
<input type="hidden" name="type" value="paste" />
  <table>
    <tr>
      <td><?php echo JText::_('CSV_FILE')?> :</td>
      <td><textarea name="fichier"></textarea></td>
    </tr>
    <tr>
      <td><?php echo JText::_('File_Type')?> :</td>
      <td><label for="typeOui"><?php echo JText::_('XML')?></label>
        <input type="radio" value="xml" name="typeFile" id="typeOui" />
        <label for="typeNon"><?php echo JText::_('CSV')?></label>
        <input type="radio" value="csv" name="typeFile" id="typeNon" checked="checked" /></td>
    </tr>
    <tr>
      <td><?php echo JText::_('TABLE_WIDTH')?> :</td>
      <td><input type="text" name="taille" value="100%" /></td>
    </tr>
    <tr>
      <td><?php echo JText::_('DISPLAY_COLS')?> :</td>
      <td><label for="enteteOui"><?php echo JText::_('YES')?></label>
        <input type="radio" value="o" name="entete" id="enteteOui" />
        <label for="enteteNon"><?php echo JText::_('NO')?></label>
        <input type="radio" value="n" name="entete" id="enteteNon" checked="checked" /></td>
    </tr>
    <tr>
      <td><?php echo JText::_('AUTODETECT')?> :</td>
      <td><label for="autoOui"><?php echo JText::_('YES')?></label>
        <input type="radio" value="o" name="autodetect" id="autoOui" />
        <label for="autoNon"><?php echo JText::_('NO')?></label>
        <input type="radio" value="n" name="autodetect" id="autoNon" checked="checked" /></td>
    </tr>
    <tr>
        <td>
        <h3><?php echo JText::_('DELIMITERS')?></h3>
            <table width="100%">
                <tr>
                    <td><?php echo JText::_('CELL_DELIMITER')?></td><td><?php echo JText::_('TEXT_DELIMITER')?></td><td><?php echo JText::_('EOL_DELIMITER')?></td>
                </tr>
                <tr>
                    <td><input type="text" name="CELL_DELIMITER" value=";" style="width:15px;" /></td>
                    <td><input type="text" name="TEXT_DELIMITER" value="" style="width:15px;" /></td>
                    <td><input type="text" name="EOL_DELIMITER" value="\n" style="width:15px;" /></td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
      <td colspan="2" align="center"><input type="submit" name="submit" /></td>
    </tr>
  </table>
  <input type="hidden" name="baseSite" value="<?php echo "/".str_replace('//','/',$baseSite)?>" />
</form>
</div>
<p align="center">More information <a href="http://www.erreurs404.net/labels/importcsv.html?piwik_campaign=ImportCSV_EditorContent">http://www.erreurs404.net/labels/importcsv.html</a></p>
<?php	
}
?>
