<?php  defined('_JEXEC') or die('Restricted access'); 
$user = JFactory::getUser();
$linkM1 = '<button onclick="document.location.assign( \'index.php?option=com_zepp_ringclient&view=landing&task=getzakaz&layout=form&id=';
//                                         http://zepp/index.php?option=com_zepp_ringclient&view=ringclient&layout=form
$linkM2= ' \')" >Мое</button>  ';

$linkA1 = '<button onclick="document.location.assign( \'index.php?option=com_zepp_ringclient&view=landing&layout=form&id=';//                                        
$linkA2= ' \')" >Править</button>  ';
$ListF=8;

$linkB1 = '<a href="index.php?option=com_zepp_ringclient&view=landing&layout=form&id=';//
$linkB2= ' " ><b>';
$linkB3= '</b></a>';

$linkZ1 = '<a href="';//
$linkZ2= ' " ><b>';
$linkZ_YES= 'Послушать разговор';
$linkZ_NO= '';
$linkZ3= '</b></a>';

$date = JFactory::getDate();
$datestr = $this->startdate;    //JRequest::getVar('startdate', $date->toFormat ('01.%m.%Y') );
$dateend = $this->enddate;      //JRequest::getVar('enddate', $date->toFormat ('%d.%m.%Y') );

$creator = JRequest::getVar('creator',0,'int');

$bcolor         = "#fff";
$statuscolorYes = '#C0EAB9';
$statuscolorNo  = '#CCD5EA';
$statuscolorErr = '#F9E8E8'

?>
<script type="text/javascript">
<!--
    function validate_form_no() { document.adminForm.task.value = "Отказались от заказов"; return true;   }

    function validate_form_ys() { document.adminForm.task.value = "Заказы в работе"; return true; }
    //-->
</script>

<!-- Шапка -->
<div id="toolbar-box">

        <div class="t"><div class="t"><div class="t"></div></div></div>
        <div class="m">
            <form action="index.php?option=com_zepp_ringclient&view=landing&layout=default" method="post" name="adminForm">
                <div  class="header" >Заказы у менеджеров</div>
                <div id="toolbar" class="toolbar">

                        <table border="0" bordercolor="#000" ><tr><td>Цвета строк:</td><td bgcolor="<?php echo $statuscolorYes; ?>" >Ушли в работу</td>
                                <td bgcolor="<?php echo $statuscolorNo; ?>" >Не состоялись</td><td bgcolor="<?php echo $statuscolorErr; ?>" >Ошибочные</td></tr></table>
                        <center><h2>Что бы зайти в заказ кликните по теме заказа</h2></center>
                        <?php if ( ($user->gid > 23)  ) : ?>
                            <input style="float:left;" type="submit" onclick="validate_form_no();" name="no" class="button" value="Отказались от заказов" />
                            <input style="float:left;" type="submit" onclick="validate_form_ys();" name="ys" class="button" value="Заказы в работе" />
                        <?php endif; ?>
                        Выбрать заказы (которые):
                        <select name="status" id="status" >
                            <option value="0" >Все</option>
                            <option value="1" >Без решения</option>
                            <option value="2" >Ушли в работу</option>
                            <option value="3" >Не состоялись</option>
                            <option value="4" >Имеют решение</option>
                            <option value="5" >Ошибочные</option>
                        </select>
                        <script>document.querySelector('select').value='<?php echo $this->status; ?>';</script>

                        Отчет по создателям <input type="checkbox" name="creator" value="1"><br>
                        <?php

                        echo JHTML::_('calendar', $value = $datestr, $name='startdate', $id='startdate', $format = '%d.%m.%Y', $attribs = 'size="10"');
                        echo JHTML::_('calendar', $value = $dateend, $name='enddate', $id='enddate', $format = '%d.%m.%Y', $attribs = 'size="10"');
                        ?>
                        <?php echo JText::_('<strong>Поиск всех вхождений строки:</strong>&nbsp;&nbsp;').$this->search.'&nbsp;&nbsp;' ?>
                        <input class="text_area" type="text" name="searchall" id="searchall" size="20" maxlength="40" value=""
                            onclick="this.value=''">
                        <?php echo $this->managerList;?>
                        <input type="submit" name="submit" class="button" value="Показать" />
                        <input type="hidden" name="boxchecked" value="0" />
                        <input type="hidden" name="task" value="" />
                        <input type="hidden" name="Itemid" value="<?php echo $this->itemid;  ?>" />
                </div>
                <div class="clr"></div>
            </form>
        </div>
        <div class="b"><div class="b"><div class="b"></div></div></div>
        <div class="clr"></div>

</div>
<!-- Шапка конец -->

<div id="element-box">
    <div class="t"><div class="t"><div class="t"></div></div></div>
    <div class="m">
    <?php if ($creator <> 1) :  ?>
<!-- // Выборка обычная -->
                    <table class="adminlist">
                        <thead>
                            <tr>
                            <?php if ( ($user->gid > 23)  ) : ?>
                                <th width="5"><?php echo JText::_( ' ' ); ?></th>
                            <?php endif; ?>
                                <th width="5"><?php echo JText::_( '№' ); ?></th>
                                <th><?php echo JText::_( 'Дата' ); ?></th>
                                <th><?php echo JText::_( 'Клиент' ); ?></th>
                                <th><?php echo JText::_( 'Тема' ); ?></th>
								<th><?php echo JText::_( 'Разговор менеджера' ); ?></th>
                                <th><?php echo JText::_( 'Телефон' ); ?></th>
                                <th><?php echo JText::_( 'Создал' ); ?></th>
                                <th><?php echo JText::_( 'Менеджер' ); ?></th>
                                <th><?php echo JText::_( 'В работе' ); ?></th>
								<th><?php echo JText::_( 'Разговор коммерческого директора' ); ?></th>
								<th><?php echo JText::_( 'рентабильность' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                    <?php
                        $k = 0;
                        for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
                            $row = &$this->items[$i];
                            $bcolor='#fff';
                            if ($row->status == 2) $bcolor=$statuscolorNo;
                            if ($row->status == 1) $bcolor=$statuscolorYes;
                            if ($row->status == 5) $bcolor=$statuscolorErr;
                    ?>
                            <tr style="background-color:<?php echo $bcolor; ?>" class="<?php echo "row$k"; ?>">
                            <?php if ( ($user->gid > 23)  ) : ?>
                                <?php $checked 	= JHTML::_('grid.id',   $i, $row->id ); ?>
                                <td><?php echo $checked; ?></td>
                            <?php endif; ?>
                                <td><?php echo $i+1 ; //$row->id; ?></td>
                                <?php
                                   $date = JFactory::getDate( $row->creator_date);
                                   $datestr = $date->toFormat ('(%H:%M)%d.%m.%Y');
                                ?>
                                <td><?php echo $datestr; ?></td>
                                <td><?php echo $row->client; ?></td>
                                <td><?php echo $linkB1.$row->id.$linkB2.$row->tema.$linkB3; ?></td>
								<td><?php 
								if ( isset($row->media_file) AND strlen($row->media_file) > 5 )
									echo $linkZ1.$row->media_file.$linkZ2.$linkZ_YES.$linkZ3; 
								else echo $linkZ_NO;
								?></td>
                                <td><?php echo $row->telefon; ?></td>
                                <td><?php echo $row->creator_name; ?></td>
                                <td><?php echo ringclientHTML::getUserName($row->manager_id); ?></td>
                                <td><?php
                                          $date = JFactory::getDate($row->manger_data);
                                          $datestr = $date->toFormat ('%d.%m.%Y');
                                          echo $datestr ?></td>
								<td><?php 
								if ( isset($row->media_file2) AND strlen($row->media_file2) > 5 )
									echo $linkZ1.$row->media_file2.$linkZ2.$linkZ_YES.$linkZ3; 
								else echo $linkZ_NO;
								?></td>
								<td><?php echo $row->rentabelnost; ?></td>
                            </tr>
                        <?php
                            $k = 1 - $k;
                        }
                        ?>

                        </tbody>
                        <tfoot>
                           <form action="index.php" method="post" name="paginationForm">

                                    <tr>
                                      <td colspan="9"><?php echo $this->pagination->getListFooter();?></td>
                                    </tr>
                               <input type="hidden" name="option" value="com_zepp_ringclient" />
                               <input type="hidden" name="view" value="landing" />
                               <input type="hidden" name="layout" value="default" />
                               <input type="hidden" name="Itemid" value="<?php echo $this->itemid;  ?>" />
                          </form>
                        </tfoot>
                    </table>
    <?php endif; ?>


<!-- // Отчет по создателям -->
    <?php if ($creator == 1) :  ?>
                    <table class="adminlist">
                        <thead>
                            <tr>
                                <th width="5"><?php echo JText::_( '№' ); ?></th>
                                <th><?php echo JText::_( 'Создатель' ); ?></th>
                                <th><?php echo JText::_( 'Количество' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $k = 0;
                            $sum_kol=0;

                            for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
                                $row = &$this->items[$i];
                                ?>
                                <tr class="<?php echo "row$k"; ?>">
                                    <td><?php echo $i+1 ; //$row->id; ?></td>
                                    <td><?php echo $row->creator_name; ?></td>
                                    <td><?php echo $row->col;
                                                    $sum_kol=$sum_kol+$row->col;
                                                    ?></td>
                                </tr>
                                <?php
                                $k = 1 - $k;
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <form action="index.php" method="post" name="paginationForm">

                                <tr>
                                  <td colspan="2"><?php echo $this->pagination->getListFooter();?></td><td><?php echo 'Всего заказов: '.$sum_kol; ?> всего (без ошибочных)</td>
                                </tr>
                                <input type="hidden" name="option" value="com_zepp_ringclient" />
                                <input type="hidden" name="view" value="landing" />
                                <input type="hidden" name="layout" value="default" />
                                <input type="hidden" name="Itemid" value="<?php echo $this->itemid;  ?>" />

                                    <!--<input type="hidden" name="searchall" value="ваичвичавиапчи" />
                                <input type="hidden" name="controller" value="ringclient" />-->
                            </form>
                        </tfoot>
                    </table>
        <?php endif; ?>

    </div>
    <div class="b"><div class="b"><div class="b"></div></div></div>
</div>

