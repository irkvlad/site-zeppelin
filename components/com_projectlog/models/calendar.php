<?php
/**
 *  ������ �����
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class projectlogModelCalendar extends JModel
{
	var $_id = null;

	function __construct()
	{
		parent::__construct();

		// Set id for project type
		$id = JRequest::getVar('id', 0, '', 'int');
		$this->setId($id);
	}

	function setId($id)
	{
		$this->_id = $id;
	}

	function getLogo()
	{
		$query = 'SELECT * FROM #__projectlog_logo WHERE project_id = ' . $this->_id;//path WHERE project_id = 46
		$db    = JFactory::getDBO();
		$db->setQuery($query);
		$logos = $db->loadObject();

		return $logos;

	}

	function getProject()
	{
		$db    = JFactory::getDBO();
		$query = 'SELECT * FROM #__projectlog_projects WHERE id = ' . $this->_id;
		$db->setQuery($query);
		$pitem = $db->loadObject();

		return $pitem;
	}


}

?>