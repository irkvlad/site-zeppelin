<?php
/**
 * Общий список проектов
 */

defined('_JEXEC') or die('Restricted access');
$plog_home_link   = JRoute::_('index.php?option=com_projectlog&view=cat&id=' . JRequest::getVar('id'));
$add_project_link = JRoute::_('index.php?option=com_projectlog&view=cat&layout=form&cid=' . JRequest::getVar('id'));
$cat_id           = JRequest::getVar('id');
/*// use below link and class for edit/add project links for modal window use :: useful when using very narrow layout //*/
//$add_project_link = JRoute::_('index.php?option=com_projectlog&view=cat&layout=form&tmpl=component&cid='.JRequest::getVar('id'));
//remove spaces between < and ?
//<a href="< ?php echo $add_project_link; ? >" class="red modal" rel="{handler: 'iframe', size: {x: 850, y: 600}}">
$doc_path  = 'media/com_projectlog/docs/';
$last_fild = JText::_('CALENDAR');

?>

<?php if (PEDIT_ACCESS and $cat_id == 6):
	if (projectlogHelperQuery::isGroupMember('10', $this->user->get('id')) or $this->user->get('id') >= 24):?>
        <div align="right"><h2>Внимание проекты создаются из <a
                        href="http://nash.zepp/index.php?option=com_zepp_ringclient&view=landing&Itemid=102">
                    Заказов </a></h2>
            <!--  <button onclick="document.location.assign(<?php //echo "'".$add_project_link."'";
			?>)">
			    	<?php //echo JText::_('ADD PROJECT');
			?> </button>
			    //<a href="<?php //echo $add_project_link;
			?>" class="red">[<?php //echo JText::_('ADD PROJECT');
			?>]</a>//-->
        </div>
	<?php endif;
elseif ($cat_id == 0): ?>
    <div align="right">

		<?php echo JText::_('NO CAT') . '<br />';

		$last_fild = JHTML::_('grid.sort', JText::_('CATEGORY'), 'p.category', $this->lists['order_Dir'], $this->lists['order']);
		?>
    </div>
<?php endif; ?>


<?php if ($this->user->get('id') == 0): ?>
    <div style="float:left;color:red" align="right">

        <b><?php echo JText::_('NON USER'); ?></b>
    </div><br/>
<?php endif;
// Проверяю есть ли заблокированные  проекты у текущего пользователя.
if (projectlogHTML::getUserPChekc($this->user->get('id')) == 1)
{
	$client = null;
	$db     = JFactory::getDBO();
	$query  = "SELECT * FROM #__zepp_client WHERE ( modifer_user = " . $this->user->get('id') . " )  AND ( send = 1 ) AND ( on_send < '" . date('Y-m-d') . "' )";
	$db->setQuery($query);
	$client = $db->loadObjectList();
	if ($client)
	{

		echo "<script language=\"javascript\" type=\"text/javascript\">
			alert('Вам необходимо напомнить о себе следующим клиентам: \\n";//print_r($client,false);       AND ( on_send < ".date('Y-m-d')." ) AND ( send = 1 )
		foreach ($client as $p)
		{
			echo $p->name . ": \\n";
			$query = "SELECT `fio` , `telefon` FROM #__zepp_client_contact WHERE ( `id_client` = " . $p->id . " )";
			$db->setQuery($query);
			$contact = $db->loadObjectList();
			if (count($contact) == 0) echo "контактов нет\\n";
			else
				foreach ($contact as $pc)
				{
					echo "контакт: " . $pc->fio . "\\t\\t" . $pc->telefon . " \\n";
				}

		}

		echo " ');	</script>";

	}

	$query = "SELECT * FROM #__projectlog_projects WHERE ( manager = " . $this->user->get('id') . " ) AND ( category = 13)";
	$db->setQuery($query);
	$projectStop = $db->loadObjectList();

	if ($projectStop)
	{
		echo "<script language=\"javascript\" type=\"text/javascript\">
			alert('У вас имеются проекты, dыполнение которых по угрозой. Вам необходимо принять решение по этим проектам: \\n";
		foreach ($projectStop as $p)
		{
			echo $p->release_id . ";\\t\\t" . $p->title . " \\n";
		}
		echo " ');	</script>";

	}

// Проверяю есть ли просроченные в изготовлении  проекты у текущего пользователя.
//##################################################################################
	$toDay = JHTML::_('date', $date = strtotime($date), $format = '%Y-%m-%d', $offset = null);
	$i     = 0;
	$d     = new DateTime($toDay);
	//echo $d->format("Y-m-d")."|||".$this->user->get('id')."|||".release_date;

	$db = JFactory::getDBO();
	if ($this->user->get('id') == 97)
	{
		$query = "SELECT * FROM #__projectlog_projects WHERE  ( category = 8) AND ( release_date <  '" . $d->format("Y-m-d") . "');";//AND ( release_date <  '" . $d->format("Y-m-d")."'
	}
	else
	{
		$query = "SELECT * FROM #__projectlog_projects WHERE ( manager = " . $this->user->get('id') . " ) AND ( category = 8) AND ( release_date <  '" . $d->format("Y-m-d") . "' );";
	}
	$db->setQuery($query);
	$projectSrok = $db->loadObjectList();


	if ($projectSrok)
	{
		echo "<script language=\"javascript\" type=\"text/javascript\">
			alert('У вас В ПРОИЗВОДСТВЕ имеются проекты, которое уже должны быть СДАНЫ заказчику, однако, до настоящего моента,  работа по ним НЕ ЗАКОНЧЕНА!!\\n";
		foreach ($projectSrok as $p)
		{
			echo $p->release_id . " ;\\t\\t" . $p->title . "\\t - срок изготовления: \\t " . $p->release_date . " ; \\n";
		}
		echo " ');	</script>";

	}
}
//##################################################################################

?>

    <script language="javascript" type="text/javascript">
        function tableOrdering(order, dir, task) {
            var form = document.adminForm;

            form.filter_order.value = order;
            form.filter_order_Dir.value = dir;
            document.adminForm.submit(task);
        }

        function listItemTask(id, task) {
            var form = document.adminForm;

            form.project_edit.value = id;
            form.task.value = task;
            document.adminForm.submit(task);
        }

        function resetForm() {
            document.adminForm.search.value = '';
            document.adminForm.filter.selectedIndex = '';
        }
    </script>

    <div class="main-article-title">
        <!-- 11111
    <?php print_R($this->catinfo, false); ?>
    22222-->
        <h2 class="contentheading"><?php echo $this->catinfo->title; ?></h2>
    </div>

<?php if (count($this->brak) > 0)
{
	echo '<div style="background:#c79f73;border: 3px outset #a86540;" >Выявлен брак:<br>';
	foreach ($this->brak as $b)
	{
		$proj_link = JRoute::_('index.php?option=com_projectlog&cat_id=' . $cat_id . '&view=project&id=' . $b->id);
		echo '<a href="' . $proj_link . '">';
		echo 'Номер: <b>' . $b->release_id . '</b>; ';
		echo 'Описание: <b>' . mb_substr($b->location_spec, 0, 60) . '...</b> <Дальше>;';
		echo '</a><br>';
	}
	echo '</div>';
} ?>

    <div class="main-article-block">
        <form name="adminForm" method="get" action="index.php">
            <table class="ptable" width="100%" cellpadding="5" cellspacing="1">
                <tr>
                    <td colspan="2">
                        <div align="left" class="prop_header_results">
							<?php if ($this->projects) :
								echo $this->pagination->getResultsCounter();
							else:
								echo '--';
							endif;
							?>
                        </div>
                    </td>
                    <td colspan="5">
                        <div align="right" class="prop_header_results">
							<?php echo JText::_('SEARCH') . ' ' . $this->lists['filter']; ?>
                            <input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>"
                                   class="text_area" onChange="document.adminForm.submit();"/>
                            <button onclick="document.adminForm.submit();"><?php echo JText::_('GO'); ?></button>
                            <button onclick="resetForm();document.adminForm.submit();"><?php echo JText::_('RESET'); ?></button>
                        </div>
                    </td>
                </tr>
				<?php

				if ($this->user->get('id') == 97)
				{
					$toDay = JHTML::_('date', $date = strtotime($date), $format = '%Y-%m-%d', $offset = null);
					$i     = 0;
					$d     = new DateTime($toDay);
					//echo $d->format("Y-m-d");
					$d->modify("-2 day");
					//echo $d->format("Y-m-d");


					$db    = JFactory::getDBO();
					$query = "SELECT * FROM #__projectlog_projects WHERE ( category = 7) AND ( deployment_to <  '" . $d->format("Y-m-d") . "' );";
					$db->setQuery($query);
					$projectAUT = $db->loadObjectList();
					//echo strtotime($yesterday)."<br />";
					if ($projectAUT)
					{
						echo "<script language=\"javascript\" type=\"text/javascript\">
			             alert('У вас имеются проекты, которые по настоящее время не приняты в работу. Вам необходимо принять решение по этим проектам: \\n";
						foreach ($projectAUT as $p)
						{
							$i++;
							echo "$i. \\t" . $p->release_id . " \\t поступил \\t" . $p->deployment_to . "; \\n";

						}
						echo " ');	</script>";
					}

				}
				if ($this->projects) :
				echo
					'<tr>
			<th width="15%">' . JHTML::_('grid.sort', JText::_('RELEASE DATE'), 'p.release_date', $this->lists['order_Dir'], $this->lists['order']) . '</th>
			<th width="15%">' . JHTML::_('grid.sort', JText::_('RELEASE NUM'), 'p.release_id', $this->lists['order_Dir'], $this->lists['order']) . '</th>
            <th width="20%">' . JHTML::_('grid.sort', JText::_('PROJECT NAME'), 'p.title', $this->lists['order_Dir'], $this->lists['order']) . '</th>
			<th width="15%">' . JHTML::_('grid.sort', JText::_('PROJECT MANAGER'), 'p.manager', $this->lists['order_Dir'], $this->lists['order']) . '</th>
   			<th width="15%">' . JHTML::_('grid.sort', JText::_('TECHNICIAN'), 'p.technicians', $this->lists['order_Dir'], $this->lists['order']) . '</th>
 			<th width="10%">' . JHTML::_('grid.sort', JText::_('Бригадир'), 'p.brigadir', $this->lists['order_Dir'], $this->lists['order']) . '</th>
			<!--<th width="20%">' . JText::_('WORC') . '</th> -->
			<th width="20%">' . $last_fild . '</th>
			</tr>';

				$i = 0;
				//print_R($this->projects,false);
				foreach ($this->projects as $p) :
				$delete_project_link = JRoute::_('index.php?option=com_projectlog&view=cat&task=deleteProject&id=' . $p->id . '&category_id=' . $cat_id);
				$proj_link           = JRoute::_('index.php?option=com_projectlog&cat_id=' . $cat_id . '&view=project&id=' . $p->id);
				$release_date        = JFactory::getDate($p->release_date);
				$calendar_link       = JRoute::_('index.php?option=com_projectlog&view=calendar&id=' . $p->id . '&Itemid=61');
				$last_fild_on        = '</td>
                        <td align="center"><a target="_blank" href="' . $calendar_link . '" class="red">[' . JText::_('CALENDAR') . ']</a>
                         </td> ';
				if ($cat_id == 0)
				{
					switch ($p->category)
					{
						case 6:
							$last_fild_on = '</td>
	                        <td align="center">' . JText::_('TITLE') . ' </td> ';
							break;
						case 7 :
							$last_fild_on = '</td>
	                        <td align="center">' . JText::_('NEWTITLE') . ' </td> ';
							break;
						case 8 :
							$last_fild_on = '</td>
	                        <td align="center">' . JText::_('NEWDTITLE') . ' </td> ';
							break;
						case 9 :
							$last_fild_on = '</td>
	                        <td align="center">' . JText::_('SERVISTITLE') . ' </td> ';
							break;
						case 10 :
							$last_fild_on = '</td>
	                        <td align="center">' . JText::_('ARHIVTITLE') . ' </td> ';
							break;
						case 12 :
							$last_fild_on = '</td>
	                        <td align="center">' . JText::_('READY') . ' </td> ';
							break;
						case 13 :
							$last_fild_on = '</td>
	                        <td align="center">
                            <img style="float: left"  title="Выполнение проекта под угрозой" src="components/com_projectlog/assets/images/cherep.jpg" width="28" height="28" alt="Стоп!!!">' .
								JText::_('Выполнение проекта под угрозой') .
								'</td> ';
							break;
						default:
							$last_fild_on = '</td>
	                        <td align="center">' . JText::_('CATEGORYS') . ' </td> ';

							if ($p->location_spec <> '' and $p->category == 12)
							{
								$last_fild_on = '</td>
	                        		<td align="center">' . JText::_('BRAK') . ' </td> ';
							}
							break;
					}
				}
				echo '<tr><td  height="80px" align="center"><div style="position:relative;">';
				// БРАК
				if ($p->location_spec <> '' and $p->category == 12) echo '<img style="position: absolute;left:40px;top:20px;"  title="Выявлен брак" src="components/com_projectlog/assets/images/6410746.png" width="28" height="28" alt="Выявлен брак">';
				echo '<strong>' . $release_date->toFormat('%d.%m.%Y') . '</strong><br/>';
				// Подрядчик
				if ($p->podrydchik <> '') echo '<img style="position: absolute;left:20px;top:20px;"  title="Изготавливается подрядчиком" src="components/com_projectlog/assets/images/podrydchik.png" width="28" height="28" alt="Изготавливается подрядчиком">';
				/*/ Сфетофор
				if ( $p->category == 8 or $p->category == 7 ){
					$ch_color_pipl='red';$ch_color_plan='red';$ch_color_mat='red';
					if ($p->mat_on<>0) {$ch_color_mat='green'; }
					if ($p->plan_on<>0) {$ch_color_plan='green'; }
					if ($p->pipl_on<>0) {$ch_color_pipl='green';} */
				?>
                <!-- <div style="position: absolute;left:0px;top:0px">
		        		<span style="width:9px;hight:5px;background-color:<?php echo $ch_color_mat; ?>;border:solid 1px black;">М</span></br>
		        		<span style="width:9px;hight:5px;background-color:<?php echo $ch_color_plan; ?>;border:solid 1px black;">Ч</span></br>
		        		<span style="width:9px;hight:5px;background-color:<?php echo $ch_color_pipl; ?>;border:solid 1px black;">Л</span>
		        	</div> -->
    </div>
	<?php //}

	foreach ($this->logo as $lg)
	{

		if ($lg->project_id == $p->id)
		{
			$tunbsrc = $doc_path . $p->id . DS . $lg->path;
			if (file_exists($doc_path . $p->id . DS . '80x80_' . $lg->path)) $tunbsrc = $doc_path . $p->id . DS . '80x80_' . $lg->path;
			echo '<img src="' . $tunbsrc . '" width="80" height="80" alt="Логотип">';
		}
	}


	echo '</td>
					  	<td align="center">' . $p->release_id . '<br />';
	if (($this->user->id == $p->manager || $this->user->id == $p->created_by && PEDIT_ACCESS) || PLOG_ADMIN):
		echo '<a href="' . $add_project_link . '&edit=' . $p->id . '" class="red">[' . JText::_('EDIT') . ']</a><br />';
		echo '<a href="' . $delete_project_link . '" class="red" onclick="if(confirm(\'' . JText::_('CONFIRM DELETE') . '\')){return true;}else{return false;};">[' . JText::_('DELETE') . ']</a>';
	endif;
	echo '


                          </td>
                        <td>
                            <a href="' . $proj_link . '">' . $p->title . '</a>';


	echo '
                        </td>
						<td>' . projectlogHTML::getusername($p->manager) . '</td>


						<td align="center">' . projectlogHTML::getusername($p->technicians) . '</td>
                        <td align="center">' . projectlogHTML::getContactName($p->brigadir) . '</td>';

	echo $last_fild_on;
	echo '

                        <td align="center">';
	$task = $p->onsite ? 'projectOffsite' : 'projectOnsite';
	$img  = $p->onsite ? 'tick.png' : 'publish_x.png';

	if (($this->user->id == $p->manager || $this->user->id == $p->created_by && PEDIT_ACCESS) || PLOG_ADMIN):
		echo '<!--<a href="javascript:void();" onClick="return listItemTask(\'' . $p->id . '\',\'' . $task . '\')"><img border="0" src="components/com_projectlog/assets/images/' . $img . '" alt="" /></a>-->';
	else:
		echo '<!--<img border="0" src="components/com_projectlog/assets/images/' . $img . '" alt="" />-->';
	endif;
	echo '
                        </td>
					  </tr>';
	$i++;
	$status;
endforeach;

	echo
		'<tr>
				<td colspan="3" align="left">
					' . $this->pagination->getPagesLinks() . '&nbsp;
				</td>
                <td colspan="4" align="right">
                    Количество записей на странице: ' . $this->pagination->getLimitBox() . '
                </td>
			</tr>
            <tr>
				<td colspan="7" align="center">
					' . $this->pagination->getPagesCounter() . '
				</td>
			</tr>';
else :

	echo
		'<tr>
			  <td colspan="7">
				<div align="center">
					' . JText::_('NO PROJECTS') . '
				</div>
			  </td>
			</tr>';

endif;
?>
    </table>
    <input type="hidden" name="option" value="com_projectlog"/>
    <input type="hidden" name="view" value="<?php echo JRequest::getVar('view'); ?>"/>
    <input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>"/>
    <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>"/>
    <input type="hidden" name="project_edit" value=""/>
    <input type="hidden" name="id" value="<?php echo JRequest::getVar('id'); ?>"/>
    <input type="hidden" name="task" value=""/>
    </form>
    </div>

<?php
if ($this->settings->get('footer')) echo '<p class="copyright">' . projectlogAdmin::footer() . '</p>';
?>