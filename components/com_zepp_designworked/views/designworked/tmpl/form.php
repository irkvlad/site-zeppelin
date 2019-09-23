<?php defined('_JEXEC') or die('Restricted access');

?>
<script type="text/javascript"><!--

function validate_form_cancel() { document.adminForm.task.value = "cancel"; return true;   }
function validate_form_add() { document.adminForm.task.value = "save"; return true;   }

	function validate_form() {
	if( document.adminForm.task.value == "cancel") return true;
    if (document.adminForm.catid.value == 0) {
		alert("Нужно указать категорию, если ее нет в списке свяжитесь с администратором");
        return false;
    }else{
		document.adminForm.cat.value = document.adminForm.catid.value;
	}
	 if (document.adminForm.userid.value == 0) {
		alert("Нужно указать дизайнера, если его нет в списке свяжитесь с администратором");
        return false;
    }else{
		document.adminForm.dis.value = document.adminForm.userid.value;
	}
    return true;
//if ( document.adminForm.submit.value == "Заказ в работе" )
  //  {
    //    var s = prompt("Номер проекта","");
      //  document.adminForm.text.value = s;
      //  valid = true;
   // }
//alert(document.adminForm.submit.value);
  //      return valid;
}
//--></script>
<form action="index.php" enctype="multipart/form-data" method="POST" name="adminForm" id="adminForm" onsubmit="return validate_form();">
<div id="toolbar-box">
	<div class="t"><div class="t"><div class="t"></div></div></div>
	<div class="m">
		<div id="toolbar" class="toolbar">

				<?php //if ( ($user->gid > 23)  ) : ?>
				<input style="float:left;" type="submit" onclick="validate_form_add();" name="submit_add" class="button" value="Сохранить" />
				<input style="float:left;" type="submit" onclick="validate_form_cancel();" name="submit_cancel" class="button" value="Назад" />
				<?php //endif; ?>


		</div>
		<div  class="header" >Галерея работ</div>
		<div class="clr"></div>
	</div>
	<div class="b"><div class="b"><div class="b"></div></div></div>
	<div class="clr"></div>

</div>

<div id="element-box">
	<div class="t"><div class="t"><div class="t"></div></div></div>
	<div class="m">
		<label>Выберите файлы: <input multiple name="file[]" type="file" /></label><br>

		<label>Название работы: <input class="text_area" type="text" name="name" id="d_name" size="60" maxlength="40" value="<?php echo $this->data->name; ?>" /></label><br>
		<label>Пояснения:<br> <textarea rows="10" cols="45" name="coment"><?php echo $this->data->coment; ?></textarea></label><br>
		<label>Присвоена категория:  <?php echo $this->catList ?></label><br>
		<label>Принадлежит дизайнеру: <?php echo $this->designerList; ?></label><br>

		<?php
		foreach($this->images as $image){
			echo 	'<div style="float: left;"><div style="width: 120px;">Удалить <input type="checkbox" name="del[]" value="'.$image['path'].'"></div><a style="
							margin: 5px;
							display: block;
							float: left;
							max-width: 180px;
							min-width: 180px;
							max-height: 128px;
							min-height: 128px;
							overflow: hidden;
							text-align: center;
					" target="_blank" href="'.$image['path'].'" ><div>'.$image['privu'].'<br>'.$image['alt'].'</div></a></div> ' ;
		}
		?>
	</div>
	<div class="b"><div class="b"><div class="b"></div></div></div>
</div>

<?php echo 'dis'.$this->dis; ?>
<?php echo 'cat'.$this->cat; ?>

	<input type="hidden" name="option" value="com_zepp_designworked" />
	<input type="hidden" name="controller" value="designworked" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="cid" value="<?php echo $this->data->id; ?>" />
	<input type="hidden" name="path" value="<?php echo $this->data->path; ?>" />
	<input type="hidden" name="privu" value="<?php echo $this->data->privu; ?>" />
	<input type="hidden" name="view" value="designworked" />
	<input type="hidden" name="dis" value="<?php echo $this->dis; ?>" />
	<input type="hidden" name="cat" value="<?php echo $this->cat; ?>" />

	<input type="hidden" name="Itemid" value="<?php echo $this->itemid;  ?>" />

	<!--	-->
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

<?php// dump($this); ?>

