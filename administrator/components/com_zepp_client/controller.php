<?php
// защита от прямого доступа
defined('_JEXEC') or die('Restricted access');
// подключаем класс JController
jimport('joomla.application.component.controller');


class clientController extends JController
{
	/**
	 * Конструктор
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Добавление
	 */
	public function add()
	{
        JRequest::setVar('view', 'Client');
		JRequest::setVar('layout', 'formClient'); //zepp_client_contact;
		$this->display();
//$this->setRedirect('index.php?option=com_zepp_client&layout=formClient');
	}

    public function cancel()
    {
    	JRequest::setVar('view', 'Client');
		//JRequest::setVar('layout', 'formClient'); //zepp_client_contact;
		$this->display();
   }

   public function save()
	{
		// проверяем токен
		//JRequest::checkToken() or jexit('Invalid Token');

		// получаем значения формы
		$data = JRequest::get('post');
		$id = JRequest::getVar('id', 0);
		$idc = JRequest::getVar('idc',0);
		$model = $this->getModel('client');
		$link = 'index.php?option=com_zepp_client';
		$type = 'message';
		if ($id){

			$msg=$model->editClient($data,$id,$idc);
			$message.=$model->getError();
         }

         else{                                      //send on_start on_send
			$client['name']=$data['name'];
			$client['legal_entity']=$data['legal_entity'];
			$client['cast']=$data['cast'];
			$client['likes']=$data['likes'];
			$client['modifer_user']=$data['modifer_user'];
			$client['on_send']=($data['on_send']) ? JHTML::_('date', $data['on_send'], '%Y-%m-%d',NULL ) : date('Y-m-d') ;
			$client['on_start']=($data['on_start']) ? JHTML::_('date', $data['on_start'], '%Y-%m-%d',NULL ) : date('Y-m-d') ;
			$client['send']=($data['send']) ? $data['send'] : 0 ;

			$id_client = $model->saveClient($client);
			if($id_client)
			{
				$i=0;
				foreach( $data['fio'] as $k)
				{
					$contact['id_client'] = $id_client;
					$contact['town'] = $data['town'][$i];
					$contact['post'] = $data['post'][$i];
					$contact['telefon'] = $data['telefon'][$i];
					$contact['email'] = $data['email'][$i];
					$contact['fio'] = $data['fio'][$i];
					$contact['birthday'] = $data['birthday'][$i];
					if ( !$model->saveContact($contact)) $message .= $model->getError();
					$i++;
				}
   			}
   		}

        if ( $model->getError())  {
			$message = $model->getError();
			// получаем ошибку из модели
			$type = 'notice';
			}

		//$message.="<br><br><br>id ".print_R($id,true)."<br><br><br>idc ".print_R($idc,true);
		//$type = 'notice';
		// перенаправялем
		$this->setRedirect($link, $message ,$type);
	}

	/**
	 * Редактирование
	 *
	 */
	public function edit()
	{

		JRequest::setVar('view', 'Edit');

		$this->display();
	}


	/**
	 * Удаляет
	 */
	public function remove() //ContactSend
	{
		$model = $this->getModel('client');
            //$c=$model->remove();
		if ($model->remove())
		{
			$message =  JText::_('CGCA ADMIN DELETE OK')."///".$model->getError();
			$type = 'message';
		}
		else
		{
			$message = JText::_('CGCA ADMIN DELETE FAILED');
			$message .= '['.$model->getError().']'; // получаем ошибку из модели
			$type = 'notice';
		}
		$this->setRedirect('index.php?option=com_zepp_client', $message);
	}

	public function ContactSend() //ContactSend
	{

			//$message = JText::_('CGCA ADMIN DELETE FAILED');
			//$message .= '['.$model->getError().']'; // получаем ошибку из модели
			//$type = 'notice';
             JRequest::setVar('sendCon', '1');
             $this->display();
		//$this->setRedirect('index.php?option=com_zepp_client&send=1', $message);
	}

	public function SortManager() //ContactSend
	{

			//$message = JText::_('CGCA ADMIN DELETE FAILED');
			//$message .= '['.$model->getError().']'; // получаем ошибку из модели
			//$type = 'notice';
             JRequest::setVar('SortManager', '1');
             $this->display();
		//$this->setRedirect('index.php?option=com_zepp_client&send=1', $message);
	}
	/**
	 * Типичный для архитектуры MVC view метод
	 */
	public function display()
	{
		parent::display();
	}
}