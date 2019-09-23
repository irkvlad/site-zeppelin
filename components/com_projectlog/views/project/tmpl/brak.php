<?php
/**
 * Обработка сообщения о браке
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('No access');

if (!projectlogHelperQuery::userAccess('dedit_access', $this->user->gid))
{
	JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

	return;
}

$page_title = JText::_('BRAK');
?>
    <script type="text/javascript">
        function checkForm() {
            if (document.adminForm.document.value == '') {
                alert('<?php echo JText::_('ENTER TITLE'); ?>');
                return false;
            }
            if (document.adminForm.name.value == '') {
                document.adminForm.name.value = document.adminForm.document.value;
                return true;
            }
        }
    </script>


<?php echo '<div class="projekt_d">'; ?>

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
                        <td><?php echo JText::_('BRAK OVERVIEW'); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <div>
                                <textarea name="brak_msg" rows="8" cols="50"></textarea>
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
            <input type="hidden" name="id" value="<?php echo JRequest::getVar('id'); ?>"/>
            <input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>"/>
            <input type="hidden" name="task" value="brak"/>
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