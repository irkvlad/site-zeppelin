<?php
/**
 * Polnocvet View for Polnocvet Component
 * 
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link 
 * @license		irkvlad
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

/**
 * Polnocvet View
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class  PolnocvetsViewFilial extends JView
{
	/**
	 * display method of Hello view
	 * @return void
	 **/
	function display($tpl = null)
	{
        $this->_layout = 'form';

		//get the hello
		$filial		=& $this->get('Data');
		$isNew		= ($filial->id < 1);
        //$companyList    = & $this->get(CompanyList);
        $model = $this->getModel();
        $companyList		=  $model->getCompanyList($filial[0]->company);

		$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
		JToolBarHelper::title(   "Филиал" );
		JToolBarHelper::save('saveFilial');
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}

		$this->assignRef('filial',		$filial);
        $this->assignRef('text',		$text);
        $this->assignRef('companyList',		$companyList);

		parent::display($tpl);
	}
}