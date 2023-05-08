<?php
/**
* CG Chat Component  - Joomla 4.x Component 
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2023 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From              : Kide ShoutBox
*/
namespace ConseilGouz\Component\CGChat\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Uri\Uri;

class BanModel extends AdminModel {

	protected $text_prefix = 'COM_CGCHAT';

	public function getForm($data = array(), $loadData = true)
	{
        // Get the form.
        $form = $this->loadForm('com_cgchat.ban', 'ban', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form))
        {
            return false;
        }

        return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_cgchat.edit.ban.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}
	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
		return parent::getItem($pk);
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param	object	A record object.
	 * @return	array	An array of conditions to add to add to ordering queries.
	 * @since	1.6
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		return $condition;
	}
}