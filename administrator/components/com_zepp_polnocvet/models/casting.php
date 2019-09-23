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


class PolnocvetsModelCasting extends JModel
{
    var $_data;
    var $_id;

    function delData($id){
        $table = $this->getTable('casting', '');
        if (!$table->delete( $id )) {
            $this->setError( $table->getErrorMsg() );
            return false;
        }
        return true;
    }

    function saveData($id,$save)
    {
        $table = $this->getTable('casting','');

        if( trim($save['cast'],$character_mask=" \t\n\r\0\x0B") == '' ){
            $this->setError("Нужно указать стоимость");
            return false;
        }
        if (!is_numeric($save['cast'])){
            $this->setError("Нужно указать стоимость");
            return false;
        }
        if($save['stanok'] == 0){
            $this->setError("Нужно указать вид работ");
            return false;
        }

        // привязываем поля формы к таблице
        if($save['plotnost'] == 0) $save['plotnost'] = 4;
        if($save['material'] == 0) $save['material'] = 17;
        if($save['color'] == 0) $save['color'] = 6;

        if ($id <= 0 ) {
            $query = ' SELECT * FROM `jos_zepp_polnocvet_casting` 
                            WHERE (`color` = '.$save['color'].' ) 
                                    AND (`plotnost` = '.$save['plotnost'].' )
                                    AND (`material` = '.$save['material'].' )
                                    AND (`stanok` = '.$save['stanok'].' ) '
            ;

            $rows = $this->_getList($query);
            if (count($rows) > 0) {
                $this->setError("Такие данные уже существуют");
                return false;
            }
        }

        if ($id > 0 ) $table->load($id);
        $table->cast = $save['cast'];
        $table->plotnost = $save['plotnost'];
        $table->material = $save['material'];
        $table->stanok = $save['stanok'];
        $table->color = $save['color'];
        $table->set_user = $save['set_user'];
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
            . ' FROM #__zepp_polnocvet_casting '
            . " ORDER BY `stanok` ASC , `material` ASC "
            ;

        return $query;
    }

    // Список материалов из таблицы материалов
    function getListMaterials($id=17,$i = -1 ) //
    {
        $db     =& JFactory::getDBO();
        $query = " SELECT id AS value , name AS text "
            ." FROM "
            . " jos_zepp_polnocvet_material "
            . " ORDER BY `name` ASC "
        ;

        $db->setQuery($query);
        $list = $db->loadObjectList();
        // Создаём первый элемент выпадающего списка (<option value="0">Выберите ...</option>)
        //$punkt[] = JHTML::_('select.option',  '0', "Выберите материал", 'value', 'text' );
        // Добавляем массив данных из базы данных
        //$array = array_merge( $punkt, $list);
        // Получаем выпадающий список
        $selectList = JHTML::_(
            'select.genericlist' /* тип элемента формы */,
            $list /* массив, каждый элемент которого содержит value и текст */,
            'material[]' /* id и name select`a формы */,
            'size="1"  onchange="adminForm.cb'.$i.'.checked=true"' /* другие атрибуты элемента select class="inputbox" */,
            'value' /* название поля в массиве объектов содержащего ключ */,
            'text' /* название поля в массиве объектов содержащего значение */,
            $id /* value элемента, который должен быть выбран (selected) по умолчанию */,
            'material' /* id select'a формы */,
            true /* пропускать ли элементы полей text через JText::_(), default = false */
        );

        return $selectList;
    }
    // Список станков из таблицы
    function getListStanok($id=0,$i = -1) //
    {
        $db     =& JFactory::getDBO();
        $query = " SELECT id AS value , name AS text "
            ." FROM "
            . " jos_zepp_polnocvet_stanok "
            . " ORDER BY `name` ASC "
        ;

        $db->setQuery($query);
        $list = $db->loadObjectList();
        // Создаём первый элемент выпадающего списка (<option value="0">Выберите ...</option>)
        $punkt[] = JHTML::_('select.option',  '0', "Выберите станок", 'value', 'text' );
        // Добавляем массив данных из базы данных
        $array = array_merge( $punkt, $list);
        // Получаем выпадающий список
        $selectList = JHTML::_(
            'select.genericlist' /* тип элемента формы */,
            $array /* массив, каждый элемент которого содержит value и текст */,
            'stanok[]' /* id и name select`a формы */,
            'size="1"   onchange="adminForm.cb'.$i.'.checked=true"' /* другие атрибуты элемента select class="inputbox" */,
            'value' /* название поля в массиве объектов содержащего ключ */,
            'text' /* название поля в массиве объектов содержащего значение */,
            $id /* value элемента, который должен быть выбран (selected) по умолчанию */,
            'stanok' /* id select'a формы */,
            true /* пропускать ли элементы полей text через JText::_(), default = false */
        );

        return $selectList;
    }
    // Список плотностей из таблицы
    function getListPlotnost($id=8,$i = -1) //
    {
        $db     =& JFactory::getDBO();
        $query = " SELECT id AS value , name AS text "
            ." FROM "
            . " jos_zepp_polnocvet_plotnost "
            . " ORDER BY `name` ASC "
        ;

        $db->setQuery($query);
        $list = $db->loadObjectList();
        // Создаём первый элемент выпадающего списка (<option value="0">Выберите ...</option>)
        //$punkt[] = JHTML::_('select.option',  '0', "Выберите плотность", 'value', 'text' );
        // Добавляем массив данных из базы данных
        //$array = array_merge( $punkt, $list);
        // Получаем выпадающий список
        $selectList = JHTML::_(
            'select.genericlist' /* тип элемента формы */,
            $list /* массив, каждый элемент которого содержит value и текст */,
            'plotnost[]' /* id и name select`a формы */,
            'size="1"   onchange="adminForm.cb'.$i.'.checked=true"' /* другие атрибуты элемента select class="inputbox" */,
            'value' /* название поля в массиве объектов содержащего ключ */,
            'text' /* название поля в массиве объектов содержащего значение */,
            $id /* value элемента, который должен быть выбран (selected) по умолчанию */,
            'plotnost' /* id select'a формы */,
            true /* пропускать ли элементы полей text через JText::_(), default = false */
        );

        return $selectList;
    }
    // Список цветов из таблицы
    function getListColor($id=6,$i = -1) //
    {
        $db =& JFactory::getDBO();
        $query = " SELECT id AS value , name AS text "
            . " FROM "
            . " jos_zepp_polnocvet_color "
            . " ORDER BY `name` DESC "
        ;

        $db->setQuery($query);
        $list = $db->loadObjectList();
        // Создаём первый элемент выпадающего списка (<option value="0">Выберите ...</option>)
        //$punkt[] = JHTML::_('select.option', '0', "Выберите цвет", 'value', 'text');
        // Добавляем массив данных из базы данных
        //$array = array_merge($punkt, $list);
        // Получаем выпадающий список
        $selectList = JHTML::_(
            'select.genericlist' /* тип элемента формы */,
            $list /* массив, каждый элемент которого содержит value и текст */,
            'color[]' /* id и name select`a формы */,
            'size="1"   onchange="adminForm.cb'.$i.'.checked=true"' /* другие атрибуты элемента select class="inputbox" */,
            'value' /* название поля в массиве объектов содержащего ключ */,
            'text' /* название поля в массиве объектов содержащего значение */,
            $id /* value элемента, который должен быть выбран (selected) по умолчанию */,
            'color' /* id select'a формы */,
            true /* пропускать ли элементы полей text через JText::_(), default = false */
        );

        return $selectList;
    }
}