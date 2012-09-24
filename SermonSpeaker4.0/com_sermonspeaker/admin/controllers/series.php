<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Series list controller class.
 *
 * @package		SermonSpeaker.Administrator
 */
class SermonspeakerControllerSeries extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 */
	public function &getModel($name = 'Serie', $prefix = 'SermonspeakerModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	/**
	 * Method to set the default series. Copied from template styles
	 *
	 * @since	1.6
	 */
	public function setDefault()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$pks = JRequest::getVar('cid', array(), 'post', 'array');

		try
		{
			if (empty($pks)) {
				throw new Exception(JText::_('JERROR_NO_ITEMS_SELECTED'));
			}

			JArrayHelper::toInteger($pks);

			// Pop off the first element.
			$id = array_shift($pks);
			$model = $this->getModel();
			$model->setDefault($id);
			$this->setMessage(JText::_('COM_SERMONSPEAKER_SUCCESS_HOME_SET'));

		}
		catch (Exception $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		$this->setRedirect('index.php?option=com_sermonspeaker&view=series');
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return	void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}
}