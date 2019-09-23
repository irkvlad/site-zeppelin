<?php defined('_JEXEC') or die('Restricted access');

?>

<form action="index.php" enctype="multipart/form-data" method="POST" name="adminForm" id="adminForm">
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

	<input type="hidden" name="option" value="com_zepp_designworked" />
	<input type="hidden" name="controller" value="designworked" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="cid" value="<?php echo $this->data->id; ?>" />
	<input type="hidden" name="path" value="<?php echo $this->data->path; ?>" />
	<input type="hidden" name="privu" value="<?php echo $this->data->privu; ?>" />

	<!--	-->
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

<?php //dump($this); ?>

