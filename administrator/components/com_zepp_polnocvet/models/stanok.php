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


class PolnocvetsModelStanok extends JModel
{
    var $_data;
    var $_id;

    function delStanok($id){
        $table = $this->getTable('stanok', '');
        if (!$table->delete( $id )) {
            $this->setError( $table->getErrorMsg() );
            return false;
        }
        return true;
    }

    function saveStanok($id,$stanok)
    {
        $table = $this->getTable('stanok','');

        if( trim($stanok['name'],$character_mask=" \t\n\r\0\x0B") == '' ){
            $this->setError("Название не может быть пустым");
            return false;
        }

        // привязываем поля формы к таблице
        if ($id <= 0 ) {
            $query = ' SELECT * FROM `jos_zepp_polnocvet_stanok` WHERE `name` LIKE \''.$stanok['name'].'\' ';;
            $rows = $this->_getList($query);
            if (count($rows) > 0) {
                $this->setError("Такой станок уже существует");
                return false;
            }
        }
        if ($id > 0 ) $table->load($id);
        $table->name = $stanok['name'];
        $table->mats = $stanok['mats'];
        $table->key = $stanok['key'];
        $table->ploshad = $stanok['ploshad'];
        $table->ispraven = $stanok['ispraven'];
        $table->set = $stanok['set'];

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
        $query = ' SELECT * '
            . ' FROM #__zepp_polnocvet_stanok '
            ;

        return $query;
    }


}