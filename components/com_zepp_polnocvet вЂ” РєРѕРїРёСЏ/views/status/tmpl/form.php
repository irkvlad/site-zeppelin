<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
?>

<script type="text/javascript">

    function validate_form() {
        if (document.adminForm.task.value) {
            return true;
        }
        return false;
    }

    function skip(){
        document.adminForm.task.value = "выход";
        adminForm.submit();
        return true;
    }

    function setBrack(){
        var s = prompt("Коментарий", "");
        document.adminForm.task.value = "Брак";
        document.adminForm.text.value = s;
        adminForm.submit();
        return true;
    }

    function setComplekt(){
        document.adminForm.task.value = "Готово";
        adminForm.submit();
        return true;
    }

    function validate_complaint(){
        var s = prompt("Текст жалобы", "");
        if (s) complaint(s);
        return false;
    }
    //--></script>

<div ></div>

<script>
    jQuery.noConflict();

    function complaint(s) //&format=raw
    {
        jQuery.ajax({
            url: "index.php?option=com_zepp_polnocvet&view=main&task=complaint&text=" + s + "&id=<?php echo $this->record->id; ?>" ,
            cache: false,
            success: function(html){
                jQuery("#complaint").html(html);
            }
        });
    }
</script>

<form action="index.php?option=com_zepp_polnocvet&view=main"
      onsubmit="return validate_form();" method="post" name="adminForm" >

    <div id="toolbar-box">
        <div class="t"><div class="t"><div class="t"></div></div></div>
        <div class="m">
            <div id="toolbar" class="toolbar">
                    <input type="button" onclick="skip();" name="send" class="button" value="Выход" />
                <?php if ( ($this->user['usergid'] >= 23) ) : ?>
                    <input style="" type="button" name="send" class="button" value="Жалоба" onclick="validate_complaint();"/>
                    <input type="button" onclick="setBrack();" name="send" class="button" value="Брак" />
                    <input type="button" onclick="setComplekt();" name="send" class="button" value="Готово" />
                <?php endif; ?>
            </div>
            <div  class="header" >
                Заказ <?php switch( $this->record->status ) {
                            case '0': echo ' не готов'; break;
                            case '1': echo ' выполнен, отправленно уведомление о готовности'; break;
                            case '2': echo ' получен, притензий нет'; break;
                            case '3': echo ' выполнен, выявлен брак'; break;
                            case '4': echo ' выполнен, брак устранен'; break;
                        } ?>
            </div>
            <div class="clr"></div>
        </div>
        <div class="b"><div class="b"><div class="b"></div></div></div>
        <div class="clr"></div>

    </div>
    <div id="element-box">
        <div class="t"><div class="t"><div class="t"></div></div></div>
        <div class="m">
            <div class="checkboxs">

<table>
    <tr><td>Менеджер:</td><td> <?php echo polnocvetHTML::getUserName($this->record->manager_id); ?></td></tr>
    <tr><td>Дата поступления заказа:</td><td> <?php echo $this->record->date_load; ?></td></tr>
    <tr><td>Место нахождение файла:</td><td> <?php echo $this->record->link; ?></td></tr>
    <tr><td>Имя файла: </td><td><?php echo $this->record->name_file; ?></td></tr>
    <tr><td>Должно быть готово:</td><td> <?php echo $this->record->set_date; ?></td></tr>
    <tr><td>Кто печатал: </td><td><?php echo polnocvetHTML::getUserName($this->record->teh_admin); ?></td></tr>
    <tr><td>Уведомление о готовности: </td><td> <?php echo $this->record->realis_date?></td></tr>
    <tr><td>Выявлен брак:  </td><td><?php if ( $this->record->brack_text === null ) echo "Не выявленно";
                            else echo '<b style="color:red;">'.$this->record->brack_text.'</b>'; ?></td></tr>
    <tr><td>Жалобы:  </td><td id="complaint"><?php if ( $this->record->complaint === null ) echo "Отсутствует";
            else echo '<b style="color:red;">'.$this->record->complaint.'</b>'; ?></td></tr>
    
    <tr><td>Статус установлен: </td><td><?php echo $this->record->set_status; ?></td></tr>
    <tr><td>Проект: </td><td><?php echo $this->record->project_id; ?></td></tr>
</table>

    </div><div class="clr"></div></div>
        <div class="b"><div class="b"><div class="b"></div></div></div>
    </div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="text" value="" />
    <input type="hidden" name="id" value="<?php echo $this->record->id; ?>" />
    <input type="hidden" name="limitstart" value="<?php echo $limitstart  ?>" />
    <input type="hidden" name="Itemid" value="<?php echo $this->itemid;  ?>" />
</form>
<?php //if ($this->userGroup['group_id'] == 10) : ?>
<!--
<h3>Кнопки ниже не работают</h3>

<input type="submit" onclick="" name="no" class="button" value="Указать проект" />
-->
<?php //endif; ?>

<script type="text/javascript">
    changeDoc();
</script>
