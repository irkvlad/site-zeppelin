<?php
/**
 * Polnocvet for RingClient Component
 * 
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link 
 * @license		irkvlad
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * Polnocvet Model
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class PolnocvetsModelPolnocvet extends JModel
{
    var $_id = null;
    var $_data = null;
    var $_filial = null;

	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}

	/**
	 * Method to set the Polnocvet identifier
	 *
	 * @access	public
	 * @param	int Polnocvet identifier
	 * @return	void
	 */
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}

	/**
	 * Method to get a hello
	 * @return object with data
	 */
	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = '';// SELECT * FROM #__polnocvet '.
					//'  WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = 0;
			$this->_data->greeting = null;
		}
		return $this->_data;
	}

	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function store()
	{	
		$row =& $this->getTable();

		$data = JRequest::get( 'post' );

		// Bind the form fields to the hello table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Make sure the hello record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $row->getErrorMsg() );
			return false;
		}

		return true;
	}

	/**
	 * Method to delete record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$row =& $this->getTable();

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
			}
		}
		return true;
	}

    function &getFilial()
    {
        // Load the data
        if (empty( $this->_filial )) {
            $query = ' SELECT * FROM #__zepp_filials ';
                    //'  WHERE id = '.$this->_id;
            $this->_db->setQuery( $query );
            $this->_filial = $this->_db->loadObject();
        }
        if (!$this->_filial) {
            $this->_filial = new stdClass();
            $this->_filial->id = 0;
            //$this->_data->greeting = null;
        }
        return $this->_filial;
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