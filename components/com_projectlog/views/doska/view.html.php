<?php
/**
 *
 */


defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class projectlogViewdoska extends JView
{
	function display($tpl = null)
	{

		$document = &JFactory::getDocument();
		$model    = &$this->getModel('');
		$document->addStyleSheet($this->baseurl . '/components/com_projectlog/assets/css/projectlog.css');
		$user     = JFactory::getUser();
		$onworc   = &$this->get('onworc');
		$toworc   = &$this->get('toworc');
		$logo     = &$this->get('Logo');
		$brak     = &$this->get('Brak');
		$doc_path = 'media/com_projectlog/docs/';

		$this->assignRef('onworc', $onworc);
		$this->assignRef('toworc', $toworc);
		$this->assignRef('logo', $logo);
		$this->assignRef('document', $document);
		$this->assignRef('user', $user);
		$this->assignRef('brak', $brak);
		$this->assignRef('doc_path', $doc_path);

		parent::display($tpl);

	}
}

?>
