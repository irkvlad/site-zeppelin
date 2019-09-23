<?php
// защита от прямого доступа
defined('_JEXEC') or die('Restricted access');

$title = "Кофликтность или Лояльность \n от -5 до +5";
$document = JFactory::getDocument();
$document->addStylesheet(JPATH_COMPONENT . DS . 'css'. DS . 'com_zepp_client.css');
$disabled='disabled';
 $user =& JFactory::getUser();
 foreach ($this->Managers as $man) if($user->id ==$man) $disabled='';
 if($user->id > 25 )  $disabled='';
 if ($user->id == 0) echo '<h1> Вы не аторизованны </h1>'  ;
//print_R($document,false);     //onclick="document.location.search='?order=shop_name'"

?>
<!--=================================================================-->
<div id="toolbar-box">
	<div class="t"><div class="t"><div class="t"></div></div></div>
	<div class="m">
		<div class="toolbar" id="toolbar">
				<table class="toolbar"><tbody><tr>
				<td class="button-toolbar" id="toolbar-j_button1_site">
					<a href="#" onclick="javascript: submitbutton('')" class="toolbar">
							<span class="icon-32-j_button1_site" title="Домой"></span>Очистить фильтры
					</a>
				</td>
				<td class="spacer"></td>
				<?php if (  ! $disabled ) { ?>
				<td class="button-toolbar" id="toolbar-new">
					<a href="#" onclick="javascript: submitbutton('add')" class="toolbar">
						<span class="icon-32-new" title="Создать"></span>Создать
					</a>
				</td>
				<td class="button-toolbar" id="toolbar-delete">
					<a href="#"
						onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('Выберите из списка для');}else{  submitbutton('remove')}"
						class="toolbar"><span class="icon-32-delete" title="Удалить"></span>Удалить
					</a>
				</td>
				<?php } ?>
				<td class="button-toolbar" id="toolbar-icon-32-send">
					<a href="#" onclick="javascript: submitbutton('ContactSend')" class="toolbar">
						<span class="icon-32-icon-32-send" title="Клиенты без обзвона"></span>Клиенты без обзвона
					</a>
				</td>
			</tr></tbody></table>
		</div>
		<div class="header icon-48-generic">Список клиентов дизайн - студии ЦеППелин</div>
		<div class="clr"></div>
	</div>
	<div class="b"><div class="b"><div class="b"></div></div></div>
</div><div class="clr"></div>
<!--=============================================================================================================-->

<div id="element-box">
	<div class="t"><div class="t"><div class="t"></div></div></div>
	<div class="m">


		<form action="index.php?option=com_zepp_client" method="post" name="adminForm" id="adminForm">

		<?php echo JText::_('<strong>Поиск всех вхождений строки:</strong>&nbsp;&nbsp;').$this->search.'&nbsp;&nbsp;' ?>
		<input class="text_area" type="text" name="searchall" id="searchall" size="20" maxlength="40" value=""
		onclick="this.value=''">&nbsp;&nbsp;&nbsp;<strong><?php
		echo JText::_('&nbsp;&nbsp;&nbsp;Выбрать по менеджерам:&nbsp;&nbsp;');
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
			<th align="center"><?php echo JText::_('Название организации (бренд)'); ?></th>
			<!--//<th><?php echo JText::_('Юр. Лицо'); ?></th>//-->
			<th align="center"><?php echo JText::_('Общая стоимость'); ?></th>
			<th align="center" title="<?php echo $title; ?>" ><?php echo JText::_('-&nbsp;\\&nbsp;+'); ?></th>
			<th align="center" ><?php echo JText::_('Менеджер'); ?></th>
			<th align="center" title="<?php echo JText::_('Напоминание'); ?>" ><img src="components/com_zepp_client/img/con_tel.png" alt="<?php echo JText::_('Напоминание'); ?>"></th>
			<!--//<th><?php echo JText::_('Город'); ?></th>
			<th><?php echo JText::_('Должность'); ?></th>//-->
			<th align="center" ><?php echo JText::_('ФИО'); ?></th>
			<th align="center" ><?php echo JText::_('Телефон'); ?></th>
			<th align="center" ><?php echo JText::_('Эл.почта'); ?></th>
		</tr>

		<?php if ($this->client):
			foreach ($this->client as $i => $clnt):
				$checked = JHTML::_('grid.id', $i, $clnt->id);
				$linkClient = 'index.php?option=com_zepp_client&task=edit&id=' . $clnt->id;

			?>
				<tr class="row<?php echo $i % 2; ?>">
					<td><?php echo $checked; ?></td>
					<td><a href="<?php echo $linkClient; ?>"><?php echo $clnt->name; ?></a></td>
					<!--//<td><?php echo $clnt->legal_entity; ?></td>//-->
					<td><?php echo $clnt->cast; ?></td>
					<td title="<?php echo $title; ?>"><?php echo $clnt->likes; ?></td>
					<?php
					if ($clnt->modifer_user)
					{
						$user =& JFactory::getUser($clnt->modifer_user);

						echo "<td>" . $user->get('name') ." </td>";
					}
					?>
					<td nowrap align="center"><?php if ( $clnt->send == 1 )
								echo $clnt->on_send;
							else echo '<img src="components/com_zepp_client/img/notice-alert.png" alt="'. JText::_('Не напоминать').'">' ?></td>
					<?php
					$i=0;$alt = null;
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
					if ($i > 1) $plus = '<font color="red">[+]</font>';
					if($i>0)
					{
					 ?>
					 	<!--//<td><?php echo $town[1]; ?></td>
						<td><?php echo $post[1]; ?></td>//-->
						<td title="<?php if ($alt) echo $alt ; ?>"><?php echo $fio[1]."&nbsp;&nbsp;&nbsp;".$plus; ?></td>
						<td><?php echo $telefon[1]; ?></td>
						<td><?php echo $email[1]; ?></td>
					<?php
					}else
					{   ?>
							<!--//<td></td>
							<td></td>//-->
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
	<div class="clr"></div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="sendCon" value="" />
	<input type="hidden" name="boxchecked" value="0" />
   </form>
<div class="clr"></div>
	</div>
	<div class="b"><div class="b"><div class="b"></div></div></div>
</div>  <div class="clr"></div>  <?php  //echo "rrr "; print_R($this->send);