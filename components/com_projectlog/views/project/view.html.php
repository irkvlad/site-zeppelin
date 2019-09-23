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
jimport('joomla.application.component.view');

class ProjectlogViewProject extends JView
{
	function display($tpl = null)
	{
		global $option;
		JHTML::_('behavior.tooltip');
		$user = JFactory::getUser();

		$settings      = &JComponentHelper::getParams('com_projectlog');
		$document      = &JFactory::getDocument();
		$this->baseurl = JURI::base();
		$document->addStyleSheet($this->baseurl . '/components/com_projectlog/assets/css/projectlog.css');

		$model   = &$this->getModel();
		$project = &$this->get('data');
		$logs    = &$this->get('logs');
		$docs    = &$this->get('docs');
		$logo    = &$this->get('logo');
		$akts    = &$this->get('akts');
		$document->setTitle($project->title);
		$doc_path = 'media/com_projectlog/docs/';

		//список  бригадиров
		$db    =& JFactory::getDBO();
		$query = "SELECT c.id AS value, c.name AS text FROM #__contact_details AS c WHERE (c.catid=20 ) AND c.published=1";
		$db->setQuery($query);
		$categorylist  = $db->loadObjectList();
		$categories    = array();
		$categories[]  = JHTML::_('select.option', '0', "Выберите бригадира", 'value', 'text');
		$categories    = array_merge($categories, $categorylist);
		$brigadir_list = $categories;

		$this->assignRef('user', $user);
		$this->assignRef('project', $project);
		$this->assignRef('logs', $logs);
		$this->assignRef('docs', $docs);
		$this->assignRef('akts', $akts);
		$this->assignRef('logo', $logo);
		$this->assignRef('doc_path', $doc_path);
		$this->assignRef('settings', $settings);
		$this->assignRef('brigadir_lis', $brigadir_list);

		parent::display($tpl);
	}
}
