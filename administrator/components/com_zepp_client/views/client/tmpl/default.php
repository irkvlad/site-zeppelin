<?php
// защита от прямого доступа
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title(JText::_('Список клиентов дизайн - студии ЦеППелин'));
JToolBarHelper::custom( $task = '', $icon = 'j_button1_site', $iconOver = '', $alt = JText::_('Домой'), $listSelect = false, $x = false );
JToolBarHelper::spacer();
JToolBarHelper::addNew();
//JToolBarHelper::editList();
JToolBarHelper::deleteList();

JToolBarHelper::custom( $task = 'ContactSend', $icon = 'icon-32-send', $iconOver = '', $alt = JText::_('Клиенты без обзвона'), $listSelect = false, $x = false );


//print_R($this->rows,false);     //onclick="document.location.search='?order=shop_name'"

$title = "Кофликтность или Лояльность \n от -5 до +5";


?>




<form action="index.php?option=com_zepp_client" method="post" name="adminForm" id="adminForm">

		<?php echo JText::_('<strong>Поиск всех вхождений строки:</strong>&nbsp;&nbsp;'); ?>
		<input class="text_area" type="text" name="searchall" id="searchall" size="60" maxlength="40" value=""
		onclick="this.value=''">&nbsp;&nbsp;&nbsp;<strong><?php
		echo $this->search; echo JText::_('&nbsp;&nbsp;&nbsp;Сортировать по менеджерам:&nbsp;&nbsp;');
		echo JHTML::_('select.genericlist', $this->listManager,'manager', null, 'value', 'text', $this->IdManager );
		//echo JHTML::_('list.users', $name="manager", $active=null, $nouser = 1, $javascript = NULL, $order = 'name', $reg = 1);

	/*	$active - здесь можно поставить id пользователя, который будет активным в списке по умолчанию. Либо ставить NULL, видимо по умолчанию забыли прописать;
		$nouser - булевая переменная. Устанавливает будет ли список с нулевым значением "- No User -";
		$javascript - здесь можно передать какое-нибудь событие для JS;
		$order - сортировка, по умолчанию по имени пользователя;
		$reg - булевая переменная. Устанавливает будует ли список только с пользователями, которые имеют доступ к админке (gid > 18), или списоок будет из всех 		пользователей. По умолчания включена;   $this->IdManagers  */

		?></strong>&nbsp;&nbsp;&nbsp;<button type="submit"  form="adminForm">Выбрать</button>
		   <br /><br />




				<table class="adminlist">



	<table class="adminlist">
		<tr>
			<th></th>
			<th><?php echo JText::_('Название организации( бренд)'); ?></th>
			<th><?php echo JText::_('Юр. Лицо'); ?></th>
			<th><?php echo JText::_('Общая стоимость'); ?></th>
			<th  title="<?php echo $title; ?>" ><?php echo JText::_('-&nbsp;\\&nbsp;+'); ?></th>
			<th><?php echo JText::_('Менеджер'); ?></th>
			<th><?php echo JText::_('Напоминание'); ?></th>
			<th><?php echo JText::_('Город'); ?></th>
			<th><?php echo JText::_('Должность'); ?></th>
			<th><?php echo JText::_('ФИО'); ?></th>
			<th><?php echo JText::_('Телефон'); ?></th>
			<th><?php echo JText::_('Эл.почта'); ?></th>
		</tr>

		<?php if ($this->client):
			foreach ($this->client as $i => $clnt):
				$checked = JHTML::_('grid.id', $i, $clnt->id);
				$linkClient = 'index.php?option=com_zepp_client&task=edit&id=' . $clnt->id;

			?>
				<tr class="row<?php echo $i % 2; ?>">
					<td><?php echo $checked; ?></td>
					<td><a href="<?php echo $linkClient; ?>"><?php echo $clnt->name; ?></a></td>
					<td><?php echo $clnt->legal_entity; ?></td>
					<td><?php echo $clnt->cast; ?></td>
					<td title="<?php echo $title; ?>"><?php echo $clnt->likes; ?></td>
					<?php
					if ($clnt->modifer_user)
					{
						$user =& JFactory::getUser($clnt->modifer_user);

						echo "<td>" . $user->get('name') ." </td>";
					}
					?>
					<td><?php if ( $clnt->send == 1 )echo $clnt->on_send; else echo  JText::_('Не напоминать'); ?></td>
					<?php
					$i=0;$alt = "\n";
					foreach($this->contact as $con)
					{
						if($con->id_client == $clnt->id)
						{
							$i++;
							$town[$i]  = $con->town;
							$post[$i]  =$con->post;
							$fio[$i]   =$con->fio ;
							$telefon[$i] =$con->telefon;
							$email[$i]   =$con->email;
							if($i>1)$alt.="$con->fio\t\t$con->telefon\t\n";
						}
						//$alt .= "</table>";  <tr><td>    </td><td>
					}

					$plus ="";
					if ($i > 1) $plus = "[+] ";
					if($i>0)
					{
					 ?>
					 	<td><?php echo $town[1]; ?></td>
						<td><?php echo $post[1]; ?></td>
						<td title=" <?php if ($alt) echo $alt ; ?> "><?php echo $fio[1]."&nbsp;&nbsp;&nbsp;".$plus; ?></td>
						<td><?php echo $telefon[1]; ?></td>
						<td><?php echo $email[1]; ?></td>
					<?php
					}else
					{   ?>
							<td></td>
							<td></td>
							<td>Контакт отсутсвует</td>
							<td></td>
							<td></td>
						<?php
					}

					?>
				</tr>
			<?php
			endforeach;
		else: ?>
			<tr>
				<td colspan="16"><?php echo JText::_('Нет данных'); ?></td>
			<tr>
		<?php endif; ?>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="sendCon" value="" />
	<input type="hidden" name="boxchecked" value="0" />
   </form>
                <?php  //echo "rrr "; print_R($this->send);