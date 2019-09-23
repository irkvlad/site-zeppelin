<?php
/**
 * Polnocvet default controller
 * 
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link
 * @license		irkvlad
 */

jimport('joomla.application.component.controller');

/**
 * Polnocvet Component Controller
 *
 * @package		HelloWorld
 */
class PolnocvetController extends JController
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display()
	{
		//$db     = & JFactory::getDBO();
		$model = $this->getModel('polnocvet');
		$userGroup = $model->getGroup_id(); //* данные текущего usera
		//$manager_id = JRequest::getVar('manager', 0 , 'int');

		$task = JRequest::getVar('task'); // Получаем запрос
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$Itemid = JRequest::getVar('Itemid');
		//print_R($task,false);
		switch( $task ) {// анализируем запрос
			case 'новый файл': //Менеджер хочет добавить файл - открываем форму для добавления файла
				/*
				if ($userGroup['usergid'] < 24 AND $userGroup['group_id'] != 10 ){
					JError::raiseWarning( 403, JText::_('Вы не являетесь администратором или менеджером') );
					break;
				}*/
													
				$link =JRoute::_( 'index.php?option=com_zepp_polnocvet&view=main&layout=form&limitstart='.$limitstart.'&Itemid='.$Itemid
					, false );
				$this->setRedirect($link);
				/**/

				break;

			case 'save_record': //Записываем все в базу

				if ($userGroup['usergid'] < 23 ){
					JError::raiseWarning( 403, JText::_('Вы не являетесь администратором или менеджером') );
					break;
				}

				$link =JRoute::_( 'index.php?option=com_zepp_polnocvet&view=main&limitstart='.$limitstart.'&Itemid='.$Itemid
					, false );

				if($m = $model->saveRecord()){  // Сохранение
						$msg = JText::_($m );
						$type = 'message';
					polnocvetHTML::sendMail3Dcehcom($m.'; Менеджер:'.$userGroup['username'].'<br>Вам нужно поставить отметку, о сроке выпонения, на сайте: <a href="http://zeppelin/zepp'.$link.'" > Перейти</a>');
						JFactory::getApplication()->enqueueMessage($msg, $type);
						$this->setRedirect($link);
				}else{
						JError::raiseWarning( 403, JText::_('Ошибка com_zepp_polnocvet\controller.php\save_record ') );
				}


				break;
			case 'Поставить дату':

				if ($userGroup['polnocvet'] != 1){
					JError::raiseWarning( 403, JText::_('Вы не можете принимать данное решение') );
					break;
				}

				$link =JRoute::_( 'index.php?option=com_zepp_polnocvet&view=main&limitstart='.$limitstart.'&Itemid='.$Itemid
					, false );

				if($m = $model->saveSetDite()){  // Сохранение
					$msg = JText::_($m[0] );
					$type = 'message';
					polnocvetHTML::sentMailToManager($m[0].'; <a href="http://zeppelin/zepp'.$link.'" > перейти на сайт</a>' , $m[1] );
					JFactory::getApplication()->enqueueMessage($msg, $type);
					$this->setRedirect($link);
				}else{
					JError::raiseWarning( 403, JText::_('Ошибка com_zepp_polnocvet\controller.php\save_record ') );
				}
				/**/

				break;
			case 'Печать готова':

				if ($userGroup['polnocvet']  != 1 ){
					JError::raiseWarning( 403, JText::_('Вы не можете принимать данное решение') );
					break;
				}

				$link =JRoute::_( 'index.php?option=com_zepp_polnocvet&view=main&limitstart='.$limitstart.'&Itemid='.$Itemid
					, false );

				if($m = $model->sentComplect()){  // Сохранение
					$msg = JText::_($m[0]);
					$type = 'message';
					polnocvetHTML::sentMailToManager($m[0].'; <a href="http://zeppelin/zepp'.$link.'" > перейти на сайт</a>' , $m[1] );
					JFactory::getApplication()->enqueueMessage($msg, $type);
					$this->setRedirect($link);
				}else{
					JError::raiseWarning( 403, JText::_('Ошибка com_zepp_polnocvet\controller.php\save_record ') );
				}
				/**/

				break;

			case 'Статус':

				if ($userGroup['usergid'] < 23 ){
					JError::raiseWarning( 403, JText::_('Вы не являетесь администратором или менеджером') );
					break;
				}
				$id = JRequest::getVar('text');

				$link =JRoute::_( 'index.php?option=com_zepp_polnocvet&view=status&layout=form&limitstart='.$limitstart.'&id='.$id.'&Itemid='.$Itemid
					, false );
				
					$this->setRedirect($link);				

				break;
			
			case 'выход':

				$link =JRoute::_( 'index.php?option=com_zepp_polnocvet&view=main&limitstart='.$limitstart.'&Itemid='.$Itemid
					, false );
				$this->setRedirect($link);
				break;

			case 'Брак':

				if ($userGroup['usergid'] < 23){
					JError::raiseWarning( 403, JText::_('Вы не являетесь администратором или менеджером') );
					break;
				}

				$link =JRoute::_( 'index.php?option=com_zepp_polnocvet&view=main&limitstart='.$limitstart.'&Itemid='.$Itemid
					, false );

				if($m = $model->setBrack()){  // Сохранение
					$msg = JText::_($m[0]);
					$type = 'message';
					polnocvetHTML::sendMailNachalnick("При выполнении заказа выявлен брак:<br>".$m[1].'<br> <a href="http://zeppelin/zepp'.$link.'" > перейти на сайт</a>','При выполнении заказа от ЦеППелин, выявлен брак');
					JFactory::getApplication()->enqueueMessage($msg, $type);
					$this->setRedirect($link);
				}else{
					JError::raiseWarning( 403, JText::_('Ошибка com_zepp_polnocvet\controller.php\save_record ') );
				}

				break;

			case 'Готово':

				if ($userGroup['usergid'] < 23 ){
					JError::raiseWarning( 403, JText::_('Вы не являетесь администратором или менеджером') );
					break;
				}

				$link =JRoute::_( 'index.php?option=com_zepp_polnocvet&view=main&limitstart='.$limitstart.'&Itemid='.$Itemid
					, false );

				if($m = $model->setComplekt()){  // Сохранение
					$msg = JText::_($m[0]);
					$type = 'message';
					polnocvetHTML::sendMail3Dcehcom("Заказ принят<br>".$m[0].'<br> <a href="http://zeppelin/zepp'.$link.'" > перейти на сайт</a>');
					JFactory::getApplication()->enqueueMessage($msg, $type);
					$this->setRedirect($link);
				}else{
					JError::raiseWarning( 403, JText::_('Ошибка com_zepp_polnocvet\controller.php\save_record ') );
				}

				break;



			case 'complaint':
				$text = JRequest::getVar('text');
				$msg = "";
				

				if ($userGroup['usergid'] < 23){
					echo JText::_('Вы не являетесь администратором или менеджером');
					exit;
				}			

				if($m = $model->setComplaint()){  // Сохранение
					$link =JRoute::_( 'index.php?option=com_zepp_polnocvet&view=main&limitstart='.$limitstart.'&Itemid='.$Itemid
						, false );
					$msg = JText::_($m[0]).'<b style="color:red;">'.": ".JText::_($m[1]).'</b>';
					polnocvetHTML::sendMailNachalnick("Жалоба на полноцветную печать:<br>".$m[1].' <br><br><br>  Дата: '.date("Y.m.d").'   '.$userGroup['username'].'<br> <a href="http://zeppelin/zepp'.$link.'" > перейти на сайт</a>','Жалоба на полноцветную печать');
					echo $msg;					
				}else{
					echo JText::_('Ошибка com_zepp_polnocvet\controller.php\save_record ') ;
				}

				exit;
				break;
		}



		parent::display();
	}

}

