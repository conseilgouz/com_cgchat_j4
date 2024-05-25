<?php
/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : Kide ShoutBox
*/

namespace ConseilGouz\Component\CGChat\Administrator\View\Icon;

// No direct access
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
    protected $state;
    protected $item;
    protected $form;
    public function display($tpl = null)
    {
        $this->state	= $this->get('State');
        $this->item		= $this->get('Item');
        $this->form		= $this->get('Form');
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
            return false;
        }
        $model	= $this->getModel();
        $icon	= $model->loadFormData();
        $isNew	= ($icon->id < 1);
        $images = $model->getImages($icon->img);
        $this->images = $images;

        $this->addToolbar();
        parent::display($tpl);
    }
    protected function addToolbar()
    {
        $user		= Factory::getApplication()->getIdentity();
        $isNew		= ($this->item->id == 0);
        ToolBarHelper::title(Text::_('COM_CGCHAT_MANAGER_ICONO'));
        ToolBarHelper::apply('icon.apply', 'JTOOLBAR_APPLY');
        ToolBarHelper::save('icon.save', 'JTOOLBAR_SAVE');
        if (empty($this->item->id)) {
            ToolBarHelper::cancel('icon.cancel', 'JTOOLBAR_CANCEL');
        } else {
            ToolBarHelper::cancel('icon.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}
