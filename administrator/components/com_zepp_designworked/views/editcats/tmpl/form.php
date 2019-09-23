<?php defined('_JEXEC') or die('Restricted access');?>


<form action="index.php" method="post" name="adminForm" id="adminForm">
<div >
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Details' ); ?></legend>

		<table class="admintable">
		<tr>
			<td width="100" align="right" class="key">
				<label for="namecat">
					<?php echo JText::_( 'Название' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="name" id="namecat" size="65" maxlength="65" value="<?php echo $this->data[0]->name;?>" />
			</td>
		</tr>
			<tr>
				<td width="100" align="right" class="key">
					<label for="commentcat">
						<?php echo JText::_( 'Пояснение' ); ?>:
					</label>
				</td>
				<td>
					<textarea rows="10" cols="45" name="comment" ><?php echo $this->data[0]->comment;?></textarea>

				</td>
			</tr>
	</table>
	</fieldset>
</div>
<div class="clr"></div>

	<input type="hidden" name="option" value="com_zepp_designworked" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="categories" />
	<input type="hidden" name="cid" value="<?php echo $this->data[0]->id; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>


</form>
