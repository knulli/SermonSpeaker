<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Serie controller class.
 *
 * @package		SermonSpeaker.Administrator
 */
class SermonspeakerControllerSermon extends JControllerForm
{
	/**
	 * Method override to check if you can add a new record.
	 * Quite useless now, but may change if we add ACLs to SermonSpeaker
	 *
	 * @param	array	$data	An array of input data.
	 * @return	boolean
	 */
	protected function allowAdd($data = array())
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$categoryId	= JArrayHelper::getValue($data, 'catid', JRequest::getInt('filter_category_id'), 'int');
		$allow		= null;

		if ($categoryId) {
			// If the category has been passed in the data or URL check it.
			$allow	= $user->authorise('core.create', 'com_sermonspeaker.category.'.$categoryId);
		}

		if ($allow === null) {
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd();
		} else {
			return $allow;
		}
	}

	/**
	 * Method to check if you can add a new record.
	 * Quite useless now, but may change if we add ACLs to SermonSpeaker
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$user		= JFactory::getUser();
		$userId		= $user->get('id');

		// Check general edit permission first.
		if ($user->authorise('core.edit', 'com_content.article.'.$recordId)) {
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', 'com_content.article.'.$recordId)) {
			// Now test the owner is the user.
			$ownerId	= (int) isset($data['created_by']) ? $data['created_by'] : 0;
			if (empty($ownerId) && $recordId) {
				// Need to do a lookup from the model.
				$record		= $this->getModel()->getItem($recordId);

				if (empty($record)) {
					return false;
				}

				$ownerId = $record->created_by;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId) {
				return true;
			}
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}

	public function reset()
	{
		$app	= JFactory::getApplication();
		$db		= JFactory::getDBO();
		$id 	= JRequest::getInt('id', 0);
		if (!$id){
			$app->redirect('index.php?option=com_sermonspeaker&view=sermons', JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			return;
		}
		$model 	= $this->getModel();
		$item 	= $model->getItem($id);
		$user	= JFactory::getUser();
		$canEdit	= $user->authorise('core.edit', 'com_sermonspeaker.category.'.$item->catid);
		$canEditOwn	= $user->authorise('core.edit.own', 'com_sermonspeaker.category.'.$item->catid) && $item->created_by == $user->id;
		if ($canEdit || $canEditOwn){
			$query	= "UPDATE #__sermon_sermons \n"
					. "SET hits='0' \n"
					. "WHERE id='".$id."'"
					;
			$db->setQuery($query);
			if (!$db->query()) {
				echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
			$app->redirect('index.php?option=com_sermonspeaker&view=sermons', JText::sprintf('COM_SERMONSPEAKER_RESET_OK', JText::_('COM_SERMONSPEAKER_SERMON'), $item->sermon_title));
			return;
		} else {
			$app->redirect('index.php?option=com_sermonspeaker&view=sermons', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			return;
		}
	}
	
	public function write_id3(){
		$app	= JFactory::getApplication();
		$id		= JRequest::getInt('id', 0);
		if (!$id){
			$app->redirect('index.php?option=com_sermonspeaker&view=sermons', JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			return;
		}
		$db =& JFactory::getDBO();
		$query	= "SELECT audiofile, videofile, sermons.created_by, sermons.catid, sermon_title, name, series_title, YEAR(sermon_date) AS date, notes, sermon_number \n"
				. "FROM #__sermon_sermons AS sermons \n"
				. "LEFT JOIN #__sermon_speakers AS speakers ON speaker_id = speakers.id \n"
				. "LEFT JOIN #__sermon_series AS series ON series_id = series.id \n"
				. "WHERE sermons.id='".$id."'"
				;
		$db->setQuery($query);
		$item	= $db->loadObject();
		$user	= JFactory::getUser();
		$canEdit	= $user->authorise('core.edit', 'com_sermonspeaker.category.'.$item->catid);
		$canEditOwn	= $user->authorise('core.edit.own', 'com_sermonspeaker.category.'.$item->catid) && $item->created_by == $user->id;
		if ($canEdit || $canEditOwn){
			$files[]	= $item->audiofile;
			$files[]	= $item->videofile;
			require_once(JPATH_COMPONENT_SITE.DS.'id3'.DS.'getid3'.DS.'getid3.php');
			$getID3		= new getID3;
			$getID3->setOption(array('encoding'=>'UTF-8'));
			require_once(JPATH_COMPONENT_SITE.DS.'id3'.DS.'getid3'.DS.'write.php');
			$writer		= new getid3_writetags;
			$writer->tagformats		= array('id3v2.3');
			$writer->overwrite_tags	= true;
			$writer->tag_encoding	= 'UTF-8';
			$TagData = array(
				'title'   => array($item->sermon_title),
				'artist'  => array($item->name),
				'album'   => array($item->series_title),
				'year'    => array($item->date),
				'comment' => array($item->notes),
				'track'   => array($item->sermon_number),
			);
			$writer->tag_data = $TagData;
			foreach ($files as $file){
				if (!$file){
					continue;
				}
				$path		= JPATH_SITE.str_replace('/', DS, $file);
				$path		= str_replace(DS.DS, DS, $path);
				if(!is_writable($path)){
					continue;
				}
				$writer->filename	= $path;
				if ($writer->WriteTags()) {
					$app->enqueueMessage('Successfully wrote tags to "'.$file.'"');
					if (!empty($writer->warnings)) {
						JError::raiseNotice(100, 'There were some warnings:<br>'.implode(', ', $writer->warnings));
					}
				} else {
					JError::raiseWarning(100, 'Failed to write tags to "'.$file.'"! '.implode(', ', $writer->errors));
				}
			}
			$app->redirect('index.php?option=com_sermonspeaker&view=sermon&layout=edit&id='.$id);
			return;
		} else {
			$app->redirect('index.php?option=com_sermonspeaker&view=sermons', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			return;
		}
	}
}