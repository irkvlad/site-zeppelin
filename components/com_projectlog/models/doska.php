<?php
/**
 *
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class projectlogModeldoska extends JModel
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
		$query = 'SELECT * FROM #__projectlog_logo ';
		$db    = JFactory::getDBO();
		$db->setQuery($query);
		$logos = $db->loadObjectList();

		return $logos;

	}

	function getOnworc()
	{                // Все записи "В работу"
		$db    = JFactory::getDBO();
		$query = 'SELECT * FROM #__projectlog_projects WHERE category = 7 ORDER BY release_date';
		$db->setQuery($query);
		$pitem = $db->loadObjectList();

		return $pitem;
	}

	function getToworc()
	{     // Все записи "В работе"
		$db    = JFactory::getDBO();
		$query = 'SELECT * FROM #__projectlog_projects WHERE category = 8 ORDER BY release_date';
		$db->setQuery($query);
		$pitem = $db->loadObjectList();

		return $pitem;
	}

	function getBrak()
	{     // Все записи "В работе"
		$db    = JFactory::getDBO();
		$query = 'SELECT * FROM #__projectlog_projects WHERE category = 12 AND location_spec IS NOT NULL ORDER BY release_date';
		$db->setQuery($query);
		$pitem = $db->loadObjectList();

		return $pitem;
	}

}

?>