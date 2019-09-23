<?php


// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

//echo "class designworkedsModelDesignworkeds extends JModel<br>";

class designworkedsModelDesignworkeds extends JModel
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
	 *
	function _buildQuery()
	{
		$query = ' SELECT * '
			. ' FROM #__zepp_designworked '
		;

		return $query;
	}

	/**
	 * Retrieves the hello data
	 * @return array Array of objects containing the data from the database
	 *
	function getData()
	{
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data ))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList( $query );
		}
//echo 'start--models\ringclient.php<br>';
//print_R($this->_data,false);
//echo 'end--models\ringclient.php<br>';

		return $this->_data;
	}



	/**
	 * Возвращает количество работ по менеджерам в катигориях
	 * @return array
	 */
	function getData(){
		$db =& JFactory::getDBO();
		$data= array();

		$designerIds=irkvladHTML::getDesignerIds();
		$catIds = designworkedHTML::getCatIds();
		//$datasList['designerIds']= $designerIds;
		//$datasList['catIds']= $catIds;

		for ($i=0, $n=count( $catIds ); $i < $n; $i++) {
			$cat = $catIds[$i];
			for ($t = 0, $tn = count($designerIds); $t < $tn; $t++) {
				$user_id=$designerIds[$t]->user_id;

				$query = " SELECT "
					. " COUNT(*) "
					//. " name  "
					. " FROM "
					. " #__zepp_designworked "
					. " WHERE catid=".$cat->id
					. " AND userid=".$user_id
				;

				$db->setQuery($query);
				$d = $db->loadResult();
				$data[$i][$t]= $d;
				//$data['query']= $query;
			}
		}
		return $data;
	}
	
	function getDataS(){
		$db =& JFactory::getDBO();
		$data= array();

		$designerIds=irkvladHTML::getDesignerIds();
		$catIds = designworkedHTML::getCatIds();
		//$datasList['designerIds']= $designerIds;
		//$datasList['catIds']= $catIds;

		for ($i=0, $n=count( $catIds ); $i < $n; $i++) {
			$cat = $catIds[$i];
			for ($t = 0, $tn = count($designerIds); $t < $tn; $t++) {
				$user_id=$designerIds[$t]->user_id;

				$query = " SELECT "
					. " path "
					//. " COUNT(*) "
					//. " name  "
					. " FROM "
					. " #__zepp_designworked "
					. " WHERE catid=".$cat->id
					. " AND userid=".$user_id
				;

				$db->setQuery($query);
				$s = $db->loadObjectList();
				
				$d = 0;
				foreach($s as $count){
					$d = $d + substr_count ( $count->path, ";" );
				}
				
				$data[$i][$t]= $d;
				
				
			}
		}
		return $data;
	}

}