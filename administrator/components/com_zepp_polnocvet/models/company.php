<?php
/**
 * Polnocvet Model for Polnocvet Component
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link
 * @license		irkvlad
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );


class PolnocvetsModelCompany extends JModel
{
    var $_data;
    var $_id;

    function delCompany($id){
        $table = $this->getTable('company', '');
        if (!$table->delete( $id )) {
            $this->setError( $table->getErrorMsg() );
            return false;
        }
        return true;
    }

    function saveCompany($id,$company)
    {
        $table = $this->getTable('company', '');

        // привязываем поля формы к таблице
        if ($id > 0 ) $table->load($id);
        $table->name = $company;
        if ($id <= 0 ) {
            $query = ' SELECT * FROM `jos_zepp_company` WHERE `name` LIKE \''.$company.'\' ';;
            $rows = $this->_getList($query);
            if (count($rows) > 0) {
                $this->setError("Такой филиал существует");
                return false;
            }
        }

        if( trim ( $company,  $character_mask = " \t\n\r\0\x0B") == ''){
            $this->setError("Название не может быть пустым");
            return false;
        }

        // проверяем данные
        if ($table->check()) {
            // сохраняем данные
            if (!$table->store()) {
                $this->setError($table->getError());
                return false;
            }
        } else {
            $this->setError($table->getError());
            return false;
        }

        return true;
    }

    function getData(){

        if (empty( $this->_data ))
        {
            $query = $this->_buildQuery();
           //$this->_data = $this->_getList( $query );
            $this->_db->setQuery( $query );
            $this->_data = $this->_db->loadObjectList();
        }

        return $this->_data;
    }

    function _buildQuery()
    {
        //$cid	= JRequest::getVar( 'cid');
       // JArrayHelper::toInteger($cid);

        $query = ' SELECT * '
            . ' FROM #__zepp_company '
            ;
        ;

        return $query;
    }


}