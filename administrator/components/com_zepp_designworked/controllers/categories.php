<?php

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class designworkedsControllercategories extends designworkedsController
{
	
	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}

	/**
	 * display the edit form
	 * @return void
	 */
	function edit()
	{

		$cid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		JRequest::setVar( 'view', 'editcats' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);
		JRequest::setVar( 'cid', $cid );

		parent::display();
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$post	= JRequest::get('post');
		$cid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$post['id'] = (int) $cid[0];

		$model = $this->getModel('editcats');

		if ($temp=$model->store($post)) {

			$msg = JText::_( 'Категория сохранена' );
		} else {
			$msg = JText::_( 'Ошибка при сохранении категории' );
		}
		$link = 'index.php?option=com_zepp_designworked&view=categories';
		$this->setRedirect($link, $msg);
	}

	/**
	 * remove record(s)
	 * @return void
	 */
	function remove()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

		$model = $this->getModel('editcats');
		if(!$model->delete($cid)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( 'index.php?option=com_zepp_designworked&view=categories', 'Удаленно' );
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$msg = JText::_( 'Отмена' );
		$this->setRedirect( 'index.php?option=com_zepp_designworked&view=categories', $msg );
	}
	
}