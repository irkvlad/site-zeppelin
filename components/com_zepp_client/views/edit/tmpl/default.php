<?php
// защита от прямого доступа
defined('_JEXEC') or die('Restricted access');


$id = "";
$id = JRequest::getVar('id','post');
$boolSend="hidden";$checkedSend='';$NotCheckedSend='checked';
if($this->client->send == 1 ) { $boolSend='';$checkedSend='checked';$NotCheckedSend='';}
// $date = "ГГГГ-ММ-ДД";
 //$d = new DateTime($date);*/
  //$d->modify("-1 day");
 //echo $d->format("Y-m-d");
 if($this->client->on_send <  date('Y-m-d')){
 $toDey = date('Y-m-d');
 $d = new DateTime($toDey);

 $d->modify("+90 day");
 $date =  $d->format("d-m-Y");
 }
 else $date = $this->client->on_send;

$disabled='disabled';
 $user =& JFactory::getUser();
 foreach ($this->Managers as $man) if($user->id ==$man) $disabled='';
 if($user->id > 25 )  $disabled='';
 if ($user->id == 0) echo '<h1> Вы не аторизованны </h1>'  ;
?>
<script type="text/javascript">
	function del(){
	    var checks = document.forms.adminForm.contact_check;
	    for (var i in checks)
	    	 if (checks[i].checked)
	    	 {	   			var nodTR = (checks[i].parentNode).parentNode;
		    	if (nodTR.childNodes[1].value > 0 )
		    	{		  			nodTR.childNodes[1].value = '-' + nodTR.childNodes[1].value;
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
		if(task == 'cancel')submitform( task );
		else if(form.modifer_user.value == '0'){
            alert( '<?php echo JText::_('Укажите менеджера!!'); ?>' );
            return false;

		}
		submitform( task );
	}

	    function checkForm()
    {
        var form = document.adminForm;

        if(form.modifer_user.value == '0'){
            alert( '<?php echo JText::_('Укажите менеджера!!'); ?>' );
            return false;
            }
       }
</script>
<form action="index.php?option=com_zepp_client" method="post" name="adminForm" id="adminForm" onsubmit="checkForm();">
<div id="toolbar-box">
	<div class="t"><div class="t"><div class="t"></div></div></div>
	<div class="m">
		<div class="toolbar" id="toolbar">
		<?php if (  ! $disabled ) {
			?>
			<table class="toolbar"><tr>

				<td class="button-toolbar" id="toolbar-save">
					<a href="#" onclick="javascript: submitbutton('save')" class="toolbar">
						<span class="icon-32-save" title="Сохранить"></span>Сохранить
					</a>
				</td>
				<td class="button-toolbar" id="toolbar-cancel">
					<a href="#" onclick="javascript: submitbutton('cancel')" class="toolbar"><span class="icon-32-cancel" title="Отменить"></span>Выйти
					</a>
				</td>
			</tr></table>
		<?php }        		?>
		</div>
		<div class="header icon-48-generic">Клиент дизайн - студии ЦеППелин</div>
		<div class="clr"></div>
	</div>
	<div class="b"><div class="b"><div class="b"></div></div></div>
</div>
<div class="clr"></div>

<div id="element-box">
	<div class="t"><div class="t"><div class="t"></div></div></div>
	<div class="m">

<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $id ?>" />

			<h2>Клиент</h2>
			<div class="t"><div class="t"><div class="t"></div></div></div><div class="m">
			<table class="admintable">
				<tr>
					<td class="key1"><?php echo JText::_('Название организации&nbsp; (бренд)'); ?></td>
					<td valign="top" width="60">
						<input <?php echo $disabled; ?> class="text_area" type="text" name="name" id="name" size="60" maxlength="80"
							value="<?php echo $this->client->name ?>" /></td> <td width="60">&nbsp;</td>
					<td class="key1" style="width:230px"><?php echo JText::_('Начало сотрудничества'); ?></td>
					<td valign="top" width="140">
						<?php
						if ( $disabled ) { echo JHTML::_('date', $this->client->on_start, '%d-%m-%Y', $disabled );}
						else {
							echo JHTML::_('calendar',
								JHTML::_('date', $this->client->on_start, '%d-%m-%Y',NULL ),
							'on_start', 'on_start', '%d-%m-%Y', array('readonly'=>'readonly','size'=>'10',$disabled));
						}?>

						</td>
					</tr>
				<tr>
					<td class="key1"><?php echo JText::_('Юр. Лицо'); ?></td>
					<td valign="top" width="60">
						<input <?php echo $disabled; ?> class="text_area" type="text" name="legal_entity" id="legal_entity" size="60" maxlength="240"
							value="<?php echo $this->client->legal_entity ?>" />
					</td><td width="60">&nbsp;</td>
					<td title='Если "ДА" , программа напомнит вам о необходимости позвонить клиенту' class="key1"><?php echo JText::_('Напоминать клиенту о необходимости сотрудничества?'); ?></td>
					<td title='Если "ДА" , программа напомнит вам о необходимости позвонить клиенту' valign="top" width="60">
						<input <?php echo $disabled; ?> <?php echo $checkedSend ?> type="radio" name="send" value="1"
								onclick="document.getElementById('send1').hidden=false;
										document.getElementById('send2').hidden=false;">&nbsp;ДА&nbsp;<input <?php echo $disabled; ?> <?php echo $NotCheckedSend ?> type="radio" name="send" value="0"
								onclick="document.getElementById('send1').hidden=true;
										document.getElementById('send2').hidden=true;">&nbsp;НЕТ
						</td>
					</tr>
				<tr>
					<td class="key1"><?php echo JText::_('Общая стоимость'); ?></td>
					<td valign="top" width="60">
						<input  <?php echo $disabled; ?> class="text_area" type="text" name="cast" id="cast" size="60" maxlength="40"
							value="<?php echo $this->client->cast ?>" onkeypress="return OnlyNum(event)" />
					</td><td width="60">&nbsp;</td>

					<td id="send1" <?php echo $boolSend; ?> class="key1"><?php echo JText::_('Кода напомнить?'); ?></td>
					<td id="send2" <?php echo $boolSend; ?> valign="top" width="60">
						<?php
						if ( $disabled ) {echo JHTML::_('date',$date, '%d-%m-%Y',$disabled );}
						else {
							echo JHTML::_('calendar',
								JHTML::_('date', $date, '%d-%m-%Y', NULL ),
							'on_send', 'on_send', '%d-%m-%Y', array('readonly'=>'readonly','size'=>'10',$disabled));
						 }?> </td>

					</tr>
				<tr>
					<td class="key1"><?php echo JText::_('Кофликтность\Лояльность'); ?></td>
					<td valign="top" width="60">
						<?php echo JHTML::_('select.integerlist', -5, 5, 1, "likes", $attribs = $disabled, $selected = $this->client->likes , $format = ""); ?>
					</td></tr>
				<tr>
					<td class="key1"><?php echo JText::_('Менеджер'); ?></td>
					<td valign="top" width="60">
<?php				echo JHTML::_('select.genericlist', $this->listManager, $name = 'modifer_user', $attribs = $disabled, $key = 'value', $text = 'text', $selected = $this->client->modifer_user );  ?>



					</td></tr>

			</table> </div>
             <div class="b"><div class="b"><div class="b"></div></div></div>
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
		<?php
				if (count($this->contact) == 0 AND !$disabled ){ ?>
				 <tr class="client">
		          	<input type="hidden" name="idc[]" value="0" />
		        	<td class="key1"><input size="5" type="checkbox" name="contact_check"></td>
					<td title="<?php echo $contact->town ?>" valign="top" width="0">
						<textarea <?php echo $disabled; ?> class="text_area" type="text" name="town[]" id="town" cols="15" maxlength="60"/></textarea>
					</td>

					<td  title="<?php echo $contact->post ?>" valign="top" width="0">
						<textarea <?php echo $disabled; ?> class="text_area" type="text" name="post[]" id="post" cols="15" maxlength="60"/></textarea>
					</td>

					<td  title="<?php echo $contact->telefon ?>" valign="top" width="0">
						<textarea <?php echo $disabled; ?> class="text_area" type="text" name="telefon[]" id="telefon" cols="30" maxlength="60"/></textarea>
					</td>

					<td  title="<?php echo $contact->fio ?>" valign="top" width="0">
						<textarea <?php echo $disabled; ?> class="text_area" type="text" name="fio[]" id="fio" cols="30" maxlength="40"/></textarea>
					</td>

					<td  title="<?php echo $contact->email ?>" valign="top" width="0">
						<textarea <?php echo $disabled; ?> class="text_area" type="text" name="email[]" id="email" cols="30" maxlength="40"/></textarea>
					</td>

				<?php }

				      foreach($this->contact as $contact) {  ?>
		        <tr class="client">
		        <?php if (  ! $disabled ) { ?>
		        	<input type="hidden" name="idc[]" value="<?php echo  $contact->id ?>" />
		        	<td class="key1"><input size="5" type="checkbox" name="contact_check"></td>
					<td title="<?php echo $contact->town ?>" valign="top" width="0">
						<textarea <?php echo $disabled; ?> class="text_area" type="text" name="town[]" id="town" cols="15" maxlength="60"/><?php echo $contact->town ?></textarea>
					</td>

					<td  title="<?php echo $contact->post ?>" valign="top" width="0">
						<textarea <?php echo $disabled; ?> class="text_area" type="text" name="post[]" id="post" cols="15" maxlength="60"/> <?php echo $contact->post ?></textarea>
					</td>

					<td  title="<?php echo $contact->telefon ?>" valign="top" width="0">
						<textarea <?php echo $disabled; ?> class="text_area" type="text" name="telefon[]" id="telefon" cols="30" maxlength="60"/><?php echo $contact->telefon ?></textarea>
					</td>

					<td  title="<?php echo $contact->fio ?>" valign="top" width="0">
						<textarea <?php echo $disabled; ?> class="text_area" type="text" name="fio[]" id="fio" cols="30" maxlength="40"/> <?php echo $contact->fio ?></textarea>
					</td>

					<td  title="<?php echo $contact->email ?>" valign="top" width="0">
						<textarea <?php echo $disabled; ?> class="text_area" type="text" name="email[]" id="email" cols="30" maxlength="40"/><?php echo $contact->email ?></textarea>
					</td>
                  <?php }
                  else { ?>
                     <td class="key1">&nbsp;</td>
					<td valign="top" width="0">&nbsp;
						<?php echo $contact->town ?>
					</td>

					<td valign="top" width="0">&nbsp;
						<?php echo $contact->post ?>
					</td>

					<td valign="top" width="0">&nbsp;
						<?php echo $contact->telefon ?>
					</td>

					<td valign="top" width="0">&nbsp;
						<?php echo $contact->fio ?>
					</td>

					<td valign="top" width="0">&nbsp;
						<?php echo $contact->email ?>
					</td>


                  <?php } ?>
				</tr>
				<?php } ?>
			</table> <br />
			<center>
			 <?php if (  ! $disabled ) { ?>	<input <?php echo $disabled; ?> type='button' value="ЕЩЕ" onclick="addbut();">&nbsp;&nbsp;&nbsp;<input <?php echo $disabled; ?> type='button' value="Удалить" onclick="del();"> <?php } ?>
			</center>

</form>
<div class="clr"></div>
		</div>
		<div class="b"><div class="b"><div class="b"></div></div></div>
   	</div>
<?php
	//print_R($this->contact,false); //   print_R($idc,false);