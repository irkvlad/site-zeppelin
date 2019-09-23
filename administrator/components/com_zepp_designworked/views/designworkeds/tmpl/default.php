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
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>			
			<th>
				<?php echo JText::_( 'Название' ); ?>
			</th>
            <th>
				<?php echo JText::_( 'Описание' ); ?>
			</th>
            <th>
				<?php echo JText::_( 'Категория' ); ?>
			</th>
            <th>
				<?php echo JText::_( 'Дизайнер' ); ?>
			</th>
            <th>
				<?php echo JText::_( 'Дата' ); ?>
			</th>

		</tr>
	</thead>
	<?php
        
//echo 'start--tmp\default.php';
//print_R($this->items);echo '<br>';

	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
                
//print_R($row);echo '<br>';

		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_zepp_designworked&controller=designworked&task=edit&cid[]='. $row->id );
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $row->id; ?>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<?php echo $row->name; ?>
			</td>
             <td>
				<?php echo $row->coment; ?>
             </td>
             <td>
				<?php echo $row->catid; ?>
			</td>
            <td>
				<?php echo $row->userid; ?>
			</td>
            <td>
				<?php echo $row->data; ?>
			</td>

                        <!--<td>
				<a href="<?php// echo $link; ?>"><?php //echo  $row->manger_data; ?></a>
			</td>-->
                        
                       
		</tr>
		<?php
		$k = 1 - $k;
	}
//echo 'end--tmp\default.php';
	?>
	</table>
</div>

<input type="hidden" name="option" value="com_zepp_designworked" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="designworked" />
</form>

