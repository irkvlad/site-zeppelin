<?php
/*
 * модуль работает в связке с компонетом по учету клиентов com_zepp_clientcontact
 * и использует его базу
 */
defined('_JEXEC') or die('Restricted access');

//* внесенные данные если есть
$butom              ='';
$link_moe           ='';
$datemysql='null';// Получаем дату
$zep_msg            = '';
$setuser=0;
$userguest=114;
$itemid = 101;
$mediaFileLink = "";
//$getself            ='';
//$username = '';
$com_contactlink    ='index.php?option=com_zepp_ringclient&view=ringclient&Itemid='.$itemid;//Сылка на компонент обработки заказов
$tdate            = JRequest::getVar('tdate');//Метка времени
$telefon            = JRequest::getVar('telefon');//Телефон клиента
$clentname          = JRequest::getVar('clentname');//Данные клиента
$username           = JRequest::getVar('username');//Кто внес данные
$tema               = JRequest::getVar('tema');//Тема заказа
$mediaFileLink      = JRequest::getVar('mediaFileLink');//ссылка на запись разговора
$clientcontact      = JRequest::getVar('clientcontact');  //признак сохранения данных
$getself             = JRequest::getVar('getself');
$db                 = & JFactory::getDBO();
$config =& JFactory::getConfig(); // Почта
$sender = array($config->getValue('config.mailfrom'), $config->getValue('config.fromname') ); // Почта
$date = JFactory::getDate(); // Получаем дату

$session = JFactory::getSession();
$one_tdate = $session->get('one_tdate', '0'); // сохраненная метка времени
$one_data =  $session->get('one_data', '');   //Метка данных
$set_data = $telefon.$clentname.$tema;

//* данные текущего usera
$user = JFactory::getUser();
$userid = 114;
if ($user->guest) {
    //$username = '';
} else {
    $userlogin = $user->username;
    $username = $user->name;
    $userid = $user->id;
    $userguest=$userid;
//Является ли user менеджером
    $query = 'SELECT group_id FROM jos_projectlog_groups_mid WHERE user_id =' . $userid;
    $db->setQuery($query);
    $group_id = $db->loadResult(); 
    if($group_id == 10)  {
        $link_moe = '<input type="submit" name="getself" class="button" value="Мое" />';

        if ($getself == 'Мое') {
            $setuser = $userid;
            $datemysql = 'NOW()';//$date->toMySQL(); // Вызов метода toMySQL
        }
    }
    if(($group_id == 10) OR ($user->gid > 23) )  {
        //Формирую кнопку перехода к распределению заказов
        $query = 'SELECT COUNT(*) FROM `jos_zepp_ringclient` WHERE `manager_id`=0';
        $db->setQuery($query);
        $count = $db->loadResult();//число нерспределенных заказов
        if($count > 0){
           $button="<button style=\"float:left;\" size=\"10\" alt=\"Перейти к заказам\" 
                        onclick=\"document.location.assign('$com_contactlink')\" >Есть заказы $count шт.</button>";
        }
    }
}

if($tdate == $one_tdate) $err = 'not sesion cheket';
else {
    $session->set( 'one_tdate', $tdate );
    $session->set( 'one_data', $set_data);
}

if ($clientcontact == 'save' AND ($tdate > $one_tdate) AND ($set_data!=$one_data)) {
    /*// для теста
    print "Текущий URI Запрос";
    echo '<br>$clientcontact- ';
    print_R($clientcontact, false);
    echo '<br>$telefon- ';
    print_R($telefon, false);
    echo '<br>$clentname- ';
    print_R($clentname, false);
    echo '<br>$username- ';
    print_R($username, false);
    echo '<br>$group_id- ';
    print_R($group_id, false);*/
 echo '<!-- '.$mediaFileLink .' = $mediaFileLink  -->';
    /* $uri = JFactory::getURI(); print_R($uri,true);
      print "\nТекущий URI Запрос\n";
      foreach ($uri as $key => $value) {
      if (is_string($value)) {
      print "<p> Свойство $key = $value </p>\n";
      } else {
      print "<p> Свойство $key - не строка </p>\n";
      } */


    //запрос к базе на сохране данных   
    $query = 'INSERT INTO `jos_zepp_ringclient` (`manager_id`,`client`,`creator_id`,`creator_name`,`telefon`,`tema`,`media_file`, `manger_data`)  VALUES  (';
    $query .= $setuser;                                          //Кто менеджер
    $query .= " , '" . $clentname . "' , ";                      //Название клиента
    $query .= $userguest;                                        //Кто содал запись
    $query .= " , '" . $username . "'";                          // Имя создавшего запись
    $query .= " , '" . $telefon . "'";                           //телефон клиента
    $query .= " , '" . $tema . "'";  //Тема заказа
	$query .= " , '" . $mediaFileLink . "'";  //Ссылка на запись разговора
    $query .= " , ";
    $query .= $datemysql ; // Дата присоединения менеджера
    $query .= " )";
	
	echo '<!-- '.$query .' = $query  -->';
    $db->setQuery($query);
    if (!$db->query()) {
        // Вывод ошибки, если запрос не выполняется
        $msg = "Ошибка при формировании заказа: $db->getErrorMsg()";
    } else {
        // Вывод ошибки, если запрос не выполняется
        $msg = 'Заказ сохранен ';
        $msg_alert = '<script language="JavaScript">
                    <!--
                    alert("Ваш заказ сохранен!");
                    //-->
                </script>';
    }
    
    if ($getself == 'Сохранить'){
        //Рассылка сообщений менеджерам  
        $mailer =& JFactory::getMailer();    
        $mailer->setSender($sender);
        $email= array();   

        //Получаем почтовые адреса менеджеров
        $db->setQuery('SELECT user_id FROM jos_projectlog_groups_mid WHERE group_id = 10');
        $managers_id = $db->loadResultArray();
        //echo "vfyfuths: "; print_R($managers_id,false);   

        foreach ( $managers_id as $m_i ){
            $db->setQuery( "SELECT email FROM #__users WHERE id=".$m_i );
            $email[] = $db->loadResult();            
        }
               
        $mailer->addRecipient($email);
        $mailer->setSubject('Появился новый заказ');
        $body   = '<h2>Появился новый заказ!</h2>'
             .'<div>'
                . '<p>Наш сотрудник принял звонок от клиента и сформировал заявку:'
                . '<br>клиент: '.$clentname            
                . '<br>описание: '.$tema           
                . '<br> ,- если вы хотите взять заявку '
                //. ', вам нужно позвонить офис менеджеру.'
                .'себе перейдите по ссылке:</p>'
                . ' <a href="http://zeppelin/zepp/'.$com_contactlink.'">http://zeppelin/zepp/'.$com_contactlink.'</a>'
             . '</div>';
        $mailer->isHTML(true);
        $mailer->setBody($body);
        //print_R($mailer,false);

        $send =& $mailer->Send();
        if ($send !== true) {
            $msg.= ' | Ошибка при отправлении почты'.$send->message;
        } else {
            $msg.=' | Уведомления менеджерам отправленны';
        }/**/
    }
    //Отправка сообщения офис-менеджеру
    $mailer =& JFactory::getMailer();
    $mailer->setSender($sender);
    $mailer->isHTML(true);
    
    $z_email = $email= array('zepp@zepp.ru');
    $mailer->addRecipient($z_email);
    $body = '<h2>Появился новый заказ!</h2>'
         .'<div>'
            . '<p>Наш сотрудник принял звонок от клиента и сформировал заявку:'
            . '<br>клиент: '.$clentname
            . '<br>телефон: '.$telefon
            . '<br>описание: '.$tema
            . '<br>заказ сформировал: '.$username
             . '<br> ,- посмотреть можно по следующей ссылке:</p>'
                . ' <a href="http://zeppelin/zepp/'.$com_contactlink.'">http://zeppelin/zepp/'.$com_contactlink.'</a>'
         . '</div>';
    $mailer->setBody($body);
    $send =& $mailer->Send();
    if ($send !== true) {
        $msg.= ' | Ошибка2 при отправлении почты'.$send->message;
    } else {
        $msg.=' | Уведомление офис менеджеру отправленно';
    }/**/
}
?>



<?php echo $msg_alert; //echo $tdate.' = '.$date->_date.' = '.$one_tdate.'===='.$err.'==='; ?>
<label style="color:#ff3333;background-color: #ffff99"><?php echo $msg; ?></label>
<form  action="index.php?option=com_zepp_ringclient&view=ringclient&Itemid=<?php echo $itemid; ?> " method="post" name="clientcontact" id="clientcontact">
    <!--//http://zepp/index.php?option=com_zepp_ringclient&view=ringclient&Itemid=101-->
    <b>Регистрация заказов | </b>
    <label for="modclt_username"><?php echo 'Ваше имя: ' ?></label>
    <input required id="modclt_username" type="text" size="15" name="username" value="<?php echo $username; ?>" class="inputbox" alt="Укажите себя" size="18" /> |

    <label for="modclt_clentname"><?php echo 'Клиент: ' ?></label>
    <input required id="modclt_clentname" type="text"  name="clentname" class="inputbox" alt="Укажите имя клиента" size="18" maxlength="130" /> |

    <label for="modclt_telefon"><?php echo 'Телефон: ' ?></label>
    <input required id="modclt_telefon" type="text"  name="telefon" class="inputbox" alt="Укажите номер телефона" size="18" maxlength="130" /> |

    <label for="modclt_tema"><?php echo 'Что нужно: ' ?></label>
    <input required id="modclt_tema" type="text"  name="tema" class="inputbox" alt="Укажите номер телефона" size="18" maxlength="130" />  |

	<label for="modclt_tema"><?php echo 'запись разговора: ' ?></label>
    <input placeholder="ссылка на запись, запись тут 192.168.5.249:50443 101 101"  id="modclt_mediaFileLink" type="text"  name="mediaFileLink" class="inputbox" alt="ссылка на запись" size="18" maxlength="255" />  |
		
    <input type="submit" name="getself" class="button" value="Отправить" />

    <?php    echo $link_moe ?>

    <input type="hidden" name="clientcontact" value="save" />
    <input type="hidden" name="tdate" value="<?php echo $date->_date; ?>" />

</form>

<?php    echo $button; ?>
