<?php defined('_JEXEC') or die('Restricted access');
$date = JFactory::getDate();
$datestr = JRequest::getVar('startdate', $date->toFormat ('01.%m.%Y') );
$dateend= JRequest::getVar('enddate', $date->toFormat ('%d.%m.%Y') ); 

$creator=JRequest::getVar('creator',0,'int');
?>
<h1>Компонет для учета заявок поступающих в ДС "ЦеППелин" </h1>
 
<form action="index.php" method="post" name="adminForm">
    Отчет по создателям <input type="checkbox" name="creator" value="1"><br>
                <?php 
                
                echo JHTML::_('calendar', $value = $datestr, $name='startdate', $id='startdate', $format = '%d.%m.%Y', $attribs = 'size="10"'); 
                echo JHTML::_('calendar', $value = $dateend, $name='enddate', $id='enddate', $format = '%d.%m.%Y', $attribs = 'size="10"'); 
                ?>
                <?php echo JText::_('<strong>Поиск всех вхождений строки:</strong>&nbsp;&nbsp;').$this->search.'&nbsp;&nbsp;' ?>
		<input class="text_area" type="text" name="searchall" id="searchall" size="20" maxlength="40" value=""
		onclick="this.value=''">
                <?php echo $this->managerList;?>
                <input type="submit" name="submit" class="button" value="Отправить" />
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
				<?php echo JText::_( 'Дата' ); ?>
			</th>
                        <th>
				<?php echo JText::_( 'Тема' ); ?>
			</th>
                        <th>
				<?php echo JText::_( 'Клиент' ); ?>
			</th>
                        <th>
				<?php echo JText::_( 'Телефон' ); ?>
			</th>
                        <th>
				<?php echo JText::_( 'Создал' ); ?>
			</th>
                        <th>
				<?php echo JText::_( 'Менеджер' ); ?>
			</th>
                        <th>
				<?php echo JText::_( 'Взял' ); ?>
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
		$link 		= JRoute::_( 'index.php?option=com_zepp_ringclient&controller=ringclient&task=edit&cid[]='. $row->id );
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $row->id; ?>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<?php echo $row->creator_date; ?>
			</td>
                        <td>
				<?php echo $row->tema; ?>
                        </td>
                        <td>
				<?php echo $row->client; ?>
			</td>
                        <td>
				<?php echo $row->telefon; ?>
			</td>
                        <td>
				<?php echo $row->creator_name; ?>
			</td>
                        <td>
				<?php echo ringclientHTML::getUserName($row->manager_id).' '.$row->manager_id; ?>
			</td>
                        <td>
				<?php echo $row->manger_data; ?>
			</td>
                        <!--<td>
				<a href="<?php echo $link; ?>"><?php echo $row->manger_data; ?></a>
			</td>-->
                        
                       
		</tr>
		<?php
		$k = 1 - $k;
	}
//echo 'end--tmp\default.php';
	?>
	</table>
</div>

<input type="hidden" name="option" value="com_zepp_ringclient" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="ringclient" />
</form>

