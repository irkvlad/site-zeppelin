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


class PolnocvetsModelFilial extends JModel
{
    var $_data;
    var $_id;

    function saveFilial($id)
    {
        $table = $this->getTable('filials', '');

        // привязываем поля формы к таблице
        if ($id > 0 ) $table->load($id);
        $table->webpage = JRequest::getVar('webpage');
        $table->company = JRequest::getVar('company');
        $table->filial = JRequest::getVar('filial');
        $table->adress = JRequest::getVar('adress');
        if ($id <= 0 ) {
            $query = ' SELECT * FROM `jos_zepp_filials` WHERE `filial` LIKE \'%База%\' ';;
            $rows = $this->_getList($query);
            if (count($rows) > 0) {
                $this->setError("Такой филиал существует");
                return false;
            }
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
            $this->_data = $this->_getList( $query );
        }

        return $this->_data;
    }

    function _buildQuery()
    {
        $cid	= JRequest::getVar( 'cid');
        JArrayHelper::toInteger($cid);

        $query = ' SELECT * '
            . ' FROM #__zepp_filials '
            . 'WHERE id = ' . $cid[0];
        ;

        return $query;
    }

    function getCompanyList($company_id)
    {

        $db     =& JFactory::getDBO(); // Формируем запрос (OR c.catid=4)

        $query = " SELECT "
            . " id AS value, "
            . " name AS text  "
            ." FROM "
            . " jos_zepp_company "
        ;

        $db->setQuery($query);
        $categorylist = $db->loadObjectList();
        // Создаём первый элемент выпадающего списка (<option value="0">Выберите категорию</option>)
        //$categories[] = JHTML::_('select.option',  '0', "Выберите менеджера", 'value', 'text' );
        // Добавляем массив данных из базы данных
        $categories = $categorylist; //array_merge( $categories, $categorylist);

        //$categories[] = JHTML::_('select.option',  '114', "Без менеджера", 'value', 'text' );
        // Получаем выпадающий список
        $company_list = JHTML::_(
            'select.genericlist' /* тип элемента формы */,
            $categories /* массив, каждый элемент которого содержит value и текст */,
            'company' /* id и name select`a формы */,
            'size="1"' /* другие атрибуты элемента select class="inputbox" */,
            'value' /* название поля в массиве объектов содержащего ключ */,
            'text' /* название поля в массиве объектов содержащего значение */,
            $company_id /* value элемента, который должен быть выбран (selected) по умолчанию */,
            'company' /* id select'a формы */,
            true /* пропускать ли элементы полей text через JText::_(), default = false */
        );

        return $company_list;
    }
}