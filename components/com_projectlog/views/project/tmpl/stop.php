<?php
/**
 * Обработка сообщения об отказе
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('No access');

if (!projectlogHelperQuery::userAccess('dedit_access', $this->user->gid))
{
	JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

	return;
}


if ($this->project->podrydchik <> "")
{
	$podrjjd = "<stron><u>Данный проект частично или полностью исполняет подрядчик. Вы уверены что хотите заблокировать его?</u></strong>.";
	$p       = '   || Подрядчик:' . $this->project->podrydchik;
}
$page_title = JText::_('Отказать в работе' . $p);
//print_r($this,false);
?>
    <script type="text/javascript">
        function checkForm() {
            var drawing = document.adminForm.getElementById('drawing');
            var weather = document.adminForm.getElementById('weather');
            var material = document.adminForm.getElementById('material');
            var textMsg = document.adminForm.getElementById('textMsg');
            var spec = document.adminForm.getElementById('spec');
            var num = document.adminForm.getElementById('num');
            var time = document.adminForm.getElementById('time');
            var text = '';

            if (drawing.checked){text += ' Нужны чертежи. '}
            if (weather.checked){text += ' Не позволят погодные условия. '}
            if (material.checked){text += ' Нужны материалы. '}
            if (time.checked){text += ' Нужно больше времени. '}

            if ( !(num.value == '') ){text += ' Нужны люди в количестве '+num.value+', '}
            if ( !(spec.value == '') ){text += ' по специальности: '+spec.value+' '}

            if (!(textMsg.value == '')) textMsg.value = text+" Иное: "+textMsg.value;
            else textMsg.value = text;

            if (textMsg.value == '') {
                alert('<?php echo JText::_('ENTER TITLE'); ?>');
                return false;
            }

        }
    </script>


<?php echo '<div class="projekt_d">';

?>

    <div class="main-article-title">
        <h2 class="contentheading"><?php echo $page_title; ?></h2>
    </div>
    <div class="main-article-block">
        <form enctype="multipart/form-data" action="index.php" method="post" name="adminForm"
              onsubmit="return checkForm();">
            <fieldset>
                <!--// <legend><?php echo JText::_('BRAK'); ?></legend>//-->
                <table class="adminform" width="100%">
                    <tr>
                        <td><?php echo $podrjjd . "<br />";
							echo JText::_('Укажите причину отказа :'); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <div>
                                <p><input type="checkbox" id="drawing" name="drawing" value="Нужны чертежи">Нужны чертежи</p>
                                <p><input type="checkbox" id="weather" name="weather" value="Погодные условия">Погодные условия</p>
                                <p><input type="checkbox" id="material" name="material" value="Нужны материалы" >Нужны материалы</p>
                                <p><input type="checkbox" id="time" name="time" value="Нужно больше времени" >Нужно больше времени</p>

                                <p>Нужны люди в количестве:
                                    <input type="number" size="1" id="num" name="num" min="1" step="1" value="">,
                                    по специальности:
                                    <input id="spec" maxlength="25" size="26" placeholder="сварщик" value="">
                                </p>
                                <p>Иное:</p>
                                <textarea id="textMsg" name="msg" rows="8" cols="120"></textarea>
                            </div>
                            <div>
                                <input type="submit" value="<?php echo JText::_('SAVE'); ?>"/>
                                <input type="button" value="<?php echo JText::_('CANCEL'); ?>"
                                       onclick="history.go(-1)"/>
                            </div>
                        </td>
                    </tr>
                </table>
            </fieldset>

            <input type="hidden" name="option" value="com_projectlog"/>
            <input type="hidden" name="view" value="project"/>
            <!--// <input type="hidden" name="userid" value="<?php echo $this->user->id; ?>" />//-->
            <input type="hidden" name="project_id" value="<?php echo JRequest::getVar('id'); ?>"/>
            <input type="hidden" name="mov" value="13"/>
            <input type="hidden" name="task" value="move"/>
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