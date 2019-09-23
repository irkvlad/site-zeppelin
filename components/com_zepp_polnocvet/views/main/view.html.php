<?php
/**
 * PolnocvetView for Polnocvet Component
 * 
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link 
 * @license		irkvlad
 */

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Polnocvet Component
 *
 * @package		Joomla.Tutorials
 * @subpackage	Components
 */
class polnocvetViewMain extends JView
{
	function display($tpl = null)
	{
		//$polnocvet = $this->get( 'Polnocvet' );
		//$this->assignRef( 'polnocvet',	$polnocvet );

		$date   = JFactory::getDate();
		$session = JFactory::getSession();
		$startDate = $session->get('startDate', $date->toFormat ('01.%m.%Y'));
		$endDate = $session->get('endDate', $date->toFormat ('%d.%m.%Y'));
		$this->assignRef('startDate',$startDate);
		$this->assignRef('endDate',$endDate);

		$group_id = $this->get( 'Group_id');
		$this->assignRef( 'user',	$group_id );

		$managerList = $this->get('ManagerList');
		$this->assignRef('managerList',$managerList);

        $colorCompany = $this->get('CompanyColor');
        $this->assignRef('colorCompany', $colorCompany);

        $listCompany = $this->get('ListCompany');
        $this->assignRef('listCompany', $listCompany);


		$polnocvet =& $this->get('Data');
		/*$totalFeniks = polnocvetHTML::getTotalFeniks($polnocvet);// $this->get(TotalFeniks);
		$totalRoland = polnocvetHTML::getTotalRoland($polnocvet);
		$totalUF	 = polnocvetHTML::getTotalUF($polnocvet);
		$totalLum	 = polnocvetHTML::getTotalLum($polnocvet);

		$this->assignRef('totalFeniks', $totalFeniks);
		$this->assignRef('totalRoland', $totalRoland);
		$this->assignRef('totalUF', $totalUF);
		$this->assignRef('totalLum', $totalLum);*/


		$pagination =& $this->get('Pagination');

		$this->assignRef( 'polnocvet',	$polnocvet );
		$this->assignRef('pagination', $pagination);

		parent::display($tpl);
	}
}

