<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm">
	<div id="editcell">

		<table class="adminlist">
			<thead>
			<tr>
				<th width="5">
					<?php echo JText::_( 'ID' ); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->company ); ?>);" />
				</th>
				<th>
					<?php echo JText::_( 'Компания' ); ?>
				</th>
			</tr>
			</thead>

			<?php
			$k = 0;
			$i = 0;
//			for ($i=0, $n=count( $this->company ); $i < $n; $i++)
			foreach ( $this->company as $row )	{
				//$row = &$this->company[$i];
				$checked 	= JHTML::_('grid.id',   $i, $i );
				//$link 		= JRoute::_( 'index.php?option=com_zepp_polnocvet&controller=polnocvet&task=edit&cid[]='. $row->id );
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $row->id; ?>
						<input type="hidden" name="ids[]" value="<?php echo $row->id; ?>" />
					</td>
					<td>
						<?php echo $checked; ?>
					</td>

					<td>
						<input class="text_area" type="text" name="company[]" id="company" size="32" maxlength="250" value="<?php echo $row->name; ?>" />
					</td>

				</tr>
				<?php
				$k = 1 - $k;
				$i++;
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php //echo 0; ?>
					<input type="hidden" name="ids[]" value="0" />
				</td>
				<td>
					<?php echo JHTML::_('grid.id',   $i, $i ); ?>
				</td>

				<td>
					<input class="text_area" type="text" name="company[]" id="company" size="32" maxlength="250" value='' />
				</td>

			</tr>
		</table>
	</div>

	<input type="hidden" name="option" value="com_zepp_polnocvet" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="polnocvet" />
</form>

