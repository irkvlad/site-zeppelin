<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col100">
	<fieldset class="adminform">
		<legend><?php  $this->text; ?></legend>

		<table class="admintable">
		<tr>
			<td width="100" align="right" class="key">
				<label for="webpage">
					<?php echo JText::_( 'Вебстраница' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="webpage" id="webpage" size="32" maxlength="250" value="<?php echo $this->filial[0]->webpage;?>" />

			</td>
		</tr>
			<tr>
				<td width="100" align="right" class="key">
					<label for="company">
						<?php echo JText::_( 'Компания' ); ?>:
					</label>
				</td>
				<td>

					<!--<input class="text_area" type="text" name="company" id="company" size="32" maxlength="250" value="<?php //echo $this->filial[0]->company;?>" />-->
					<?php echo $this->companyList; ?>

				</td>
			</tr>
			<tr>
				<td width="100" align="right" class="key">
					<label for="filial">
						<?php echo JText::_( 'Филиал' ); ?>:
					</label>
				</td>
				<td>

					<input class="text_area" type="text" name="filial" id="filial" size="32" maxlength="250" value="<?php echo $this->filial[0]->filial;?>" />

				</td>
			</tr>
			<tr>
				<td width="100" align="right" class="key">
					<label for="adress">
						<?php echo JText::_( 'Адрес' ); ?>:
					</label>
				</td>
				<td>

					<input class="text_area" type="text" name="adress" id="adress" size="32" maxlength="250" value="<?php echo $this->filial[0]->adress;?>" />
				</td>
			</tr>

	</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_zepp_polnocvet" />
<input type="hidden" name="id" value="<?php echo $this->filial[0]->id; ?>" />
<input type="hidden" name="task" value="" />
</form>

