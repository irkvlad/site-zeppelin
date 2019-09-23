<?php
/**
 *     ������������� �����������
 */


defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class projectlogViewcalendar extends JView
{
	function display($tpl = null)
	{

		$document = &JFactory::getDocument();
		$model    = &$this->getModel('');
		$user     = JFactory::getUser();

		$project = &$this->get('project');
		$logo    = &$this->get('Logo');

		$this->assignRef('project', $project);
		$this->assignRef('logo', $logo);
		$this->assignRef('document', $document);
		$this->assignRef('user', $user);

		parent::display($tpl);

	}
}

?>
