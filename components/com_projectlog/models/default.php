<?php

/**
 *   Вид Доска
 * // $date = "ГГГГ-ММ-ДД";
 * //$d = new DateTime($date);*/
//$d->modify("-1 day");
//echo $d->format("Y-m-d");


defined('_JEXEC') or die('Restricted access');

$weekCol = JRequest::getVar('week');
if ($weekCol == '')
{
	$weekCol = 3;
}

$day   = JRequest::getVar('day');
$toDay = JHTML::_('date', $date = null, $format = '%Y-%m-%d', $offset = null);

if ($day == '')
{
	$date_toworc = strtotime($this->toworc[0]->release_date);
	$date_onworc = strtotime($this->onworc[0]->deployment_to);
	$t_date      = new DateTime($toDay);
	$id_day      = $t_date->format("w") - 1;
	if ($id_day == -1)
	{
		$id_day = 6;
	};
	$t_date->modify("-" . $id_day . " day");
	$day = $t_date->format("Y-m-d");

}
else
{

	$t_date = new DateTime($day);
}


$w_date = $t_date->format("W");      // порядковы номер недели
$mCSS   = 'ff';
if (fmod($t_date->format("m"), 2) == 0)
{
	$mCSS = 'ea';
}
$lenMoon = $t_date->format("t");
$in_day  = $t_date->format("w") - 1;
if ($in_day == -1)
{
	$in_day = 6;
};


{ //***********************************
	$t_date->modify("-1 day");
	$last_day = $t_date->format("Y-m-d");
	$t_date->modify("+2 day");
	$next_day = $t_date->format("Y-m-d");
	$t_date->modify("-1 day");
} //***********************************
{//************************************
	$t_date->modify("-7 day");
	$id_day = $t_date->format("w") - 1;
	if ($id_day == -1)
	{
		$id_day = 6;
	};
	$t_date->modify("-" . $id_day . " day");
	$last_week = $t_date->format("Y-m-d");
	$t_date->modify("+" . $id_day . " day");

	$t_date->modify("+14 day");
	$id_day = $t_date->format("w") - 1;
	if ($id_day == -1)
	{
		$id_day = 6;
	};
	$t_date->modify("-" . $id_day . " day");
	$next_week = $t_date->format("Y-m-d");
	$t_date->modify("+" . $id_day . " day");
	$t_date->modify("-7 day");
}//************************************ $last_week $next_week

if ($this->toworc)
{
	{//next следующий проект
		$d = count($this->toworc) - 1;
		//$next_p=$this->toworc[$d]->release_date;

		while ((strtotime($t_date->format("Y-m-d")) + 604800 * $weekCol <= strtotime($this->toworc[$d]->release_date)) and $this->toworc[$d])
		{
			$next_p      = $this->toworc[$d]->release_date;
			$link_next_p = JRoute::_('index.php?option=com_projectlog&view=doska&week=' . $weekCol . '&day=' . $next_p . '&task=week');
			$d           = $d - 1;
		}

		$link_next_p = JRoute::_('index.php?option=com_projectlog&view=doska&week=' . $weekCol . '&day=' . $next_p . '&task=week');
	}

	{// Предыдущий проект
		$d = 0;
		while ((strtotime($t_date->format("Y-m-d")) > strtotime($this->toworc[$d]->release_date)) and $this->toworc[$d])
		{
			$last_p      = $this->toworc[$d]->release_date;
			$link_last_p = JRoute::_('index.php?option=com_projectlog&view=doska&week=' . $weekCol . '&day=' . $last_p . '&task=week');
			$d           = $d + 1;
		}

	}
}

$link_last_day  = JRoute::_('index.php?option=com_projectlog&view=doska&week=' . $weekCol . '&day=' . $last_day . '&task=week');
$link_next_day  = JRoute::_('index.php?option=com_projectlog&view=doska&week=' . $weekCol . '&day=' . $next_day . '&task=week');
$link_last_week = JRoute::_('index.php?option=com_projectlog&view=doska&week=' . $weekCol . '&day=' . $last_week . '&task=week');
$link_next_week = JRoute::_('index.php?option=com_projectlog&view=doska&week=' . $weekCol . '&day=' . $next_week . '&task=week');
$link_toDay     = JRoute::_('index.php?option=com_projectlog&view=doska&week=' . $weekCol . '&day=' . $toDay . '&task=week');
$link_end_day   = JRoute::_('index.php?option=com_projectlog&view=doska&week=' . $weekCol . '&day=' . $this->toworc[count($this->toworc) - 1]->release_date . '&task=week');
$link_fest_day  = JRoute::_('index.php?option=com_projectlog&view=doska&week=' . $weekCol . '&day=' . $this->toworc[0]->release_date . '&task=week');

$link_weekly_2 = JRoute::_('index.php?option=com_projectlog&view=doska&week=2&task=week&day=' . $day);
$link_weekly_3 = JRoute::_('index.php?option=com_projectlog&view=doska&week=3&task=week&day=' . $day);
$link_weekly_4 = JRoute::_('index.php?option=com_projectlog&view=doska&week=4&task=week&day=' . $day);
$link_weekly_6 = JRoute::_('index.php?option=com_projectlog&view=doska&week=6&task=week&day=' . $day);
$link_weekly_8 = JRoute::_('index.php?option=com_projectlog&view=doska&week=8&task=week&day=' . $day);
?>


<div class="toworc">

    <table align="center">
        <tr>
            <td width='218px'><?php if ($last_p): ?>
                    <a class="last_p"
                       href="<?php echo $link_fest_day ?>"> <?php echo $this->toworc[0]->release_date . '-Первый проект'; ?> </a>
				<?php endif; ?></td>
            <td width='170px'><?php if ($last_p): ?>
                    <a class="last_p" href="<?php echo $link_last_p ?>"> &lt;Предыдущий проек&lt; </a>
				<?php endif; ?></td>
            <td width='10px'><a class="lastweekly" href="<?php echo $link_last_week ?>">&lt;Неделя&lt;</a></td>
            <td width='10px'><a class="lastday" href="<?php echo $link_last_day ?>">&le;День</a></td>
            <td width='10px'><a class="today" href="<?php echo $link_toDay ?>">Сегодня</a></td>
            <td width='10px'><a class="nextday" href="<?php echo $link_next_day ?>">День&ge;</a></td>
            <td width='10px'><a class="nextweekly" href="<?php echo $link_next_week ?>">&gt;Неделя&gt;</a></td>
            <td width='170px'><?php if ($next_p): ?>
                    <a class="next_p" href="<?php echo $link_next_p ?>"> &gt;Следующий проект&gt; </a>
				<?php endif; ?></td>
            <td width='218px'><?php if ($next_p): ?>
                <a class="next_p"
                   href="<?php echo $link_end_day ?>"> <?php echo $this->toworc[count($this->toworc) - 1]->release_date . '-Последний проект'; ?> </a>
            </td>
			<?php endif; ?></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td><a class="twoweekly" href="<?php echo $link_weekly_2 ?>"> 2 </a></td>
            <td><a class="threeweekly" href="<?php echo $link_weekly_3 ?>"> 3 </a></td>
            <td><a class="fourweekly" href="<?php echo $link_weekly_4 ?>"> 4 </a></td>
            <td><a class="fourweekly" href="<?php echo $link_weekly_6 ?>"> 6 </a></td>
            <td><a class="fourweekly" href="<?php echo $link_weekly_8 ?>"> 8 </a></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Период автоматического обновления инфрмации - два часа</td>
        </tr>
    </table>
    <br/>
	<?php //  Вывод брака  #####################################################################################
	if ($this->brak)
	{

		$s       = 0;
		$jumpR   = 0;
		$logotip = '';
		foreach ($this->brak as $p)
		{
			if ($p->location_spec <> '')
			{
				foreach ($this->logo as $lg)
				{

					if ($lg->project_id == $p->id)
					{
						$logotip = $p->id . '/' . $lg->path;
					}
				}


				$release_date = $p->release_date;
				$shot_title   = $p->shot_title;
				$title        = $p->title;
				if ($shot_title == '') $shot_title = strtok($title, ' ');
				$proj_link = JRoute::_('index.php?option=com_projectlog&day=' . $day . '&week=' . $weekCol . '&view=project&id=' . $p->id);

				$red    = "background-color:red;z-index:100;";
				$red_id = 'id=jumpR' . $jumpR;
				$jumpR  = $jumpR + 1;


				$s  = $s + 1;
				$xy = 5 * $s + 20;
				$yx = 5 * $s + 20;

				?>
                <div onclick="this.style.zIndex=parseInt(maxIndex())+1;" class="onworck win_drag move"
                     style=<?php echo $red . "left:" . $xy . "px;top:" . $yx . "px"; ?>>
                    <strong>Выявлен брак: </strong>
                    <img style="position: absolute;" title="Выявлен брак"
                         src="components/com_projectlog/assets/images/6410746.png" width="28" height="28"
                         alt="Выявлен брак">

                    <div
						<?php echo $red_id; ?>
                            class="pointer"
                            ondblclick="document.location.assign(<?php echo "'" . $proj_link . "'"; ?>)"
                            onclick="document.getElementById(<?php echo "'br" . $s . "'"; ?>).style.display='block';
                                    document.getElementById(<?php echo "'brtr" . $s . "'"; ?>).style.display='table';
                                    document.getElementById(<?php echo "'br" . $s . "'"; ?>).style.zIndex=parseInt(maxIndex())+1;"
                            style="display: block; padding: 2px; border-top: 1px solid #000; ?>; color:#<?php echo $p->workorder_id; ?>; background-color:#<?php echo $p->projecttype; ?>">

                        <img align="left" width="50px" height="70px"
                             src="media/com_projectlog/docs/<?php echo $logotip; ?>"/>
						<?php if ($p->podrydchik <> '') echo '<img style="position: absolute;left:0;" title="Изготавливается подрядчиком" src="components/com_projectlog/assets/images/podrydchik.png" width="28" height="28" alt="Изготавливается подрядчиком">'; ?>
                        <strong><?php echo JHTML::_('date', $p->release_date, JText::_('%d.%m')); ?></strong><br/>
                        <strong><?php echo $shot_title; ?></strong><br/>
                        <!--//<strong><?php echo $p->release_id; ?></strong><br />
								<strong><?php echo $p->job_id; ?></strong><br />
								<strong><?php echo projectlogHTML::getusername($p->technicians); ?></strong><br />//-->
                        <strong><?php echo projectlogHTML::getusername($p->manager); ?></strong><br/>

                    </div>
                </div>
                <!--////============ Формирование всплывающего окна БРАК ===================parseInt(=//-->

                <div class="win_drag pointer" id="<?php echo 'br' . $s; ?>"
                     onclick="this.style.zIndex=parseInt(maxIndex())+1;"
                     ondblclick="document.location.assign(<?php echo "'" . $proj_link . "'"; ?>)"
                     style='width:500px;display:none;position:fixed;left:<?php echo $xy; ?>%;top:<?php echo $yx; ?>%;border: 3px ridge black;padding:5px;color:#<?php echo $p->workorder_id; ?>;background-color:#<?php echo $p->projecttype; ?>;'>

                    <table width="100%" id="brtr<?php echo $jumpR; ?>" style="display:none">
                        <tr>
                            <td colspan="2" valign="top" style="border: solid 3px #ccc;">
                                <span class="red">В процессе изготовления изделий был допущен брак </span><br/>
                                <strong>( Снять статус выявленного брака может только директор! )</strong><br/><br/>

                                <table style="border-top: solid 1px #ccc;border-bottom: solid 1px #ccc;"
                                       width="100% align=" center
                                ">
                        <tr>
                            <td>
                                <u>Описание выявленного брака:</u><br/><?php echo $p->location_spec; ?>
                            </td>
                        </tr>
                    </table>
                    </td></tr></table>


                    <strong><?php echo $p->title; ?></strong>
					<?php if ($p->podrydchik <> '') echo '<br /><strong>' . JText::_('PODRYDCHIK2') . '</strong> <span>' . $p->podrydchik . '</span>'; ?>


					<?php
					if (PLOG_ADMIN >= 25): $acces_mov = true; endif;


					if (DEDIT_ACCESS and $acces_mov)
					{
						; ?>
                        <form enctype="multipart/form-data" action="index.php" method="post" name="adminForm">

                            <input type="submit" style="float:right;" value="Брак устранен"/>    <!-- отправить -->
                            <input type="hidden" name="option" value="com_projectlog"/>
                            <input type="hidden" name="view" value="doska"/>
                            <input type="hidden" name="brak_msg" value=""/>
                            <input type="hidden" name="id" value="<?php echo $p->id; ?>"/>
                            <input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>"/>
                            <input type="hidden" name="task" value="brak"/>
                            <input type="hidden" name="week" value="<?php echo $weekCol; ?>"/>
                            <input type="hidden" name="day" value="<?php echo $day; ?>"/>
							<?php echo JHTML::_('form.token'); ?>
                        </form>

					<?php } ?>


                    <div>
                        <img vspace="15px" align='left' style="width:5.8cm;height:6cm;"
                             src="media/com_projectlog/docs/<?php echo $logotip; ?>"/>
						<?php if ($p->podrydchik <> '') echo '<img style="position: absolute;left:0;" title="Изготавливается подрядчиком ' . $p->podrydchik . '" src="components/com_projectlog/assets/images/podrydchik.png" width="28" height="28" alt="Изготавливается подрядчиком ' . $p->podrydchik . '">'; ?>
                        <div style="width: 245px;float:right;margin: 25px 10px;background-color:#fff;padding:5px;">
                            <span class="content_header"><?php echo JText::_('RELEASE DATE'); ?>:</span>
							<?php echo JHTML::_('date', $p->release_date, JText::_('%d.%m.%Y')); ?>
                            <span class="content_header"><?php echo JText::_('TASK NUM'); ?>:</span>
                            <span class="red2"><?php echo ($p->task_id) ? $p->task_id : '&nbsp;'; ?></span><br/>
                            <span class="content_header"><?php echo JText::_('PROJECT MANAGER'); ?>:</span>
							<?php
							if ($p->manager)
							{
								echo projectlogHTML::getusername($p->manager);
								$managerdetails = projectlogHTML::userDetails($p->manager);
								if ($managerdetails)
								{
									echo ($managerdetails->email_to) ? '<br /><a href="mailto:' . $managerdetails->email_to . '">' . $managerdetails->email_to . '</a>' : '';
									echo ($managerdetails->telephone) ? '<br />' . $managerdetails->telephone : '';
								}
							}
							else
							{
								echo '&nbsp;';
							}
							?> <br/>
                            <span class="content_header"><?php echo JText::_('PROJECT LEAD'); ?>:</span>
							<?php echo ($p->chief) ? projectlogHTML::getusername($p->chief) : '&nbsp;'; ?><br/>
                            <span class="content_header"><?php echo JText::_('TECHNICIAN'); ?>:</span>
							<?php echo ($p->technicians) ? projectlogHTML::getusername($p->technicians) : '&nbsp;'; ?>
                            <br/>
                            <div class="right_details">
                                <span class="content_header"><?php echo JText::_('JOB NUM'); ?></span><br/>
                                <!--<input type="text" class="inputbox" name="job_id" value="<?php echo $p->job_id; ?>" />-->
								<?php echo $p->job_id; ?> <br/>
                            </div>

							<?php
							$db    = JFactory::getDBO();
							$query = 'SELECT * FROM #__projectlog_docs WHERE project_id = ' . $p->id . ' ORDER BY date DESC';
							$db->setQuery($query);
							$docs = $db->loadObjectlist();
							//if( DOC_ACCESS ):  // Список документов
							if ($docs) :
								echo '<div class="right_details" >';
								echo '<div class="content_header2">' . JText::_('RELATED DOCS') . ':</div>';
								foreach ($docs as $d):
									if ($d->name == '') $d->name = $d->path;
									//$delete_doc_link = JRoute::_('index.php?option=com_projectlog&view=project&project_id='. $this->project->id . '&task=deleteDoc&id=' . $d->id);
									echo '<div class="doc_item">
											&gt;&nbsp;<a style="word-wrap: break-word" href="' . $this->doc_path . $p->id . '/' . $d->path . '" type="bin" target="_blank" class="hasTip" title="' . JText::_('DOCUMENT') . ' :: ' . JText::_('SUBMITTED BY') . ': ' . projectlogHTML::getusername($d->submittedby) . '<br />' . JText::_('FILE') . ': ' . $d->path . '<br />' . JText::_('SUBMITTED DATE') . ': ' . $d->date . '">
												' . $d->name . '
											</a>';
									//if(($this->user->id == $d->submittedby && DEDIT_ACCESS) || PLOG_ADMIN ):
									//	echo '<a href="' . $delete_doc_link . '" onclick="if(confirm(\''.JText::_('CONFIRM DELETE').'\')){return true;}else{return false;};" class="red3">'.JText::_('DELETE').'</a>';
									//endif;
									echo '</div>';
								endforeach;
								echo '</div>';
							endif; ?> </div>


                    </div>
                    <br clear="all"/>
                    <strong><?php echo $p->release_id; ?></strong>
                    <strong><?php echo projectlogHTML::getusername($p->technicians); ?></strong>
                    <div style="padding:5px;margin: 10px;background-color:#fff;" class="right_details">
                        <div class="content_header2"><?php echo JText::_('DESCRIPTION'); ?>:</div>
						<?php if ($p->description) :
							echo $p->description;
						endif; ?>
                        <span class="content_header"><?php echo JText::_('GEN LOC'); ?>:</span><br/>
						<?php if ($p->location_gen) :
							echo $p->location_gen;
						endif; ?>

                    </div>
                    <br/>

                    <button onclick="document.getElementById(<?php echo "'br" . $s . "'"; ?>).style.display='none';
                            document.getElementById(<?php echo "'brtr" . $s . "'"; ?>).style.display='none';"> Закрыть
                    </button>
                    <button onclick="document.location.assign(<?php echo "'" . $proj_link . "'"; ?>)"> Открыть</button>

                </div>

			<?php }
		}

	} ?>

    <!--//################################################################################//-->

    <table cellspacing='0px' cellpadding='0px' width="100%" border="2">
        <tr>

            <!--//####################################    Таблица проектов в работе   ############################################//-->
			<?php
			$w = $w_date + $weekCol;
			$s = 0;
			if (projectlogHelperQuery::isGroupMember(11, $this->user->get('id')) or PLOG_ADMIN >= 25): $acces_mov = true; endif;
			if ($in_day == 0)
			{
				$w = $w - 1;
			}
			for ($i = $w_date;
			$i <= $w;
			$i++)
			{

			$id_day = $t_date->format("w");
			if ($id_day == 0)
			{
				$id_day = 7;
			};
			$id_mun = $t_date->format("M");

			if ($id_day == 1)
			{
				$d1 = $lenMoon - $t_date->format("d"); // разница между текужщей днем и количеством дней в месяце

				if ((0 <= $d1 and $d1 <= 2) or (28 <= $d1 and $d1 <= 30))
				{// Если в пределах то меняем цвет таблицы, отделяя месяца по цвету
					$lenMoon = $t_date->format("t");
					if ($mCSS == 'ea')
					{
						$mCSS = 'ff';
					}
					else
					{
						$mCSS = 'ea';
					}
				}
			}

			echo "<td valign='top' class=" . $mCSS . "> Неделя " . $i . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Месяц " . projectlogHTML::timep($id_mun) .
				"<table cellspacing='0px' cellpadding='0px' width='100%' style='border: 1px solid #000;'>
	 				<tr>";      // Номер недели

			$f = 7;
			if ($i == $w_date + $weekCol)
			{
				$f = $in_day;
			}
			for ($j = $id_day;
			$j <= $f;
			$j++)
			{

			echo "<td style='border-left: 1px solid #000;'  valign='top' class=dey" . $j . ">
	 		 					День " . $t_date->format("d.m") .
				"<table align='center' cellspacing='0px' cellpadding='0px' >";  // дата по дням недели


			// Вывод проектов в работе по датам
			foreach ($this->toworc as $p)
			{
			$checked_pipl  = '';
			$ch_color_pipl = 'red';
			$checked_plan  = '';
			$ch_color_plan = 'red';
			$checked_mat   = '';
			$ch_color_mat  = 'red';
			$red           = '';
			if ($p->mat_on <> 0)
			{
				$checked_mat  = 'checked';
				$ch_color_mat = 'green';
			}
			if ($p->plan_on <> 0)
			{
				$checked_plan  = 'checked';
				$ch_color_plan = 'green';
			}
			if ($p->pipl_on <> 0)
			{
				$checked_pipl  = 'checked';
				$ch_color_pipl = 'green';
			}
			$proj_link     = JRoute::_('index.php?option=com_projectlog&day=' . $day . '&week=' . $weekCol . '&view=project&id=' . $p->id);
			$add_moov_link = JRoute::_('index.php?option=com_projectlog&view=doska&project_id=' . $p->id . '&mov=12&task=move&week=' . $weekCol . '&day=' . $day);
			$logotip       = '';

			foreach ($this->logo as $lg)
			{

				if ($lg->project_id == $p->id)
				{
					$logotip = $p->id . '/' . $lg->path;
				}
			}


			$technicians = explode(" ", projectlogHTML::getusername($p->technicians));
			$title       = $p->title;
			$shot_title  = $p->shot_title;
			if ($shot_title == '') $shot_title = strtok($title, ' ');


			if (strtotime($p->release_date) < strtotime($toDay)) $red = "red";
			if ($p->release_date == $t_date->format("Y-m-d"))
			{  //
			$s  = $s + 1;
			$xy = 5 * $s; ?>
        <tr>
            <td width='0px' valign='top'>
                <table style="border: 1px solid #000;color:#<?php echo $p->workorder_id; ?>;background-color:#<?php echo $p->projecttype; ?>">
                    <tr>
                        <td>
                            <div style="position:relative;">
                                <div style="position: absolute;left:-5px;top:15px">

                                    <span style="width:9px;hight:5px;background-color:<?php echo $ch_color_mat; ?>;border:solid 1px black;">М</span><br/><span
                                            style="width:9px;hight:5px;background-color:<?php echo $ch_color_plan; ?>;border:solid 1px black;">Ч</span><br/><span
                                            style="width:9px;hight:5px;background-color:<?php echo $ch_color_pipl; ?>;border:solid 1px black;">Л</span>
                                </div>
                            </div>

                            <script type="text/javascript">

                                shirina = 7 *<?php echo $weekCol;?>;
                                shirina2 = 56 *<?php echo $weekCol;?>;
                                shirina = parseInt((window.screen.availWidth - shirina2) / shirina + 2);
                                if (shirina < 33) shirina = 33;
                                document.write('<div style="position:relative;background-color:<?php echo $red; ?>;display:block;width:' + shirina + 'px;overflow-x:hidden"');
                            </script>

                            ondblclick="document.location.assign(<?php echo "'" . $proj_link . "'"; ?>)"
                            onclick="document.getElementById(<?php echo "'to" . $s . "'"; ?>
                            ).style.display='block';document.getElementById(<?php echo "'to" . $s . "'"; ?>
                            ).style.zIndex=parseInt(maxIndex())+1;" style="display: block; padding: 2px; border-top: 1px
                            solid #000; ?>; color:#<?php echo $p->workorder_id; ?>;
                            background-color:#<?php echo $p->projecttype; ?>">


                            <strong><?php echo $shot_title; ?></strong><br/>

                            <script type="text/javascript">
                                document.write('<img align="bottom" width="' + shirina + 'px" height="' + shirina + 'px" src="media/com_projectlog/docs/<?php echo $logotip;?>">');
                            </script>

							<?php if ($p->podrydchik <> '') echo '<img style="position: absolute;left:0px;top:30px" title="Изготавливается подрядчиком ' . $p->podrydchik . '" src="components/com_projectlog/assets/images/podrydchik.png" width="28" height="28" alt="Изготавливается подрядчиком ' . $p->podrydchik . '">'; ?>
                            <br/>


                            <strong><?php echo $technicians[0]; ?></strong><br/>
</div>
    </div>
    </div>

<!--////============ Формирование всплывающего окна ====================//-->
    <div class="win_drag" id="to<?php echo $s; ?>"
         onclick="this.style.zIndex=parseInt(maxIndex())+1;"

         style='width:530px;display:none;position:fixed;left:25%;top:15%;border: 3px ridge black;padding:5px;color:#000;background-color:#F0F0F0'>

		<?php if ((DEDIT_ACCESS and $acces_mov) or $this->user->get('id') == $p->manager)
		{ ?>
			<?php if (strtotime($p->release_date) <= strtotime($toDay) or PLOG_ADMIN >= 25)
		{ ?>
            <button style="float:right;" onclick="
                    if (confirm('Проект действительно выполнен? Переместить его в категорию “Выполнено”?')) {
                    document.location.assign(<?php echo "'" . $add_moov_link . "'"; ?>)
                    }"> Выполнено
            </button>


		<?php } ?>
		<?php } ?>
        <br/>

		<?php echo projectlogHTML::kalendarik('227', $shot_title, $p->release_date, $p->job_id, $p->release_id, strtok(projectlogHTML::getusername($p->technicians), " "), $p->projecttype, $p->workorder_id, 'media/com_projectlog/docs/' . $logotip, '219', '227', $p->podrydchik); ?>

		<?php projectlogHTML::kal_info($p->podrydchik, $p->task_id, $p->client, $p->manager, $p->chief, $p->technicians, $p->id, $this->doc_path, $p->description, $p->location_gen);
		?>

        <div style="margin: 25px 10px;background-color:#F0F0F0;padding:5px;">
            <button onclick="document.getElementById(<?php echo "'to" . $s . "'"; ?>).style.display='none';"> Закрыть
            </button>
            <button onclick="document.location.assign(<?php echo "'" . $proj_link . "'"; ?>)"> Открыть</button>

			<?php if (projectlogHelperQuery::isGroupMember(11, $this->user->get('id')) or PLOG_ADMIN >= 25)
			{ ?>
            <div class="sfetofor">
                <div style="padding:5px;color:#000;background-color:#fff;">
                    <form action="index.php" method="post" name="adminForm" id="adminForm">
                        <label style="background-color:<?php echo $ch_color_mat; ?>;">
                            <input type="checkbox" name="mat" value="1" <?php echo $checked_mat; ?> >
                            <strong style="background-color:#fff;"> Материалы </strong><br/></label>
                        <label style="background-color:<?php echo $ch_color_plan; ?>;">
                            <input type="checkbox" name="plan" value="1" <?php echo $checked_plan; ?> >
                            <strong style="background-color:#fff;"> Чертежи </strong><br/></label>
                        <label style="background-color:<?php echo $ch_color_pipl; ?>;">
                            <input type="checkbox" name="pipl" value="1" <?php echo $checked_pipl; ?> >
                            <strong style="background-color:#fff;"> Люди </strong><br/></label>

                        <input type="submit" value="<?php echo JText::_('SAVE'); ?>"/>
						<?php echo JHTML::_('form.token'); ?>
                        <input type="hidden" name="option" value="com_projectlog"/>
                        <input type="hidden" name="category" value="<?php echo JRequest::getVar('category'); ?>"/>
                        <input type="hidden" name="task" value="saveSfetofor"/>
                        <input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>"/>
                        <input type="hidden" name="id" value="<?php echo $p->id; ?>"/>
                        <input type="hidden" name="view" value="doska"/>
                        <input type="hidden" name="week" value="<?php echo $weekCol; ?>"/>
                        <input type="hidden" name="day" value="<?php echo $day; ?>"/>
                    </form>
                </div>
            </div>
        </div>


	<?php } ?>
    </div>

    </div>

    </td></tr>
    </table>
    </td></tr>


<?php
}

}

echo "</table></td>";
$t_date->modify("+1 day");

}
echo "</tr></table></td>";
} ?>
</tr></table>


<?php
// Вывод поступивших в работу проектов по датам
$s       = 0;
$jump    = 0;
$logotip = '';
foreach ($this->onworc as $p)
{
	$checked_pipl = '';
	$checked_plan = '';
	$checked_mat  = '';
	if ($p->mat_on <> 0) $checked_mat = 'checked';
	if ($p->plan_on <> 0) $checked_plan = 'checked';
	if ($p->pipl_on <> 0) $checked_pipl = 'checked';
	$red_id = '';
	$red    = '';
	foreach ($this->logo as $lg)
	{

		if ($lg->project_id == $p->id)
		{
			$logotip = $p->id . '/' . $lg->path;
		}
	}


	$release_date = $p->release_date;
	$title        = $p->title;
	$shot_title   = $p->shot_title;
	if ($shot_title == '') $shot_title = strtok($title, ' ');
	$proj_link = JRoute::_('index.php?option=com_projectlog&day=' . $day . '&week=' . $weekCol . '&view=project&id=' . $p->id);


	if (strtotime($p->deployment_to) < strtotime($toDay))
	{
		$red    = "background-color:red;z-index:100;";
		$red_id = 'id=jump' . $jump;
		$jump   = $jump + 1;
	}

	$s  = $s + 1;
	$xy = 10 * $s;
	$yx = $xy * 5;
	?>
    <div onclick="this.style.zIndex=parseInt(maxIndex())+1;" class="onworck win_drag move"
         style=<?php echo $red . "left:" . $yx . "px;"; ?>>
        <strong>Поступил новый проект <?php echo JHTML::_('date', $p->deployment_to, JText::_('%d.%m')); ?></strong>

        <div
			<?php echo $red_id; ?>
                class="pointer"
                onclick="document.getElementById(<?php echo "'on" . $s . "'"; ?>).style.display='block';document.getElementById(<?php echo "'on" . $s . "'"; ?>).style.zIndex=parseInt(maxIndex())+1;"
                style="display: block; padding: 2px; border-top: 1px solid #000; ?>; color:#<?php echo $p->workorder_id; ?>; background-color:#<?php echo $p->projecttype; ?>">

            <img align="left" width="50px" height="70px" src="media/com_projectlog/docs/<?php echo $logotip; ?>"/>
			<?php if ($p->podrydchik <> '')
			{
				echo '<img style="position: absolute;left:0;" title="Изготавливается подрядчиком ' . $p->podrydchik . '" src="components/com_projectlog/assets/images/podrydchik.png" width="28" height="28" alt="Изготавливается подрядчиком ' . $p->podrydchik . '" />';
			} ?>
            <strong><?php echo JHTML::_('date', $p->release_date, JText::_('%d.%m')); ?></strong><br/>
            <strong><?php echo $shot_title; ?></strong><br/>
            <strong><?php echo projectlogHTML::getusername($p->manager); ?></strong><br/>

        </div>
    </div>
    <!--////============ Формирование всплывающего окна ===================parseInt(=//-->
    <div class="win_drag" id="<?php echo 'on' . $s; ?>"
         onclick="this.style.zIndex=parseInt(maxIndex())+1;"
         style='width:530px;display:none;position:fixed;left:25%;top:15%;border: 3px ridge black;padding:5px;color:#000;background-color:#F0F0F0'>


        <!--//// Находится на базе  (принять в работу)//-->
		<?php
		if (projectlogHelperQuery::isGroupMember(11, $this->user->get('id')) or PLOG_ADMIN >= 25): $acces_mov = true; endif;
		$add_moov_link = JRoute::_('index.php?option=com_projectlog&day=' . $day . '&week=' . $weekCol . '&view=doska&project_id=' . $p->id . '&mov=8&task=move'); ?>

		<?php if (DEDIT_ACCESS and $acces_mov): ?>
            <button style="float:right;" onclick="document.location.assign(<?php echo "'" . $add_moov_link . "'"; ?>)">
                Взять в работу
            </button>    <!-- отправить -->
		<?php endif; ?>
        <br/>

		<?php echo projectlogHTML::kalendarik('227', $shot_title, $p->release_date, $p->job_id, $p->release_id, strtok(projectlogHTML::getusername($p->technicians), " "), $p->projecttype, $p->workorder_id, 'media/com_projectlog/docs/' . $logotip, '219', '227', $p->podrydchik); ?>

		<?php projectlogHTML::kal_info($p->podrydchik, $p->task_id, $p->client, $p->manager, $p->chief, $p->technicians, $p->id, $this->doc_path, $p->description, $p->location_gen); ?>


        <div style="margin: 25px 10px;background-color:#F0F0F0;padding:5px;">
            <button onclick="document.getElementById(<?php echo "'on" . $s . "'"; ?>).style.display='none';"> Закрыть
            </button>
            <button onclick="document.location.assign(<?php echo "'" . $proj_link . "'"; ?>)"> Открыть</button>
            <div class="sfetofor">
                <div style="padding:5px;color:#000;background-color:#fff;">
                    <form action="index.php" method="post" name="adminForm" id="adminForm">
                        <label>
                            <input type="checkbox" name="mat"
                                   value="1" <?php echo $checked_mat; ?> >Материалы<br/></label>
                        <label>
                            <input type="checkbox" name="plan"
                                   value="1" <?php echo $checked_plan; ?> >Чертежи<br/></label>
                        <label>
                            <input type="checkbox" name="pipl" value="1" <?php echo $checked_pipl; ?> >Люди
                            <br/></label>

                        <input type="submit" value="<?php echo JText::_('SAVE'); ?>"/>
						<?php echo JHTML::_('form.token'); ?>
                        <input type="hidden" name="option" value="com_projectlog"/>
                        <input type="hidden" name="category" value="<?php echo JRequest::getVar('category'); ?>"/>
                        <input type="hidden" name="task" value="saveSfetofor"/>
                        <input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>"/>
                        <input type="hidden" name="id" value="<?php echo $p->id; ?>"/>
                        <input type="hidden" name="view" value="doska"/>
                        <input type="hidden" name="week" value="<?php echo $weekCol; ?>"/>
                        <input type="hidden" name="day" value="<?php echo $day; ?>"/>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php } ?>

<script type="text/javascript">

    /************************Максимальный Z-INDEX *************************/
    function maxIndex() {
        var max = 0;
        var elems = document.getElementsByTagName('div');

        for (var i = 0; i < elems.length; i++) {
            //alert(elems[i].style.zIndex+' max='+max); window.scrin[width]
            if (max < elems[i].style.zIndex) max = elems[i].style.zIndex;
        }
        //alert(elems[0].style.zIndex);
        return max;
    }

    //maxIndex();
</script>


<script type="text/javascript">
    /*****************************Двганье объектов по экрану*****************************************/
    var dragMaster1 = (
        function () {
            var dragObject
            var mouseOffset

            function getMouseOffset(target, e) {
                var docPos = getPosition(target)
                return {x: e.pageX - docPos.x, y: e.pageY - docPos.y}
            }

            function mouseUp() {
                dragObject = null
                // clear events
                document.onmousemove = null
                document.onmouseup = null
                document.ondragstart = null
                document.body.onselectstart = null
            }

            function mouseMove(e) {
                e = fixEvent(e)
                with (dragObject.style) {
                    position = 'absolute'
                    top = e.pageY - mouseOffset.y + 'px'
                    left = e.pageX - mouseOffset.x + 'px'
                }
                return false
            }

            function mouseDown(e) {
                e = fixEvent(e)
                if (e.which != 1) return
                dragObject = this
                mouseOffset = getMouseOffset(this, e)
                document.onmousemove = mouseMove
                document.onmouseup = mouseUp
                // отменить перенос и выделение текста при клике на тексте
                document.ondragstart = function () {
                    return false
                }
                document.body.onselectstart = function () {
                    return false
                }
                return false
            }

            return {
                makeDraggable: function (element) {
                    element.onmousedown = mouseDown
                }
            }
        }()
    )

    function getPosition(e) {
        var left = 0
        var top = 0
        while (e.offsetParent) {
            left += e.offsetLeft
            top += e.offsetTop
            e = e.offsetParent
        }
        left += e.offsetLeft
        top += e.offsetTop
        return {x: left, y: top}
    }

    function fixEvent(e) {
        // получить объект событие для IE
        e = e || window.event

        // добавить pageX/pageY для IE
        if (e.pageX == null && e.clientX != null) {
            var html = document.documentElement
            var body = document.body
            e.pageX = e.clientX + (html && html.scrollLeft || body && body.scrollLeft || 0) - (html.clientLeft || 0)
            e.pageY = e.clientY + (html && html.scrollTop || body && body.scrollTop || 0) - (html.clientTop || 0)
        }

        // добавить which для IE
        if (!e.which && e.button) {
            e.which = e.button & 1 ? 1 : ( e.button & 2 ? 3 : ( e.button & 4 ? 2 : 0 ) )
        }

        return e
    }

    //$(document).ready(function() {
    //var dragObjects = document.getElementById('dragObjects1').getElementsByTagName('img');
    var dragObjects = document.getElementsByClassName('win_drag');
    for (var i = 0; i < dragObjects.length; i++) {
        dragMaster1.makeDraggable(dragObjects[i]);
    }
    //})


    /***********************************Прыганье и обновление****************************************************************/
    function getRandomGr() {
        return Math.floor(Math.random() * 5) + (Math.random() * 5);
    }

    function chingerCss() {
        var i = 0;

        while (document.getElementById('jump' + i)) {
            document.getElementById('jump' + i).style.marginTop = getRandomGr() + 'px';
            document.getElementById('jump' + i).style.marginBottom = getRandomGr() + 'px';

            i++;

        }
        i = 0;
        while (document.getElementById('jumpR' + i)) {
            document.getElementById('jumpR' + i).style.backgroundColor = '#F48168';

            i++;

        }
    }
    //var rel = true;
    setInterval(chingerCss, 150);
    setTimeout(function () {
        document.location.reload()
    }, 7200000);
    /**************************************************************************************************/
</script>


<!--/*echo $date_toworc."-".$this->toworc[0]->release_date."<br />";
		echo $date_onworc."-".$this->onworc[0]->deployment_to."<br />";print_r($logs);echo '-$this->logs<br />'; ?>*/-->
