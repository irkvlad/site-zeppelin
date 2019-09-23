<?php defined('_JEXEC') or die('Restricted access'); 
$user = JFactory::getUser();
$linkZ1 = '<a href="';//
$linkZ2= ' " ><b>';
$linkZ_YES= 'Послушать разговор';
$linkZ_NO= '';
$linkZ3= '</b></a>';
?>
<script type="text/javascript"><!--
    function validate_form_send(){

        if ( document.adminForm.manager.value == 0 ){
                    alert ( "Вы не выбрали менеджера" );
                    return false;
        }
        document.adminForm.text.value = '';
        document.adminForm.task.value = "editzakaz";
        return true;
    }

    function validate_form_no() {
        var s = prompt("Коментарий", "");
        document.adminForm.text.value = s;
        document.adminForm.task.value = "Ошибочный заказ";
        return true;
    }

    function validate_form() {
        if (document.adminForm.task.value) {
            return true;
        }
        return false;
    }
	
	function validate_form_media_file() {
		document.adminForm.text.value = 'save_media_file';
		document.adminForm.task.value = "save_media_file";
    return true;
}
//--></script>

<form action="index.php?option=com_zepp_ringclient&view=ringclient&id=<?php echo $this->Record['id'] ?> "
              onsubmit="return validate_form();" method="post" name="adminForm">

    <div id="toolbar-box">
        <div class="t"><div class="t"><div class="t"></div></div></div>
        <div class="m">
            <div id="toolbar" class="toolbar">
                <?php if ( ($user->gid > 23) ) : ?>
                    <input type="submit" onclick="validate_form_no();" name="no" class="button" value="Ошибочный заказ" />
                <?php endif; ?>
            </div>
            <div  class="header" > Ваш заказ </div>
            <div class="clr"></div>
        </div>
        <div class="b"><div class="b"><div class="b"></div></div></div>
        <div class="clr"></div>

    </div>
    <div id="element-box">
        <div class="t"><div class="t"><div class="t"></div></div></div>
        <div class="m">

                <div id="editcell">
                    <table class="adminlist">
                        <thead>
                        </thead>
                        <tbody>
                            <!--Array (
                            [id] =>
                            [manager_id] =>
                            [client] => Клиент
                            [creator_id] => кто создал
                            [creator_name] => кто создал
                            [telefon] => 12321431
                            [tema] =>
                            [creator_date] => Когда создали
                            [manger_data] => когда взял менеджер
                            [project_id] => ) -->

                            <tr class="row0" ><td>Заказчик: </td><td> <?php echo $this->Record['client'] ?> </td></tr>
                            <tr class="row1" ><td>Тема: </td><td> <?php echo $this->Record['tema'] ?> </td></tr>
							<tr class="row1" ><td>Разговор: </td><td> <?php 
							
							if ( isset($this->Record['media_file']) AND strlen($this->Record['media_file']) > 5 )
									echo $linkZ1.$this->Record['media_file'].$linkZ2.$linkZ_YES.$linkZ3; 
								else echo $linkZ_NO;
							
							
							?> </td></tr>
                            <tr class="row0" ><td>Телефон заказчика: </td><td> <?php echo $this->Record['telefon'] ?> </td></tr>
                            <tr class="row1" ><td>Заказ создал: </td> <td><?php echo $this->Record['creator_name'] ?>  </td></tr>
                            <tr class="row0" ><td>Дата обращения: </td> <td><?php echo $this->Record['creator_date'] ?>  </td></tr>
                            <?php if ($user->gid > 23) : ?>
                                <tr class="row1" ><td>Менеджер: </td><td> <?php echo ringclientHTML::getUserName($this->Record['manager_id']) ?> </td></tr>

                            <?php endif; ?>
                        </tbody>
                    </table>

                    <?php if ($user->gid > 23) : ?>
						<?php if ( !isset($this->Record['media_file']) AND strlen($this->Record['media_file']) <6 )	 : ?>
							Запись разговора: 
							<input placeholder="ссылка на запись"
								name="media_file" class="inputbox" type="text" size="18" maxlength="255" alt="ссылка на запись"/>
							<input type="submit" onclick="validate_form_media_file();" name="sent" class="button" value="Сохранить" />
							<br />
							<br />
						<?php endif; ?>
                        <?php echo $this->managerList;?>
                        <input type="submit" onclick="validate_form_send();" name="submit" class="button" value="Отправить" />
                    <?php endif; ?>
                </div>

        </div>
        <div class="b"><div class="b"><div class="b"></div></div></div>
    </div>
    <input type="hidden" name="text" value="" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="Itemid" value="<?php echo $this->itemid;  ?>" />
 </form>
        <?php //if ($this->userGroup['group_id'] == 10) : ?>
            <h3>Кнопки ниже не работают</h3>

            <input type="submit" onclick="" name="no" class="button" value="Указать проект" />

        <?php //endif; ?>