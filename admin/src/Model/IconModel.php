<?php
/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : Kide ShoutBox
*/

namespace ConseilGouz\Component\CGChat\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Uri\Uri;

class IconModel extends AdminModel
{
    protected $text_prefix = 'COM_CGCHAT';

    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_cgchat.icon', 'icon', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

        return $form;
    }


    public function getImages($value)
    {
        $return = '<select class="required" name="jform[img]" id="jform_img" aria-required="true" required="required" onchange="cgchat_show_img(this.value)">';
        $path = JPATH_ROOT."/media/com_cgchat/templates/default/images/icons";
        $files = Folder::files($path, "\.(png|gif|jpg)");
        $first = '';
        foreach ($files as $file) {
            if (!$first) {
                $first = $file;
            }
            $return .= '<option value="'.$file.'"'.($value == $file ? ' selected' : '').'>'.$file.'</option>';
        }
        $return .= '</select>';
        $return .= ' <img id="cgchat_image" src="'.URI::root().'media/com_cgchat/templates/default/images/icons/'.($value ? $value : $first).'" />';
        return $return;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    public function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_cgchat.edit.icon.data', array());

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
