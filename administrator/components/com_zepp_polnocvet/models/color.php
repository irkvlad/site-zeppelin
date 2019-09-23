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


class PolnocvetsModelColor extends JModel
{
    var $_data;
    var $_id;

    function delData($id){
        $table = $this->getTable('color', '');
        if (!$table->delete( $id )) {
            $this->setError( $table->getErrorMsg() );
            return false;
        }
        return true;
    }

    function saveData($id,$save)
    {
        $table = $this->getTable('color','');

        if( trim($save['name'],$character_mask=" \t\n\r\0\x0B") == '' ){
            $this->setError("Название не может быть пустым");
            return false;
        }

        // привязываем поля формы к таблице
        if ($id <= 0 ) {
            $query = ' SELECT * FROM `jos_zepp_polnocvet_color` WHERE( `name` LIKE \''.$save['name'].'\') OR (`color` LIKE \''.$save['color'].'\') ';
            $rows = $this->_getList($query);
            if (count($rows) > 0) {
                $this->setError("Такие данные уже существуют");
                return false;
            }
        }
        if ($id > 0 ) $table->load($id);
        $table->name = $save['name'];
        $table->color = $save['color'];
        $table->set = $save['set'];

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
            $this->_db->setQuery( $query );
            $this->_data = $this->_db->loadObjectList();
        }

        return $this->_data;
    }

    function _buildQuery()
    {
        $query = ' SELECT * '
            . ' FROM #__zepp_polnocvet_color '
            ;

        return $query;
    }


}