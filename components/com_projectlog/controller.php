<?php

defined('_JEXEC') or die('Restricted access');

function _translit($_str)
{
	$tr = array(
		'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G',
		'Д' => 'D', 'Е' => 'E', 'Ж' => 'J', 'З' => 'Z', 'И' => 'I',
		'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
		'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
		'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'TS', 'Ч' => 'CH',
		'Ш' => 'SH', 'Щ' => 'SCH', 'Ъ' => '', 'Ы' => 'YI', 'Ь' => '',
		'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA', 'а' => 'a', 'б' => 'b',
		'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ж' => 'j',
		'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l',
		'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
		'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h',
		'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => 'y',
		'ы' => 'yi', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', ' ' => '_');

	return strtr($_str, $tr);
}

jimport('joomla.application.component.controller');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'admin.class.php');
require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'html.helper.php');
require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'query.php');

class projectlogController extends JController
{
	function display()
	{
		$params  = &JComponentHelper::getParams('com_projectlog');
		$offline = $params->get('offline');
		if ($offline == 1)
		{
			//Активен \ нет??
		}
		else
		{ // Права пользователя?
			{
				$user         = JFactory::getUser();
				$basic_access = projectlogHelperQuery::userAccess('basic_access', $user->gid);
				$log_access   = projectlogHelperQuery::userAccess('log_access', $user->gid);
				$doc_access   = projectlogHelperQuery::userAccess('doc_access', $user->gid);
				$pedit_access = projectlogHelperQuery::userAccess('pedit_access', $user->gid);
				$ledit_access = projectlogHelperQuery::userAccess('ledit_access', $user->gid);
				$dedit_access = projectlogHelperQuery::userAccess('dedit_access', $user->gid);
				$plog_admin   = ($user->get('gid') >= 25) ? true : false;
				define('BASIC_ACCESS', $basic_access);
				define('LOG_ACCESS', $log_access);
				define('DOC_ACCESS', $doc_access);
				define('PEDIT_ACCESS', $pedit_access);
				define('LEDIT_ACCESS', $ledit_access);
				define('DEDIT_ACCESS', $dedit_access);
				define('PLOG_ADMIN', $plog_admin);

				if (!BASIC_ACCESS)
				{
					echo '<div align="center">
                        <a href="http://www.thethinkery.net" target="_blank">
                            <img src="administrator/components/com_projectlog/assets/images/projectlog1.jpg" border="0" alt="Project Log" />
                        </a><br />
                        <strong>' . JText::_('PLOG NOT AUTHORIZED') . '</strong>
                      </div>';
				}
				else
				{
					parent::display();
				}
			}
			// Дальше если все в порядке

			$task = JRequest::getVar('task'); // Получаем запрос
			switch ($task) // анализируем запрос
			{
				case 'saveProject':
				{    //сохранить проект projectlogModelCat

					if (PEDIT_ACCESS):
						jimport('joomla.mail.helper');
						$settings = &JComponentHelper::getParams('com_projectlog');
						$post     = JRequest::get('post', JREQUEST_ALLOWRAW);
						JRequest::checkToken() or die('Invalid Token!');
						$model = $this->getModel('cat');
						$rid   = $model->saveProject($post);  //$rid=false;

						if ($rid)
						{
							$msg .= JText::_('PROJECT SAVED') . '<!--' . $log . '-->';
							if ($settings->get('approval') && !$post['id']) $msg .= ' -- ' . JText::_('APPROVAL REQUIRED');
							$type = 'message';
							if ($model->getError())
							{
								$msg  .= '<br>' . $model->getError();
								$type = 'notice';
							}

						}
						else
						{
							$msg  = JText::_('При сохранении проекта возникла ошибка ' . $model->getError());
							$type = 'error';

							$link = JRoute::_('index.php?option=com_projectlog&view=cat&id=' . $post['category'] . '&Itemid=' . $item_id, false);
							$this->setRedirect($link, $msg, $type);

							return;
						}
						$link = JRoute::_('index.php?option=com_projectlog&view=' . $post['view'] . '&id=' . $rid . '&Itemid=' . $post['Itemid'] . '&week=' . $post['week'] . '&day=' . $post['day'], false);
						$this->setRedirect($link, $msg, $type);
					else:
						JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

						return;
					endif;
				}
					break;

				case 'deleteProject':
				{ // удалить проект projectlogModelCat
					if (PEDIT_ACCESS):
						$id      = JRequest::getVar('id');
						$cat_id  = JRequest::getVar('category_id');
						$item_id = JRequest::getVar('Itemid');
						$model   = $this->getModel('cat');
						if ($model->deleteProject($id))
						{
							$msg  = JText::_('PROJECT DELETED');
							$type = 'message';
						}
						else
						{
							$msg  = JText::_('PROJECT NOT DELETED' . ' - ' . $model->getError());
							$type = 'notice';
						}
						$link = JRoute::_('index.php?option=com_projectlog&view=cat&id=' . $cat_id . '&Itemid=' . $item_id, false);
						$this->setRedirect($link, $msg, $type);
					else:
						JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

						return;
					endif;
				}
					break;

				case 'saveLog':
				{    // Сохранить примечание project
					if (LEDIT_ACCESS):
						jimport('joomla.mail.helper');
						$post = JRequest::get('post', JREQUEST_ALLOWRAW);
						JRequest::checkToken() or die('Invalid Token!');
						$model = $this->getModel('project');
						if ($model->saveLog($post))
						{
							$msg  = JText::_('LOG SAVED');
							$type = 'message';
						}
						else
						{
							$msg  = JText::_('LOG NOT SAVED' . ' - ' . $model->getError());
							$type = 'notice';
						}
						$link = JRoute::_('index.php?option=com_projectlog&view=' . $post['view'] . '&id=' . $post['project_id'] . '&week=' . $post['week'] . '&day=' . $post['day'] . '&Itemid=' . $post['Itemid'], false);
						$this->setRedirect($link, $msg, $type);
					else:
						JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

						return;
					endif;
				}
					break;

				case 'deleteLog':
				{    //удалить примечание project
					if (LEDIT_ACCESS):
						$id         = JRequest::getVar('id');
						$project_id = JRequest::getVar('project_id');
						$item_id    = JRequest::getVar('Itemid');
						$view       = JRequest::getVar('view');
						$model      = $this->getModel('project');
						if ($model->deleteLog($id))
						{
							$msg  = JText::_('LOG DELETED');
							$type = 'message';
						}
						else
						{
							$msg  = JText::_('LOG NOT DELETED' . ' - ' . $model->getError());
							$type = 'notice';
						}
						$link = JRoute::_('index.php?option=com_projectlog&view=' . $view . '&id=' . $project_id . '&week=' . $post['week'] . '&day=' . $post['day'] . '&Itemid=' . $item_id, false);
						$this->setRedirect($link, $msg, $type);
					else:
						JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

						return;
					endif;
				}
					break;

				case 'saveDoc':
				{    // Записать файл project
					if (DEDIT_ACCESS): // Если есть право
						global $mainframe;
						jimport('joomla.mail.helper');
						$post = JRequest::get('post'); // получаем переменные из запроса
						JRequest::checkToken() or die('Invalid Token!'); //проверяем корректность запроса
						$model = $this->getModel('project'); //Подключаем функции
						$file  = JRequest::getVar('document', null, 'files', 'array'); // получаем значения переменных из document (массив)
						$id    = JRequest::getVar('project_id');
						if ($file['name'])
						{ // Имя файла ??
							jimport('joomla.filesystem.file');
							if (!ctype_alnum($file['name']))
							{
								$file['name'] = _translit($file['name']);
							} //Транслитерация русских символов
							$file['name'] = strtolower(JFile::makeSafe($file['name'])); // чистим символы в имени файла


							$retr = $model->saveFile($file, $id);    // Запись файла

							if ( !( $retr === 'ERR' ) )
							{
								$post['path'] = $retr;
							}
							else
							{
								$link = JRoute::_('index.php?option=com_projectlog&view=project&layout=docform&id=' . $post['project_id'] . '&week=' . $post['week'] . '&day=' . $post['day'] . '&Itemid=' . $post['Itemid'], false);
								$msg  = JText::_('DOC NOT SAVED') . ' - ' . $file['name'] . '-' . JText::_('FILE NOT UPLOADED');;
								$type = 'notice';
								$mainframe->redirect($link, $msg, $type);
							}

							if ($model->saveDoc($post))
							{
								$msg  = JText::_('DOC SAVED');
								$type = 'message';
							}
							else
							{
								$msg  = JText::_('DOC NOT SAVED' . ' - (' . $model->getError());
								$type = 'notice';
							}

							$link = JRoute::_('index.php?option=com_projectlog&view=project&layout=docform&id=' . $post['project_id'] . '&week=' . $post['week'] . '&day=' . $post['day'] . '&Itemid=' . $post['Itemid'], false); //'index.php?option=com_projectlog&view='.$post['view'].'&id='.$post['project_id'].'&Itemid='.$post['Itemid']
							$this->setRedirect($link, $msg, $type);

						}
						else
						{
							$link = JRoute::_('index.php?option=com_projectlog&view=project&layout=docform&id=' . $post['project_id'] . '&week=' . $post['week'] . '&day=' . $post['day'] . '&Itemid=' . $post['Itemid'], false);
							$msg  = JText::_('NO FILE');
							$type = 'notice';
							$this->setRedirect($link, $msg, $type);
						}
					else:
						JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

						return;
					endif;
				}
					break;

				case 'deleteDoc':
				{    //project
					if (DEDIT_ACCESS):
						$id         = JRequest::getVar('id');
						$project_id = JRequest::getVar('project_id');
						$item_id    = JRequest::getVar('Itemid');
						$view       = JRequest::getVar('view');
						$model      = $this->getModel('project');
						if ($model->deleteDoc($id, $project_id))
						{
							$msg  = JText::_('DOC DELETED');
							$type = 'message';
						}
						else
						{
							$msg  = Jtext::_('DOC NOT DELETED') . ' - ' . $model->getError();
							$type = 'notice';
						}
						$link = JRoute::_('index.php?option=com_projectlog&view=' . $view . '&id=' . $project_id . '&week=' . $post['week'] . '&day=' . $post['day'] . '&Itemid=' . $item_id, false);
						$this->setRedirect($link, $msg, $type);
					else:
						JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

						return;
					endif;
				}
					break;

				case "projectOnsite":
				{    //projectlogModelCat
					if (PEDIT_ACCESS):
						$cid     = JRequest::getVar('project_edit');
						$id      = JRequest::getVar('id');
						$item_id = JRequest::getVar('Itemid');
						$view    = JRequest::getVar('view');
						$model   = $this->getModel('cat');
						$model->projectSitestatus($cid, 1);
						$link = JRoute::_('index.php?option=com_projectlog&view=' . $view . '&id=' . $id . '&Itemid=' . $item_id, false);
						$this->setRedirect($link, '');
					else:
						JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

						return;
					endif;
				}
					break;

				case "projectOffsite":
				{    //projectlogModelCat
					if (PEDIT_ACCESS):
						$cid     = JRequest::getVar('project_edit');
						$id      = JRequest::getVar('id');
						$item_id = JRequest::getVar('Itemid');
						$view    = JRequest::getVar('view');
						$model   = $this->getModel('cat');
						$model->projectSitestatus($cid, 0);
						$link = JRoute::_('index.php?option=com_projectlog&view=' . $view . '&id=' . $id . '&Itemid=' . $item_id, false);
						$this->setRedirect($link, '');
					else:
						JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

						return;
					endif;
				}
					break;

				case "changeStatus":
				{    //projectlogModelCat
					if (PEDIT_ACCESS):
						$cid     = JRequest::getVar('project_edit');
						$id      = JRequest::getVar('id');
						$item_id = JRequest::getVar('Itemid');
						$view    = JRequest::getVar('view');
						$model   = $this->getModel('cat');
						$model->changeStatus($cid);
						$link = JRoute::_('index.php?option=com_projectlog&view=' . $view . '&id=' . $id . '&week=' . $post['week'] . '&day=' . $post['day'] . '&Itemid=' . $item_id, false);
						$this->setRedirect($link, '');
					else:
						JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

						return;
					endif;
				}
					break;

				case 'saveLogo':
				{    // Записать Логотип project
					if (DEDIT_ACCESS): // Если есть право
						global $mainframe;
						jimport('joomla.mail.helper');
						$post = JRequest::get('post'); // получаем переменные из запроса
						JRequest::checkToken() or die('Invalid Token!'); //проверяем корректность запроса
						$model = $this->getModel('project'); //Подключаем функции
						$file  = JRequest::getVar('document', null, 'files', 'array'); // получаем значения переменных из document (массив)
						$id    = JRequest::getVar('project_id');
						$lid   = $model->existLogo($id);

						if ($lid)
						{
							if ($model->deleteLogo($lid, $id))
							{
								$msg  = JText::_('LOG RELOAD') . '3' . $id . $lid;
								$type = 'message';
							}
							$msg  = JText::_('LOG RELOAD2') . '2' . $id . $lid;
							$type = 'message';
						}

						if ($file['name'])
						{ // Имя файла ??
							jimport('joomla.filesystem.file');
							if (!ctype_alnum($file['name']))
							{
								$file['name'] = _translit($file['name']);
							} //Транслитерация русских символов
							$file['name'] = strtolower(JFile::makeSafe($file['name'])); // чистим символы в имени файла


							$error = $model->saveFile($file, $id);    // Запись файла

							if (!($error==='ERR'))
							{
								$post['path'] = $file['name'];
								//Делаем миниатюры
								$dest  = JPATH_SITE . DS . 'media' . DS . 'com_projectlog' . DS . 'docs' . DS . $id . DS . $file['name'];
								$dest2 = JPATH_SITE . DS . 'media' . DS . 'com_projectlog' . DS . 'docs' . DS . $id . DS . '80x80_' . $file['name'];
								$dest3 = JPATH_SITE . DS . 'media' . DS . 'com_projectlog' . DS . 'docs' . DS . $id . DS . '227x219_' . $file['name'];//width="227" height="219"

								$im = imagecreatefromjpeg($dest); //создаем поток
								$ox = imagesx($im);  //ширина и высота  исходного изображения
								$oy = imagesy($im);

								$nx  = 80;   //ширина и высота  конечного изображения
								$ny  = 80;
								$nx2 = 227;   //ширина и высота  конечного изображения
								$ny2 = 219;

								$nm = imagecreatetruecolor($nx, $ny);  //пустое изображение
								imagecopyresampled($nm, $im, 0, 0, 0, 0, $nx, $ny, $ox, $oy); //преобразовываем изображение
								imagejpeg($nm, $dest2);     //сохраняем изображение

								$nm2 = imagecreatetruecolor($nx2, $ny2);  //пустое изображение
								imagecopyresampled($nm2, $im, 0, 0, 0, 0, $nx2, $ny2, $ox, $oy); //преобразовываем изображение
								imagejpeg($nm2, $dest3);     //сохраняем изображение

							}
							else
							{
								$link = JRoute::_('index.php?option=com_projectlog&view=project&layout=lnkform&id=' . $post['project_id'] . '&week=' . $post['week'] . '&day=' . $post['day'] . '&Itemid=' . $post['Itemid'], false);
								$msg  = JText::_('DOC NOT SAVED') . ' - ' . $file['name'] . '-' . JText::_('FILE NOT UPLOADED');;
								$type = 'notice';
								$mainframe->redirect($link, $msg, $type);
							}

							if ($model->saveLogo($post))
							{
								$msg  = JText::_('DOC SAVED');
								$type = 'message';
							}
							else
							{
								$msg  = JText::_('DOC NOT SAVED' . ' - (' . $model->getError());
								$type = 'notice';
							}
							$link = JRoute::_('index.php?option=com_projectlog&view=' . $post['view'] . '&id=' . $post['project_id'] . '&week=' . $post['week'] . '&day=' . $post['day'] . '&Itemid=' . $post['Itemid'], false); //'index.php?option=com_projectlog&view='.$post['view'].'&id='.$post['project_id'].'&Itemid='.$post['Itemid']
							$this->setRedirect($link, $msg, $type);
						}
						else
						{
							$link = JRoute::_('index.php?option=com_projectlog&view=project&layout=lnkform&id=' . $post['project_id'] . '&week=' . $post['week'] . '&day=' . $post['day'] . '&Itemid=' . $post['Itemid'], false);
							$msg  = JText::_('NO FILE');
							$type = 'notice';
							$this->setRedirect($link, $msg, $type);
						}
					else:
						JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

						return;
					endif;
				}
					break;

				case 'deleteLogo':
				{    //project
					if (DEDIT_ACCESS):
						$id         = JRequest::getVar('id');
						$project_id = JRequest::getVar('project_id');
						$item_id    = JRequest::getVar('Itemid');
						$view       = JRequest::getVar('view');
						$model      = $this->getModel('project');
						if ($model->deleteLogo($id, $project_id))
						{
							$msg  = JText::_('DOC DELETED');
							$type = 'message';
						}
						else
						{
							$msg  = Jtext::_('DOC NOT DELETED') . ' - ' . $model->getError() . $id . $project_id;
							$type = 'notice';
						}
						$link = JRoute::_('index.php?option=com_projectlog&view=' . $view . '&id=' . $project_id . '&week=' . $post['week'] . '&day=' . $post['day'] . '&Itemid=' . $item_id, false);
						$this->setRedirect($link, $msg, $type);
					else:
						JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

						return;
					endif;
				}
					break;

				case 'move':
				{    //сохранить проект projectlogModelCat
					if (DEDIT_ACCESS):
						//$id = JRequest::getVar('id');
						$project_id = JRequest::getVar('project_id');
						$item_id    = JRequest::getVar('Itemid');
						$view       = JRequest::getVar('view');
						$week       = JRequest::getVar('week');
						$day        = JRequest::getVar('day');
						//$category=JRequest::getVar('category');
						$mov   = JRequest::getVar('mov');
						$msg   = JRequest::getVar('msg');
						$model = $this->getModel('project');

						if ($model->moveProject($mov, $project_id, $msg))
						{
							$msg  = JText::_('Проект отправлен:  ' . date('d-m-Y'));
							$type = 'message';
						}
						else
						{
							$msg  = Jtext::_('NOT MOVE') . ' Error-' . $model->getError() . ' $mov-' . $mov . ' $id-' . $project_id;
							$type = 'notice';
						}
						$link = JRoute::_('index.php?option=com_projectlog&view=' . $view . '&id=' . $project_id . '&Itemid=' . $item_id . '&week=' . $week . '&day=' . $day, false);
						$this->setRedirect($link, $msg, $type);
					else:
						JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

						return;
					endif;
				}
					break;

				case 'saveCalendar':
				{    //сохранить проект projectlogModelCat
					if (PEDIT_ACCESS):
						jimport('joomla.mail.helper');
						$settings = &JComponentHelper::getParams('com_projectlog');
						$post     = JRequest::get('post', JREQUEST_ALLOWRAW);
						JRequest::checkToken() or die('Invalid Token!');
						$model = $this->getModel('cat');
						if ($model->saveProject($post))
						{
							$msg = JText::_('PROJECT SAVED');
							if ($settings->get('approval') && !$post['id']) $msg .= ' -- ' . JText::_('APPROVAL REQUIRED');
							$type = 'message';
						}
						else
						{
							$msg  = JText::_('PROJECT NOT SAVED' . ' ---44 ' . $model->getError());
							$type = 'notice';
						}

						$link = JRoute::_('index.php?option=com_projectlog&view=' . $post['view'] . '&id=' . $post['id'] . '&Itemid=' . $post['Itemid'], false);
						$this->setRedirect($link, $msg, $type);
					else:
						JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

						return;
					endif;
				}
					break;

				case 'week':
				{
					$category = JRequest::getVar('category');
					$week     = JRequest::getVar('week');
					$day      = JRequest::getVar('day');
					$Itemid   = JRequest::getVar('Itemid');
					$link     = JRoute::_('index.php?option=com_projectlog&view=doska&id=' . $category . '&week=' . $week . '&day=' . $day . '&Itemid=' . $Itemid, false);
					$this->setRedirect($link, $msg, $type);
				}
					break;

				case 'brak':
				{
					if (PEDIT_ACCESS):
						jimport('joomla.mail.helper');
						$settings = &JComponentHelper::getParams('com_projectlog');
						$post     = JRequest::get('post', JREQUEST_ALLOWRAW);
						JRequest::checkToken() or die('Invalid Token!');
						$model = $this->getModel('cat');

						$model->brak($post);
						$link = JRoute::_('index.php?option=com_projectlog&view=project&id=' . $post['id'] . '&week=' . $post['week'] . '&day=' . $post['day'] . '&Itemid=' . $post['Itemid'], false);//
						// 'index.php?option=com_projectlog&view=project&project_id='. $this->project->id.'&task=brak'&id=".$this->project->id.
						$this->setRedirect($link, '');
					else:
						JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

						return;
					endif;
				}
					break;

				case 's_f_on_serv':
				{
					if (PEDIT_ACCESS):
						//jimport( 'joomla.mail.helper' );
						// $settings   = & JComponentHelper::getParams( 'com_projectlog' );
						$post = JRequest::get('', JREQUEST_ALLOWRAW);
						//JRequest::checkToken() or die( 'Invalid Token!' );

						$model = $this->getModel('project');

						$path = $model->s_f_on_serv($post);
						$link = JRoute::_('index.php?option=com_projectlog&view=project&id=' . $post['project_id'] . '&week=' . $post['week'] . '&day=' . $post['day'] . '&Itemid=50', false);
						if (!stripos($path, 'ERROR'))
						{
							$this->setRedirect($link, 'Файлы скопированы. <br>Путь:<br><h3> \\\\zeppelin\\project\\' . $path . '</h3> <br> Cкопируйте этот путь в адресную строку "Проводника" или любого другого файлового менеджера. ');
						}
						else
						{
							$this->setRedirect($link, 'Ошибка "' . $path . '"');
						}
					else:
						JError::raiseWarning(403, JText::_('$post'));

						return;
					endif;
				}
					break;

				case 'brigadir':
				{
					if (PEDIT_ACCESS):
						$post  = JRequest::get('', JREQUEST_ALLOWRAW);
						$model = $this->getModel('project');
						$path  = $model->brigadir($post);

						if (!stripos($path, 'ERROR'))
						{
							$link = JRoute::_('index.php?option=com_projectlog&view=project&id=' . $post['id']);
							$this->setRedirect($link);
						}
						else
						{
							$err = print_R($post, true);
							JError::raiseWarning(403, JText::_('Ошибка сохранения Бригадира' . $err));
						}

					else:
						JError::raiseWarning(403, JText::_('$post'));

						return;
					endif;

				}
					break;

				case 'copyProject':
				{    //скопировать проект projectlogModelCat
					if (PEDIT_ACCESS):
						jimport('joomla.mail.helper');
						$settings = &JComponentHelper::getParams('com_projectlog');
						$post     = JRequest::get('', JREQUEST_ALLOWRAW);

						$model = $this->getModel('cat');
						$rid   = $model->copyProject($post);

						if ($rid)
						{
							$msg = JText::_('Проект скопирован. ВНИМАНИЕ! ЭТО КОПИЯ' . $model->getError());
							if ($settings->get('approval') && !$post['id']) $msg .= ' -- ' . JText::_('APPROVAL REQUIRED');
							$type = 'message';
						}
						else
						{
							$msg  = JText::_('PROJECT NOT SAVED' . ' ---44 ' . $model->getError());
							$type = 'notice';
						}
						$link = JRoute::_('index.php?option=com_projectlog&view=project' .
							'&id=' . $rid .//заменить
							'&week=' . $post['week'] .
							'&day=' . $post['day']
							, false);

						$this->setRedirect($link, $msg, $type);
					else:
						JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

						return;
					endif;

				}
					break;

				case 'saveAkt':
				{    // Записать Логотип project
					if (DEDIT_ACCESS): // Если есть право
						global $mainframe;
						jimport('joomla.mail.helper');
						$post = JRequest::get('post'); // получаем переменные из запроса
						JRequest::checkToken() or die('Invalid Token!'); //проверяем корректность запроса
						$model = $this->getModel('project'); //Подключаем функции
						$file  = JRequest::getVar('document', null, 'files', 'array'); // получаем значения переменных из document (массив)
						$id    = JRequest::getVar('project_id');

						//TODO нужнали проверка на существование файла
						/*$lid   = $model->existLogo($id);

						if ($lid)
						{
							if ($model->deleteLogo($lid, $id))
							{
								$msg  = JText::_('LOG RELOAD') . '3' . $id . $lid;
								$type = 'message';
							}
							$msg  = JText::_('LOG RELOAD2') . '2' . $id . $lid;
							$type = 'message';
						}*/

						if ($file['name'])
						{ // Имя файла ??
							jimport('joomla.filesystem.file');
							if (!ctype_alnum($file['name']))
							{
								$file['name'] = _translit($file['name']);
							} //Транслитерация русских символов
							$file['name'] = JFile::makeSafe($file['name']); // чистим символы в имени файла

							$retr = $model->saveFile($file, $id);    // Запись файла

							if ( !($retr === 'ERR') )
							{
								$file['name'] = $retr;
							}
							else
							{
								$link = JRoute::_('index.php?option=com_projectlog&view=project&layout=orderactform&id=' . $post['project_id'] . '&week=' . $post['week'] . '&day=' . $post['day'] . '&Itemid=' . $post['Itemid'], false);
								$msg  = JText::_('DOC NOT SAVED') . ' - ' . $file['name'] . '-' . JText::_('FILE NOT UPLOADED');
								$type = 'notice';
								$mainframe->redirect($link, $msg, $type);
							}

							$post['path'] = $file;

							if ($model->saveAkt($post))
							{
								$msg  = JText::_('DOC SAVED');
								$type = 'message';
							}
							else
							{
								$msg  = JText::_('DOC NOT SAVED' . ' - (' . $model->getError());
								$type = 'notice';
							}

							//TODO Перкинуть проекты в архив ???

							$link = JRoute::_('index.php?option=com_projectlog&mov=10&task=move&view=' . $post['view'] . '&project_id=' . $post['project_id'] . '&week=' . $post['week'] . '&day=' . $post['day'] . '&Itemid=' . $post['Itemid'], false); //'index.php?option=com_projectlog&view='.$post['view'].'&id='.$post['project_id'].'&Itemid='.$post['Itemid']
							$this->setRedirect($link, $msg, $type);
						}
						else
						{
							$link = JRoute::_('index.php?option=com_projectlog&view=project&layout=orderactform&id=' . $post['project_id'] . '&week=' . $post['week'] . '&day=' . $post['day'] . '&Itemid=' . $post['Itemid'], false);
							$msg  = JText::_('NO FILE');
							$type = 'notice';
							$this->setRedirect($link, $msg, $type);
						}
					else:
						JError::raiseWarning(403, JText::_('PLOG NOT AUTHORIZED'));

						return;
					endif;
				}
					break;
			}
		}
	}
}

?>