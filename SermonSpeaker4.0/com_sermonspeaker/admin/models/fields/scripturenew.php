<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Scripture Field class for the SermonSpeaker
 *
 * @package		SermonSpeaker
 * @since		4.0
 */
class JFormFieldScripturenew extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Scripturenew';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$app = JFactory::getApplication();
		if ($app->isAdmin()){
			$url = 'index.php?option=com_sermonspeaker&view=scripture&layout=modal&tmpl=component';
		} else {
			$url = JRoute::_('index.php?view=scripture&layout=modal&tmpl=component');
		}

		$html 	= '<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'" class="inputbox" />';
		$html	.= '<a class="modal" href="'.$url.'"rel="{handler: \'iframe\', size: {x: 500, y: 200}}"><img src="'.JURI::root().'media/com_sermonspeaker/images/plus.png"></a>';

		return $html;
	}
}