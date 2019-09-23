<?php
/**
 * Добавить проект или Править
 */

defined('_JEXEC') or die('Restricted access');

JHTMLBehavior::formvalidation();
$editor = &JFactory::getEditor();

if (!projectlogHelperQuery::userAccess('pedit_access', $this->user->gid))
{
	JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

	return;
}

$weekCol = JRequest::getVar('week');
$day     = JRequest::getVar('day');

if ($day <> '')
{
	echo '<div class="projekt">';
}
$client         = JRequest::getVar('client', '');
$telefon        = JRequest::getVar('telefon', '');
$tema           = JRequest::getVar('tema', '');
$ringclient_ids = JRequest::getVar('ringclient_ids', ''); /// !!
$project_ids    = JRequest::getVar('project_ids', '');
//$statusdata = JRequest::getVar('statusdata','');
//$statustext = JRequest::getVar('statustext','');

if (JRequest::getVar('edit'))
{
	$pid           = JRequest::getVar('edit');
	$this->project = projectlogModelCat::getProject($pid);
	$page_title    = JText::_('EDIT PROJECT');
}
else
{
	$project     = new stdClass();
	$project->id = 0;
	//$project->category      = null;
	$project->group_access    = null;
	$project->release_id      = $this->usercolor->pr_user_id;
	$project->job_id          = $tema;
	$project->task_id         = null;
	$project->workorder_id    = $this->usercolor->color;
	$project->title           = $client;
	$project->shot_title      = $client;
	$project->description     = null;
	$project->release_date    = null;
	$project->contract_from   = null;
	$project->contract_to     = null;
	$project->location_gen    = null;
	$project->location_spec   = null;
	$project->manager         = null;
	$project->chief           = null;
	$project->technicians     = null;
	$project->brigadir        = null;
	$project->deployment_from = null;
	$project->deployment_to   = null;
	$project->onsite          = null;
	$project->projecttype     = $this->usercolor->bgcolor;
	$project->client          = $telefon;
	$project->status          = null;
	$project->approved        = null;
	$project->published       = null;
	$project->pipl_on         = 0;
	$project->mat_on          = 0;
	$project->plan_on         = 0;
	$project->podrydchik      = null;
	$project->ringclient_ids  = $ringclient_ids;

	$this->project = $project;
	$page_title    = JText::_('ADD PROJECT');
}

//Поля для уведомлений  об изминении значений
$onLoadM        = $this->project->manager;     //менеджер
$onLoadD        = $this->project->release_date;//Дата реализации
$ONL_podrydchik = $this->project->podrydchik;   //Подрядчик
//Техзадание
$ONL_title        = $this->project->title;        //Заказчик
$ONL_job_id       = $this->project->job_id;       //Заказ
$ONL_description  = $this->project->description;  //Материалы
$ONL_technicians  = $this->project->technicians;  //Технолог
$ONL_client       = $this->project->client;       //Контакт
$ONL_location_gen = $this->project->location_gen; //Доставка\Монтаж

$lists['groups']    = projectlogHTML::groupSelect('group_access', 'size="1" class="inputbox" style="width: 200px;"', $this->project->group_access);
$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $this->project->published);
$lists['onsite']    = JHTML::_('select.booleanlist', 'onsite', 'class="inputbox"', $this->project->onsite);
$lists['approved']  = JHTML::_('select.booleanlist', 'approved', 'class="inputbox"', $this->project->approved);
$calendar_link      = JRoute::_('index.php?option=com_projectlog&view=calendar&id=' . $this->project->id . '&Itemid=61');
//$lists['color'] = projectlogHTML::colorSelect('color', 'size="1" class="inputbox required"');
//$lists['categories'] = projectlogHTML::catSelect('category', 'size="1" class="inputbox" style="width: 200px;"', $this->project->category);


?>


    <script type="text/javascript">

        function checkForm() {
            var form = document.adminForm;


            if (document.formvalidator.isValid(form)) {
                form.check.value = '<?php echo JUtility::getToken(); ?>';
                alert('<?php echo JText::_('ENTER REQUIRED'); ?>');
                return false;
            } else if (form.title.value == '') {
                alert('<?php echo JText::_('TITLE NAME'); ?>');
                return false;
            } else if (form.release_date.value == '' || form.release_date.value == '0000-00-00') {
                alert('<?php echo JText::_('ENTER RELEASE DATE'); ?>');
                return false;
            } else if (form.manager.selectedIndex == '') {
                alert('<?php echo JText::_('ENTER MANAGER'); ?>');
                return false;
            } else if (form.release_id.value == '') {
                alert('<?php echo JText::_('ENTER RELEASE NUM'); ?>');
                return false;
            }


        }
    </script>


    <div class="main-article-title">
        <h2 class="contentheading"><?php echo $page_title; ?></h2>
    </div><br/>

    <form action="index.php" method="post" name="adminForm" id="adminForm" onsubmit="return checkForm();">
        <table width="100%">
            <tr>
                <td width="70%" valign="top">
                    <table class="admintable" width="100%">
                        <tr>
                            <td>
                                <table>
                                    <tr>

                                        <td class="key"><?php echo JText::_('RELEASE NUM'); ?><span
                                                    style="color:red;">*</span><br/>
                                            <input type="text" class="inputbox" name="release_id"
                                                   value="<?php echo $this->project->release_id; ?>"/></td>

                                        <td class="key"><?php echo JText::_('PROJECT MANAGER'); ?><span
                                                    style="color:red;">*</span><br/>
											<?php echo $this->manager_list; ?></td>

                                        <td class="key"><?php echo JText::_('TECHNICIAN'); ?><br/>
											<?php echo JHTML::_('select.genericlist', $this->technicians_list, 'technicians', 'size="1"', 'value', 'text', $this->project->technicians, 'technicians', true); ?>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td colspan="2" class="key"><?php echo JText::_('TITLE NAME'); ?><span
                                                    style="color:red;">*</span><br/>
                                            <input class="inputbox required" type="text" name="title" size="50"
                                                   maxlength="100"
                                                   value="<?php echo $this->escape($this->project->title); ?>"/>
                                        <td class="key"><?php echo JText::_('SHOT TITLE'); ?><br/>
                                            <input type="text" class="inputbox required" name="shot_title"
                                                   maxlength="15" size="15"
                                                   title="Можно не заполнять если останется пустым, то на календариках и на доске будет отображатся только первое слово из Контрагента"
                                                   value="<?php echo $this->project->shot_title; ?>"/><?php echo JText::_('SHOT TITLE2'); ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <table>
                                    <tr>
                                        <td class="key"><?php echo JText::_('RELEASE DATE'); ?><span style="color:red;">*</span><br/>


											<?php //<?php echo JHTML::_('calendar', $value = '5', $name='test', $id='test', $format = '%Y-%m-%d', $attribs = null);

											echo JHTML::_('calendar', $this->project->release_date, 'release_date', 'release_date'); ?>
                                        </td>
                                        <td style="padding-left: 15px;" valign="top"
                                            class="key"><?php echo JText::_('PROJECT LEAD'); ?><br/>
											<?php echo JHTML::_('select.genericlist', $this->chief_list, 'chief', 'size="1"', 'value', 'text', $this->project->chief, 'chief', true); ?>
                                        </td>

                                        <td class="key"><?php echo JText::_('PODRYDCHIK'); ?><br/>
                                            <input type="text" class="inputbox" name="podrydchik" size="50"
                                                   title='Если указан, на доске будет показан признак, что изделие делает подрядчик ввиде жолтой буквы "П"'
                                                   value="<?php echo $this->project->podrydchik; ?>"/></td>
                                    </tr>
                                    </td></table>
                        </tr>


                        <tr>
                            <td class="key"><?php echo JText::_('DESCRIPTION'); ?><br/>
								<?php if ($this->settings->get('plogeditor')):
									echo $editor->display('description', $this->project->description, '90%;', '250', '75', '20', array('pagebreak', 'readmore'));
								else:
									?>
                                    <textarea name="description" rows="8"
                                              cols="50"><?php echo $this->project->description; ?></textarea>
								<?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="submit" value="<?php echo JText::_('SAVE'); ?>"/>
                                <input type="button" value="<?php echo JText::_('CANCEL'); ?>"
                                       onclick="history.go(-1)"/>
								<?php echo '<span style="padding:5px;background-color:#' . $this->project->projecttype . ';"><a style="color:#' . $this->project->workorder_id . ';" target="_blank" href="' . $calendar_link . '" >' . JText::_('Текущий цвет. Изменить? ') . '</a></span>&nbsp;&nbsp;'; ?>
								<?php echo '<span style="padding:5px;color:#' . $this->usercolor->color . ';background-color:#' . $this->usercolor->bgcolor . ';">' . JText::_('Присвоенные вам цвета ') . '</span><br />'; ?>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="30%" valign="top" style="border-left: solid 1px #ccc; padding: 0 0 0 8px;">
                    <div style="background: #666; margin-bottom: 8px; border-bottom: solid 1px #999;padding: 3px 5px; font-weight: bold; color: #fff;">
						<?php echo JText::_('PROJECT DETAILS'); ?>
                    </div>
                    <table class="admintable" width="100%">
                        <tr>
                            <td class="key">
                                <fieldset style="background:none;border:solid">
                                    <legend><?php echo JText::_('GEN LOC'); ?></legend>
                                    <input type="radio" name="genloc" value="M"
                                           onclick="document.getElementById('DM').value='Монтаж:\n'; document.getElementById('DM').disabled=false;"
                                    >&nbsp;&nbsp;&nbsp;<input type="radio" name="genloc" value="D"
                                                              onclick="document.getElementById('DM').value='Доставка:\n'; document.getElementById('DM').disabled=false;"><br/>
                                    <textarea id="DM" name="location_gen" class="inputbox" rows="4" cols="25"
                                              disabled="true"><?php echo $this->escape($this->project->location_gen); ?></textarea>
                                </fieldset>
                        </tr>
                        <tr>
                            <td class="key"><?php echo JText::_('JOB NUM'); ?><br/>
                                <!--<input type="text" class="inputbox" name="job_id" value="<?php echo $this->project->job_id; ?>" />-->
                                <textarea name="job_id" rows="3" maxlength="60"
                                          title='Этот текст будет помещен на календарик перед картинкой, всего 60 символов '
                                          cols="25"><?php echo $this->project->job_id; ?></textarea></td>
                            </td>
                        </tr>


                        <tr>
                            <td class="key"><?php echo JText::_('TASK NUM'); ?><br/>
                                <input type="text" class="inputbox" name="task_id"
                                       value="<?php echo $this->project->task_id; ?>"/></td>
                        </tr>
                        <tr>
                            <td class="key"><?php echo JText::_('CLIENT'); ?><br/>
                                <textarea name="client" rows="6"
                                          cols="25"><?php echo $this->project->client; ?></textarea>
                                <!--//<input type="text" class="inputbox" name="client" value="<?php echo $this->project->client; ?>" />//-->
                            </td>
                        </tr>
                        <tr>
                            <td>
								<?php //print_r($this->project) ; print 'www'.$this->project->id; ?>
								<?php

								$db    = JFactory::getDBO();
								$query = 'SELECT * FROM #__projectlog_docs WHERE project_id = ' . $this->project->id . ' ORDER BY date DESC';
								$db->setQuery($query);
								$docs = $db->loadObjectlist();


								if (DOC_ACCESS):  // Список документов
									if ($docs) :
										echo '<div class="right_details">';
										echo '<div class="content_header2">' . JText::_('RELATED DOCS') . ':</div>';
										foreach ($docs as $d):
											if ($d->name == '') $d->name = $d->path;
											$delete_doc_link = JRoute::_('index.php?option=com_projectlog&view=project&project_id=' . $this->project->id . '&task=deleteDoc&id=' . $d->id);
											echo '<div class="doc_item">
							<a href="' . $this->doc_path . $this->project->id . '/' . $d->path . '" type="bin" target="_blank" class="hasTip" title="' . JText::_('DOCUMENT') . ' :: ' . JText::_('SUBMITTED BY') . ': ' . projectlogHTML::getusername($d->submittedby) . '<br />' . JText::_('FILE') . ': ' . $d->path . '<br />' . JText::_('SUBMITTED DATE') . ': ' . $d->date . '">
								' . $d->name . '
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

								<?php
								$i    = 0;
								$logs = false;
								foreach ($this->logo as $d):
									if ($d->project_id == $this->project->id) :
										$logs[$i] = $d;
										$i        = $i + 1;
									endif;
								endforeach;

								if (DOC_ACCESS):   // Лого
									if ($logs) :
										echo '<div class="right_details">';
										echo '<div class="content_header2">' . JText::_('RELATED LOGO') . ':</div>';

										foreach ($logs as $d):
											$delete_doc_link = JRoute::_('index.php?option=com_projectlog&view=project&project_id=' . $this->project->id . '&task=deleteLogo&id=' . $d->id);
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

                                <!-- Файлы логотипа документов-->

                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <hr>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>
		<?php echo JHTML::_('form.token'); ?>
        <input type="hidden" name="option" value="com_projectlog"/>
        <input type="hidden" name="workorder_id" value="<?php echo $this->project->workorder_id; ?>"/>
        <input type="hidden" name="projecttype" value="<?php echo $this->project->projecttype; ?>"/>
		<?php if (JRequest::getVar('edit')) echo '<input type="hidden" name="category" value="' . $this->project->category . '" /> '; ?>
        <input type="hidden" name="task" value="saveProject"/>
        <input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>"/>
        <input type="hidden" name="id" value="<?php echo $this->project->id; ?>"/>
        <input type="hidden" name="onloadm" value="<?php echo $onLoadM; ?>"/>
        <input type="hidden" name="onloadd" value="<?php echo $onLoadD; ?>"/>
        <input type="hidden" name="ONL_podrydchik" value="<?php echo $ONL_podrydchik; ?>"/>
        <input type="hidden" name="ONL_title" value="<?php echo htmlspecialchars($ONL_title); ?>"/>
        <input type="hidden" name="ONL_job_id" value="<?php echo $ONL_job_id; ?>"/>
        <input type="hidden" name="ONL_description" value="<?php echo htmlspecialchars($ONL_description); ?>"/>
        <input type="hidden" name="ONL_technicians" value="<?php echo $ONL_technicians; ?>"/>
        <input type="hidden" name="ONL_client" value="<?php echo $ONL_client; ?>"/>
        <input type="hidden" name="ONL_location_gen" value="<?php echo htmlspecialchars($ONL_location_gen); ?>"/>
        <input type="hidden" name="ringclient_ids" value="<?php echo $ringclient_ids; ?>"/>
        <input type="hidden" name="project_ids" value="<?php echo $project_ids; ?>"/>
        <!--<input type="hidden" name="statusdata" value="<?php //echo $statusdata; ?>" />
    <input type="hidden" name="statustext" value="<?php //echo $statustext; ?>" /> -->
		<?php if ($day == '')
		{ ?>
            <input type="hidden" name="view" value="project"/>
		<?php }
		else
		{ ?>
            <input type="hidden" name="view" value="doska"/>
            <input type="hidden" name="week" value="<?php echo $weekCol; ?>"/>
            <input type="hidden" name="day" value="<?php echo $day; ?>"/>

		<?php } ?>

    </form>

<?php
if ($this->settings->get('footer')) echo '<p class="copyright">' . projectlogAdmin::footer() . '</p>';
echo JHTML::_('behavior.keepalive');

if ($day <> '')
{
	echo '</div>';
}
?>