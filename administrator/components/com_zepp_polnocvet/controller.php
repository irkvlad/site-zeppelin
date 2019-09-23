<?php
/**
 * Polnocvet default controller
 * 
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link 
 * @license		irkvlad
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Polnocvet Component Controller
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class PolnocvetsController extends JController
{
    function __construct( $config = array() )
    {
        parent::__construct( $config );
        // Register Extra tasks
        $this->registerTask( 'edit',			'edit' );
        $this->registerTask( 'addCompany',			'addCompany' );

    }
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display()
	{
		parent::display();
	}

	function addFilial(){
        $view =& $this->getView( 'filial' );
        $model	=& $this->getModel( 'filial' );
        $view->setModel( $model, true );
        $view->display();
    }

    function saveFilial(){
     $this->setRedirect('index.php?option=com_zepp_polnocvet');
     $model = $this->getModel('filial');
     $id = JRequest::getVar('id', 0, '', 'int');
     if ($model->saveFilial($id))  JFactory::getApplication()->enqueueMessage('Сохранено', 'message');
     else  JError::raiseWarning(403, JText::_('Ошибка'));
 }

    function remove(){

        // Check for request forgeries
        //JRequest::checkToken() or jexit( 'Invalid Token' );

        // Get some variables from the request
        $cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
        JArrayHelper::toInteger($cid);

        if (!count($cid)) {
            $this->setRedirect('index.php?option=com_zepp_polnocvet', JText::_('No Items Selected') );
            return false;
        }

        $model =& $this->getModel( 'polnocvets' );
        if ($n = $model->Del($cid)) {
            $msg = JText::sprintf( 'Item(s) sent to the Trash', $n );
        } else {
            $msg = $model->getError();
        }
        $this->setRedirect( 'index.php?option=com_zepp_polnocvet', $msg );

    }

    function edit(){
        $view =& $this->getView( 'filial' );
        $model	=& $this->getModel( 'filial' );
        $view->setModel( $model, true );
        $view->display();
    }
//************************************************ Фирмы *****************************************//
    function addCompany(){
        $view =& $this->getView( 'company' );
        $model	=& $this->getModel( 'company' );
        $view->setModel( $model, true );
        $view->display();
    }

    function saveСompany(){

        //$this->setRedirect('index.php?option=com_zepp_polnocvet&view=company');http://nash.zepp/administrator/index.php
        $model = $this->getModel('company');
        $cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
        $company	= JRequest::getVar( 'company', array(), 'post', 'array' );
        $ids     =  JRequest::getVar('ids', array(), 'post', 'array' );
        JArrayHelper::toInteger($ids);
        JArrayHelper::toInteger($cid);

        foreach ( $cid as $i) {
            if ($model->saveCompany($ids[$i], $company[$i])) JFactory::getApplication()->enqueueMessage('Сохранено', 'message');
            else
                JError::raiseWarning(403, $model->_errors[0]);
            $i++;
        }
        $this->addCompany();
    }

    function removeCompany (){
        $model = $this->getModel('company');
        $cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
        $ids     =  JRequest::getVar('ids', array(), 'post', 'array' );
        JArrayHelper::toInteger($ids);
        JArrayHelper::toInteger($cid);

        foreach ( $cid as $i) {
            if ($model->delCompany($ids[$i])) JFactory::getApplication()->enqueueMessage('Сохранено', 'message');
            else  JError::raiseWarning(403, $model->_errors[0]);
            $i++;
        }
        $this->addCompany();

    }
//************************************************ Станки *****************************************//
    function addStanok(){
        $view =& $this->getView( 'stanok' );
        $model	=& $this->getModel( 'stanok' );
        $view->setModel( $model, true );
        $view->display();
    }

    function saveStanok(){
        $model = $this->getModel('stanok');
        $cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
        $ids     =  JRequest::getVar('ids', array(), 'post', 'array' );

        $name	= JRequest::getVar( 'name', array(), 'post', 'array' );
        $key	= JRequest::getVar( 'key', array(), 'post', 'array' );
        $mats	= JRequest::getVar( 'mats', array(), 'post', 'array' );
        $ploshad	= JRequest::getVar( 'ploshad', array(), 'post', 'array' );
        $ispraven	= JRequest::getVar( 'ispraven', array(), 'post', 'array' );
        $set	= JRequest::getVar( 'set', array(), 'post', 'array' );

        JArrayHelper::toInteger($ids);
        JArrayHelper::toInteger($cid);

        foreach ( $cid as $i) {
            $revMats=0;
            $revIspr=0;
            $revSet=0;
            foreach ($mats as $m){
                if($m == $ids[$i]) $revMats=1;
            }
            foreach ($ispraven as $isp){
                if($isp == $ids[$i]) $revIspr=1;
            }
            foreach ($set as $s){
                if($s == $ids[$i]) $revSet=1;
            }

            $stanok = array(name => $name[$i],key => $key[$i],mats=>$revMats,ploshad => $ploshad[$i],ispraven => $revIspr,set=>$revSet);


            if ($model->saveStanok($ids[$i], $stanok)) JFactory::getApplication()->enqueueMessage('Сохранено', 'message');
            else
                JError::raiseWarning(403, $model->_errors[0]);

        }
        $this->addStanok();
    }

    function removeStanok (){
        $model = $this->getModel('stanok');
        $cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
        $ids     =  JRequest::getVar('ids', array(), 'post', 'array' );
        JArrayHelper::toInteger($ids);
        JArrayHelper::toInteger($cid);

        foreach ( $cid as $i) {
            if ($model->delStanok($ids[$i])) JFactory::getApplication()->enqueueMessage('Сохранено', 'message');
            else  JError::raiseWarning(403, $model->_errors[0]);
            $i++;
        }
        $this->addStanok();

    }
    //************************************************ Плотности *****************************************//
    function addPlotnost(){
        $view =& $this->getView( 'plotnost' );
        $model	=& $this->getModel( 'plotnost' );
        $view->setModel( $model, true );
        $view->display();
    }

    function savePlotnost(){
        $model = $this->getModel('plotnost');
        $cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
        $ids     =  JRequest::getVar('ids', array(), 'post', 'array' );

        $name	= JRequest::getVar( 'name', array(), 'post', 'array' );
        $set	= JRequest::getVar( 'set', array(), 'post', 'array' );

        JArrayHelper::toInteger($ids);
        JArrayHelper::toInteger($cid);

        foreach ( $cid as $i) {
            $revSet=0;
            foreach ($set as $s){
                if($s == $ids[$i]) $revSet=1;
            }

            $data= array(name => $name[$i],set=>$revSet);


            if ($model->saveData($ids[$i], $data)) JFactory::getApplication()->enqueueMessage('Сохранено', 'message');
            else
                JError::raiseWarning(403, $model->_errors[0]);

        }
        $this->addPlotnost();
    }

    function removePlotnost (){
        $model = $this->getModel('plotnost');
        $cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
        $ids     =  JRequest::getVar('ids', array(), 'post', 'array' );
        JArrayHelper::toInteger($ids);
        JArrayHelper::toInteger($cid);

        foreach ( $cid as $i) {
            if ($model->delData($ids[$i])) JFactory::getApplication()->enqueueMessage('Сохранено', 'message');
            else  JError::raiseWarning(403, $model->_errors[0]);
            $i++;
        }
        $this->addPlotnost();

    }
    //************************************************ Цвета *****************************************//
    function addColor(){
        $view =& $this->getView( 'color' );
        $model	=& $this->getModel( 'color' );
        $view->setModel( $model, true );
        $view->display();
    }

    function saveColor(){
        $model = $this->getModel('color');
        $cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
        $ids     =  JRequest::getVar('ids', array(), 'post', 'array' );

        $name	= JRequest::getVar( 'name', array(), 'post', 'array' );
        $color	= JRequest::getVar( 'color', array(), 'post', 'array' );
        $set	= JRequest::getVar( 'set', array(), 'post', 'array' );

        JArrayHelper::toInteger($ids);
        JArrayHelper::toInteger($cid);

        foreach ( $cid as $i) {
            $revSet=0;
            foreach ($set as $s){
                if($s == $ids[$i]) $revSet=1;
            }

            $data= array(name => $name[$i],color=>$color[$i],set=>$revSet);

            if ($model->saveData($ids[$i], $data)) JFactory::getApplication()->enqueueMessage('Сохранено', 'message');
            else
                JError::raiseWarning(403, $model->_errors[0]);

        }
        $this->addColor();
    }

    function removeColor (){
        $model = $this->getModel('color');
        $cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
        $ids     =  JRequest::getVar('ids', array(), 'post', 'array' );
        JArrayHelper::toInteger($ids);
        JArrayHelper::toInteger($cid);

        foreach ( $cid as $i) {
            if ($model->delData($ids[$i])) JFactory::getApplication()->enqueueMessage('Сохранено', 'message');
            else  JError::raiseWarning(403, $model->_errors[0]);

        }
        $this->addColor();
    }
    //************************************************ Материалы *****************************************//
    function addMaterial(){
        $view =& $this->getView( 'material' );
        $model	=& $this->getModel( 'material' );
        $view->setModel( $model, true );
        $view->display();
    }

    function saveMaterial()
    {
        $model = $this->getModel('material');
        $cid = JRequest::getVar('cid', array(), 'post', 'array');
        $ids = JRequest::getVar('ids', array(), 'post', 'array');

        $name = JRequest::getVar('name', array(), 'post', 'array');
        $set = JRequest::getVar('set', array(), 'post', 'array');
        $plotnost = JRequest::getVar('plotnost', array(), 'post', 'array');
        $texture = JRequest::getVar('texture', array(), 'post', 'array');
        $color = JRequest::getVar('color', array(), 'post', 'array');

        JArrayHelper::toInteger($ids);
        JArrayHelper::toInteger($cid);

        foreach ($cid as $i) {
            $revSet = 0;
            foreach ($set as $s) {
                if ($s == $ids[$i]) $revSet = 1;
            }
            $revPlotnost = 0;
            foreach ($plotnost as $p) {
                if ($p == $ids[$i]) $revPlotnost = 1;
            }
            $revTexture = 0;
            foreach ($texture as $t) {
                if ($t == $ids[$i]) $revTexture = 1;
            }
            $revColor = 0;
            foreach ($color as $c) {
                if ($c == $ids[$i]) $revColor = 1;
            }

            $data = array(name => $name[$i], set => $revSet, plotnost=>$revPlotnost, texture=>$revTexture, color=>$revColor);


            if ($model->saveData($ids[$i], $data)) JFactory::getApplication()->enqueueMessage('Сохранено', 'message');
            else
                JError::raiseWarning(403, $model->_errors[0]);

        }
        $this->addMaterial();
    }

    function removeMaterial (){
        $model = $this->getModel('material');
        $cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
        $ids     =  JRequest::getVar('ids', array(), 'post', 'array' );
        JArrayHelper::toInteger($ids);
        JArrayHelper::toInteger($cid);

        foreach ( $cid as $i) {
            if ($model->delData($ids[$i])) JFactory::getApplication()->enqueueMessage('Сохранено', 'message');
            else  JError::raiseWarning(403, $model->_errors[0]);
        }
        $this->addMaterial();
    }
    //************************************************ Цены *****************************************//
    function addСasting(){
        $view   =& $this->getView( 'casting' );
        $model	=& $this->getModel( 'casting' );
        $view->setModel( $model, true );
        $view->display();
    }

    function saveСasting(){
        $model = $this->getModel('casting');
        $cid	    = JRequest::getVar( 'cid', array(), 'post', 'array' );
        $ids        = JRequest::getVar('ids', array(), 'post', 'array' );

        $set	    = JRequest::getVar( 'set', array(), 'post', 'array' );
        $stanok	    = JRequest::getVar( 'stanok', array(), 'post', 'array' );
        $material	= JRequest::getVar( 'material', array(), 'post', 'array' );
        $plotnost	= JRequest::getVar( 'plotnost', array(), 'post', 'array' );
        $color	    = JRequest::getVar( 'color', array(), 'post', 'array' );
        $cast	    = JRequest::getVar( 'cast', array(), 'post', 'array' );
        $set_user	= $GLOBALS['_SESSION']['__default']['user']->id;

        JArrayHelper::toInteger($ids);
        JArrayHelper::toInteger($cid);

        foreach ( $cid as $i) {
            $revSet=0;
            foreach ($set as $s){
                if($s == $ids[$i]) $revSet=1;
            }

            $data= array(set=>$revSet,stanok=>$stanok[$i],material=>$material[$i],plotnost=>$plotnost[$i],cast=>$cast[$i],color => $color[$i],set_user=>$set_user);

            if ($model->saveData($ids[$i], $data)) JFactory::getApplication()->enqueueMessage('Сохранено', 'message');
            else
                JError::raiseWarning(403, $model->_errors[0]);

        }
        $this->addСasting();
    }

    function removeСasting (){
        $model  = $this->getModel('casting');
        $cid	= JRequest::getVar('cid', array(), 'post', 'array' );
        $ids    = JRequest::getVar('ids', array(), 'post', 'array' );
        JArrayHelper::toInteger($ids);
        JArrayHelper::toInteger($cid);

        foreach ( $cid as $i) {
            if ($model->delData($ids[$i])) JFactory::getApplication()->enqueueMessage('Сохранено', 'message');
            else  JError::raiseWarning(403, $model->_errors[0]);
            $i++;
        }
        $this->addСasting();
    }
}

