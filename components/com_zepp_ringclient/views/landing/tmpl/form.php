<?php
/***********************
 * $this->Record: Array (
 *                          [id] =>
 *                          [manager_id] =>
 *                          [client] => Клиент
 *                          [creator_id] => кто создал
 *                          [creator_name] => кто создал
 *                          [telefon] => 12321431
 *                          [tema] =>
 *                          [creator_date] => Когда создали
 *                          [manger_data] => когда взял менеджер
 *                          [project_id] =>
 *                       )
 * MySQL:
 * id 	            int(11) 	    Нет
 * manager_id 	    int(11) 	    Нет  	114  	            id менеджера из users
 * client 	        varchar(30) 	Да  	NULL  	            имя фамилия и прочь. клиента
 * creator_id 	    int(11) 	    Нет  	114  	            Кто создал заказ или кто его принял . это ид зарегистрированных пользователей по умолчанию 114- пусто
 * creator_name 	varchar(18) 	Да  	NULL  	            Это имя создавшего заказ если зарегистрировавшийся то из базы users, если нет то то что укажет.
 * telefon 	        varchar(18) 	Да  	NULL  	            Телефон клиента
 * tema 	        text 	        Да  	NULL  	            Что нужно описание
 * creator_date 	timestamp 	    Нет  	CURRENT_TIMESTAMP  	Автоматическая метка времени , при создании
 * manger_data 	    date 	        Да  	NULL
 * project_id 	    int(11) 	    Да  	NULL  	            Если создан проект - ид проекта
 * status 	        int(11) 	    Нет  	0  	                Статус заявки: 0 - нет движения, 1-есть проект, 2 - отказались
 * statustext 	    text 	        Да  	NULL  	            Пояснение к статусу
 * statusdata 	    date 	        Да  	NULL  	            Дата установки статуса
 *
 *******************/



defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();

$bcolor='#fff';
if ($this->Record['status'] == 2) $bcolor=' #CCD5EA';
if ($this->Record['status'] == 1) $bcolor=' #C0EAB9';

$linkZ1 = '<a href="';//
$linkZ2= ' " ><b>';
$linkZ_YES= 'Послушать разговор';
$linkZ_NO= '';
$linkZ3= '</b></a>';
?>
<script type="text/javascript">


<!--

function validate_form_no() {
    var s = prompt("Укажите причину", "");
    document.adminForm.text.value = s;
    document.adminForm.task.value = "Отказались от заказа";
    return true;
}

function validate_form_err() {
    var s = prompt("Коментарий", "");
    document.adminForm.text.value = s;
    document.adminForm.task.value = "Ошибочный заказ";
    return true;
}

function validate_form_sv() {
    document.adminForm.text.value = 'editzakaz';
    document.adminForm.task.value = "editzakaz";
    return true;
}

function validate_form_media_file() {
    document.adminForm.text.value = 'save_media_file';
    document.adminForm.task.value = "save_media_file";
    return true;
}


function validate_form_del_media_file() {
    document.adminForm.text.value = 'del_media_file';
    document.adminForm.task.value = "del_media_file";
    return true;
}

function validate_form_media_file2() {
    document.adminForm.text.value = 'save_media_file2';
    document.adminForm.task.value = "save_media_file2";
    return true;
}


function validate_form_del_media_file2() {
    document.adminForm.text.value = 'del_media_file2';
    document.adminForm.task.value = "del_media_file2";
    return true;
}

function validate_form_rentabelnost() {
    document.adminForm.text.value = 'save_rentabelnost';
    document.adminForm.task.value = "save_rentabelnost";
    return true;
}


function validate_form_ys() {
    if (document.adminForm.release.value){
        document.adminForm.text.value = document.adminForm.release.value;
        document.adminForm.task.value = "Заказ в работе";
    } else {
        var s = prompt("Номер проекта", "");
        document.adminForm.text.value = s;
        document.adminForm.task.value = "Заказ в работе";
        return true;
    }
}

function validate_form_cn() {
        document.adminForm.text.value = "Выйти";
        document.adminForm.task.value = "Выйти";
    return true;
}

function validate_form_new() {
    document.adminForm.text.value = "Новый проект";
    document.adminForm.task.value = "Новый";
    return true;
}

function validate_form() {
    if (document.adminForm.text.value) {
        return true;
    }
    return false;
//if ( document.adminForm.submit.value == "Заказ в работе" )
  //  {
    //    var s = prompt("Номер проекта","");
      //  document.adminForm.text.value = s;
      //  valid = true;
   // }
//alert(document.adminForm.submit.value);
  //      return valid;
}

//-->
</script>
<form action="index.php?option=com_zepp_ringclient&view=landing&id=<?php echo $this->Record['id'] ?> "
       method="post" name="adminForm" onsubmit="return validate_form();">

    <div id="toolbar-box">
        <div class="t"><div class="t"><div class="t"></div></div></div>
        <div class="m">
            <div id="toolbar" class="toolbar" >
                <?php if ( ($user->gid > 23) OR ($user->id == $this->Record['manager_id']) ) : ?>
                    <input type="submit" onclick="validate_form_err();" name="no" class="button" value="Ошибочный заказ" />
                    <input type="submit" onclick="validate_form_no();" name="no" class="button" value="Отказались от заказа" />
                    <input type="submit" name="new" class="button" value="Новый проект" onclick="validate_form_new();" />
                <?php endif; ?>
               
                <?php if ( ($user->gid > 23) OR ($user->id == $this->Record['manager_id'])  ) : ?>

                    <!--<input type="submit" onclick="validate_form_ys();" name="ys" class="button" value="Заказ в работе" />
                    <input name="release" list="releases" onclick="this.value='';" value="Укажите номер проекта"/>
                    <?php //echo $this->release_id; ?>-->
                <?php endif; ?>
                <input type="submit" onclick="validate_form_cn();" name="cancel" class="button" value="Выйти" />

            </div>
            <div  class="header" > Ваш заказ </div>
            <div class="clr"></div>
        </div>
        <div class="b"><div class="b"><div class="b"></div></div></div>
        <div class="clr"></div>

    </div>

    <div id="element-box">
        <div class="t"><div class="t"><div class="t"></div></div></div>
        <div style="background-color: <?php echo $bcolor; ?>" class="m">


                <div id="editcell">
                    <table class="adminlist">
                        <thead>
                        </thead>
                        <tbody>


                            <tr class="row0" ><td>Заказчик: </td><td> <?php echo $this->Record['client'] ?> </td></tr>
                            <tr class="row1" ><td>Тема: </td><td> <?php echo $this->Record['tema'] ?> </td></tr>
							<tr class="row0" ><td>Разговор менеджера: </td><td> <?php 
							
							if ( isset($this->Record['media_file']) AND strlen($this->Record['media_file']) > 5 ){
									echo $linkZ1.$this->Record['media_file'].$linkZ2.$linkZ_YES.$linkZ3;
									echo ' <input type="submit" onclick="validate_form_del_media_file();" name="sent" class="button" value="Удалить запись" />';
							}
							else echo $linkZ_NO;
							
							
							?> </td></tr>
							<tr class="row1" ><td>Разговор коммерческого директора: </td><td> <?php 
							
							if ( isset($this->Record['media_file2']) AND strlen($this->Record['media_file2']) > 5 ){
									echo $linkZ1.$this->Record['media_file2'].$linkZ2.$linkZ_YES.$linkZ3;
									echo ' <input type="submit" onclick="validate_form_del_media_file2();" name="sent" class="button" value="Удалить запись" />';
							}
							else echo $linkZ_NO;
							
							
							?> </td></tr>
                            <tr class="row0" ><td>Контакты заказчика: </td><td> <?php echo $this->Record['telefon'] ?> </td></tr>
                            <tr class="row1" ><td>Заказ создал: </td> <td><?php echo $this->Record['creator_name'] ?>  </td></tr>
                            <tr class="row0" ><td>Дата обращения: </td> <td><?php echo $this->Record['creator_date'] ?>  </td></tr>
                            <tr class="row1" ><td>Статус проекта: </td> <td><?php echo $this->Record['statustext'] ?>  </td></tr>
                            <tr class="row0" ><td>Дата устаноки статуса: </td> <td><?php echo $this->Record['statusdata'] ?>  </td></tr>
							<tr class="row1" ><td>Рентабильность: </td><td> 
														<textarea 
																name="rentabelnost" class="inputbox"  rows="4" cols="60" alt="рентабильность">
																<?php echo $this->Record['rentabelnost'] ?>
														</textarea>
							<input type="submit" onclick="validate_form_rentabelnost();" name="sent" class="button" value="Сохранить" />							
							 </td></tr>
                            <?php if ($user->gid > 23) : ?>
                                <tr class="row1" ><td>Менеджер: </td><td> <?php echo ringclientHTML::getUserName($this->Record['manager_id']) ?> </td></tr>
                            <?php endif; ?>

                        </tbody>
                    </table>
						<?php if ( !isset($this->Record['media_file']) OR strlen($this->Record['media_file']) <6 )	 : ?>
							Запись разговора менеджера: 
							<input placeholder="ссылка на запись"
								name="media_file" class="inputbox" type="text" size="18" maxlength="255" alt="ссылка на запись"/>
							<input type="submit" onclick="validate_form_media_file();" name="sent" class="button" value="Сохранить" />							
							<br />
						<?php endif; ?>
						<?php if ( !isset($this->Record['media_file2']) OR strlen($this->Record['media_file2']) <6 )	 : ?>
							Запись разговора коммерческого директора: 
							<input placeholder="ссылка на запись"
								name="media_file2" class="inputbox" type="text" size="18" maxlength="255" alt="ссылка на запись"/>
							<input type="submit" onclick="validate_form_media_file2();" name="sent" class="button" value="Сохранить" />
							
							<br />
							<br />
						<?php endif; ?>
						
                    <?php if ($user->gid > 23) : ?>	
                        <?php echo $this->managerList;?>
                        <input type="submit" onclick="validate_form_sv();" name="sent" class="button" value="Отправить" />						
                    <?php endif; ?>
                </div>

             </div>
        <div class="b"><div class="b"><div class="b"></div></div></div>
    </div>
    <input type="hidden" name="text" value="" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="Itemid" value="<?php echo $this->itemid;  ?>" />


                    
<?php // if ($this->userGroup['group_id'] == 10) : ?>
            <h3>Кнопки ниже не работают</h3>
    <input type="submit" onclick="" name="no" class="button" value="Указать проект" />

        <?php // endif; ?>
</form>
