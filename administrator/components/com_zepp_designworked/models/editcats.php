<?php


// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );


class designworkedsModelEditcats  extends JModel
{
	/**
	 * Hellos data array
	 *
	 * @var array
	 */
	var $_data;


	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	function _buildQuery()
	{
		$cid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$query = ' SELECT * '
			. ' FROM #__zepp_designworked_cat WHERE id='
			.$cid[0].' '
		;

		return $query;
	}

	/**
	 * Retrieves the hello data
	 * @return array Array of objects containing the data from the database
	 */
	function getData()
	{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList( $query );

		return $this->_data;
	}

	function store($post)
	{
		$row =& $this->getTable('zepp_designworked_cat');

		// Присваеваем значения
		if (!$row->bind($post)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		/*// Имеется ид
        if ($row->id) {
                $where = 'id = ' . (int) $row->id ;
                //$row->ordering = $row->getNextOrder( $where );
				//$this->setError('$row->id='.$row->id.' | $where='.$where);
            }*/


        // Проверяем на корректность
            if (!$row->check()) {
                $this->setError($this->_db->getErrorMsg());
                return false;
            }

		// Сохраняем в базе
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $row->id.' | '.$where;
	}

	function delete($cid = array())
	{
		if (count( $cid ))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );
			$query = 'DELETE FROM #__zepp_designworked_cat'
				. ' WHERE id IN ( '.$cids.' )';
			$this->_db->setQuery( $query );
			if(!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}
}