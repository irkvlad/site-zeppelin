<?php
/**
 *
 */

defined('_JEXEC') or die('Restricted access');

class polnocvetHTML { //polnocvetHTML::getUserName($user_id);
    
    function getUserName($user_id){
        $database = JFactory::getDBO();
        $database->setQuery( "SELECT name FROM #__users WHERE id = ".$user_id );
    return $database->loadResult();
    }
    
    function getContactName($user_id){
        $database = JFactory::getDBO();
        $database->setQuery( "SELECT name FROM  #__contact_details WHERE id = ".$user_id );
    return $database->loadResult();
    }

    function getUserPChekc($user_id){
        $database = JFactory::getDBO();
        $database->setQuery( "SELECT pochta_chek FROM #__users WHERE id = ".$user_id );
    return $database->loadResult();
    }

    function userDetails( $user_id )
    {
        $database = JFactory::getDBO();
        $database->setQuery( "SELECT * FROM #__contact_details WHERE user_id = " . $user_id );
    return $database->loadObject();
    }

    function userEmail( $user_id )
    {
        $database = JFactory::getDBO();
        $database->setQuery( "SELECT email FROM #__users WHERE id = " . $user_id );
    return $database->loadResult();
    }
    
    function managersEmails()
    {
        //Получаем почтовые адреса менеджеров
        $emails= array(); 
        $db                 = & JFactory::getDBO();
        $db->setQuery('SELECT user_id FROM jos_projectlog_groups_mid WHERE group_id = 10');
        $managers_id = $db->loadResultArray();        

        foreach ( $managers_id as $m_i ){
            $db->setQuery( "SELECT email FROM #__users WHERE id=".$m_i );
            $emails[] = $db->loadResult();            
        }    
       
        return $emails;
    }
    
    function sentMailToAdmin($body)
    {
        //Отправка сообщения офис-менеджеру
       $config =& JFactory::getConfig(); // Почта
       $sender = array($config->getValue('config.mailfrom'), $config->getValue('config.fromname') ); 
       $mailer =& JFactory::getMailer();
       $mailer->setSender($sender);
       $mailer->isHTML(true);

       $z_email = $email= array('zepp@zepp.ru');
       $mailer->addRecipient($z_email);
       $mailer->setSubject('Заказ отправлен менеджеру');
       
       $mailer->setBody($body);
       $send =& $mailer->Send();
       if ($send !== true) {
           JError::raiseWarning( 403, JText::_('Ошибка при отправлении почты sentMailToAdmin'.$send->message));
           return false;
       } 
       return true;
    }

    function sendMail3Dcehcom($body,$tema='Поступил файл для печати от ЦеППелин'){
        //Отправка сообщения в цехком
        $config =& JFactory::getConfig(); // Почта
        $sender = array($config->getValue('config.mailfrom'), $config->getValue('config.fromname') );
        $mailer =& JFactory::getMailer();
        $mailer->setSender($sender);
        $mailer->isHTML(true);
        
        $z_email = $email= array('polnocvet@zepp.ru'); // 3d@cehcom.ru
        $mailer->addRecipient($z_email);
        $mailer->setSubject($tema);

        $mailer->setBody($body);
        $send =& $mailer->Send();
        if ($send !== true) {
            JError::raiseWarning( 403, JText::_('Ошибка при отправлении почты sentMailToAdmin'.$send->message));
            return false;
        }
        return true;
    }

    function sendMailNachalnick($body,$tema){
        //Отправка сообщения в цехком
        $config =& JFactory::getConfig(); // Почта
        $sender = array($config->getValue('config.mailfrom'), $config->getValue('config.fromname') );
        $mailer =& JFactory::getMailer();
        $mailer->setSender($sender);
        $mailer->isHTML(true);

        $z_email = $email= array('vadimsuit@yandex.ru'); // 3d@cehcom.ru
        $mailer->addRecipient($z_email);
        $mailer->setSubject($tema);

        $mailer->setBody($body);
        $send =& $mailer->Send();
        if ($send !== true) {
            JError::raiseWarning( 403, JText::_('Ошибка при отправлении почты sentMailToAdmin'.$send->message));
            return false;
        }
        return true;
    }
    
    function sentMailToManager($body, $manager_id = 0)
    {        
        $config =& JFactory::getConfig(); 
        $sender = array($config->getValue('config.mailfrom'), $config->getValue('config.fromname') );   
        $mailer =& JFactory::getMailer();    
        $mailer->setSender($sender);
        if ($manager_id == 0) {
            $email= polnocvetHTML::managersEmails();
        }else{
            $email= polnocvetHTML::userEmail( $manager_id );
        }
          
        $mailer->addRecipient($email);
        $mailer->setSubject('Заказ отправлен менеджеру');
        
        $mailer->isHTML(true);
        $mailer->setBody($body);
        //print_R($mailer,false);
       // if (polnocvetHTML::getUserPChekc($manager_id) == 1) {
            $send =& $mailer->Send();
            if ($send !== true) {
                JError::raiseWarning(403, JText::_('Ошибка при отправлении почты sentMailToManager' . $send->message));
                return false;
            }
        //}

        // Для ZeppProjekt
       // $db = JFactory::getDBO();
       // $query ="INSERT INTO #__zepp_pochta ( from_user_id , text , to_user_id , tema , project_id) VALUES ( 146 ,  '".$body."' , ".$manager_id." , 3 , 0 ) ;";
      //  $db->setQuery($query);
      //  if(!$db->query()) {
       //     $this->setError($db->getErrorMsg());
            //return false;
        //}
    }
            
    function timep($id_mun){
        //if ($time == NULL) $time = time();
        //$timep = "" . date("j M Y года в H:i:s", $time) . "";
        $id_mun = str_replace("Jan", "Январь", $id_mun);
        $id_mun = str_replace("Feb", "Февраль", $id_mun);
        $id_mun = str_replace("Mar", "Март", $id_mun);
        $id_mun = str_replace("May", "Май", $id_mun);
        $id_mun = str_replace("Apr", "Апрель", $id_mun);
        $id_mun = str_replace("Jun", "Июнь", $id_mun);
        $id_mun = str_replace("Jul", "Июль", $id_mun);
        $id_mun = str_replace("Aug", "Август", $id_mun);
        $id_mun = str_replace("Sep", "Сентябрь", $id_mun);
        $id_mun = str_replace("Oct", "Октябрь", $id_mun);
        $id_mun = str_replace("Nov", "Ноябрь", $id_mun);
        $id_mun = str_replace("Dec", "Декабрь", $id_mun);
    return $id_mun;
    }
        
    function _translit($_str)
    {
        $tr = array(
            'А'=>'A','Б'=>'B','В'=>'V','Г'=>'G',
            'Д'=>'D','Е'=>'E','Ж'=>'J','З'=>'Z','И'=>'I',
            'Й'=>'Y','К'=>'K','Л'=>'L','М'=>'M','Н'=>'N',
            'О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T',
            'У'=>'U','Ф'=>'F','Х'=>'H','Ц'=>'TS','Ч'=>'CH',
            'Ш'=>'SH','Щ'=>'SCH','Ъ'=>'','Ы'=>'YI','Ь'=>'',
            'Э'=>'E','Ю'=>'YU','Я'=>'YA','а'=>'a','б'=>'b',
            'в'=>'v','г'=>'g','д'=>'d','е'=>'e','ж'=>'j',
            'з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l',
            'м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r',
            'с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h',
            'ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sch','ъ'=>'y',
            'ы'=>'yi','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya', ' ' => '_');

    return strtr($_str,$tr);
    }

    /**
     * Количество квадратных метров день
     */
    function getTotalFeniks( & $polnocvet){
        $db                 = & JFactory::getDBO();
        $db->setQuery('SELECT SUM(`ploschad`) as date , `set_date`, `stanok` FROM `jos_zepp_polnocvet` WHERE (`set_date` >= curdate()) AND (`stanok` = \'ф\' OR `stanok` = \'Ф\' ) GROUP BY `set_date`');
        $sum_ploschad = $db->loadObjectList();
        return $sum_ploschad;
    }

    /**
     * Количество квадратных метров день
     */
    function getTotalRoland( & $polnocvet){
        $db                 = & JFactory::getDBO();
        $db->setQuery('SELECT SUM(`ploschad`) as date , `set_date`, `stanok` FROM `jos_zepp_polnocvet` WHERE (`set_date` >= curdate()) AND (`stanok` = \'р\' OR `stanok` = \'Р\' ) GROUP BY `set_date`');
        $sum_ploschad = $db->loadObjectList();
        return $sum_ploschad;
    }

    /**
     * Количество квадратных метров день
     */
    function getTotalUF( & $polnocvet){
        $db                 = & JFactory::getDBO();
        $db->setQuery('SELECT SUM(`ploschad`) as date , `set_date`, `stanok` FROM `jos_zepp_polnocvet` WHERE (`set_date` >= curdate()) AND (`stanok` = \'у\' OR `stanok` = \'У\' ) GROUP BY `set_date`');
        $sum_ploschad = $db->loadObjectList();
        return $sum_ploschad;
    }

    /**
     * Количество квадратных метров день
     */
    function getTotalLum( & $polnocvet){
        $db                 = & JFactory::getDBO();
        $db->setQuery('SELECT SUM(`ploschad`) as date , `set_date`, `stanok` FROM `jos_zepp_polnocvet` WHERE (`set_date` >= curdate()) AND (`stanok` = \'л\' OR `stanok` = \'Л\' ) GROUP BY `set_date`');
        $sum_ploschad = $db->loadObjectList();
        return $sum_ploschad;
    }

}