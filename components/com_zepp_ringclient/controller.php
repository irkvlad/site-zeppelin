<?php

jimport('joomla.application.component.controller');

class ringclientController extends JController
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display()
	{
            //$db     = & JFactory::getDBO();
            $model = $this->getModel('ringclient');
            $userGroup = $model->getGroup_id(); //* данные текущего usera
            
            $manager_id = JRequest::getVar('manager', 0 , 'int');
            $id = JRequest::getVar('id',0,'int');
			$link =JRoute::_( 'index.php?option=com_zepp_ringclient&view=landing&layout=form'
									.'&id='.$id.'&Itemid=102'
									 , false );

            $cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
            
            
            $task = JRequest::getVar('task'); // Получаем запрос

        //print_R($task,false);
            switch( $task ) {// анализируем запрос
            // Менеджер забирает заказ себе
                case 'getzakaz':
                    if ($userGroup['group_id'] == 10){                        
                        
                        if($id === 0) { break; }
                        
                        if($model->saveManager($id , $userGroup['userid'])){  // Сохранение заказа за менеджером
                            $msg = JText::_('Вы приняли заказ');
                            $type = 'message';
                            JFactory::getApplication()->enqueueMessage($msg, $type);
                        }else{
                            JError::raiseWarning( 403, JText::_('Ошибка сохрания') );
                        } 
                    } else {
                        JError::raiseWarning( 403, JText::_('Вы не являетесь менеджером') );
                        break;
                    }
                break;

            //Администратор отдает заказ менеджеру
                case 'editzakaz':
                     if($id === 0 OR $manager_id === 0) break;
                     
                    if ($userGroup['usergid'] < 24){
                       JError::raiseWarning( 403, JText::_('Вы не являетесь администратором') );
                        break;
                    } 
                   // $id = JRequest::getVar('id');
                        
                    if($model->saveManager($id , $manager_id)){  // Сохранение заказа за менеджером
                        $msg = JText::_('Заказ отправлен');
                        $type = 'message';
                        JFactory::getApplication()->enqueueMessage($msg, $type);
                    }else{
                        JError::raiseWarning( 403, JText::_('Ошибка сохрания') );
                    }                    	
                    
                break;
				
				//Сохранение записи разговора
                case 'save_media_file':
					$media_file = JRequest::getVar('media_file','');
                    if($id === 0 OR !isset($media_file) OR strlen($media_file) < 6 ) $this->setRedirect($link);;
                     
                    if (($userGroup['group_id'] == 10) OR ($userGroup['usergid'] >= 24)){
                      
					//	$id = JRequest::getVar('id');
							
						if($model->saveMediaFile($id , $media_file)){  // Сохранение заказа за менеджером
							$msg = JText::_('Запись сохранена');
							$type = 'message';
							JFactory::getApplication()->enqueueMessage($msg, $type);
						}else{
							JError::raiseWarning( 403, JText::_('Ошибка сохрания записи') );
						} 
					}  else {
						JError::raiseWarning( 403, JText::_('Вы не являетесь менеджером') );
                        break;
                    }   

					$this->setRedirect($link);					
                    
                break;
				
				//Удаление записи разговора
                case 'del_media_file':
					$media_file = '';
                    if($id === 0 ) break;
                     
                    if (($userGroup['group_id'] == 10) OR ($userGroup['usergid'] >= 24)){
                      
						//$id = JRequest::getVar('id');
							
						if($model->saveMediaFile($id , $media_file)){  // Сохранение заказа за менеджером
							$msg = JText::_('Сылка на запись удалена');
							$type = 'message';
							JFactory::getApplication()->enqueueMessage($msg, $type);
						}else{
							JError::raiseWarning( 403, JText::_('Ошибка удаления записи') );
						} 
					}  else {
						JError::raiseWarning( 403, JText::_('Вы не являетесь менеджером') );
                        break;
                    }    

					$this->setRedirect($link);
                    
                break;

//Сохранение записи разговора2
                case 'save_media_file2':
				$media_file = JRequest::getVar('media_file2','');
                     if($id === 0 OR !isset($media_file) OR strlen($media_file) < 6 ) $this->setRedirect($link);
                     
                    if (($userGroup['group_id'] == 10) OR ($userGroup['usergid'] >= 24)){
                      
						//$id = JRequest::getVar('id');
							
						if($model->saveMediaFile2($id , $media_file)){  // Сохранение заказа за менеджером
							$msg = JText::_('Запись сохранена');
							$type = 'message';
							JFactory::getApplication()->enqueueMessage($msg, $type);
						}else{
							JError::raiseWarning( 403, JText::_('Ошибка сохрания записи') );
						} 
					}  else {
						JError::raiseWarning( 403, JText::_('Вы не являетесь менеджером') );
                        break;
                    }  
					
					$this->setRedirect($link);	
                    
                break;
				
				//Удаление записи разговора2
                case 'del_media_file2':
					$media_file = '';
                    if($id === 0 ) break;
                     
                    if (($userGroup['group_id'] == 10) OR ($userGroup['usergid'] >= 24)){
                      
						//$id = JRequest::getVar('id');
							
						if($model->saveMediaFile2($id , $media_file)){  // Сохранение заказа за менеджером
							$msg = JText::_('Сылка на запись удалена');
							$type = 'message';
							JFactory::getApplication()->enqueueMessage($msg, $type);
						}else{
							JError::raiseWarning( 403, JText::_('Ошибка удаления записи') );
						} 
					}  else {
						JError::raiseWarning( 403, JText::_('Вы не являетесь менеджером') );
                        break;
                    }  
					
					$this->setRedirect($link);                	
                    
                break;
				
				//Сохранение записи рентабельности
                case 'save_rentabelnost':
					$media_file = JRequest::getVar('rentabelnost','');
                     if($id === 0 OR !isset($media_file) ) $this->setRedirect($link);;
                     
                    if (($userGroup['group_id'] == 10) OR ($userGroup['usergid'] >= 24)){
                      
						//$id = JRequest::getVar('id');
							
						if($model->saveRentabelnost($id , $media_file)){  // Сохранение заказа за менеджером
							$msg = JText::_('Запись сохранена');
							$type = 'message';
							JFactory::getApplication()->enqueueMessage($msg, $type);
						}else{
							JError::raiseWarning( 403, JText::_('Ошибка сохрания записи') );
						} 
					}  else {
						JError::raiseWarning( 403, JText::_('Вы не являетесь менеджером') );
                        break;
                    } 
						
					
					$this->setRedirect($link);
                    
                break;
				
				
                case 'Отказались от заказа':
                    if($id === 0) break;
                    if( ($userGroup['group_id'] <> 10) AND ($userGroup['usergid'] < 24)) {
                        JError::raiseWarning( 403, JText::_('Вы не являетесь администратором ' . $userGroup['group_id']).' \ '.$userGroup['usergid'] );
                        break;
                    }


                    $text = JRequest::getVar('text','');

                    $model = $this->getModel('landing');
                    if($model->setStatusNO($id , $text)) {
                        $msg = JText::_('Статус сохранен');
                        $type = 'message';
                        JFactory::getApplication()->enqueueMessage($msg, $type);

                    }else{
                        JError::raiseWarning( 403, JText::_('Ошибка сохранения статуса') );
                    }
                 break;

                case 'Заказ в работе':
                    if($id === 0) break;
                    if( ($userGroup['group_id'] <> 10) AND ($userGroup['usergid'] < 24)) {
                        JError::raiseWarning( 403, JText::_('Вы не являетесь администратором ' . $userGroup['group_id']).' \ '.$userGroup['usergid'] );
                        break;
                    }


                    $text = JRequest::getVar('text','');

                    $model = $this->getModel('landing');
                    if($model->setStatusYS($id , $text)) {
                        $msg = JText::_('Статус сохранен');
                        $type = 'message';
                        JFactory::getApplication()->enqueueMessage($msg, $type);

                    }else{
                        JError::raiseWarning( 403, JText::_('Ошибка сохранения статуса') );
                    }

                    //echo 'Заказ в работе ид='.$id.'text='.$text;
                 break;

                case 'Заказы в работе':
                    //if($id === 0) break;
                     if( ($userGroup['group_id'] <> 10) AND ($userGroup['usergid'] < 24)) {
                         JError::raiseWarning( 403, JText::_('Вы не являетесь администратором ' . $userGroup['group_id']).' \ '.$userGroup['usergid'] );
                         return;
                     }


                     $text = "Статус установил ". $userGroup['username'] ;

                     $model = $this->getModel('landing');
                     if (count( $cids )) {
                         foreach($cids as $cid) {
                                if($model->setStatusYS($cid , $text)) {
                                    $msg = JText::_('Статус сохранен');
                                    $type = 'message';
                                    JFactory::getApplication()->enqueueMessage($msg, $type);

                                }else{
                                    JError::raiseWarning( 403, JText::_('Ошибка сохранения статуса') );
                                }

                         }
                     }

                    //echo 'Заказ в работе ид='.$id.'text='.$text;
                    break;

                case 'Отказались от заказов':
                    //if($id === 0) break;
                    if( ($userGroup['group_id'] <> 10) AND ($userGroup['usergid'] < 24)) {
                        JError::raiseWarning( 403, JText::_('Вы не являетесь администратором ' . $userGroup['group_id']).' \ '.$userGroup['usergid'] );
                        break;
                    }


                    $text = "Статус установил ". $userGroup['username'] ;

                    $model = $this->getModel('landing');
                    if (count( $cids )) {
                        foreach($cids as $cid) {
                            if($model->setStatusNO($cid , $text)) {
                                $msg = JText::_('Статус сохранен');
                                $type = 'message';
                                JFactory::getApplication()->enqueueMessage($msg, $type);

                            }else{
                                JError::raiseWarning( 403, JText::_('Ошибка сохранения статуса') );
                            }
                        }
                    }
                    break;
            // Ошибочный заказ
                case 'Ошибочный заказ':
                    if($id === 0) break;
                    if( $userGroup['usergid'] < 23) {
                        JError::raiseWarning( 403, JText::_('Вы не являетесь администратором ' . $userGroup['group_id']).' \ '.$userGroup['usergid'] );
                        break;
                    }


                    $text = "Ошибка:".JRequest::getVar('text','').". Статус установил ". $userGroup['username'] ;
//print_R($task,false);
                    $model = $this->getModel('landing');
                    if($model->setStatusER($id , $text)) {
                        $msg = JText::_('Статус сохранен');
                        $type = 'message';
                        JFactory::getApplication()->enqueueMessage($msg, $type);

                    }else{
                        JError::raiseWarning( 403, JText::_('Ошибка сохранения статуса') );
                    }
                    break;

                //Менеджер создает НОВЫЙ проект
                case 'Новый':
                    if($id === 0 ) break;

                    if ($userGroup['usergid'] < 24 AND $userGroup['group_id'] != 10 ){
                        JError::raiseWarning( 403, JText::_('Вы не являетесь администратором или менеджером') );
                        break;
                    }
                    //$id = JRequest::getVar('id');

                    $record=$model->getRecord_id($id);
                        /* Array (
                         [id] =>
                         [manager_id] =>
                         [client] => Клиент
                         [creator_id] => кто создал
                         [creator_name] => кто создал
                         [telefon] => 12321431
                         [tema] =>
                         [creator_date] => Когда создали
                         [manger_data] => когда взял менеджер
                         [project_id] => ) */

                    $link =JRoute::_( 'index.php?option=com_projectlog&view=cat&layout=form'
                                    . '&client='.$record['client'] //клиент
                                    . '&telefon='.$record['telefon'] //телефон
                                    . '&tema='.$record['tema'] // описание
                                    . '&ringclient_ids='.$id // Ид заказа
                                    . '&project_ids='.$record['project_ids'] // созданные проекты
                                    //. '&statusdata='.$record['statusdata'] // Дата установки статуса
                                    //. '&statustext='.$record['statustext']
                        , false );
                        $this->setRedirect($link);
                    /**/

                    break;
            }
		parent::display();
                
	}

}

