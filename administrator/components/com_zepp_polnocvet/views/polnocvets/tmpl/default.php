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
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->filials ); ?>);" />
			</th>			
			<th>
				<?php echo JText::_( 'Вебстраница' ); ?>
			</th>
			<th>
				<?php echo JText::_( 'Компания' ); ?>
			</th>
			<th>
				<?php echo JText::_( 'Филиал' ); ?>
			</th>
			<th>
				<?php echo JText::_( 'Адрес' ); ?>
			</th>
		</tr>
	</thead>

	<?php
	$k = 0;
	for ($i=0, $n=count( $this->filials ); $i < $n; $i++)	{
		$row = &$this->filials[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_zepp_polnocvet&controller=polnocvet&task=edit&cid[]='. $row->id );
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $row->id; ?>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<a href="<?php echo $link; ?>"><?php echo $row->webpage; ?></a>
			</td>
			<td>
				<a href="<?php echo $link; ?>"><?php echo $row->name; ?></a>
			</td>
			<td>
				<a href="<?php echo $link; ?>"><?php echo $row->filial; ?></a>
			</td>
			<td>
				<a href="<?php echo $link; ?>"><?php echo $row->adress; ?></a>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</table>
</div>

<input type="hidden" name="option" value="com_zepp_polnocvet" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="polnocvet" />
</form>
