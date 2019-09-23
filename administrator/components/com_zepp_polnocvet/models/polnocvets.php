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

/**
 * Polnocvet
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class PolnocvetsModelPolnocvets extends JModel
{
	/**
	 * Polnocvet data array
	 *
	 * @var array
	 */
	 var $_filials;


	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	function _buildQueryFilials()
	{
		$query = ' SELECT f.*, c.name '
			. ' FROM #__zepp_filials as f, #__zepp_company as c'
            . ' WHERE f.company = c.id'
		;

		return $query;
	}

	/**
	 * Retrieves the Polnocvet data
	 * @return array Array of objects containing the data from the database
	 */
	function getFilials()
	{
		// Lets load the data if it doesn't already exist
		if (empty( $this->_Filials ))
		{
			$query = $this->_buildQueryFilials();
			$this->_Filials = $this->_getList( $query );
		}

		return $this->_Filials;
	}

	function Del($ids){


        JArrayHelper::toInteger($ids);

        if (!empty( $ids )) {
            $db = &$this->getDBO();

            // Delete associated module and template mappings
            $where = 'WHERE id = ' . implode( ' OR id = ', $ids );

            $query = 'DELETE FROM #__zepp_filials '
                . $where;
            $db->setQuery( $query );
            if (!$db->query()) {
                $this->setError( $db->getErrorMsg() );
                return false;
            }
        }

        return true;
    }

    function getCompanyList($company_id=1)
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