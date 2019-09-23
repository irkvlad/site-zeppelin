<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$MAX_FENIKS = 12;
$MAX_ROLAND = 12;
$MAX_TOTALUF = 12;
$MAX_TOTALLUM = 12;

$BgCOLOR_BRACK =        "#E11C0E"; //"#E11C0E";
$BgCOLOR_OVERDUE =      "#B3440F";//"#B3440F";
$BgCOLOR_FINISHED =     "#9ffcca";//"#050701";
$BgCOLOR_TODAY =        "#fff";//#fff";
$BgCOLOR_NEW =          "#fff";//"#fff";

$COLOR_BRACK =      "#fdf4cb";//"#fdf4cb";
$COLOR_OVERDUE =    "#003";//"#B3440F";
$COLOR_FINISHED =   "#221B4B";//"#050701";
$COLOR_TODAY =      "#000";//"#000";
$COLOR_NEW =        "#2301FF";//"#BBD8AC";


$date = JFactory::getDate();
$datestr = $this->startDate;    //JRequest::getVar('startdate', $date->toFormat ('01.%m.%Y') );
$dateend = $this->endDate;
//$dateNow = $date->toFormat ('%Y-%m-%d');
$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
?>

<script type="text/javascript"><!--
    function changeCompany(){
        document.adminForm.task.value = "company";

        adminForm.submit();
        return true;
    }

    function setStatus(id){
        document.adminForm.task.value = "Статус";
        document.adminForm.text.value = id;
        adminForm.submit();
        return true;
    }
    
    function sentComplect(id){
        document.adminForm.task.value = "Печать готова";
        document.adminForm.text.value = id;
        document.adminForm.submit();
        return true;
    }

    function validate_form_file() {
        //var s = prompt("Коментарий", "");
        //document.adminForm.text.value = s;
        document.adminForm.task.value = "новый файл";
        return true;
    }

    function validate_form_set(setdate, id) {
        document.adminForm.task.value = "Поставить дату";
        document.adminForm.text.value = id;
        document.adminForm.setsetdite.value = setdate.value;
        document.adminForm.submit();
        return true;
    }

    function validate_form() {
        if (document.adminForm.task.value) {
            return true;
        }
        return false;
    }

    function validate_new(){
        document.adminForm.task.value = "новые";
        document.adminForm.text.value = "Показать новые";
        return true;
    }

    function validate_today(){
        document.adminForm.task.value = "сегодня";
        document.adminForm.text.value = "Показать сегодня";
        return true;
    }
    function validate_overdue(){
        document.adminForm.task.value = "просроченные";
        document.adminForm.text.value = "Показать просроченные";
        return true;
    }
    function validate_brack(){
        document.adminForm.task.value = "брак";
        document.adminForm.text.value = "Показать брак";
        return true;
    }
    function validate_finished(){
        document.adminForm.task.value = "готовые";
        document.adminForm.text.value = "Показать готовые";
        return true;
    }

    function validate_cansell(){
        return true;
    }
    //--></script>



<form action="index.php?option=com_zepp_polnocvet&view=main ?> "
      onsubmit="return validate_form();" method="post" name="adminForm">

    <div id="toolbar-box">
        <div class="t"><div class="t"><div class="t"></div></div></div>
        <div class="m">
            <div id="toolbar" class="toolbar">
                <?php
                    echo $this->listCompany;
                 //if ( ($this->user['usergid'] >= 23) ) : ?>
                 <!--   <input type="submit" onclick="validate_form_file();" name="file" class="button" value="Добавить файл для печати" /> -->
                <?php //endif; ?>                
                <input style="border: 6px solid <? echo $COLOR_NEW; ?>;" type="submit" name="look" class="button" value="Новые" onclick="validate_new();"/>
                <input style="border: 6px solid <? echo $BgCOLOR_TODAY; ?>;" type="submit" name="look" class="button" value="Сегодня" onclick="validate_today();"/>
                <input style="border: 6px solid <? echo $BgCOLOR_OVERDUE; ?>" type="submit" name="look" class="button" value="Просроченные" onclick="validate_overdue();"/>
                <input style="border: 6px solid <? echo $BgCOLOR_BRACK; ?>;" type="submit" name="look" class="button" value="Брак" onclick="validate_brack();"/>
                <input style="border: 6px solid <? echo $BgCOLOR_FINISHED; ?>;" type="submit" name="look" class="button" value="Готовые" onclick="validate_finished();"/>
                <input style="" type="submit" name="look" class="button" value="Сброс" onclick="validate_cansell();"/>
                <br><br>
                <?php
                    echo JHTML::_('calendar', $value = $datestr, $name='startDate', $id='startDate', $format = '%d.%m.%Y', $attribs = 'size="10"');
                    echo " | ";
                    echo JHTML::_('calendar', $value = $dateend, $name='endDate', $id='endDate', $format = '%d.%m.%Y', $attribs = 'size="10"');
                    echo " | ";
                    echo $this->managerList;
               
                ?>
                <input type="submit" name="get" class="button" value="Показать" />
            </div>
            <div  class="header" > Полноцветная печать </div>
            <div class="clr"></div>
        </div>
        <div class="b"><div class="b"><div class="b"></div></div></div>
        <div class="clr"></div>

    </div>
 <!--
    <div id="element-box">
        <div class="t"><div class="t"><div class="t"></div></div></div>
        <div class="m">
            <div id="editcell">
                <h3>График загруженности печати</h3>
                <table class="adminlist" >
                    <thead>

                        <tr><td>Феникс: </td> <?php
                                foreach ($this->totalFeniks as $feniks){
                                    $styl="";
                                    if($MAX_FENIKS < $feniks->date ) $styl = "style='color: red';";
                                        echo " <td> Дата: <b>".$feniks->set_date."</b>; всего: <b $styl>".$feniks->date.'</b> </td> ';
                                } ?>
                        </tr>
                        <tr><td>Роланд: </td> <?php
                        foreach ($this->totalRoland as $roland){
                            $styl="";
                            if($MAX_ROLAND < $roland->date ) $styl = "style='color: red';";
                            echo " <td> Дата: <b>".$roland->set_date."</b>; всего: <b $styl>".$roland->date.'</b> </td> ';
                        } ?>
                        </tr>
                        <tr><td>Уф: </td> <?php
                            foreach ($this->totalUF as $totalUF){
                                $styl="";
                                if($MAX_TOTALUF < $totalUF->date ) $styl = "style='color: red';";
                                echo " <td> Дата: <b>".$totalUF->set_date."</b>; всего: <b $styl>".$totalUF->date.'</b> </td> ';
                            } ?>
                        </tr>
                        <tr><td>Ламинация: </td> <?php
                            foreach ($this->totalLum as $totalLum){
                                $styl="";
                                if($MAX_TOTALLUM < $totalLum->date ) $styl = "style='color: red';";
                                echo " <td> Дата: <b>".$totalLum->set_date."</b>; всего: <b $styl>".$totalLum->date.'</b> </td> ';
                            } ?>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="b"><div class="b"><div class="b"></div></div></div>
    </div>
-->
    <div id="element-box">
        <div class="t"><div class="t"><div class="t"></div></div></div>
        <div class="m">
            <div id="editcell">
                <table class="adminlist">
                    <thead>
                        <tr>
                            <td></td>
                            <td>Когда поступил: </td>
                            <td>Сылка на файл: </td>
                            <td>Имя файла: </td>
                            <td>Менеджер: </td>
                            <td>Напечатаем к дате: </td>
                            <td>Дата готовности: </td>
                            <td>Статус: </td>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->polnocvet as $record){
                        $bracks = $record->brack_text;
                        $brack = explode('|', $bracks);
                        $bgColor=$color="";
                        if ($record->status == 3 )                                                  { $bgColor = $BgCOLOR_BRACK;      $color = $COLOR_BRACK;}
                        else if ( !is_null ($record->set_date) AND $record->set_date < $dateNow )   { $bgColor = $BgCOLOR_OVERDUE;    $color = $COLOR_OVERDUE;}
                        else if ( $record->status == 2 OR $record->status == 4 )                    { $bgColor = $BgCOLOR_FINISHED;   $color = $COLOR_FINISHED;}
                        else if ( is_null ($record->set_date) )                                            { $bgColor = $BgCOLOR_NEW;        $color = $COLOR_NEW;}
                        else if ( $record->set_date == $dateNow )                                   { $bgColor = $BgCOLOR_TODAY;      $color = $COLOR_TODAY;}
                        ?>
                        <tr style="background-color: <? echo $bgColor; ?>;color: <? echo $color; ?>;" >
                            <td style="background-color: <?php echo $this->colorCompany[$record->company]; ?>" > &nbsp </td>
                            <!-- Дата -->
                            <td><?php echo $record->date_load; ?> </td>

                            <!-- Сылка -->
                            <td><?php
                                $pos = strpos($record->link, "Смотри файл на сервере:");
                                if ( strpos($record->link, "Смотри файл на сервере:") === false )  echo $record->link;
                                else echo  $record->link;
                                ?> </td>

                            <!-- Файл -->
                            <td><?php echo $record->name_file; ?>  </td>

                            <!-- Менеджер -->
                            <td><?php echo polnocvetHTML::getUserName($record->manager_id); ?>  </td>

                            <!-- Будет исполнено -->
                            <?php  if(!$record->set_date AND $this->user['polnocvet']) {
                                echo
                                '<td>'.JHTML::_('calendar', $value = '', $name='setdate'.$record->id, $id='setdate'.$record->id, $format = '%Y.%m.%d', $attribs = 'onchange="validate_form_set(this,'.$record->id.');" size="10"').'</td>';
                            }else { ?>
                                <td><?php echo $record->set_date; ?>  </td>
                             <?php } ?>

                            <!-- Дата исполнения -->
                            <?php
                                switch( $record->status ) {
                                    case '0':
                                        echo '<td>';
                                        if( $this->user['polnocvet'])
                                                echo '<button onclick="sentComplect('.$record->id.')" class="button" >Сообщить о готовности</button>';
                                        echo '</td>';
                                        break;
                                    case '1':
                                        echo "<td>Отправленно уведомление о готовности<br>$record->realis_date</td>";
                                        break;
                                    case '2':
                                        echo '<td>'.$record->set_status.'</td>';
                                        break;
                                    case '3':
                                        echo '<td>'.$record->set_status.'</td>';
                                        break;
                                    case '4':
                                        echo '<td>'.$record->set_status.'</td>';
                                        break;
                            }; ?>

                            <!-- Статус -->
                            <?php
                                switch( $record->status ) {
                                    case '0':
                                        echo '<td>не готов';
                                        break;
                                    case '1':
                                        echo '<td>не готов';
                                        break;
                                    case '2':
                                        echo '<td><b>Готов</b>';
                                        break;
                                    case '3':
                                        echo '<td><b style="color:red;">Брак: '.$brack[count($brack) - 1].'</b>';
                                        break;
                                    case '4':
                                        echo '<td><b>Брак устранен; Готов</b>';
                                        break;
                                }
                                echo '</td><td>';

                                if ($this->user['usergid'] >= 23)  echo '<button onclick="setStatus('.$record->id.')" class="button" >Изменить</button>';
                                echo '</td>';
                            ?>




                        </tr>
                    <?php } ?>


                    </tbody>
                    <input type="hidden" name="text" value="" />
                    <input type="hidden" name="setsetdite" value="" />
                    <input type="hidden" name="task" value="go" />
                    <input type="hidden" name="Itemid" value="<?php echo $this->itemid;  ?>" />
                    <input type="hidden" name="limitstart" value="<?php echo $limitstart  ?>" />
</form>
<tfoot>
<form action="index.php" method="post" name="paginationForm">

    <tr>
        <td colspan="9"><?php echo $this->pagination->getListFooter();?></td>
    </tr>
    <input type="hidden" name="option" value="com_zepp_polnocvet" />
    <input type="hidden" name="view" value="main" />
    <input type="hidden" name="layout" value="default" />
    <input type="hidden" name="Itemid" value="<?php echo $this->itemid;  ?>" />
    
</form>
</tfoot>

                </table>
            </div>
        </div>
        <div class="b"><div class="b"><div class="b"></div></div></div>
    </div>


