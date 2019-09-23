<?php
/**
 * Просмотр проекта
 */

// Check to ensure this file is included in Joomla!
//defined('_JEXEC') or die('No access');

if ($this->project->group_access && !PLOG_ADMIN)
{
	//if user is in group add project to list
	if (!projectlogHelperQuery::isGroupMember($this->project->group_access, $this->user->get('id')))
	{
		JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

		return;
	}
}

$weekCol = JRequest::getVar('week');
$day     = JRequest::getVar('day');
$cat_id  = JRequest::getVar('cat_id');

if ($day <> '')
{
	$doska_link = JRoute::_('index.php?option=com_projectlog&view=doska&id=&week=' . $weekCol . '&day=' . $day . '&Itemid=65');
	echo '<div class="projekt">';
}
//print_R($this->logo,false);
$add_moov_link        = '';
$add_moov_servis_link = '';
$add_print_link       = '';
$acces_dok            = false;
$acces_edit           = false;
$acces_mov            = false;
// Ссылки на функции
$plog_home_link = JRoute::_('index.php?option=com_projectlog&view=cat&id=' . $cat_id);   // Список
$add_log_link   = JRoute::_('index.php?option=com_projectlog&view=project&layout=form&id=' . $this->project->id . '&week=' . $weekCol . '&day=' . $day); // Коментарий
$add_doc_link   = JRoute::_('index.php?option=com_projectlog&view=project&layout=docform&id=' . $this->project->id . '&week=' . $weekCol . '&day=' . $day);//  Добавить документ
$add_stop_link  = JRoute::_('index.php?option=com_projectlog&view=project&layout=stop&id=' . $this->project->id . '&cat=' . $cat_id . '&week=' . $weekCol . '&day=' . $day);//  Stop
$add_lnk_link   = JRoute::_('index.php?option=com_projectlog&view=project&layout=lnkform&id=' . $this->project->id . '&week=' . $weekCol . '&day=' . $day);//  Добавить ярлык
$add_edit_link  = JRoute::_('index.php?option=com_projectlog&day=' . $day . '&week=' . $weekCol . '&view=cat&layout=form&cid=' . $this->project->category);//   Править проект
$add_brak_link  = JRoute::_('index.php?option=com_projectlog&view=project&layout=brak&id=' . $this->project->id . '&week=' . $weekCol . '&day=' . $day);// Брак
$calendar_link  = JRoute::_('index.php?option=com_projectlog&view=calendar&id=' . $this->project->id . '&Itemid=61');
$add_lnk_teh    = JRoute::_('index.php?option=com_projectlog&view=project&layout=tehform&id=' . $this->project->id . '&week=' . $weekCol . '&day=' . $day . '&Itemid=61');//  Техзадание
$add_brigadir   = JRoute::_('index.php?option=com_projectlog&view=project&id=' . $this->project->id . '&task=brigadir');
$add_copy_link  = JRoute::_('index.php?option=com_projectlog&view=cat&day=' . $day . '&week=' . $weekCol . '&id=' . $this->project->id . '&task=copyProject');//   Копировать проект
$add_log_act    = JRoute::_('index.php?option=com_projectlog&view=project&layout=orderactform&id=' . $this->project->id . '&week=' . $weekCol . '&day=' . $day);//  Добавить акт

if ((
		($this->user->id == $this->project->manager ||          // Доступ имеют: менеджер
			$this->user->id == $this->project->chief ||    //         Дизайнер
			$this->user->id == $this->project->technicians)  //      Технолог
		&& PEDIT_ACCESS)
	|| PLOG_ADMIN
):
	$acces_dok = true;                    // добаление файлов
	if ($this->user->id == $this->project->manager
		or PLOG_ADMIN
		or $this->user->id == $this->project->created_by
	):     // Доступ имеют:  только менеджер(текущий и сосздатель))
		$acces_edit = true;                                    // к правке файлов
	endif;endif;

switch ($this->project->category)
{
	case 0:   // Ошибка (Передвинуть передвинуть в проектирование)
		$add_moov_link = JRoute::_('index.php?option=com_projectlog&view=project&project_id=' . $this->project->id . '&mov=6&task=move');// Отправить в производство
		$move_text     = 'Проектирование';
		$proekt_title  = 'Ошибка! Переместить в ';
		if ($this->user->id == $this->project->manager or PLOG_ADMIN): $acces_mov = true; endif;
		break;

	case 6:   // только создан проект (Передвинуть на базу)
		$buttomtitle = null;
		if (count($this->docs) == 0 AND count($this->logo) == 0)
		{
			$buttomtitle = "Не забудьте пристегнуть файлы И КАЛЕНДАРИК!";

		}

        elseif (count($this->docs) == 0)
		{
			$buttomtitle = "Не забудьте пристегнуть файлы!";
		}

        elseif (count($this->logo) == 0)
		{
			$buttomtitle = "Не забудьте пристегнуть КАЛЕНДАРИК!";
		}
		$add_moov_link = JRoute::_('index.php?option=com_projectlog&view=project&project_id=' . $this->project->id . '&mov=7&task=move');// Отправить в производство
		$move_text     = 'MOVENEW';
		$proekt_title  = 'TITLE';
		if ($this->user->id == $this->project->manager or PLOG_ADMIN): $acces_mov = true; endif;
		break;

	case 7:    // Находится на базе  (принять в работу)
		$add_moov_link  = JRoute::_('index.php?option=com_projectlog&view=project&project_id=' . $this->project->id . '&mov=8&task=move&week=' . $weekCol . '&day=' . $day);
		$add_print_link = JRoute::_('index.php?option=com_projectlog&view=project&project_id=' . $this->project->id . '&task=s_f_on_serv&week=' . $weekCol . '&day=' . $day);
		$msg            = "Без комментариев";
		$move_text_stop = 'Выполнение проекта под угрозой';
		$move_text      = 'MOVEDNEW';
		$proekt_title   = 'NEWTITLE';
		if (projectlogHelperQuery::isGroupMember(11, $this->user->get('id')) or PLOG_ADMIN >= 25):
            $acces_mov_stop = true;
			$acces_mov = true;
        endif;
		break;

	case 8:    // Находится в работе  (переместить в выполнено)
		$add_moov_link  = JRoute::_('index.php?option=com_projectlog&view=project&project_id=' . $this->project->id . '&mov=12&task=move&week=' . $weekCol . '&day=' . $day);
		$add_print_link = JRoute::_('index.php?option=com_projectlog&view=project&project_id=' . $this->project->id . '&task=s_f_on_serv&week=' . $weekCol . '&day=' . $day);
		$move_text      = 'READY';
		$proekt_title   = 'NEWDTITLE';
		$move_text_stop = 'Выполнение проекта под угрозой';
		if ($this->user->id == $this->project->manager or projectlogHelperQuery::isGroupMember(11, $this->user->get('id')) or PLOG_ADMIN >= 25): $acces_mov = true; endif;
		if (projectlogHelperQuery::isGroupMember(11, $this->user->get('id')) or PLOG_ADMIN >= 25): $acces_mov_stop = true; endif;
		break;

	case 12:  // Был выполнен (Выбор отдать в архив , взять на гарантию или выявлен брак)
		if ($this->project->location_spec == '')
		{
			$add_moov_link        = JRoute::_('index.php?option=com_projectlog&view=project&project_id=' . $this->project->id . '&mov=10&task=move');  // Архив
			$add_moov_servis_link = JRoute::_('index.php?option=com_projectlog&view=project&project_id=' . $this->project->id . '&mov=9&task=move');   // Гарантия
			$add_moov_link2       = JRoute::_('index.php?option=com_projectlog&view=project&project_id=' . $this->project->id . '&mov=8&task=move&week=' . $weekCol . '&day=' . $day);
			$move_text            = 'MOVEARHIV';  //
			$move_text_servis     = 'MOVESERVIS';
			$proekt_title         = 'READYTITLE';
			$comment_text         = "Если вы передаете проект на гарантийное обслуживание, Вы должны на странице редактирования:
                                        <br /> 1. Указать в комментариях срок гарантии.
                                        <br /> 2. Изменить менеджера по проекуту со своего имени на имя менеджера занимающегося гарантией.
                                        <br /> 3. Сохранить проект.
                                        <br /> 4. Нажать кнопку <Взять на гарантию>. <br />
                                        <i>К сожалению, в настоящий момент сложно отследить коллизии возникающие в данных, когда один проект редактируют одновременно два человека. Если вы передали управление проектом другому менеджеру, не вносите изменений в проект</i>
                                        <br /><br />
                                        ";
			if ($this->user->id == $this->project->manager or PLOG_ADMIN or $this->user->id == $this->project->created_by): $acces_mov = true; endif;
			$brak_onclik = "document.location.assign('" . $add_brak_link . "');";
		}
		else
		{
			$proekt_title = 'READYTITLE';
			$brak_writed  = $this->project->location_spec;
			$style_brak   = "background-color: red;";
			$brak_onclik  = '';
		}
		break;

	case 9:   // Гарантия (выбор передвинуть в архив или на базу)
		$add_moov_link        = JRoute::_('index.php?option=com_projectlog&view=project&project_id=' . $this->project->id . '&mov=10&task=move');   // архив
		$add_moov_servis_link = JRoute::_('index.php?option=com_projectlog&view=project&project_id=' . $this->project->id . '&mov=7&task=move');  //на базу
		$move_text            = 'MOVEARHIV';
		$move_text_servis     = 'MOVENEW';
		$proekt_title         = 'SERVISTITLE';
		if ($this->user->id == $this->project->manager or PLOG_ADMIN): $acces_mov = true; endif;
		break;

	case 10:   // Архив (выбор передвинуть в гарнтию или на базу)   !!!Должно быть КОПИРОВАТЬ !!!

		$proekt_title = 'ARHIVTITLE';
		$acces_mov    = false;

		break;

	case 13:    // Отказан  ( Принять в работу)
		$img            = '<img style=""  title="Выполнение проекта под угрозой" src="components/com_projectlog/assets/images/cherep.jpg" width="28" height="28" alt="Стоп!!!">';
		$add_moov_link  = JRoute::_('index.php?option=com_projectlog&view=project&project_id=' . $this->project->id . '&mov=8&task=move&week=' . $weekCol . '&day=' . $day);
		$add_print_link = JRoute::_('index.php?option=com_projectlog&view=project&project_id=' . $this->project->id . '&task=s_f_on_serv&week=' . $weekCol . '&day=' . $day);
		$move_text      = 'MOVEDNEW';
		$proekt_title   = 'Выполнение проекта под угрозой';
		if (projectlogHelperQuery::isGroupMember(11, $this->user->get('id')) or PLOG_ADMIN >= 25): $acces_mov = true; endif;
		break;
}


$deploy_from   = JFactory::getDate($this->project->deployment_from);
$deploy_to     = JFactory::getDate($this->project->deployment_to);
$release_date  = JFactory::getDate($this->project->release_date);
$contract_from = JFactory::getDate($this->project->contract_from);
$contract_to   = JFactory::getDate($this->project->contract_to);

$shot_title = $this->project->shot_title;
if ($shot_title == '') $shot_title = strtok($this->project->title, ' ');
echo "<strong style='text-align:left;'>" . $comment_text . "</strong>";
?>

    <div align="right">


		<?php
		//      //###############################     Кнопки   №№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№

		if ($day == '')
		{ ?>
            <!-- Список -->
            <button style="float:left;" onclick="document.location.assign(<?php echo "'" . $plog_home_link . "'"; ?>)">
				<?php echo JText::_('PROJECTS HOME'); ?> </button>
		<?php }
		else
		{ ?>
            <button style="float:left;" onclick="document.location.assign(<?php echo "'" . $doska_link . "'"; ?>)">
                Назад
            </button>
		<?php } ?>
        <span class="content_header" style="font-size: 16px;"><?php echo $img . JText::_($proekt_title); ?></span>&nbsp;
		<?php if (DEDIT_ACCESS and $acces_mov): ?>
            <button <?php

			if ($buttomtitle)
			{

				echo "title='" . $buttomtitle . "'";
				$onclickbutton = "if (confirm('Переместить проект в категорию “" . JText::_($move_text) . "”?')){
       	       				 if (confirm('" . $buttomtitle . "\\n\\n   Продолжить?')) {document.location.assign('" . $add_moov_link . "')}}";
			}
			else
			{
				$onclickbutton = "if (confirm('Переместить проект в категорию “" . JText::_($move_text) . "”?')){document.location.assign('" . $add_moov_link . "')}";
			} ?> onclick=<?php echo '"' . $onclickbutton . '"'; ?>)>
				<?php echo JText::_($move_text); ?> </button>
		<?php endif; ?>

        <!-- Заблокировать -->
		<?php if (DEDIT_ACCESS and $acces_mov_stop): ?>
            <button onclick="if (confirm('Выполнение проекта под угрозой?')) {
                    document.location.assign(<?php echo "'" . $add_stop_link . "'"; ?>)
                    }">

				<?php echo JText::_($move_text_stop); ?> </button>
		<?php endif; ?>
        <!-- Вернуть -->
		<?php if (

                    DEDIT_ACCESS and
                    (
                        ( projectlogHelperQuery::isGroupMember(11, $this->user->get('id')) or PLOG_ADMIN >= 25)
                        AND $add_moov_link2
                    )
                )
		{
			echo "<button onclick=\"if (confirm('Вернуть проект? ')){document.location.assign('" . $add_moov_link2 . "')}\">         Вернуть                 </button>";

		} ?>
        <!-- отправить -->
		<?php if (DEDIT_ACCESS and $add_moov_servis_link and $acces_mov): ?>
            <!--//<a href="<?php echo $add_moov_servis_link; ?>" class="red">[<?php echo JText::_($move_text_servis); ?>]</a>//-->
            <button onclick="document.location.assign(<?php echo "'" . $add_moov_servis_link . "'"; ?>)">
                <!---->
				<?php echo JText::_($move_text_servis); ?> </button>
		<?php endif; ?>
        <br/><br/>
        <!-- Править -->
		<?php if (DEDIT_ACCESS and $acces_edit): ?>
             <button style="float:left;"
                    onclick="document.location.assign(<?php echo "'" . $add_edit_link . "&edit=" . $this->project->id . "'"; ?>)">
				<?php echo JText::_('EDIT'); ?> </button>
            <!-- -->
            <button style="float:left;" onclick="document.location.assign(<?php echo "'" . $add_copy_link . "'"; ?>)">
				<?php echo JText::_('Копировать'); ?> </button>
		<?php endif; ?>
        <!-- Брак -->
		<?php if (DEDIT_ACCESS and ($acces_edit or $style_brak) and $this->project->category == 12): ?>                                   <!-- Править -->
            <button style="float:left;<?php echo $style_brak; ?>" onclick="<?php echo $brak_onclik; ?>">
				<?php echo JText::_('BRAK') ?> </button>
		<?php endif; ?>
        <!-- Акт -->
	    <?php if (LEDIT_ACCESS or $acces_dok): ?>
            <button onclick="document.location.assign(<?php echo "'" . $add_log_act . "'"; ?>)">
			    <?php echo JText::_('Акт'); ?> </button>
	    <?php endif; ?>
        <!-- Коментарий -->
		<?php if (LEDIT_ACCESS or $acces_dok): ?>
           <button onclick="document.location.assign(<?php echo "'" . $add_log_link . "'"; ?>)">
				<?php echo JText::_('ADD LOG'); ?> </button>
		<?php endif; ?>
        <!-- Документ -->
		<?php if (DEDIT_ACCESS or $acces_dok): ?>
            <button onclick="document.location.assign(<?php echo "'" . $add_doc_link . "'"; ?>)">
				<?php echo JText::_('ADD DOC'); ?> </button>
		<?php endif; ?>
        <!-- Календарик -->
		<?php if (DEDIT_ACCESS or $acces_dok):
			if ($day != '' or projectlogHelperQuery::isGroupMember(11, $this->user->get('id')))
			{ ?>

                <button onclick="document.location.assign( <?php echo "'" . $calendar_link . "'"; ?> )">
					<?php echo JText::_('ADD LNK'); ?> </button>

			<?php }
			else
			{ ?>
                <button onclick="document.location.assign( <?php echo "'" . $add_lnk_link . "'"; ?> )">
					<?php echo JText::_('ADD LNK'); ?> </button>
			<?php } ?>
            <!-- Тех. задание -->
            <button onclick="document.location.assign(<?php echo "'" . $add_lnk_teh . "'"; ?>)">
                Тех. задание
            </button>
            <br/>
            <!-- Заблокировать -->
			<?php if (DEDIT_ACCESS and $acces_mov_stop): ?>
                <form style="padding: 10px 0 0 0;" action="index.php" method="post" name="adminForm" id="adminForm">
                    <?php echo '<strong>Бригадир</strong>&nbsp;&nbsp;' . JHTML::_('select.genericlist', $this->brigadir_lis, 'brigadir',
                            'size="1" onchange="document.location=\'' . $add_brigadir . '&amp;brigadir=\' +this.options[this.selectedIndex].value " '           //.assign(\''. $add_brigadir . '\' + \'&amp;brigadir=\' + this.options[this.selectedIndex].value)
                            , 'value', 'text', $this->project->brigadir, 'brigadir', true);
                    ?>
                </form>
		    <?php endif; ?>

		<?php endif; ?>


    </div><br/>

    <div class="main-article-title">
        <h2 class="contentheading"><?php echo $shot_title; ?></h2>
    </div>
    <div class="main-article-block">
        <!--//№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№    Брак   №№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№//-->
        <table width="100%" cellpadding="6">
			<?php if ($brak_writed)
			{ ?>
            <tr>
                <td colspan="2" valign="top" style="border: solid 3px #ccc;">
                    <span class="red">В процессе изготовления издели был допущен брак</span>&nbsp;
                    <strong>( Снять статус выявленного брака может только директор! )</strong><br/><br/>

                    <table style="border-top: solid 1px #ccc;border-bottom: solid 1px #ccc;" width="100% align=" center
                    ">
            <tr>
                <td>
                    <u>Описание выявленного брака:</u><br/><?php echo $brak_writed; ?>
                </td>
            </tr>
        </table>
		<?php
		if (PLOG_ADMIN >= 25): $acces_mov = true; endif;


		if (DEDIT_ACCESS and $acces_mov)
		{
			; ?>
            <form enctype="multipart/form-data" action="index.php" method="post" name="adminForm">

                <input type="submit" style="float:right;" value="Брак устранен"/>    <!-- отправить -->
                <input type="hidden" name="option" value="com_projectlog"/>
                <input type="hidden" name="view" value="project"/>
                <input type="hidden" name="brak_msg" value=""/>
                <input type="hidden" name="id" value="<?php echo $this->project->id; ?>"/>
                <input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>"/>
                <input type="hidden" name="task" value="brak"/>
                <input type="hidden" name="week" value="<?php echo $weekCol; ?>"/>
                <input type="hidden" name="day" value="<?php echo $day; ?>"/>
				<?php echo JHTML::_('form.token'); ?>
            </form>

		<?php } ?>
        </td></tr>
		<?php }
		//echo $this->project->category."<br />";
		//echo $this->project->id."<br />";
		if ($this->project->category == 13)
		{
			$db    = JFactory::getDBO();
			$query = "SELECT cherep_msq FROM #__projectlog_projects WHERE ( id = " . $this->project->id . " ) ";
			$db->setQuery($query);
			$StopText = $db->loadResult();
			// print_r($StopText,false);
			echo "<h3>Выпонение проекта под угрозой, по причине:</h3><br /><b> " . $StopText . "</b><br />";
		}

		?>


        <!--//№№№№№№№№№№№№№№№№№№№№№№№№№       Вывод полей     №№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№№//-->
        <tr>
            <td colspan="2" valign="top" style="border-bottom: solid 1px #ccc;"><!--//Название//-->
                <strong><?php echo JText::_('TITLE NAME'); ?></strong> <span
                        class="red"><?php echo $this->project->title; ?></span>&nbsp;&nbsp;&nbsp;&nbsp;
                <strong><?php echo JText::_('RELEASE NUM'); ?></strong> <span
                        class="red"><?php echo ($this->project->release_id) ? $this->project->release_id : '&nbsp;'; ?></span>
                <!--//Подрядчик//-->
                <strong><?php echo ($this->project->podrydchik) ? '|&nbsp;' . JText::_('PODRYDCHIK2') : '&nbsp;'; ?></strong>
                <span class="red"><?php echo ($this->project->podrydchik) ? $this->project->podrydchik : '&nbsp;'; ?></span>
                <!--//<strong><?php echo JText::_('TASK NUM'); ?>:</strong> <span class="red"><?php echo ($this->project->task_id) ? $this->project->task_id : '&nbsp;'; ?></span> |//-->
                <!--<strong><?php echo JText::_('WORKORDER NUM'); ?>:</strong> <span class="red"><?php echo ($this->project->workorder_id) ? $this->project->workorder_id : '&nbsp;'; ?></span>-->
            </td>
        </tr>
        <tr>
            <td width="75%" valign="top">
				<?php if ($this->project->description) : ?>   <!--//Описание//-->
                    <span class="content_header"><?php echo JText::_('DESCRIPTION'); ?>:</span><br/>
					<?php echo $this->project->description;
				endif;
				if ($this->project->location_gen) : ?><br/><br/>   <!--//Место//-->
                    <span class="content_header"><?php echo JText::_('GEN LOC'); ?>:</span><br/>
					<?php echo $this->project->location_gen;
				endif; ?>


				<?php
				if (LOG_ACCESS):          // Коментарий
					//display results for logs
					if ($this->logs) :
						echo '<br /><br /><br /><br /><div class="right_details"></div>


                         <div id="commentlog" style="display:none;">  <h3><u> Коментарии: </u></h3>';


						$i = 0;
						foreach ($this->logs as $l) :
							$ldate           = JFactory::getDate($l->date);
							$lmod            = JFactory::getDate($l->modified);
							$delete_log_link = JRoute::_('index.php?option=com_projectlog&view=project&project_id=' . $this->project->id . '&week=' . $weekCol . '&day=' . $day . '&task=deleteLog&id=' . $l->id);
							$edit_log_link   = JRoute::_('index.php?option=com_projectlog&view=project&layout=form&id=' . $this->project->id . '&week=' . $weekCol . '&day=' . $day . '&edit=' . $l->id);
							$c_title         = '<span class="content_header">' . JText::_('CREATED') . ' <strong>' . $ldate->toFormat('%b %d, %Y в %H:%M') . ' автор: </strong>  <strong>' . projectlogHTML::getusername($l->loggedby) . '</strong></span>';
							echo '<div class="right_details">
                            	<span class="content_header">' . $c_title . '</span>';

							if (($this->user->id == $l->loggedby && LEDIT_ACCESS) || PLOG_ADMIN):
								echo '<div align="right" style="padding-right: 5px;"><a href="' . $edit_log_link . '">' . JText::_('EDIT') . '</a> | <a href="' . $delete_log_link . '" onclick="if(confirm(\'' . JText::_('CONFIRM DELETE') . '\')){return true;}else{return false;};">' . JText::_('DELETE') . '</a></div>';
							endif;

							echo '<div style="padding: 10px 25px;"> ';
							if ($l->modified_by):
								echo '<div>' . JText::_('MODIFIED') . ' <strong>' . $lmod->toFormat('%b %d, %Y at %H:%M') . '</strong>  <strong>' . projectlogHTML::getusername($l->modified_by) . '</strong></div>';
							endif;
							echo '<div style="margin-top: 10px;">' . $l->title . '<br />' . $l->description . '</div>
                                  </div>
                            </div>';

							$i++;

						endforeach;

						echo '</div> <br /><br />
                        <input type="button" onclick="if (document.getElementById(\'commentlog\').style.display == \'none\') {
                        		document.getElementById(\'commentlog\').style.display = \'block\';
                        		this.value = \'Спрятать коментарий\';
                        	}else{
                        		document.getElementById(\'commentlog\').style.display = \'none\';
                        		this.value =  \' Коментарий &nbsp;&nbsp;' . $i . 'шт. \';
                        	}" value=" Коментарий &nbsp;&nbsp;' . $i . 'шт." />  <br /><br /><br /><br /><div class="right_details"></div>

                        <br /><br /><br /><br />';
					else :

						echo
							'<br /><br />
                         <div align="center" style="border-top: solid 1px #ff0000;">
                            ' . JText::_('NO PROJECT LOGS') . '
                         </div>';

					endif;
				endif;


				// Акты

				if ($this->akts) :
                    echo '<div class="content_header2">' . JText::_('Акты подписаны') . ': </div>';
					foreach ($this->akts as $d):
						$delete_akt_link = JRoute::_('#');?>
						<div class="doc_item">
							<a
                                    href="<?php echo $this->doc_path . $this->project->id . '/' . $d->puth; ?>"
                                    type="bin"
                                    target="_blank"
                                    class="hasTip"
                                    title= "<?php echo  JText::_('DOCUMENT'). ' :: '. JText::_('SUBMITTED BY')	. ': '	. projectlogHTML::getusername($d->submittedby)	. '<br />'	. JText::_('FILE')	. ': '	. $d->puth	. '<br />'. JText::_('SUBMITTED DATE')	. ': '	. $d->save ?>"
                            >
								<?php echo $d->puth; ?>

							</a>

							<?php /*if (($this->user->id == $d->submittedby && DEDIT_ACCESS) || PLOG_ADMIN): */?><!--
							<br />
                            <a href="<?php /*echo $delete_akt_link; */?>"
                                     onclick="if(confirm(' <?php /*echo JText::_('CONFIRM DELETE') */?>')){return true;}else{return false;};"
                                     class="red" >
                                        [<?php /*echo  JText::_('DELETE') */?>]
                            </a>
						    --><?php /*endif; */?>
						</div>
						<?php endforeach; ?>
					<?php endif; ?>



            </td>
            <td width="25%" rowspan="3" style="border-left: solid 1px #ccc;" valign="top">
                <div class="right_details">
                    <!--//Дата сдачи//-->
                    <span class="content_header"><?php echo JText::_('RELEASE DATE'); ?>:</span><br/>
					<?php echo ($this->project->release_date != '0000-00-00') ? $release_date->toFormat('%d %b, %Y') : '&nbsp;'; ?>
                </div>
                <div class="right_details"> <!--// Контрагент //-->
                    <span class="content_header"><?php echo JText::_('JOB NUM'); ?></span><br/>
                    <!--<input type="text" class="inputbox" name="job_id" value="<?php echo $this->project->job_id; ?>" />-->
					<?php echo $this->project->job_id; ?> <br/>
                </div>

                <div class="right_details">     <!--//Менеджер //-->
                    <span class="content_header"><?php echo JText::_('PROJECT MANAGER'); ?>:</span><br/>
					<?php
					if ($this->project->manager)
					{
						echo projectlogHTML::getusername($this->project->manager);
						$managerdetails = projectlogHTML::userDetails($this->project->manager);
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
					?>
                </div>
                <div class="right_details">   <!--// Дизайнер //-->
                    <span class="content_header"><?php echo JText::_('PROJECT LEAD'); ?>:</span><br/>
					<?php echo ($this->project->chief) ? projectlogHTML::getusername($this->project->chief) : '&nbsp;'; ?>
                </div>
                <div class="right_details">    <!--// Техник //-->
                    <span class="content_header"><?php echo JText::_('TECHNICIAN'); ?>:</span><br/>
					<?php
					if ($this->project->technicians) :
						$cad_techs = explode(',', $this->project->technicians);
						foreach ($cad_techs as $c):
							echo projectlogHTML::getusername($c) . '<br />';
						endforeach;
					else:
						echo '&nbsp;';
					endif;
					?>
                </div>
                <!--//Сумма//-->
                <span class="content_header"><?php echo JText::_('TASK NUM'); ?></span><br/>
				<?php if ($this->project->task_id) echo number_format($this->project->task_id, 2, ',', ' ');; ?><br/>
                <!--// // Заказчик//-->
                <span class="content_header"><?php echo JText::_('CLIENT'); ?></span><br/>
				<?php echo $this->project->client; ?><br/>


				<?php // print_r($this->project) ;
				if (DOC_ACCESS):  // Список документов


					echo '<div class="right_details">';
					echo '<div class="content_header2">' . JText::_('RELATED DOCS') . ':';
					if (projectlogHelperQuery::isGroupMember(11, $this->user->get('id')) or PLOG_ADMIN >= 25)
					{ ?>
                        <button style="float:right;"
                                onclick="document.location.assign('<?php echo $add_print_link; ?>')"
                                title='<?php echo sprintf(JText::_('PRINT HELP'), strtok(projectlogHTML::getusername($this->project->manager), " ") . '\\' . $this->project->release_id); ?>'
                        >
							<?php echo JText::_('PRINT LINK'); ?> </button>
					<?php };


					echo '</div>';

					if ($this->docs) :
						foreach ($this->docs as $d):
							if ($d->name == '') $d->name = $d->path;
							$delete_doc_link = JRoute::_('index.php?option=com_projectlog&view=project&project_id=' . $this->project->id . '&week=' . $weekCol . '&day=' . $day . '&task=deleteDoc&id=' . $d->id);
							echo '<div class="doc_item">
							<a href="' . $this->doc_path . $this->project->id . '/' . $d->path . '" type="bin" target="_blank" class="hasTip" title="' . JText::_('DOCUMENT') . ' :: ' . JText::_('SUBMITTED BY') . ': ' . projectlogHTML::getusername($d->submittedby) . '<br />' . JText::_('FILE') . ': ' . $d->path . '<br />' . JText::_('SUBMITTED DATE') . ': ' . $d->time . '">
								' . $d->name . '
							</a>';
							if (($this->user->id == $d->submittedby && DEDIT_ACCESS) || PLOG_ADMIN):
								echo '<br /><a href="' . $delete_doc_link . '" onclick="if(confirm(\'' . JText::_('CONFIRM DELETE') . '\')){return true;}else{return false;};" class="red">[' . JText::_('DELETE') . ']</a>';
							endif;
							echo '</div>';
						endforeach;
					endif;
					echo '</div>';

				endif;
				?>


				<?php
				//print_r($this->logo);
				if (DOC_ACCESS):    // Лого
					if ($this->logo) :
						echo '<div class="right_details">';
						echo '<div class="content_header2">' . JText::_('RELATED LOGO') . ':</div>';

						foreach ($this->logo as $d):
							$delete_doc_link = JRoute::_('index.php?option=com_projectlog&view=project&project_id=' . $this->project->id . '&week=' . $weekCol . '&day=' . $day . '&task=deleteLogo&id=' . $d->id);
							echo '<div class="doc_item">
							<a href="' . $this->doc_path . $this->project->id . '/' . $d->path . '" type="bin" target="_blank" class="hasTip" title="' . JText::_('DOCUMENT') . ' :: ' . JText::_('SUBMITTED BY') . ': ' . projectlogHTML::getusername($d->submittedby) . '<br />' . JText::_('FILE') . ': ' . $d->path . '<br />' . JText::_('SUBMITTED DATE') . ': ' . $d->date . '">
								' . $d->path . '
							</a>';
							if (($this->user->id == $d->submittedby && DEDIT_ACCESS) || PLOG_ADMIN):
								echo '<br /><a href="' . $delete_doc_link . '" onclick="if(confirm(\'' . JText::_('CONFIRM DELETE') . '\')){return true;}else{return false;};" class="red">[' . JText::_('DELETE') . ']</a>';
							endif;
							echo '</div>';
						endforeach;
						echo '</div>';
					endif;
				endif;
				?>
            </td>
        </tr>
        <tr>
            <td colspan="2" valign="top">


            </td>
        </tr>
        </table>
    </div>
<?php if ($this->settings->get('footer')) echo '<p class="copyright">' . projectlogAdmin::footer() . '</p>';
if ($day <> '')
{
	echo '</div>';
}
?>