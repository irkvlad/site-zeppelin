<?php
// защита от прямого доступа
defined('_JEXEC') or die('Restricted access');

JHTMLBehavior::formvalidation();

JToolBarHelper::title(JText::_('Список клиентов дизайн - студии ЦеППелин'));
JToolBarHelper::custom( $task = '', $icon = 'j_button1_site', $iconOver = '', $alt = JText::_('Домой'), $listSelect = false, $x = false );
JToolBarHelper::spacer();
JToolBarHelper::save();
JToolBarHelper::cancel();
$id = "";
$id = JRequest::getVar('id', '', 'post');
$boolSend='hidden';$checkedSend='';$NotCheckedSend='checked';
//if($this->client->send == 1 ) { $boolSend='';$checkedSend='checked';$NotCheckedSend='';}
// $date = "ГГГГ-ММ-ДД";
 //$d = new DateTime($date);*/
  //$d->modify("-1 day");
 //echo $d->format("Y-m-d");
// if($this->client->on_send <  date('Y-m-d')){
 $toDey = date('Y-m-d');
 $d = new DateTime($toDey);

 $d->modify("+90 day");
 $date =  $d->format("d-m-Y");
// }
// else $date = $this->client->on_send
 $user =& JFactory::getUser();

?>
<script type="text/javascript">
	function del(){
	    var checks = document.forms.adminForm.contact_check;
	    for (var i in checks)
	    	 if (checks[i].checked)
	    	 {
	   			var nodTR = (checks[i].parentNode).parentNode;
		    	if (nodTR.childNodes[1].value > 0 )
		    	{
		  			nodTR.childNodes[1].value = '-' + nodTR.childNodes[1].value;
		  			checks[i].checked = false;
		  			nodTR.hidden = true;
		  			continue;
		  		}

		        document.getElementById('clients').removeChild(nodTR);
			 }
	}



	function addbut() {
	    var clone = document.getElementsByClassName('client')[0].cloneNode(true);
        clone.childNodes[1].value = 0;
	    document.getElementById('clients').appendChild(clone);
	}

	 function OnlyNum(e)
	  {
	    var keynum;
	    var keychar;
	    var numcheck;
	    var return2;
	    if(window.event)
	    {
	      keynum = e.keyCode;
	    } else if(e.which) {
	      keynum = e.which;
	    }
	  keychar = String.fromCharCode(keynum);
	  if (keynum < 45 || keynum > 57) {
	    return2 = false;
	    if (keynum == 8) return2 = true;
	    }
	    else return2 = true;
	    return return2;
	  }

	function submitbutton(task)
	{
		var form = document.adminForm;
		if(form.modifer_user.value == '0'){
            alert( '<?php echo JText::_('Укажите менеджера!!'); ?>' );
            return false;

		}
		submitform( task );
	}


</script>
<form action="index.php?option=com_zepp_client" method="post" name="adminForm" id="adminForm"  >
<input type="hidden" name="task" value="" />
<input type="hidden" name="manager" value="<?php echo $this->IdManager ?>" />
				<h2>Клиент</h2>
			<table class="admintable">
				<tr>
					<td class="key1"><?php echo JText::_('Название организации&nbsp; (бренд)'); ?></td>
					<td valign="top" width="60">
						<input class="text_area" type="text" name="name" id="name" size="60" maxlength="80"
							value="" /></td> <td width="200">&nbsp;</td>
					<td class="key1" style="width:230px"><?php echo JText::_('Начало сотрудничества'); ?></td>
					<td valign="top" width="140">
						<?php
						echo JHTML::_('calendar',
								JHTML::_('date',date('Y-m-d'), '%d-%m-%Y',NULL ),
							'on_start', 'on_start', '%d-%m-%Y', array('readonly'=>'readonly'));
						?>

						</td>
					</tr>
				<tr>
					<td class="key1"><?php echo JText::_('Юр. Лицо'); ?></td>
					<td valign="top" width="60">
						<input class="text_area" type="text" name="legal_entity" id="legal_entity" size="60" maxlength="240"
							value="" />
					</td><td width="200">&nbsp;</td>
					<td title='Если "ДА" , программа напомнит вам о необходимости позвонить клиенту' class="key1"><?php echo JText::_('Напоминать клиенту о необходимости сотрудничества?'); ?></td>
					<td title='Если "ДА" , программа напомнит вам о необходимости позвонить клиенту' valign="top" width="60">
						<input <?php echo $checkedSend ?> type="radio" name="send" value="1"
								onclick="document.getElementById('send1').hidden=false;
										document.getElementById('send2').hidden=false;">&nbsp;ДА&nbsp;<input <?php echo $NotCheckedSend ?> type="radio" name="send" value="0"
								onclick="document.getElementById('send1').hidden=true;
										document.getElementById('send2').hidden=true;">&nbsp;НЕТ
						</td>
					</tr>
				<tr>
					<td class="key1"><?php echo JText::_('Общая стоимость'); ?></td>
					<td valign="top" width="60">
						<input class="text_area" type="text" name="cast" id="cast" size="60" maxlength="40"
							value="" onkeypress="return OnlyNum(event)" />
					</td><td width="200">&nbsp;</td>

					<td id="send1" <?php echo $boolSend; ?> class="key1"><?php echo JText::_('Кода напомнить?'); ?></td>
					<td id="send2" <?php echo $boolSend; ?> valign="top" width="60">
						<?php
						echo JHTML::_('calendar',
								JHTML::_('date', $date, '%d-%m-%Y', NULL ),
							'on_send', 'on_send', '%d-%m-%Y', array('readonly'=>'readonly'));
						 ?> </td>

					</tr>
				<tr>
					<td class="key1"><?php echo JText::_('Кофликтность\Лояльность'); ?></td>
					<td valign="top" width="60">
						<?php echo JHTML::_('select.integerlist', -5, 5, 1, "likes", $attribs = null, 0 , $format = ""); ?>
					</td></tr>
				<tr>
					<td class="key1"><?php echo JText::_('Менеджер'); ?></td>
					<td valign="top" width="60">
<?php				echo JHTML::_('select.genericlist', $this->listManager,'modifer_user', null, 'value', 'text', $user->id );  ?>



					</td></tr>

			</table>

	   		<h2>Контакты</h2>
	   		<table id="clients">
		        <tr>
		        	<td class="key1"></td>
		        	<td class="key1"><?php echo JText::_('Город'); ?></td>
		        	<td class="key1"><?php echo JText::_('Должность'); ?></td>
		        	<td class="key1"><?php echo JText::_('Телефон'); ?></td>
		        	<td class="key1"><?php echo JText::_('ФИО'); ?></td>
		        	<td class="key1"><?php echo JText::_('Эл.почта'); ?></td>
		        </tr>
		        <tr class="client">
		        	<td class="key1"><input type="checkbox" name="contact_check"></td>
					<td valign="top" width="60">
						<input class="text_area" type="text" name="town[]" id="town" size="60" maxlength="40" value="" />
					</td>

					<td valign="top" width="60">
						<input class="text_area" type="text" name="post[]" id="post" size="60" maxlength="40" value="" />
					</td>

					<td valign="top" width="60">
						<input class="text_area" type="text" name="telefon[]" id="telefon" size="60" maxlength="60" value="" />
					</td>

					<td valign="top" width="60">
						<input class="text_area" type="text" name="fio[]" id="fio" size="60" maxlength="40" value="" />
					</td>

					<td valign="top" width="60">
						<input class="text_area" type="text" name="email[]" id="email" size="60" maxlength="40" value="" />
					</td>
				</tr>
			</table> <br />
			<center>
				<input type='button' value="ЕЩЕ" onclick="addbut();">&nbsp;&nbsp;&nbsp;<input type='button' value="Удалить" onclick="del();">
			</center>
</form>
<?php
	//print_R($this->listManager,false);