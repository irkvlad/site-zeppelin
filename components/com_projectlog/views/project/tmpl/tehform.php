<?php
/**
 * Печать тех.задания
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('No access');

if (!projectlogHelperQuery::userAccess('dedit_access', $this->user->gid))
{
	JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

	return;

}
$weekCol = JRequest::getVar('week');
$day     = JRequest::getVar('day');
if ($day <> '')
{
	$doska_link = JRoute::_('index.php?option=com_projectlog&view=doska&id=&week=' . $weekCol . '&day=' . $day . '&Itemid=65');

}
$link = JRoute::_('index.php?option=com_projectlog&view=project&id=' . $this->project->id . '&week=' . $weekCol . '&day=' . $day . '&Itemid=49');// Обратно

$shot_title = $this->project->shot_title;
if ($shot_title == '') $shot_title = strtok($this->project->title, ' ');
echo '<div class="projekt" style="line-height: 1.5;color:#000;background-color:#fff;width: 200cm;">';
if ($day == '')
{ ?>
    <!-- Список -->
    <button style="float:left;" onclick="document.location.assign(<?php echo "'" . $link . "'"; ?>)">
        Назад
    </button>
<?php }
else
{ ?>
    <!--//<a href="<?php echo $doska_link; ?>" class="red">[ Назад ]</a>//-->
    <button style="float:left;" onclick="document.location.assign(<?php echo "'" . $doska_link . "'"; ?>)">
        Назад
    </button>
<?php }

//float:right;padding:16px;width: 250px;
if ($this->project->podrydchik <> '') echo '<br /><strong>' . JText::_('PODRYDCHIK2') . '</strong> <span>' . $this->project->podrydchik . '</span><br />';
echo '<h1 style="padding-left: 2.4cm;">Бланк заказа № ' . $this->project->release_id . '&nbsp;&nbsp;"' . $shot_title . '"&nbsp;';
if (strtotime($this->project->deployment_to) <> 0)
{
	echo '&nbsp;&nbsp;&nbsp;от ' . JHTML::_('date', $this->project->deployment_to, $format = '%d.%m', $offset = null) . '</h1>';
}
else
{
	echo '&nbsp;&nbsp;&nbsp;от <span style="color:red;">Проект не принят в работу!</span></h1>';
}
echo '<h2 style="padding-left:10cm;"> Срок сдачи: ' . JHTML::_('date', $this->project->release_date, $format = '%d.%m', $offset = null) . '</h2><br />
                    <table border="0"  cellpadding="3" cellspacing="2" style="max-width:200cm;">
                    <tr><td>
                    <b><big>Заказчик:</big></b></td><td><b><big><big>' . $this->project->title . '</big></big></b> </td>';

if (projectlogHTML::getusername($this->project->technicians) == '')
{
	echo '        <tr><td><b><big>Стоимость:</big></b></td><td><b><big><big>' . $this->project->task_id . '</big></big></b> </td>';
}

echo '<tr><td><b><big>Заказ:</big></b></td><td><b><big><big>' . $this->project->job_id . '</big></big></b> </td>
                    <tr valign="top"><td><b><big>Материалы:</big></b></td><td style="line-height: 0.9;max-width:10cm;">' . $this->project->description . ' </td>
                    <tr><td><b><big>Технолог:</big></b></td><td><b><big><big>' . projectlogHTML::getusername($this->project->technicians) . '</big></big></b> </td>
                    <tr><td><b><big>Контактное лицо \ адрес:</big></b></td><td><b><big><big>' . $this->project->client . '</big></big></b> </td>
                    <tr><td><b><big>Монтаж \ доставка:</big></b></td><td><b><big><big>' . $this->project->location_gen . '</big></big></b> </td>
                     </tr></table>
                     <br />';


$db    = JFactory::getDBO();
$query = 'SELECT * FROM #__projectlog_logo WHERE project_id = ' . $this->project->id . ' ORDER BY date DESC';
$db->setQuery($query);
$logo = $db->loadObjectlist();
$w    = 20;      //height:' . $h . 'cm;

foreach ($logo as $l):

	$path = 'media/com_projectlog/docs/' . $l->project_id . '/' . $l->path;
	echo '<div class="doc_item">
										<img style="height:14cm;"  src="' . $path . '" />';//width:' . $w .

	echo '</div>';
endforeach;


?>


</div>