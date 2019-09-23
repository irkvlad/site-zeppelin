<?php
// защита от прямого доступа
defined('_JEXEC') or die('Restricted access');
// кнопки
//JToolBarHelper::save();
//JToolBarHelper::cancel();
$thri_df = JRequest::getVar('thri_df');
$photoshopf = JRequest::getVar('photoshopf');
$corelf = JRequest::getVar('corelf');
$auto_cadf = JRequest::getVar('auto_cadf');
$web_disignf = JRequest::getVar('web_disignf');
$starGroupf = JRequest::getVar('starGroupf');
$logo_path = str_replace("../", "", $this->row->logo_path);

?>
 <h2>Детали</h2>
<form enctype="multipart/form-data" action="index.php?option=com_portfolios" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
		<legend><?php echo JText::_('CGCA DETAILS'); ?></legend>

	<table class="admintable">
		<tr>
			<td rowspan="8" class="key2">
				<img width="256" id="logo" src="<?php echo $logo_path; ?>" alt="Фото" />
				<!--//<input onchange="document.getElementById('logo').src='../administrator/components/com_portfolios/img/img_load.jpg'" size="0" alt="Загрузить фото" type="file" name="logo_path" accept="image/*" />//-->
			</td>
			<td valign="bottom" width="60" class="key1"><?php echo JText::_('FIO'); ?>:</td>
			<td rowspan="3" class="key2" valign="top" >
				<div style='width : 120px;float:left;'>
				<?php


					($this->thri_d)? $checked1 ="checked" : $checked1 ="";
					($this->photoshop)? $checked2 ="checked" : $checked2 ="";
					($this->corel)? $checked3 ="checked" : $checked3 ="";
					($this->auto_cad)? $checked4 ="checked" : $checked4 ="";
					($this->web_disign)? $checked5 ="checked" : $checked5 ="";

					?>


					<input disabled="disabled" readonly type="checkbox" name="thri_d" value="1" <?php echo $checked1; ?> >3D Max<br>
					<input disabled="disabled" readonly type="checkbox" name="photoshop" value="1" <?php echo $checked2; ?> >Photo Shop<br>
					<input disabled="disabled" readonly type="checkbox" name="corel" value="1" <?php echo $checked3; ?> >Corel<br>
					<input disabled="disabled" readonly type="checkbox" name="auto_cad" value="1" <?php echo $checked4; ?> >Auto Cad<br>
					<input disabled="disabled" readonly type="checkbox" name="web_disign" value="1" <?php echo $checked5; ?> >Web - дизайн
                </div>
                <div style='width : 120px;float:left;'>  <b >Рейтинг</b>
				<?php
                      //echo $this->starGroup    ;
                      ?>
                     <br /><br />  <font size="15" color="#ffcc00" ><?php
                    for ($i = 0 ; $i < $this->star_reyting;$i++)
					{
						echo  "*";
						}
					?></font><font size="15" color="708090" ><?php
					  for ($i = 0 ; $i < 5 - $this->star_reyting;$i++)
					{
						echo  "*";
						}  ?></font>


    			</div>
			</td>
		</tr>
		<tr>

			<td valign="top" width="60">
				<input readonly class="text_area" type="text" name="fio" id="fio" size="60" maxlength="40" value="<?php echo $this->row->fio; ?>" />
			</td>
		</tr>


		<tr>

			<td valign="bottom" width="60" class="key1"><?php echo JText::_('DATE_ROJJDENIY'); ?>:</td>

		</tr>
		<tr>

			<td valign="top" width="60">
				<input readonly class="date_rojjdeniy" type="text" name="date_rojjdeniy" id="date_rojjdeniy" size="60" maxlength="10" value="<?php echo $this->row->date_rojjdeniy; ?>" />
                </td>
            <td rowspan="8" class="key1" valign="top" width="60" higth="100">
                <span class="key1">Характеристика</span><br />
                <textarea readonly class="text_area" rows="27" cols="45" name="privut_notes" id="privut_notes" style="width: 330px; height: 405px;text-align: left;"><?php
                 echo $this->row->privut_notes; ?></textarea>
			</td>
		</tr>
		<tr>

			<td valign="bottom" width="60" class="key1"><?php echo JText::_('TELEFON'); ?>:</td>

		</tr>
		<tr>
			<td valign="top" width="60" >
				<input readonly class="text_area" type="text" name="telefon" id="telefon" size="60" maxlength="16" value="<?php echo $this->row->telefon; ?>" />
			</td>
		</tr>
		<tr>

			<td valign="bottom" width="60" class="key1"><?php echo JText::_('EMAIL'); ?>:</td>
		</tr>

		<tr>

			<td valign="top" width="60">
				<input readonly class="text_area" type="text" name="email" id="email" size="60" maxlength="50" value="<?php echo $this->row->email; ?>" />
			</td>
		</tr>

		<tr>
			<td width="60" class="key"><?php echo JText::_('STUDENT'); ?>:</td>
			<td width="60">
				<textarea readonly class="text_area" rows="4" cols="57" name="student" id="student" ><?php echo $this->student; ?></textarea>

			</td>
		</tr>
		<tr>
			<td width="60" class="key"><?php echo JText::_('WORCKED'); ?>:</td>
			<td width="60">
				<textarea readonly class="text_area" rows="4" cols="57" name="worcked" id="worcked" ><?php echo $this->worcked; ?></textarea>

			</td>
		</tr>
		<tr>
			<td width="60" class="key"><?php echo JText::_('NOTES'); ?>:</td>
			<td width="60">
				<textarea readonly class="text_area" rows="11" cols="57" name="notes" id="notes" ><?php echo $this->notes; ?></textarea>

			</td>
		</tr>
		<tr>
			<td colspan="3" width="60" class="key1"><?php echo JText::_('PHOTO_PATH'); ?>:</td>
		</tr>
		<tr>
			<td colspan="3" class="key2">

<?php
$portfolio=explode(";", $this->photo_path);
$portfolio = str_replace("../", "", $portfolio);
echo "<table><tr>";
for ($i=0;$i<4;$i++){
    echo '<td valign="bottom">';

	// echo '<img onclick=\'this.style.display="none";document.photo'.$i.'.src=this.src;document.getElementById("id'.$i.'").type = "hidden";document.getElementById("id'.$i.'").value = "del";\'style="';
  if(!$portfolio[$i]) {}else{
  //echo 'float:left;cursor:pointer" width="25" height="25" src="../administrator/components/com_portfolios/img/icon_error.gif" alt="Удалить" /><a href="'.$portfolio[$i].'" target="_blank">
      echo  '<a href="'.$portfolio[$i].'" target="_blank"><img id="photo'.$i.'" width="150"  src="'.$portfolio[$i].'"  /></a><br />';
    //echo '<br> <input onchange="document.getElementById(\'photo'.$i.'\').src=\'../administrator/components/com_portfolios/img/img_load.jpg\'" id="id'.$i.'" size="0" alt="Загрузить фото" type="file" name="photo_path'.$i.'" accept="image/*" />
		//</td>';
		}
}
echo "</tr></table>";

 ?>
			</td>
		</tr>

	</table>



	</fieldset>
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="com_portfolios" />
	<input type="hidden" name="task" value="save_fild" />
	<input type="hidden" name="thri_df" value="<?php echo $thri_df; ?>" />
	<input type="hidden" name="photoshopf" value="<?php echo $photoshopf; ?>" />
	<input type="hidden" name="corelf" value="<?php echo $corelf; ?>" />
	<input type="hidden" name="auto_cadf" value="<?php echo $auto_cadf; ?>" />
	<input type="hidden" name="web_disignf" value="<?php echo $web_disignf; ?>" />
	<input type="hidden" name="starGroupf" value="<?php echo $starGroupf; ?>" />


	<?php echo JHTML::_('form.token'); ?>
</form>
<script language="JavaScript">

</script>