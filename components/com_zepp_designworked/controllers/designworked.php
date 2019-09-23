<?php

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once( './dumphper.php' );
//echo "\controllers\designworked.php1111<br>";

class designworkedsControllerDesignworked extends designworkedsController
{
	
	function __construct()
	{
//echo "designworkedsControllerDesignworked __construct()1111111<br>";
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
//echo "designworkedsControllerDesignworked edit()<br>";
		$dis	= JRequest::getVar( 'dis' );
		$cat	= JRequest::getVar( 'cat' );
		
		JRequest::setVar( 'view', 'designworked' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar( 'cat', $cat );
		JRequest::setVar( 'dis', $dis  );
		//JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$msg="";

		$post	= JRequest::get('post');
		$cid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$post['id'] = (int) $cid[0];

		$model = $this->getModel('designworked');
		$data=$model->store($post);

		if ($data['error']) {
			$msg .= JText::_( 'Ошибка при сохранении Записи:<br>'.$data['error']);
		}

		$msg .= JText::_( 'Запись сохранена:<br>'.$data['info']);

		//$dump['POST']= $post;
      //  $dump['DATA']= $data;
//dump($dump);
       $link = 'index.php?option=com_zepp_designworked&view=designworked&layout=form&Itemid=109';
	   $dis=JRequest::getVar( 'dis' );
		$cat=JRequest::getVar( 'cat' );
		
		if ($dis > 0 AND $cat > 0 ) $link='index.php?option=com_zepp_designworked&view=designworked&dis='.$dis.'&cat='.$cat.'&cid='.$post['id'];
		$this->setRedirect( $link , $msg );
		//$link = 'index.php?option=com_zepp_designworked&view=designworkeds';
		$this->setRedirect($link, $msg);

	}

	/**
	 * remove record(s)
	 * @return void
	 */
	function remove()
	{
		$model = $this->getModel('designworked');
		if(!$model->delete()) {
			$msg = JText::_( 'Ошибка удаления' );
		} else {
			$msg = JText::_( ' Удалено' );
		}
//dump($data);
		$this->setRedirect( 'index.php?option=com_zepp_designworked&view=designworkeds', $msg );
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$msg = JText::_( 'Отмена' );
		$dis=JRequest::getVar( 'dis' );
		$cat=JRequest::getVar( 'cat' );
		$link='index.php?option=com_zepp_designworked&view=designworkeds';
		if ($dis > 0 AND $cat > 0 AND $post['id'] > 0 ) $link='index.php?option=com_zepp_designworked&view=designworked&dis='.$dis.'&cat='.$cat.'&cid='.$post['id'];
		$this->setRedirect( $link , $msg );
	}
	
}