<?php
/**
 * @version       1.5.3 2009-10-12
 * @package       Joomla
 * @subpackage    Project Log
 * @copyright (C) 2009 the Thinkery
 * @link          http://thethinkery.net
 * @license       GNU/GPL see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

class projectlogHTML
{

	function buildNoResults($accent_color, $wrapper = null)
	{
		$html = '';
		if ($wrapper) $html .= '<table class="rctable">';
		$html .= '<tr>
					 <td colspan="2" align="center">
						<div class="rc_noresults" style="border-color: ' . $accent_color . ';">
							<img src="administrator' . DS . 'components' . DS . 'com_projectlog' . DS . 'assets' . DS . 'images' . DS . 'projectlog1.jpg" alt="' . JText::_('NO RECORDS') . '" /><br />
							' . JText::_('NO RECORDS TEXT') . '
						</div>
				    </td>
				 </tr>';
		if ($wrapper) $html .= '</table>';

		return $html;
	}

	function buildThinkeryFooter($accent_color)
	{
		$html = '';
		$html .= '<div class="rc_project_footer">
						' . JText::_('PROJECT LOG FOOTER') . ' ' . projectlogAdmin::_getversion() . ' ' . JText::_('BY') . ' <a href="http://www.thethinkery.net" target="_blank" style="color: ' . $accent_color . ';">theThinkery.net</a>
				  </div>';

		return $html;
	}

	function sentence_case($string)
	{
		$sentences  = preg_split('/([.?!]+)/', $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		$new_string = '';
		foreach ($sentences as $key => $sentence)
		{
			$new_string .= ($key & 1) == 0 ?
				ucfirst(strtolower(trim($sentence))) :
				$sentence . ' ';
		}

		return trim($new_string);
	}

	function getItemid($view)
	{
		//get item id for agent listings page
		$database = JFactory::getDBO();
		$query    = "SELECT id FROM #__menu WHERE link LIKE '%index.php?option=com_projectlog&view=" . $view . "' AND published = 1";
		$database->setQuery($query);

		return $database->loadResult();
	}

	function statusSelect($tag, $attrib, $sel = null)
	{
		$stats   = array();
		$stats[] = JHTML::_('select.option', '', JText::_('SELECT'));
		$stats[] = JHTML::_('select.option', JText::_('IN PROGRESS'), JText::_('IN PROGRESS'));
		$stats[] = JHTML::_('select.option', JText::_('ON HOLD'), JText::_('ON HOLD'));
		$stats[] = JHTML::_('select.option', JText::_('COMPLETE'), JText::_('COMPLETE'));

		return JHTML::_('select.genericlist', $stats, $tag, $attrib, 'value', 'text', $sel);
	}


	function colorSelect($tag, $attrib, $sel = null)
	{

		$color   = array();
		$color[] = JHTML::_('select.option', 'fff', JText::_('COLOR'));
		$color[] = JHTML::_('select.option', '000', JText::_('COLOR1'));
		$color[] = JHTML::_('select.option', '00f', '<div style="background-color:#00f;">wwwww</div>');
		$color[] = JHTML::_('select.option', '0ff', '<div style="background-color:#0ff;">wwwwww</div>');
		$color[] = JHTML::_('select.option', 'f00', '<div style="background-color:#f00;">wwwwwww;</div>');
		$color[] = JHTML::_('select.option', 'ff0', '<font style="background-color:#ff0;">wwwwwww</font>');
		$color[] = JHTML::_('select.option', '0f0', '<font style="background-color:#0f0;">wwwwwww</font>');
		$color[] = JHTML::_('select.option', 'f0f', '<font style="background-color:#f0f;">wwwwwww</font>');
		$color[] = JHTML::_('select.option', '888', '<font style="background-color:#888;">wwwwwwww</font>');

		return JHTML::_('select.genericlist', $color, $tag, $attrib, 'value', 'text', $sel);
	}


	function groupSelect($tag, $attrib, $sel = null)
	{
		$database = JFactory::getDBO();
		$groups   = array();
		$groups[] = JHTML::_('select.option', '', JText::_('GROUP'));
		$database->setQuery("SELECT DISTINCT(id) AS 'value', name AS 'text' FROM #__projectlog_groups ORDER BY name ASC");

		$groups = array_merge($groups, $database->loadObjectList());

		return JHTML::_('select.genericlist', $groups, $tag, $attrib, 'value', 'text', $sel);
	}

	function catSelect($tag, $attrib, $sel = null)
	{
		$database = JFactory::getDBO();
		$cats     = array();
		$database->setQuery("SELECT DISTINCT(id) AS 'value', title AS 'text' FROM #__projectlog_categories ORDER BY id ASC");
		$cats = array_merge($cats, $database->loadObjectList());

		return JHTML::_('select.genericlist', $cats, $tag, $attrib, 'value', 'text', $sel);
	}

	function projectSelect($tag, $attrib, $sel = null)
	{
		$database   = JFactory::getDBO();
		$projects   = array();
		$projects[] = JHTML::_('select.option', '', JText::_('PROJECT'));
		$database->setQuery("SELECT DISTINCT(id) AS 'value', title AS 'text' FROM #__projectlog_projects WHERE published = 1 ORDER BY title ASC");

		$projects = array_merge($projects, $database->loadObjectList());

		return JHTML::_('select.genericlist', $projects, $tag, $attrib, 'value', 'text', $sel);
	}

	function getGroupName($group_id)
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT name FROM #__projectlog_groups WHERE id = " . $group_id);

		return $database->loadResult();
	}

	function getGroupCount($group_id)
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT COUNT(id) FROM #__projectlog_groups_mid WHERE group_id = " . $group_id);

		return $database->loadResult();
	}

	function getCatName($cat_id)
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT title FROM #__projectlog_categories WHERE id = " . $cat_id);

		return $database->loadResult();
	}

	function getCatCount($group_id)
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT COUNT(id) FROM #__projectlog_projects WHERE group_access = " . $group_id);

		return $database->loadResult();
	}

	function getProjectName($project_id)
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT title FROM #__projectlog_projects WHERE id = " . $project_id);

		return $database->loadResult();
	}

	function getUserName($user_id)
	{
		If($user_id > 0){
		$database = JFactory::getDBO();
		$database->setQuery("SELECT name FROM #__users WHERE id = " . $user_id);

		return $database->loadResult();}
		else return "";
	}

	function getContactName($user_id)
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT name FROM  #__contact_details WHERE id = " . $user_id);

		return $database->loadResult();
	}

	function getUserPChekc($user_id)
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT pochta_chek FROM #__users WHERE id = " . $user_id);

		return $database->loadResult();
	}

	function userDetails($user_id)
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT * FROM #__contact_details WHERE user_id = " . $user_id);

		return $database->loadObject();
	}

	function userEmail($user_id)
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT email FROM #__users WHERE id = " . $user_id);

		return $database->loadResult();
	}


	/**
	 * ПОЧТА
	 *   Всем админам и менеджерам
	 *   brak - поставили брак
	 *   manager - Смеили менеджера
	 * @param $type - string Тема письма ( brak , manager , release_date , podrydchik , akt)
	 * @param $user - int id Текущего пользователя
	 * @param $project - int id проекта
	 * @param $onLoadM -
	 * @param $attachment - файл
	 */
	function notifyAdmin($type, $user, $project, $onLoadM, $attachment)
	{
		global $mainframe;
		jimport('joomla.mail.helper');
		$mode      = 1;
		//$date      = date('M d Y');
		$ipaddress = $_SERVER['REMOTE_ADDR'];
		$uname     = ($user) ? $user->name : 'N/A';
		//$uemail    = ($user) ? $user->email : 'N/A';

		$db    = JFactory::getDBO();
		$query = 'SELECT * FROM #__projectlog_projects WHERE id = ' . $project;
		$db->setQuery($query);
		$proect = $db->loadObject();


		switch ($type)
		{

			case 'brak':
				if ($type and $proect->category == 12 and $proect->location_spec <> '')
				{
					$subject  = $mainframe->getCfg('sitename') . ' ' . JText::_('BRAK');
					$add_type = sprintf(JText::_('BRAK TITLE'), $mainframe->getCfg('sitename'));

					$m_email  = projectlogHTML::userEmail($proect->manager);
					//$m_name   = projectlogHTML::getUserName($proect->manager);
					$t_email  = projectlogHTML::userEmail($proect->technicians);
					//$t_name   = projectlogHTML::getUserName($proect->technicians);
					//$a_email  = 'baza@zepp.ru';
					$a2_email = 'andrey.0008@yandex.ru';
                    $a3_email = 'black@zepp.ru';
					$link     = JRoute::_('http://zeppelin/zepp/index.php?option=com_projectlog&view=project&id=' . $project, false);


					$admin_email = $mainframe->getCfg('mailfrom');
					$z_email     = 'zepp@zepp.ru';

					$body = '<p>' . $add_type . '</p>
		                     <p><strong>' . JText::_('PROJECT') . ':</strong><br />' . $proect->title . '</p>
		                     <p><strong>' . JText::_('RELEASE NUM') . ':</strong><br />' . $proect->release_id . '</p>
		                     <br /><strong>  <a href="' . $link . '"> Ссылка на проект </a>' .
						'<p><strong>' . JText::_('BRAK CREATED BY') . ':</strong><br />' . $uname . '</p>
		                     <p>  <i> ' . $proect->location_spec . '</i><br />
		                     <span style="font-size: 10px; color: #999;">

		                        IP: ' . $ipaddress . '
		                     </span>
		                     </p>';

					JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $z_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);
					JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $admin_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);
                    JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $a3_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);


					if (projectlogHTML::getUserPChekc($proect->manager) == 1)
						JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $m_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);
					if (projectlogHTML::getUserPChekc($proect->technicians) == 1)
						JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $t_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);
					if (projectlogHTML::getUserPChekc(97) == 1)
						JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $a2_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);
				}
				break;

			case 'manager':
				$subject  = JText::_('NEW MANAGER');
				$add_type = JText::_('NEW MANAGER');

				$m_email  = projectlogHTML::userEmail($proect->manager);
				$m_name   = projectlogHTML::getUserName($proect->manager);
				$t_email  = projectlogHTML::userEmail($proect->technicians);
				//$t_name   = projectlogHTML::getUserName($proect->technicians);
				$a_email  = 'baza@zepp.ru';
				$a2_email = 'andrey.0008@yandex.ru';
				$link     = JRoute::_('http://zeppelin/zepp/index.php?option=com_projectlog&view=project&id=' . $project, false);

				$body = '<p>' . $add_type . '</p>
		                     <p><strong>' . JText::_('PROJECT') . ':</strong><br />' . $proect->title . '</p>
		                     <p><strong>' . JText::_('RELEASE NUM') . ':</strong><br />' . $proect->release_id . '</p>
		                     <br /><strong>  <a href="' . $link . '"> Ссылка на проект </a>' .
							'<p><strong>' . $uname . JText::_('NEW MANAGER CREATED BY') . ':</strong></p>
		                     <p> Предыдущий менеджер: ' . projectlogHTML::getUserName($onLoadM) . '. <br />Текущий:' . $m_name . ' . </p>
		                     <p>  <i> ' . $proect->location_spec . '</i><br />
		                     	<span style="font-size: 10px; color: #999;">

		                        IP: ' . $ipaddress . '
		                     </span>
		                     </p>';

				if (projectlogHTML::getUserPChekc($proect->manager) == 1)
					JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $m_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);
				if (projectlogHTML::getUserPChekc($onLoadM) == 1)
					JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), projectlogHTML::userEmail($onLoadM), $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);

				// Для ZeppProjekt
				$db    = JFactory::getDBO();
				$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( " . $user->id . " ,  '" . $body . "' , " . $proect->manager . " , 4 , " . $proect->id . " ) ;";
				$db->setQuery($query);
				if (!$db->query())
				{
					$this->setError($db->getErrorMsg());
					//return false;
				}
				$db    = JFactory::getDBO();
				$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( " . $user->id . " ,  '" . $body . "' , " . $user->id . " , 4 , " . $proect->id . " ) ;";
				$db->setQuery($query);
				if (!$db->query())
				{
					$this->setError($db->getErrorMsg());
					//return false;
				}

				if ($proect->category == 7 or $proect->category == 8 or $proect->category == 13)
				{
					if (projectlogHTML::getUserPChekc($proect->technicians) == 1)
						JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $t_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);
					//if (projectlogHTML::getUserPChekc(87) == 1)
					// JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $a_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);
					if (projectlogHTML::getUserPChekc(97) == 1)
						JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $a2_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);

					// Для ZeppProjekt
					/*$db = JFactory::getDBO();
					 $query ="INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( ".$user->id." ,  '".$body."' , 87 , 4 , ".$proect->id. " ) ;";
					 $db->setQuery($query);
					 if(!$db->query()) {
						  $this->setError($db->getErrorMsg());
						  //return false;
					 }   */
					$db    = JFactory::getDBO();
					$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( " . $user->id . " ,  '" . $body . "' , 97 , 4 , " . $proect->id . " ) ;";
					$db->setQuery($query);
					if (!$db->query())
					{
						$this->setError($db->getErrorMsg());
						//return false;
					}
					$db    = JFactory::getDBO();
					$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( " . $user->id . " ,  '" . $body . "' , " . $proect->technicians . " , 4 , " . $proect->id . " ) ;";
					$db->setQuery($query);
					if (!$db->query())
					{
						$this->setError($db->getErrorMsg());
						//return false;
					}
				}
				break;

			case 'release_date':
				$subject  = JText::_('NEW RELEASE_DATE');
				$add_type = JText::_('NEW RELEASE_DATE');

				$m_email  = projectlogHTML::userEmail($proect->manager);
				$m_name   = projectlogHTML::getUserName($proect->manager);
				$t_email  = projectlogHTML::userEmail($proect->technicians);
				$t_name   = projectlogHTML::getUserName($proect->technicians);  //  projectlogHTML::getUserName($onLoadM)
				$a_email  = 'baza@zepp.ru';
				$a2_email = 'andrey.0008@yandex.ru';
				$link     = JRoute::_('http://zeppelin/zepp/index.php?option=com_projectlog&view=project&id=' . $project, false);

				$body = '<p>' . $add_type . '</p>
		                     <p><strong>' . JText::_('PROJECT') . ':</strong><br />' . $proect->title . '</p>
		                     <p><strong>' . JText::_('RELEASE NUM') . ':</strong><br />' . $proect->release_id . '</p>
		                     <br /><strong>  <a href="' . $link . '"> Ссылка на проект </a>' .
					'<p><strong>' . $uname . JText::_('NEW RELEASE_DATE CREATED BY') . ':</strong></p>
		                     <p> Предыдущая дата реализации:&nbsp; ' . JHTML::_('date', $onLoadM, JText::_('%d.%m.%Y')) . '. <br />Перенесена на:&nbsp;' . JHTML::_('date', $proect->release_date, JText::_('%d.%m.%Y')) . ' . </p>
		                     <p>  <i> ' . $proect->location_spec . '</i><br />
		                     <span style="font-size: 10px; color: #999;">

		                        IP: ' . $ipaddress . '
		                     </span>
		                     </p>';
				// JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), 'vl@zepp.ru', $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);
				if (projectlogHTML::getUserPChekc($proect->manager) == 1)
					JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $m_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);

				// Для ZeppProjekt
				$db    = JFactory::getDBO();
				$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( " . $user->id . " ,  '" . $body . "' , " . $proect->manager . " , 4 , " . $proect->id . " ) ;";
				$db->setQuery($query);
				if (!$db->query())
				{
					$this->setError($db->getErrorMsg());
					//return false;
				}


				if ($proect->category == 7 or $proect->category == 8 or $proect->category == 13)
				{
					if (projectlogHTML::getUserPChekc($proect->technicians) == 1)
						JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $t_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);
					//if (projectlogHTML::getUserPChekc(87) == 1)
					// JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $a_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);
					if (projectlogHTML::getUserPChekc(97) == 1)
						JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $a2_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);

					// Для ZeppProjekt

					$db    = JFactory::getDBO();
					$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( " . $user->id . " ,  '" . $body . "' , 97 , 4 , " . $proect->id . " ) ;";
					$db->setQuery($query);
					if (!$db->query())
					{
						$this->setError($db->getErrorMsg());
						//return false;
					}
					$db    = JFactory::getDBO();
					$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( " . $user->id . " ,  '" . $body . "' , " . $proect->technicians . " , 4 , " . $proect->id . " ) ;";
					$db->setQuery($query);
					if (!$db->query())
					{
						$this->setError($db->getErrorMsg());
						//return false;
					}
				}
				break;

			case 'podrydchik'  :
				$subject  = JText::_('был изменен или установлен ПОДРЯДЧИК');
				$add_type = JText::_('был изменен или установлен ПОДРЯДЧИК');

				$m_email  = projectlogHTML::userEmail($proect->manager);
				$m_name   = projectlogHTML::getUserName($proect->manager);
				$t_email  = projectlogHTML::userEmail($proect->technicians);
				$t_name   = projectlogHTML::getUserName($proect->technicians);
				$a_email  = 'baza@zepp.ru';
				$a2_email = 'andrey.0008@yandex.ru';
				$link     = JRoute::_('http://zeppelin/zepp/index.php?option=com_projectlog&view=project&id=' . $project, false);

				$body = '<p>' . $add_type . '</p>
						<p><strong>' . JText::_('PROJECT') . ':</strong><br />' . $proect->title . '</p>
						<p><strong>' . JText::_('RELEASE NUM') . ':</strong><br />' . $proect->release_id . '</p>
						<br /><strong>  <a href="' . $link . '"> Ссылка на проект </a>' .
					'<p><strong> Изминения произвел:' . $uname . '</strong></p>
						<p> Текущий подрядчик:&nbsp; ' . $proect->podrydchik . ' . </p>
						<p>  <i> ' . $proect->location_spec . '</i><br />
						<span style="font-size: 10px; color: #999;">IP: ' . $ipaddress . '</span> </p>';

				if (projectlogHTML::getUserPChekc($proect->manager) == 1)
					JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $m_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);

				// Для ZeppProjekt
				$db    = JFactory::getDBO();
				$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( " . $user->id . " ,  '" . $body . "' , " . $proect->manager . " , 4 , " . $proect->id . " ) ;";
				$db->setQuery($query);
				if (!$db->query())
				{
					$this->setError($db->getErrorMsg());
				}

				if ($proect->category == 7 or $proect->category == 8 or $proect->category == 13)
				{
					if (projectlogHTML::getUserPChekc($proect->technicians) == 1)
						JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $t_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);
					if (projectlogHTML::getUserPChekc(97) == 1)
						JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $a2_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);

					$db    = JFactory::getDBO();
					$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( " . $user->id . " ,  '" . $body . "' , 97 , 4 , " . $proect->id . " ) ;";
					$db->setQuery($query);
					if (!$db->query())
					{
						$this->setError($db->getErrorMsg());
					}
					$db    = JFactory::getDBO();
					$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( " . $user->id . " ,  '" . $body . "' , " . $proect->technicians . " , 4 , " . $proect->id . " ) ;";
					$db->setQuery($query);
					if (!$db->query())
					{
						$this->setError($db->getErrorMsg());
					}
				}
				break;

			case 'akt':

				$mailer =& JFactory::getMailer();
				$mailer->isHTML(true);


				$config =& JFactory::getConfig();
				$sender = array($config->getValue('config.mailfrom'), $config->getValue('config.fromname'));
				$mailer->setSender($sender);


				$subject  = $mainframe->getCfg('sitename');
				$add_type = sprintf(JText::_('Подписан акт'), $mainframe->getCfg('sitename'));

				//$m_name   = projectlogHTML::getUserName($proect->manager);

				$link = JRoute::_('http://zeppelin/zepp/index.php?option=com_projectlog&view=project&id=' . $project, false);

				$emails            = array();
				$emails['glavbuh'] = "ksenia@zepp.ru";

				$emails['z_email'] = "zepp@zepp.ru";

				if (projectlogHTML::getUserPChekc($proect->manager) == 1)
				{
					$emails['m_email'] = projectlogHTML::userEmail($proect->manager);
				}


				if (projectlogHTML::getUserPChekc(87) == 1)
				{
					$emails['a_email'] = "baza@zepp.ru";

				}
				if (projectlogHTML::getUserPChekc(97) == 1)
				{
					$emails['a2_email'] = "andrey.0008@yandex.ru";
				}

				$mailer->addRecipient($emails);

				$body = '<p>' . $add_type . '</p>
		                 <p><strong>' . JText::_('PROJECT') . ':</strong><br />' . $proect->title . '</p>
		                 <p><strong>' . JText::_('RELEASE NUM') . ':</strong><br />' . $proect->release_id . '</p>
		                 <br /><strong>  <a href="' . $link . '"> Ссылка на проект </a>' .
					'<p><strong>' . JText::_('Подписан Акт') . ':</strong><br />' . $uname . '</p>
		                 <p>  <i> ' . $proect->location_spec . '</i><br />
		                 <span style="font-size: 10px; color: #999;">

		                 IP: ' . $ipaddress . '
		                 </span>
		                 </p>';

				$mailer->setSubject($subject);
				$mailer->setBody($body);
				$mailer->addAttachment($attachment);
				$mailer->Send();

				// Сообщение в программу
				if ($proect->category > 6)
				{
					$db    = JFactory::getDBO();

					$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) 
											VALUES ( " . $user->id . " ,  '" . $body . "' , " . 87 . " , 4 , " . $proect->id . " ) ;";
					$db->setQuery($query);
					if (!$db->query())
					{
						$this->setError($db->getErrorMsg());
					}


					$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) 
											VALUES ( " . $user->id . " ,  '" . $body . "' , 97 , 4 , " . $proect->id . " ) ;";
					$db->setQuery($query);
					if (!$db->query())
					{
						$this->setError($db->getErrorMsg());
					}
				}
				break;
		}


	}


/* ПОЧТА
*   log - добавлен комментарий
*   doc - добавлен файл
*   del_doc - удален фйл
*   del_proj удален прект
*
*/

function notifyDoc($type, $user, $project_id, $faile)
{
	global $mainframe;
	jimport('joomla.mail.helper');
	$mode      = 1;
	$date      = date('M d Y');
	$ipaddress = $_SERVER['REMOTE_ADDR'];
	$uname     = ($user) ? $user->email : 'N/A';

	if ($project_id)
	{
		$db    = JFactory::getDBO();
		$query = 'SELECT * FROM #__projectlog_projects WHERE id = ' . $project_id;
		$db->setQuery($query);
		$proect = $db->loadObject();

		$db    = JFactory::getDBO();
		$query = 'SELECT * FROM #__projectlog_docs WHERE project_id = ' . $project_id . ' ORDER BY id ';
		$db->setQuery($query);
		$dok = $db->loadObjectlist();

		$db    = JFactory::getDBO();
		$query = 'SELECT * FROM #__projectlog_logs WHERE project_id = ' . $project_id . ' ORDER BY date ';
		$db->setQuery($query);
		$log = $db->loadObjectlist();


		$doc_path = 'http://zeppelin/zepp/media/com_projectlog/docs/';
		$m_email  = projectlogHTML::userEmail($proect->manager);
		$m_name   = projectlogHTML::getUserName($proect->manager);

		if ($proect->technicians)
		{
			$t_email = projectlogHTML::userEmail($proect->technicians);
			$t_name  = projectlogHTML::getUserName($proect->technicians);

		}
	}

	$a_email     = 'baza@zepp.ru';
	$a2_email    = 'andrey.0008@yandex.ru';
	$link        = JRoute::_('http://zeppelin/zepp/index.php?option=com_projectlog&view=project&id=' . $project_id, false);
	$pochta_chek = 4;
	$idd         = $project_id;


	if ($type)
	{
		switch ($type)
		{

			case 'log':
				$subject  = $mainframe->getCfg('sitename') . ' ' . JText::_('NEW LOG SUBJECT');
				$add_type = sprintf(JText::_('NEW LOG EMAIL'), $mainframe->getCfg('sitename'));
				$i        = 1;
				foreach ($log as $l)
				{
					$text = '<strong>' . JText::_('LOG') . ' ' . $i . ': </strong><p><i>' . $l->title . '</i></p> <strong>' . JText::_('CREATED BY COMENT') . '</strong> ' .
						projectlogHTML::getusername($l->loggedby) . ' &nbsp;&nbsp;&nbsp;&nbsp;(' . $l->date . ')<br />=========================<br /><b>' . $l->title . '</b><br />' . $l->description . '<br /><br />';
					$i    = $i + 1;
				}
				break;


			case 'doc':
				$subject  = $mainframe->getCfg('sitename') . ' ' . JText::_('NEW DOC SUBJECT');
				$add_type = sprintf(JText::_('NEW DOC EMAIL'), $mainframe->getCfg('sitename'));
				$i        = 1;
				foreach ($dok as $d)
				{
					$text = '<strong>' . JText::_('DOCUMENT') . ' ' . $i . ': </strong><p><i>' . $d->path . '</i></p> <strong>' . JText::_('CREATED BY DOC') . '</strong> ' .
						projectlogHTML::getusername($d->submittedby) . ' &nbsp;&nbsp;&nbsp;&nbsp;' . $date . '<br />=========================<br />';
					$i    = $i + 1;
				}
				break;

			case 'del_doc':
				$subject  = $mainframe->getCfg('sitename') . ' ' . JText::_('DOC DELETED');
				$add_type = sprintf(JText::_('DOC DELETED'), $mainframe->getCfg('sitename'));

				$text = '<strong>' . JText::_('DOCUMENT') . ' : </strong><p><i>' . $faile . '</i></p> <strong>' . JText::_('DOC DELETED') . '</strong> ' .
					$user->name . ' &nbsp;&nbsp;&nbsp;&nbsp;(' . $date . ')<br />=========================<br />';


				break;

			case 'del_proj':
				$pochta_chek = 2;
				$idd         = $faile[0]->id;
				$proect      = $faile[0];
				$m_email     = projectlogHTML::userEmail($faile[0]->manager);
				$m_name      = projectlogHTML::getusername($faile[0]->manager);
				$subject     = $mainframe->getCfg('sitename') . ' ' . JText::_('PROJECT DELETED');
				$add_type    = sprintf(JText::_('PROJECT DELETED'), $mainframe->getCfg('sitename'));

				$text = '
		                           <strong>' . JText::_('PROJECT MANAGER') . ' : </strong>' . projectlogHTML::getusername($faile[0]->manager) . '<br />
		                   <strong>' . JText::_('PROJECT DELETED') . '</strong> ' .
					$user->name . ' &nbsp;&nbsp;&nbsp;&nbsp;(' . $date . ')<br />=========================<br />';
				$link = '';

				break;

			case 'teh_list':

				$add_type = $subject = JText::_('Тех. задание изменилось');

				$text = '<strong>' . $add_type . ' : </strong><p>в полях:<br /> ' . $faile . ' были изменены значение</p> <strong>' . JText::_('Значения изменил:') . '</strong> ' .
					$user->name . ' &nbsp;&nbsp;&nbsp;&nbsp;(' . $date . ')<br />=========================<br />';


				break;


		}
		$body       = '<p>' . $add_type . '</p>
                     <p><strong> <a href="' . $link . '"> ' . JText::_('PROJECT') . '</a>:&nbsp;&nbsp;&nbsp;</strong>' .
			$proect->title . '<br /><strong> '
			. JText::_('RELEASE NUM') . ':&nbsp;&nbsp;&nbsp;</strong> ' . $proect->release_id . '</p>
                     <br />' . $text . '<br /><span> IP: ' . $ipaddress . '</span> ';
		$toto_email = '';


		if ($uname <> $t_email and $t_email <> '' and ($proect->category == 7 or $proect->category == 8 or $proect->category == 12 or $proect->category == 13))
		{
			if (projectlogHTML::getUserPChekc($proect->technicians) == 1) JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $t_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);
			$toto_email = $toto_email . ' Технолог: ' . $t_name . '- ' . $t_email . ',<br />';

			// Для ZeppProjekt
			$db    = JFactory::getDBO();
			$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) 
	VALUES ( " . $user->id . " ,  '" . $body . "' , " . $proect->technicians . " , " . $pochta_chek . " , " . $idd . " ) ;";
			$db->setQuery($query);
			if (!$db->query())
			{
				$this->setError($db->getErrorMsg());
				//return false;
			}


		}

		if ($uname <> $a2_email and ($proect->category == 7 or $proect->category == 8 or $proect->category == 12 or $proect->category == 13))
		{
			if (projectlogHTML::getUserPChekc(97) == 1) JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $a2_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);
			$toto_email = $toto_email . ' Нач. производства- ' . $a2_email . ',<br />';

			// Для ZeppProjekt
			$db    = JFactory::getDBO();
			$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( " . $user->id . " ,  '" . $body . "' , 97 , " . $pochta_chek . " , " . $idd . " ) ;";
			$db->setQuery($query);
			if (!$db->query())
			{
				$this->setError($db->getErrorMsg());
				//return false;
			}

		}

		if ($uname <> $a_email and ($proect->category == 7 or $proect->category == 8 or $proect->category == 12 or $proect->category == 13))
		{
			if (projectlogHTML::getUserPChekc(87) == 1) JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $a_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);
			$toto_email = $toto_email . ' База- ' . $a_email . '<br />';

			// Для ZeppProjekt
			$db    = JFactory::getDBO();
			$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( " . $user->id . " ,  '" . $body . "' , 87 , " . $pochta_chek . " , " . $idd . " ) ;";
			$db->setQuery($query);
			if (!$db->query())
			{
				$this->setError($db->getErrorMsg());
				//return false;
			}

		}

		if ($uname <> $m_email and $m_email <> '' and ($proect->category == 7 or $proect->category == 8 or $proect->category == 12 or $proect->category == 13))
		{
			if (projectlogHTML::getUserPChekc($proect->manager) == 1) JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $m_email, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);
			$toto_email = $toto_email . ' Менеджер:' . $m_name . '- ' . $m_email . '<br />';

			// Для ZeppProjekt
			$db    = JFactory::getDBO();
			$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( " . $user->id . " ,  '" . $body . "' , " . $proect->manager . " , " . $pochta_chek . " , " . $idd . " ) ;";
			$db->setQuery($query);
			if (!$db->query())
			{
				$this->setError($db->getErrorMsg());
				//return false;
			}

		}

		if ($proect->category == 7 or $proect->category == 8 or $proect->category == 12 or $proect->category == 13)
		{

			$body = $body . '<br />Сообщения о добавлении отправленны на почтовые адреса:<br />' . $toto_email . ' Вам : ' . $uname;

			if (projectlogHTML::getUserPChekc($user->id) == 1) JUtility::sendMail($admin_email, $mainframe->getCfg('fromname'), $uname, $subject, $body, $mode, $cc, $bcc, $attachment, $from_email, $from_name);
			// Для ZeppProjekt
			$db    = JFactory::getDBO();
			$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( " . $user->id . " ,  '" . $body . "' , " . $user->id . " , " . $pochta_chek . " , " . $idd . " ) ;";
			$db->setQuery($query);
			if (!$db->query())
			{
				$this->setError($db->getErrorMsg());
				//return false;
			}
		}


	}
}

/* ПОЧТА
*   Новый проект в работу
*   Принято в работу
*   Выполнено
*   Гарантия
*   Заблокирован
*/
function notifyUsers($project_id, $mov, $msg)
{
	global $mainframe;
	jimport('joomla.mail.helper');
	$mode      = 2;
	$date      = date(' d.m.Y');
	$ipaddress = $_SERVER['REMOTE_ADDR'];


	if ($project_id)
	{
		$db    = JFactory::getDBO();
		$query = 'SELECT * FROM #__projectlog_projects WHERE id = ' . $project_id;
		$db->setQuery($query);
		$proect = $db->loadObject();


		$db    = JFactory::getDBO();
		$query = 'SELECT * FROM #__projectlog_docs WHERE project_id = ' . $project_id;
		$db->setQuery($query);
		$dok      = $db->loadObjectlist();
		$doc_path = 'http://zeppelin/zepp/media/com_projectlog/docs/';

		switch ($mov)
		{
			// Новый проект в работу
			case '7':
			{
				$subject = "Новый проект OT " . projectlogHTML::getusername($proect->manager);
				if ($proect->manager)
				{

					$managerdetails = projectlogHTML::userDetails($proect->manager);
					if ($managerdetails)
					{
						$memail   = $managerdetails->email_to;
						$mtelefon = $managerdetails->telephone;
					}

				}
				else
				{
					$memail   = '';
					$mtelefon = '';
				}


				$user_email  = 'baza@zepp.ru';
				$user_email3 = 'andrey.0008@yandex.ru';
				//$user_email2 = 'vl@zepp.ru';
				$link  = JRoute::_('http://zeppelin/zepp/index.php?option=com_projectlog&view=project&id=' . $project_id, false);
				$klink = JRoute::_('http://zeppelin/zepp/index.php?option=com_projectlog&view=calendar&id=' . $project_id . '&Itemid=61', false);

				$body = '  <strong>В работу, ' . $date . ', поступил новый проект: </strong><br>
                    	<strong>Контрагент: </strong>' . $proect->title .
					'<br /><strong>Номер заказа: </strong>' . $proect->release_id .
					'<br /><strong>Менеджер: </strong>' . projectlogHTML::getusername($proect->manager) . '<strong>  Телефоны: </strong>' . $mtelefon .
					'<br /><strong>Технолог: </strong>' . projectlogHTML::getusername($proect->technicians) .
					'<br /><strong>Срок выполнения заказа: </strong>' . JHTML::_('date', $proect->release_date, JText::_('%d.%m.%Y')) .
					'<br /><strong> Ссылка на <a href="' . $link . '"> Проект </a>' .
					'<br /><strong> Ссылка на <a href="' . $klink . '"> Календарик </a>';

				if ($dok) :
					$body = $body . '<br /> Файлы проекта: ';
					foreach ($dok as $d):
						$body = $body . '<br >     <a href="' . $doc_path . $project_id . '/' . $d->path . '" >	' . $d->path . '</a>';
						//
					endforeach;

				endif;


				$body = $body . '<br /> <br />Отправлено с IP: ' . $ipaddress . ' | $mov - ' . $mov;
				if (projectlogHTML::getUserPChekc(87) == 1)
					JUtility::sendMail($memail, 'Дизайн-студия ЦеППелин', $user_email, $subject, $body, $mode, $cc, $bcc, $attachment);
				//	JUtility::sendMail($memail, 'Дизайн-студия ЦеППелин', $user_email2, $subject, $body, $mode, $cc, $bcc, $attachment);
				if (projectlogHTML::getUserPChekc(97) == 1)
					JUtility::sendMail($memail, 'Дизайн-студия ЦеППелин', $user_email3, $subject, $body, $mode, $cc, $bcc, $attachment);

				if ($proect->technicians)
				{

					//$managerdetails
					$temail = projectlogHTML::userEmail($proect->technicians);
					//if($managerdetails){
					//$temail= $managerdetails->email_to;
					if (projectlogHTML::getUserPChekc($proect->technicians) == 1)
						JUtility::sendMail($memail, projectlogHTML::getusername($proect->manager), $temail, $subject, $body, $mode, $cc, $bcc, $attachment);
					//}

				}

				// Для ZeppProjekt

				$db    = JFactory::getDBO();
				$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( " . $proect->manager . " ,  '" . $body . "' , 97 , 3 , " . $project_id . " ) ;";
				$db->setQuery($query);
				if (!$db->query())
				{
					$this->setError($db->getErrorMsg() . $moveDate . $category + 1);
					//return false;
				}
				$db    = JFactory::getDBO();
				$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( " . $proect->manager . " ,  '" . $body . "' , 87 , 3 , " . $project_id . " ) ;";
				$db->setQuery($query);
				if (!$db->query())
				{
					$this->setError($db->getErrorMsg() . $moveDate . $category + 1);
					//return false;
				}

				/* $db = JFactory::getDBO();
				 $query = 'SELECT move  FROM #__users WHERE id = 97';
				 $db->setQuery($query);
				 $chek =$db->loadResult();

				 $query ="UPDATE #__users SET move = ".($chek + 1)." WHERE  id = 97 OR id = 87" ;
				 $db->setQuery($query);
				 if(!$db->query()) {
					 $this->setError($db->getErrorMsg().$moveDate.$category+1);

				 }*/
			}
				break;

			// Принято в работу
			case '8':
			{
				$subject = "Ваш проект " . $proect->release_id . "принят в работу ";
				if ($proect->manager)
				{

					$managerdetails = projectlogHTML::userDetails($proect->manager);
					if ($managerdetails)
					{
						$user_email = $managerdetails->email_to;
					}

				}
				else
				{
					$user_email = '';
				}
				$link = JRoute::_('http://zeppelin/zepp/index.php?option=com_projectlog&view=project&id=' . $project_id, false);

				$body = $date . ' <strong>принят в работу Ваш проект: </strong><br>
                    	<strong>Контрагент: </strong>' . $proect->title .
					'<br /><strong>Номер заказа: </strong>' . $proect->release_id .
					'<br /><strong>Менеджер: </strong>' . projectlogHTML::getusername($proect->manager) . '<strong>  Телефоны: </strong>' . $mtelefon .
					'<br /><strong>Технолог: </strong>' . projectlogHTML::getusername($proect->technicians) .
					'<br /><strong>Срок выполнения заказа: </strong>' . JHTML::_('date', $proect->release_date, JText::_('%d.%m.%Y')) .
					'<br /><strong> Ссылка на <a href="' . $link . '"> Проект </a>'
					. '<br /> <br />Отправлено с IP: ' . $ipaddress . ' | $mov - ' . $mov;
				if (projectlogHTML::getUserPChekc($proect->manager) == 1)
					JUtility::sendMail('baza@zepp.ru', 'Дизайн-студия ЦеППелин', $user_email, $subject, $body, $mode, $cc, $bcc, $attachment);


				if ($proect->technicians)
				{

					//$managerdetails
					$temail = projectlogHTML::userEmail($proect->technicians);
					//if($managerdetails){
					//$temail= $managerdetails->email_to;
					if (projectlogHTML::getUserPChekc($proect->technicians) == 1)
						JUtility::sendMail($memail, projectlogHTML::getusername($proect->manager), $temail, $subject, $body, $mode, $cc, $bcc, $attachment);
					//}

				}
				/* if($proect->technicians){

				 $managerdetails = projectlogHTML::userDetails($proect->technicians);
					 if($managerdetails){
						 $temail= $managerdetails->email_to;
					   if (projectlogHTML::getUserPChekc($proect->technicians) == 1)
						 JUtility::sendMail('baza@zepp.ru', 'Дизайн-студия ЦеППелин', $temail, $subject, $body, $mode, $cc, $bcc, $attachment);
					 }
				 }*/

				// Для ZeppProjekt
				$db    = JFactory::getDBO();
				$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( 97 ,  '" . $body . "' , " . $proect->manager . " , 3 , " . $project_id . " ) ;";
				$db->setQuery($query);
				if (!$db->query())
				{
					$this->setError($db->getErrorMsg() . $moveDate . $category + 1);
					//return false;
				}
				$db    = JFactory::getDBO();
				$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( 97 ,  '" . $body . "' , " . $proect->technicians . " , 3 , " . $project_id . " ) ;";
				$db->setQuery($query);
				if (!$db->query())
				{
					$this->setError($db->getErrorMsg() . $moveDate . $category + 1);
					//return false;
				}

				/* $db = JFactory::getDBO();
				 $query = 'SELECT move  FROM #__users WHERE id = '.$proect->manager;
				 $db->setQuery($query);
				 $chek =$db->loadResult();

				 $query ="UPDATE #__users SET move = ".($chek + 1)." WHERE  id = ".$proect->manager ;
				 $db->setQuery($query);
				 if(!$db->query()) {
					 $this->setError($db->getErrorMsg().$moveDate.$category+1);
					 //return false;
				 }

				 $db = JFactory::getDBO();
				 $query = 'SELECT move  FROM #__users WHERE id = '.$proect->technicians;
				 $db->setQuery($query);
				 $chek =$db->loadResult();

				 $query ="UPDATE #__users SET move = ".($chek + 1)." WHERE  id = ".$proect->technicians ;
				 $db->setQuery($query);
				 if(!$db->query()) {
					 $this->setError($db->getErrorMsg().$moveDate.$category+1);
					 //return false;
				 } */
			}
				break;
			// Выполнено
			case '12':
			{
				$subject = "Ваш проект " . $proect->release_id . "готов ! ";
				if ($proect->manager)
				{

					$managerdetails = projectlogHTML::userDetails($proect->manager);
					if ($managerdetails)
					{
						$user_email = $managerdetails->email_to;
					}

				}
				else
				{
					$user_email = '';
				}
				$link = JRoute::_('http://zeppelin/zepp/index.php?option=com_projectlog&view=project&id=' . $project_id, false);

				$body = $date . ' <strong>был выполнен Ваш проект: </strong><br>
                    	<strong>Контрагент: </strong>' . $proect->title .
					'<br /><strong>Номер заказа: </strong>' . $proect->release_id .
					'<br /><strong>Менеджер: </strong>' . projectlogHTML::getusername($proect->manager) . '<strong>  Телефоны: </strong>' . $mtelefon .
					'<br /><strong>Технолог: </strong>' . projectlogHTML::getusername($proect->technicians) .
					'<br /><strong>Срок выполнения заказа: </strong>' . JHTML::_('date', $proect->release_date, JText::_('%d.%m.%Y')) .
					'<br /><strong> Ссылка на <a href="' . $link . '"> Проект </a>' .
					'<br /><br /><i>Проект остается в категории "Выполнен" до тех пор пока не будет подписан акт, и получена вся сумма.
                    	После этог прект <b>НУЖНО </b>отправить в архив или взять на гарантию!
                    	Если Вы сделаете это раньше, то некоторые суммы не будут зачисленны в ваш отчет</i>'
					. '<br /> <br />Отправлено с IP: ' . $ipaddress . ' | $mov - ' . $mov;
				if (projectlogHTML::getUserPChekc($proect->manager) == 1)
					JUtility::sendMail('baza@zepp.ru', 'Дизайн-студия ЦеППелин', $user_email, $subject, $body, $mode, $cc, $bcc, $attachment);

				if ($proect->technicians)
				{

					//$managerdetails
					$temail = projectlogHTML::userEmail($proect->technicians);
					//if($managerdetails){
					//$temail= $managerdetails->email_to;
					if (projectlogHTML::getUserPChekc($proect->technicians) == 1)
						JUtility::sendMail($memail, projectlogHTML::getusername($proect->manager), $temail, $subject, $body, $mode, $cc, $bcc, $attachment);
					//}

				}

				/* if($proect->technicians){

					$managerdetails = projectlogHTML::userDetails($proect->technicians);
						if($managerdetails){
							$temail= $managerdetails->email_to;
						  if (projectlogHTML::getUserPChekc($proect->technicians) == 1)
							JUtility::sendMail('baza@zepp.ru', 'Дизайн-студия ЦеППелин', $temail, $subject, $body, $mode, $cc, $bcc, $attachment);
						}
					}
*/

				// Для ZeppProjekt
				$db    = JFactory::getDBO();
				$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( 97 ,  '" . $body . "' , " . $proect->manager . " , 3 , " . $project_id . " ) ;";
				$db->setQuery($query);
				if (!$db->query())
				{
					$this->setError($db->getErrorMsg() . $moveDate . $category + 1);
					//return false;
				}

				/* $db = JFactory::getDBO();
				 $query = 'SELECT move  FROM #__users WHERE id = '.$proect->manager;
				 $db->setQuery($query);
				 $chek =$db->loadResult();

				 $query ="UPDATE #__users SET move = ".($chek + 1)." WHERE  id = ".$proect->manager ;
				 $db->setQuery($query);
				 if(!$db->query()) {
					 $this->setError($db->getErrorMsg().$moveDate.$category+1);
					 //return false;
				 }
					*/
			}
				break;

			// Гарантия
			case '9':
			{
				$subject = "Проект " . $proect->release_id . " отправлен на гарантийное обслуживание ! ";

				$link = JRoute::_('http://zeppelin/zepp/index.php?option=com_projectlog&view=project&id=' . $project_id, false);

				$body = $date . ' <strong>был отправлен на гарантию проект созданный ' . projectlogHTML::getusername($proect->created_by) . ' : </strong><br>
                    	<strong>Контрагент: </strong>' . $proect->title .
					'<br /><strong>Номер заказа: </strong>' . $proect->release_id .
					'<br /><strong>Текущий менеджер: </strong>' . projectlogHTML::getusername($proect->manager) . '<strong>  Телефоны: </strong>' . $mtelefon .
					'<br /><strong>Технолог: </strong>' . projectlogHTML::getusername($proect->technicians) .
					'<br /><strong>Срок гарантии: </strong>' . JHTML::_('date', $proect->release_date, JText::_('%d.%m.%Y')) .
					'<br /><strong> Ссылка на <a href="' . $link . '"> Проект </a>'

					. '<br /> <br />Отправлено с IP: ' . $ipaddress . ' | $mov - ' . $mov;
				if ($proect->manager)
				{

					$managerdetails = projectlogHTML::userDetails($proect->manager);
					if ($managerdetails)
					{
						$user_email = $managerdetails->email_to;
						$bodyM      = '<br /><br /><b><i>Если вы не являетесь менеджером обслуживающим гарантийные проекты, но это уведомление пришло к вам,
                    	значит вы указаны как текущий менеджер по проекту. Текущему менеджеру проекта нужно изменить на странице редактирования менеджера проекта на менеджера,
                    	который будет осуществлять гарантийное обслуживание проекта!</b></i>' .
							'<br /><br /><b><i>Если вы не указаны как менеджер, то вы не сможете вносить исправления в проект.
                    	Предыдущий менеджер должен назначить менеджером Вас !</b></i>';
						if (projectlogHTML::getUserPChekc($proect->manager) == 1)
							JUtility::sendMail('baza@zepp.ru', 'Дизайн-студия ЦеППелин', $user_email, $subject, $body . $bodyM, $mode, $cc, $bcc, $attachment);
					}

				}

				if ($proect->technicians)
				{

					//$managerdetails
					$temail = projectlogHTML::userEmail($proect->technicians);
					//if($managerdetails){
					//$temail= $managerdetails->email_to;

					$body = $body . '<br /><br /><b><i>Если вы не являетесь технологом обслуживающим гарантийные проекты, но это уведомление пришло к вам,
                    	значит вы указаны как текущий технолог по проекту. Менеджеру нужно изменить на странице редактирования технолога проекта на технолога,
                    	который будет осуществлять гарантийное обслуживание проекта!</b></i>' .
						'<br /><br /><b><i>Если вы не указаны как менеджер, то вы не сможете вносить исправления в проект.
                    	Вносить исправления может только текущий менеджер!</b></i>';
					if (projectlogHTML::getUserPChekc($proect->technicians) == 1)
						JUtility::sendMail($memail, projectlogHTML::getusername($proect->manager), $temail, $subject, $body, $mode, $cc, $bcc, $attachment);
					//}

				}

				/*  if($proect->technicians){

				  $managerdetails = projectlogHTML::userDetails($proect->technicians);
					  if($managerdetails){
						  $temail= $managerdetails->email_to;
						  $body=$body.'<br /><br /><b><i>Если вы не являетесь технологом обслуживающим гарантийные проекты, но это уведомление пришло к вам,
					  значит вы указаны как текущий технолог по проекту. Менеджеру нужно изменить на странице редактирования технолога проекта на технолога,
					  который будет осуществлять гарантийное обслуживание проекта!</b></i>'.
					  '<br /><br /><b><i>Если вы не указаны как менеджер, то вы не сможете вносить исправления в проект.
					  Вносить исправления может только текущий менеджер!</b></i>';
					  if (projectlogHTML::getUserPChekc($proect->technicians) == 1)
						  JUtility::sendMail('baza@zepp.ru', 'Дизайн-студия ЦеППелин', $temail, $subject, $body, $mode, $cc, $bcc, $attachment);
					  }
				  }*/


				// Для ZeppProjekt
				$db    = JFactory::getDBO();
				$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( " . $proect->created_by . " ,  '" . $body . "' , " . $proect->manager . " , 3 , " . $project_id . " ) ;";
				$db->setQuery($query);
				if (!$db->query())
				{
					$this->setError($db->getErrorMsg() . $moveDate . $category + 1);
					//return false;
				}
				$db    = JFactory::getDBO();
				$query = "INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES (  " . $proect->manager . ",  '" . $body . "' , " . $proect->technicians . " , 3 , " . $project_id . " ) ;";
				$db->setQuery($query);
				if (!$db->query())
				{
					$this->setError($db->getErrorMsg() . $moveDate . $category + 1);
					//return false;
				}

				/* $db = JFactory::getDBO();
				 $query = 'SELECT move  FROM #__users WHERE id = '.$proect->manager;
				 $db->setQuery($query);
				 $chek =$db->loadResult();

				 $query ="UPDATE #__users SET move = ".($chek + 1)." WHERE  id = ".$proect->manager ;
				 $db->setQuery($query);
				 if(!$db->query()) {
					 $this->setError($db->getErrorMsg().$moveDate.$category+1);
					 //return false;
				 }

				 $db = JFactory::getDBO();
				 $query = 'SELECT move  FROM #__users WHERE id = '.$proect->technicians;
				 $db->setQuery($query);
				 $chek =$db->loadResult();

				 $query ="UPDATE #__users SET move = ".($chek + 1)." WHERE  id = ".$proect->technicians ;
				 $db->setQuery($query);
				 if(!$db->query()) {
					 $this->setError($db->getErrorMsg().$moveDate.$category+1);
					 //return false;
				 }
				  */
			}
				break;

			// Заблокирован
			case '13':
			{
				$subject = "Проект остановлен!! " . $proect->release_id . " ! ";
				if ($proect->manager)
				{
					$managerdetails = projectlogHTML::userDetails($proect->manager);
					if ($managerdetails)
					{
						$user_email = $managerdetails->email_to;
					}

				}
				else
				{
					$user_email = '';
				}
				$link = JRoute::_('http://zeppelin/zepp/index.php?option=com_projectlog&view=project&id=' . $project_id, false);

				$body = $date . '<p><strong>Работы по вашему проекту приостановленны по причине: </strong><br>' .
					$msg . '</p><br>
                        СРОЧНО ПРИМИТЕ РЕШЕНИЯ ДЛЯ ИСПРАВЛЕНИЯ СИТУАЦИИ <br>
                        <h3><u> Проект: </u></h3>
                        <strong>Подрядчик: </strong>' . $proect->podrydchik .
					'<br /><strong>Контрагент: </strong>' . $proect->title .
					'<br /><strong>Номер заказа: </strong>' . $proect->release_id .
					'<br /><strong>Менеджер: </strong>' . projectlogHTML::getusername($proect->manager) . '<strong>  Телефоны: </strong>' . $mtelefon .
					'<br /><strong>Технолог: </strong>' . projectlogHTML::getusername($proect->technicians) .
					'<br /><strong>Срок выполнения заказа: </strong>' . JHTML::_('date', $proect->release_date, JText::_('%d.%m.%Y')) .
					'<br /><strong> Ссылка на <a href="' . $link . '"> Проект </a>' .
					'<br /><br /><u>Пояснения:</u><br /><i>Проект остается в категории "Остановленные проекты" до тех пор пока начальник производства не согласится его взять в работу. <br>
                        Начальник производства не может вносить исправления в проект: изменять даты, назначать подрядчика ... .<br />
                        Вы не можете переместить проект из этой категории в другую.'
					. '<br /> <br />' . $proect->id;

                JUtility::sendMail('andrey.0008@yandex.ru', 'Дизайн-студия ЦеППелин', 'black@zepp.ru', $subject, $body, $mode, $cc, $bcc, $attachment);
                JUtility::sendMail('andrey.0008@yandex.ruu', 'Дизайн-студия ЦеППелин', 'vl@zepp.ru', $subject, $body, $mode, $cc, $bcc, $attachment);


                if (projectlogHTML::getUserPChekc($proect->manager) == 1)
					JUtility::sendMail('andrey.0008@yandex.ru', 'Дизайн-студия ЦеППелин', $user_email, $subject, $body, $mode, $cc, $bcc, $attachment);


				// Для ZeppProjekt
				$query = "UPDATE #__projectlog_projects SET cherep_msq = '" . $msg . "' , deployment_from = NOW( ) WHERE  id = " . $proect->id;
				$db->setQuery($query);
				if (!$db->query())
				{
					$this->setError($db->getErrorMsg() . $moveDate . $category + 1);

				}
			}
				break;

		}
	}
}

function timep($id_mun)
{
	//if ($time == NULL) $time = time();
	//$timep = "" . date("j M Y года в H:i:s", $time) . "";
	$id_mun = str_replace("Jan", "Январь", $id_mun);
	$id_mun = str_replace("Feb", "Февраль", $id_mun);
	$id_mun = str_replace("Mar", "Март", $id_mun);
	$id_mun = str_replace("May", "Май", $id_mun);
	$id_mun = str_replace("Apr", "Апрель", $id_mun);
	$id_mun = str_replace("Jun", "Июнь", $id_mun);
	$id_mun = str_replace("Jul", "Июль", $id_mun);
	$id_mun = str_replace("Aug", "Август", $id_mun);
	$id_mun = str_replace("Sep", "Сентябрь", $id_mun);
	$id_mun = str_replace("Oct", "Октябрь", $id_mun);
	$id_mun = str_replace("Nov", "Ноябрь", $id_mun);
	$id_mun = str_replace("Dec", "Декабрь", $id_mun);

	return $id_mun;
}


function kalendarik($w_k, $shot_title, $release_date, $job_id, $release_id, $technicians, $projecttype, $workorder_id, $path, $w, $h, $podrydchik)
{

	/****************************************
	 * Ширина календарика - $w_k
	 * Короткое имя - $shot_title
	 * Дата реализации - $release_date
	 * Контрагент - $job_id
	 * Номерзаказа - $release_id
	 * Технолог - $technicians
	 * Цв.Фона - $projecttype
	 * Цв.Шрифта - $workorder_id
	 * Картинка - $path
	 * Размеры - $w,$h
	 *****************************************/
	$k_out = '
        <table  style="color:#' . $workorder_id . '; border: 2px solid #000;"  align="left" bgcolor="#' . $projecttype . '">
         <tr><td>

				<table width="229" border="0" cellpadding="5" cellspacing="0" bgcolor="#' . $projecttype . '">
					<tr>
						<td height="5%" nowrap align="left"><strong style="font-size:16px;font-weight:900;">' . $shot_title . '</strong></td>
						<td height="5%" nowrap align="right"><strong style="font-size:16px;font-weight:900;">' . JHTML::_('date', $release_date, JText::_('%d.%m')) . ' </strong></td>
					</tr>
					<tr >
						<td width="' . $w_k . '" align="center" colspan=2 style="color:#000;" bgcolor="#fff">' . $job_id . '</td>
					</tr>

					<tr>
						<td colspan=2 align="center" bgcolor="#fff" >';

	if ($podrydchik <> '') $k_out .= '<img  title="Изготавливается подрядчиком ' . $podrydchik . '" src="components/com_projectlog/assets/images/podrydchik.png" width="28" height="28" alt="Изготавливается подрядчиком ' . $podrydchik . '">';

	if ($path <> '') $k_out .= '<img width="' . $w . '" height="' . $h . '" src="' . $path . '" />
       					</td>
					</tr>
					<tr>
						<td height="5%" nowrap align="left"><strong style="font-size:16px;font-weight:900;">' . $release_id . '</strong></td>
						<td height="5%" nowrap align="right"><strong style="font-size:16px;font-weight:900;">' . $technicians . '</strong></td>
					</tr>
				</table>
			</td></tr>
	</table>';

	return $k_out;
}


function kal_info($podrydchik, $task_id, $client, $manager, $chief, $technicians, $id, $doc_path, $description, $location_gen)
{


	echo '<div style="color:#000;float:right;padding:16px;width: 250px;">';
	if ($podrydchik <> '') echo '<br /><strong>' . JText::_('PODRYDCHIK2') . '</strong> <span>' . $podrydchik . '</span><br />';
	echo '<span class="content_header">' . JText::_('TASK NUM') . ':</span>
                    <span class="red2">';
	echo ($task_id) ? $task_id : '&nbsp;';
	echo '</span><br />

					<span class="content_header">' . JText::_('CLIENT') . ':</b></span>
 					<span class="red2">' . $client . '</strong></span><br />

					<span class="content_header">' . JText::_('PROJECT MANAGER') . ':</span>';

	if ($manager)
	{
		echo projectlogHTML::getusername($manager);
		$managerdetails = projectlogHTML::userDetails($manager);
		if ($managerdetails)
		{
			echo ($managerdetails->email_to) ? '<br /><a href="mailto:' . $managerdetails->email_to . '">' . $managerdetails->email_to . '</a>' : '';
			echo ($managerdetails->telephone) ? '<br />' . $managerdetails->telephone : '';

		}
		else
		{
			echo '&nbsp;';
		}
	}
	echo '
					<br />

					<span class="content_header">' . JText::_('PROJECT LEAD') . ':</span>';
	echo ($chief) ? projectlogHTML::getusername($chief) : '&nbsp;';
	echo '<br />';

	echo '<span class="content_header">' . JText::_('TECHNICIAN') . ':</span>';
	echo ($technicians) ? projectlogHTML::getusername($technicians) : '&nbsp;';
	echo '<br />';

	$db    = JFactory::getDBO();
	$query = 'SELECT * FROM #__projectlog_docs WHERE project_id = ' . $id . ' ORDER BY date DESC';
	$db->setQuery($query);
	$docs = $db->loadObjectlist();

	$db    = JFactory::getDBO();
	$query = 'SELECT * FROM #__projectlog_logo WHERE project_id = ' . $id . ' ORDER BY date DESC';
	$db->setQuery($query);
	$logo = $db->loadObjectlist();


	if ($docs) :
		echo '<div class="right_details">';
		echo '<div class="content_header2">' . JText::_('RELATED DOCS') . ':</div>';
		foreach ($docs as $d):
			if ($d->name == '') $d->name = $d->path;

			echo '<div class="doc_item">
										&gt;&nbsp;<a href="' . $doc_path . $id . '/' . $d->path . '"
														type="bin"
														style="word-wrap: break-word"
														target="_blank"
														class="hasTip"
														title="' . JText::_('DOCUMENT') . ' :: ' . JText::_('SUBMITTED BY') . ': ' . projectlogHTML::getusername($d->submittedby) . ' <br /> ' . JText::_('FILE') . ': ' . $d->path . ' <br /> ' . JText::_('SUBMITTED DATE') . ': ' . $d->date . '" >
													' . $d->name . '
													</a>';

			echo '</div>';
		endforeach;
		foreach ($logo as $l):
			echo '<div class="doc_item">
										&gt;&nbsp;<a href="' . $doc_path . $id . '/' . $l->path . '"
														type="bin"
														style="word-wrap: break-word"
														target="_blank"
														class="hasTip"
														title="' . JText::_('DOCUMENT') . ' :: ' . JText::_('SUBMITTED BY') . ': ' . projectlogHTML::getusername($l->submittedby) . ' <br /> ' . JText::_('FILE') . ': ' . $l->path . ' <br /> ' . JText::_('SUBMITTED DATE') . ': ' . $l->date . '" >
													' . $l->path . '
													</a>';

			echo '</div>';
		endforeach;
		echo '</div>';
	endif;

	$calendar_link = JRoute::_('index.php?option=com_projectlog&view=calendar&id=' . $id . '&Itemid=61');

	echo '	<div class="right_details">

								<div class="content_header2">' . JText::_('CALENDAR') . ':</div>
									<a target="_blank" href="' . $calendar_link . '" >Открыть</a>
							</div>

			</div><br clear="all" />
     		<div style="display:block;float:left;width: 100%;">
				<div style="margin: 10px;background-color:#fff;padding:5px;" class="right_details">
					<div class="content_header2">' . JText::_('DESCRIPTION') . ':</div>
					<div style="color:#000;background-color:#fff;" >';
	if ($description) :
		echo $description;
	endif;
	echo '<span class="content_header">' . JText::_('GEN LOC') . ':</span><br />';
	if ($location_gen) :
		echo $location_gen;
	endif;
	echo '</div>
				</div><br />
			</div>';
}


// Возвращает бланк заказа , передается id проекта
function tehform_prn($project, $path)
{


	$tehform_prn = '';

	$shot_title = $project->shot_title;
	if ($shot_title == '') $shot_title = strtok($project->title, ' ');

	if ($project->podrydchik <> '') $tehform_prn .=

		'<strong>' . JText::_('PODRYDCHIK2') . '</strong> <span>' . $project->podrydchik . '</span><br />';

	//$tehform_prn .= '<h1 align="center">Бланк заказа № '.$project->release_id.'</h1>';

	if (strtotime($project->deployment_to) <> 0)
	{
		//$tehform_prn .= '<h3 align="left" >от '.JHTML::_('date', $project->deployment_to, $format = '%d.%m', $offset = NULL ).'</h3>';
		$deployment_to = 'от ' . JHTML::_('date', $project->deployment_to, $format = '%d.%m', $offset = null);
	}
	else
	{
		// $tehform_prn .= '<h3 align="left" >от _____________________</h3>';
		$deployment_to = 'от _____________________';
	}

	//$tehform_prn .= '<h3 align="right"> Срок сдачи: '.JHTML::_('date', $project->release_date , $format = '%d.%m', $offset = NULL ).'</h3>';

	$tehform_prn .= '
            <table class=MsoTableGrid border=0 cellspacing=0 cellpadding=0 width="98%"
                style="width:98.88%;border-collapse:collapse;border:none;mso-yfti-tbllook:
                1184;mso-padding-alt:0cm 5.4pt 0cm 5.4pt;mso-border-insideh:none;mso-border-insidev:
                none">

                <tr style="mso-yfti-irow:0;mso-yfti-firstrow:yes;mso-yfti-lastrow:yes;
                    page-break-inside:avoid">
                    <td width=527 valign=top style="width:395.25pt;padding:0cm 5.4pt 0cm 5.4pt">
                        <p class=MsoNormal align=center style="text-align:center">
                        <b style="mso-bidi-font-weight:normal"><span style=\'font-size:18.0pt;mso-fareast-font-family:
                        "Times New Roman"\'>Бланк заказа № ' . $project->release_id . '</span></b>
                        <b style="mso-bidi-font-weight: normal">
                        <span lang=EN-US style=\'font-size:18.0pt;mso-fareast-font-family:
                        "Times New Roman";mso-ansi-language:EN-US\'><o:p></o:p></span></b></p>
                    </td>
                    <td width=104 valign=top style=\'width:77.95pt;padding:0cm 5.4pt 0cm 5.4pt\'>
  <p class=MsoNormal align=right style=\'text-align:right\'><span
  style=\'font-size:18.0pt;mso-fareast-font-family:"Times New Roman"\'>' . $deployment_to . '</span><b
  style=\'mso-bidi-font-weight:normal\'><span lang=EN-US style=\'font-size:18.0pt;
  mso-fareast-font-family:"Times New Roman";mso-ansi-language:EN-US\'><o:p></o:p></span></b></p>
  </td>
 </tr>
</table>';
	$tehform_prn .= '<h3 align="right"> Срок сдачи: ' . JHTML::_('date', $project->release_date, $format = '%d.%m', $offset = null) . '</h3>




            <table border="0"  cellpadding="3" cellspacing="2" >
               <tr><td><b>Заказчик:</b></td>        <td><b>' . $project->title . '</b></td></tr>';

	if (projectlogHTML::getusername($project->technicians) == '')
	{
		$tehform_prn .= '

                <tr><td><b>Стоимость:</b></td>      <td><b>' . $project->task_id . '</b></td></tr>';

	}

	$tehform_prn .= '

             <tr><td><b>Заказ:</b></td>         <td><b>' . $project->job_id . '</b></td></tr>
             <tr><td valign="top"><b>Материалы:</b></td>     <td>' . $project->description . '</td></tr>
             <tr><td><b>Монтаж \ доставка:</b></td><td><b>' . $project->location_gen . '</b></td></tr>
             <tr><td><b>Контактное лицо \ адрес:</b></td><td><b>' . $project->client . '</b></td></tr>
             <tr><td><b>Технолог:</b></td>      <td><b>' . projectlogHTML::getusername($project->technicians) . '</b></td></tr>

             </table>
             <br />';


	$tehform_prn .= '<img width="600"  height="580" src="' . $path . '" />';


	return $tehform_prn;
}
}

/**
 * Class SimpleImage
 *
 * Вызываем класс
 * $image = new SimpleImage();
 * $image->load("Сдесь сама фотка фотка");
 * $image->resizeToWidth(70); // В аргумент ширину картинки, которая нужна(Она пропорц. уменьш.)
 * $image->save("Путь сохранение"); // Сохраняем
 *
 */
class SimpleImage
{


	var $image;
	var $image_type;

	function load($filename)
	{
		$image_info       = getimagesize($filename);
		$this->image_type = $image_info[2];
		if ($this->image_type == IMAGETYPE_JPEG)
		{
			$this->image = imagecreatefromjpeg($filename);
		}
		elseif ($this->image_type == IMAGETYPE_GIF)
		{
			$this->image = imagecreatefromgif($filename);
		}
		elseif ($this->image_type == IMAGETYPE_PNG)
		{
			$this->image = imagecreatefrompng($filename);
		}
	}

	function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 75, $permissions = null)
	{
		if ($image_type == IMAGETYPE_JPEG)
		{
			imagejpeg($this->image, $filename, $compression);
		}
		elseif ($image_type == IMAGETYPE_GIF)
		{
			imagegif($this->image, $filename);
		}
		elseif ($image_type == IMAGETYPE_PNG)
		{
			imagepng($this->image, $filename);
		}
		if ($permissions != null)
		{
			chmod($filename, $permissions);
		}
	}

	function output($image_type = IMAGETYPE_JPEG)
	{
		if ($image_type == IMAGETYPE_JPEG)
		{
			imagejpeg($this->image);
		}
		elseif ($image_type == IMAGETYPE_GIF)
		{
			imagegif($this->image);
		}
		elseif ($image_type == IMAGETYPE_PNG)
		{
			imagepng($this->image);
		}
	}

	function getWidth()
	{
		return imagesx($this->image);
	}

	function getHeight()
	{
		return imagesy($this->image);
	}

	function resizeToHeight($height)
	{
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width, $height);
	}

	function resizeToWidth($width)
	{
		$ratio  = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		$this->resize($width, $height);
	}

	function scale($scale)
	{
		$width  = $this->getWidth() * $scale / 100;
		$height = $this->getheight() * $scale / 100;
		$this->resize($width, $height);
	}

	function resize($width, $height)
	{
		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->image = $new_image;
	}
}

?>

