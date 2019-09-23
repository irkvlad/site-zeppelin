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
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->data); ?>);" />
				</th>
				<th>
					<?php echo JText::_( 'Материал' ); ?>
				</th>
				<th>
					<?php echo JText::_( 'Активно' ); ?>
				</th>
				<th>
					<?php echo JText::_( 'Плотность' ); ?>
				</th>
				<th>
					<?php echo JText::_( 'Текстура' ); ?>
				</th>
				<th>
					<?php echo JText::_( 'Цвет' ); ?>
				</th>
			</tr>
			</thead>

			<?php
			$k = 0;
			$i = 0;

			foreach ( $this->data as $row )	{
				$checked 	= JHTML::_('grid.id',   $i, $i );
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
						<input class="text_area" type="text" name="name[]" id="name" size="60" maxlength="60" value="<?php echo $row->name; ?>" onchange="adminForm.<?php echo "cb$i" ; ?>.checked=true" />
					</td>
					<td>
						<?php 	if($row->set !=0) echo "<input type=\"checkbox\" name=\"set[]\" checked value=\"$row->id\" onclick=\"adminForm.cb$i.checked=true\" />";
						else echo "<input type=\"checkbox\" name=\"set[]\" value=\"$row->id\" onclick=\"adminForm.cb$i.checked=true\" />"; ?>
					</td>
					<td>
						<?php 	if($row->plotnost !=0) echo "<input type=\"checkbox\" name=\"plotnost[]\" checked value=\"$row->id\" onclick=\"adminForm.cb$i.checked=true\" />";
						else echo "<input type=\"checkbox\" name=\"plotnost[]\" value=\"$row->id\" onclick=\"adminForm.cb$i.checked=true\" />"; ?>
					</td>
					<td>
						<?php 	if($row->texture !=0) echo "<input type=\"checkbox\" name=\"texture[]\" checked value=\"$row->id\" onclick=\"adminForm.cb$i.checked=true\" />";
						else echo "<input type=\"checkbox\" name=\"texture[]\" value=\"$row->id\" onclick=\"adminForm.cb$i.checked=true\" />"; ?>
					</td>
					<td>
						<?php 	if($row->color !=0) echo "<input type=\"checkbox\" name=\"color[]\" checked value=\"$row->id\" onclick=\"adminForm.cb$i.checked=true\" />";
						else echo "<input type=\"checkbox\" name=\"color[]\" value=\"$row->id\" onclick=\"adminForm.cb$i.checked=true\" />"; ?>
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
					<input class="text_area" type="text" name="name[]" id="name" size="60" maxlength="60" value='' onchange="adminForm.<?php echo "cb$i" ; ?>.checked=true" />
				</td>
                <td>
                   <input type="checkbox" name="set[]" checked value="0" onclick="adminForm.cb$i.checked=true" />;
                </td>
				<td>
					<input type="checkbox" name="plotnost[]"  value="0" onclick="adminForm.cb$i.checked=true" />;
				</td>
				<td>
					<input type="checkbox" name="texture[]"  value="0" onclick="adminForm.cb$i.checked=true" />;
				</td>
				<td>
					<input type="checkbox" name="color[]"  value="0" onclick="adminForm.cb$i.checked=true" />;
				</td>

			</tr>
		</table>
	</div>

	<input type="hidden" name="option" value="com_zepp_polnocvet" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="polnocvet" />
</form>

