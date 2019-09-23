<?php
/**
 * @version       1.5.3 2009-10-12
 * @package       Joomla
 * @subpackage    Project Log
 * @copyright (C) 2009 the Thinkery
 * @link          http://thethinkery.net
 * @license       GNU/GPL see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('No access');

if (!projectlogHelperQuery::userAccess('dedit_access', $this->user->gid))
{
	JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

	return;
}

$page_title    = JText::_('ADD LNK');
$calendar_link = JRoute::_('index.php?option=com_projectlog&view=calendar&id=' . $this->project->id . '&Itemid=61');
foreach ($this->logo as $lg)
{

	if ($lg->project_id == $this->project->id)
	{
		$logotip = $this->project->id . '/' . $lg->path;
	}

	if (file_exists('media' . DS . 'com_projectlog' . DS . 'docs' . DS . $this->project->id . DS . '227x219_' . $lg->path)) $logotip = $this->project->id . DS . '227x219_' . $lg->path;
}
//echo $lg->path.'===='.$logotip.'!!!'.$this->project->id.DS.'227x219_'.$lg->path.'&&&&&&'.file_exists('media'.DS.'com_projectlog'.DS.'docs'.DS.$this->project->id.DS.'227x219_'.$lg->path);
?>
<script type="text/javascript">


    function checkForm() {
       	
        var fileElement = document.getElementById("doc");
        var fileExtension = "";
		
		if (document.adminForm.document.value == '') {
            alert('<?php echo JText::_('ENTER TITLE'); ?>');
            return false;
        }
		
        if (fileElement.value.lastIndexOf(".") > 0) {
            fileExtension = fileElement.value.substring(fileElement.value.lastIndexOf(".") + 1, fileElement.value.length);
        }
        if (fileExtension.toLowerCase() == "jpg") {
            return true;
        }
        else {
            alert("Допустимый тип файла только jpg");
            return false;
        }
    }
</script>


    

<?php echo '<div class="projekt_d">'; ?>


<div class="main-article-title">
    <h2 class="contentheading"><?php echo $page_title; ?></h2>
	<?php echo projectlogHTML::kalendarik('227', $this->project->shot_title, $this->project->release_date, $this->project->job_id, $this->project->release_id, strtok(projectlogHTML::getusername($this->project->technicians), " "), $this->project->projecttype, $this->project->workorder_id, 'media/com_projectlog/docs/' . $logotip, '219', '227', $this->project->podrydchik); ?>
</div>


<div class="main-article-block">

    <form enctype="multipart/form-data" action="index.php" method="post" name="adminForm"
          onsubmit="return checkForm();">
        <fieldset>
            <!--<legend><?php echo JText::_('ADD DOC'); ?></legend>-->
            <table class="adminform" width="100%">
                <tr>
                    <td><?php echo sprintf(JText::_('LNK OVERVIEW'), $this->settings->get('doc_types')); ?></td>
                    <td><a target="_blank" href="<?php echo $calendar_link ?>"
                           class="red">[<?php echo JText::_('Подобрать цвета шрифта и фона'); ?>]</a></td>
                </tr>
                <tr>
                    <td>
                        <div>
                            <!--<input class="inputbox" type="text" id="name" name="name" size="50" maxlength="100" value="" /><br />-->
                            <input class="inputbox" type="file" accept=".jpg,.jpeg" id="doc" name="document" size="30"
                                   style="margin: 5px 0px;"/>
                        </div>
                        <div>
                            <input type="submit" value="<?php echo JText::_('SAVE'); ?>"/>
                            <input type="button" value="<?php echo JText::_('LAST'); ?>" onclick="history.go(-1)"/>
                        </div>
                    </td>
                </tr>

            </table>
        </fieldset>


        <input type="hidden" name="name" value="Логотип"/>
        <input type="hidden" name="option" value="com_projectlog"/>
        <input type="hidden" name="view" value="project"/>
        <input type="hidden" name="userid" value="<?php echo $this->user->id; ?>"/>
        <input type="hidden" name="project_id" value="<?php echo $this->project->id; ?>"/>
        <input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>"/>
        <input type="hidden" name="task" value="saveLogo"/>
        <input type="hidden" name="week" value="<?php echo JRequest::getVar('week'); ?>"/>
        <input type="hidden" name="day" value="<?php echo JRequest::getVar('day'); ?>"/>
		<?php echo JHTML::_('form.token'); ?>
    </form>
</div>
<?php
if ($this->settings->get('footer')) echo '<p class="copyright">' . projectlogAdmin::footer() . '</p>';
echo JHTML::_('behavior.keepalive');

echo '</div>';

?>
