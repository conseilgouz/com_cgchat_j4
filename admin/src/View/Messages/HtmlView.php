<?php
/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Package			: CG Chat
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : Kide ShoutBox
*/

namespace ConseilGouz\Component\CGChat\Administrator\View\Messages;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $pagination;
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        // Initialise variables.
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->state		= $this->get('State');

        // Check for errors.
        if (\count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar()
    {
        $canDo = ContentHelper::getActions('com_cgchat');
        $user = Factory::getApplication()->getIdentity();

        ToolbarHelper::title(Text::_('COM_CGCHAT_MESSAGES'), 'page.png');

        if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_cgchat', 'core.create'))) > 0) {
            ToolbarHelper::addNew('message.add');
        }

        if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
            ToolbarHelper::editList('message.edit');
        }

        if ($canDo->get('core.edit.state')) {
            ToolbarHelper::divider();
            ToolbarHelper::publish('messages.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('messages.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }
        if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete')) {
            ToolBarHelper::deleteList('', 'messages.delete', 'JTOOLBAR_EMPTY_TRASH');
        } elseif ($canDo->get('core.edit.state')) {
            ToolBarHelper::trash('messages.trash');
        }
        if ($canDo->get('core.admin')) {
            ToolbarHelper::divider();
            ToolbarHelper::inlinehelp();
            ToolbarHelper::preferences('com_cgchat');
        }
    }
}
