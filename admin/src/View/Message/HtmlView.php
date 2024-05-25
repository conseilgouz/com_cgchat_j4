<?php
/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : Kide ShoutBox
*/

namespace ConseilGouz\Component\CGChat\Administrator\View\Message;

// No direct access
\defined('_JEXEC') or die;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
    protected $form;
    protected $pagination;
    protected $state;
    protected $item;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {

        $model       = $this->getModel();
        $this->form		= $this->get('Form');
        $this->item		= $this->get('Item');
        $this->formControl = $this->form ? $this->form->getFormControl() : null;

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar()
    {
        $state = $this->get('State');
        $canDo = ContentHelper::getActions('com_cgchat');

        $user		= Factory::getApplication()->getIdentity();
        $userId		= $user->id;
        if (!isset($this->item->id)) {
            $this->item->id = 0;
        }
        $isNew		= ($this->item->id == 0);

        ToolBarHelper::title($isNew ? Text::_('CG_ISO_ITEM_NEW') : Text::_('CG_ISO_ITEM_EDIT'), '#xs#.png');

        // If not checked out, can save the item.
        if ($canDo->get('core.edit')) {
            ToolBarHelper::apply('message.apply');
            ToolBarHelper::save('message.save');
        }
        ToolBarHelper::cancel('message.cancel', 'JTOOLBAR_CLOSE');
        ToolbarHelper::inlinehelp();
    }

}
