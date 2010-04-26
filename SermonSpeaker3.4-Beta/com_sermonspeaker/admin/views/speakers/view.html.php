<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewSpeakers extends JView
{
	function display( $tpl = null )
	{
		global $mainframe, $option;

		$lists['order']		= $mainframe->getUserStateFromRequest("$option.speakers.filter_order",'filter_order','id','cmd' );
		$lists['order_Dir']	= $mainframe->getUserStateFromRequest("$option.speakers.filter_order_Dir",'filter_order_Dir','','word' );
		$filter_state		= $mainframe->getUserStateFromRequest("$option.speakers.filter_state",'filter_state','','word' );
		$search				= $mainframe->getUserStateFromRequest("$option.speakers.search",'search','','string' );
		$search				= JString::strtolower( $search );

		$pagination =& $this->get('Pagination');	// Paginationwerte aus Model lesen
		$items	=& $this->get('speakers');			// Daten aus Model lesen

		// state filter (Funktion aus Joomla)
		$lists['state']	= JHTML::_('grid.state',  $filter_state );

		// search filter
		$lists['search']= $search;

        // push data into the template
		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);
	}
}