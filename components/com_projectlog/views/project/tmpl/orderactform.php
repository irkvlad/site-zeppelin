<?php
/**
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('No access');

if (!projectlogHelperQuery::userAccess('dedit_access', $this->user->gid))
{
	JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

	return;
}

$page_title = JText::_('Загрузить акт выполнения заказа');
?>
    <script type="text/javascript">
        function checkForm() {
            if (document.adminForm.document.value == '') {
                alert('<?php echo JText::_('ENTER TITLE'); ?>');
                return false;
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
                <table class="adminform" width="100%">
                    <tr>
                        <td><?php echo sprintf(JText::_('DOC ACT'), $this->settings->get('doc_types')); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <div>
	                            <?php  echo JHTML::_('calendar', date('d-m-Y'), 'date', 'date' , $format = '%d-%m-%Y' ); ?><br/>
                                <input class="inputbox" type="file" id="doc" name="document" size="30"
                                       style="margin: 5px 0px;"/>
                            </div>
                            <div>
                                <input type="submit" value="<?php echo JText::_('SAVE'); ?>"/>
                                <input type="button" value="<?php echo JText::_('LAST'); ?>"
                                       onclick=" window.location.search = 'option=com_projectlog&view=project&id=<?php

								       echo $this->project->id . '&Itemid=' . JRequest::getVar('Itemid'); ?>'"/>
                            </div>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <input type="hidden" name="option" value="com_projectlog"/>
            <input type="hidden" name="view" value="project"/>
            <input type="hidden" name="userid" value="<?php echo $this->user->id; ?>"/>
            <input type="hidden" name="project_id" value="<?php echo $this->project->id; ?>"/>
            <input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>"/>
            <input type="hidden" name="task" value="saveAkt"/>
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