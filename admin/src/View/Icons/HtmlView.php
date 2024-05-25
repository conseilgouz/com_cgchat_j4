<?php
/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : Kide ShoutBox
*/

namespace ConseilGouz\Component\CGChat\Administrator\View\Icons;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $pagination;
    protected $state;

    public function display($tpl = null)
    {
        $this->state		= $this->get('State');
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
            return false;
        }

        $this->addToolbar();
        parent::display($tpl);
    }
    protected function addToolbar()
    {
        $state	= $this->get('State');

        ToolbarHelper::title(Text::_('COM_CGCHAT_MANAGER_ICONOS'));
        ToolbarHelper::addNew('icon.add', 'JTOOLBAR_NEW');
        ToolbarHelper::editList('icon.edit', 'JTOOLBAR_EDIT');
        ToolbarHelper::deleteList('', 'icons.delete', 'JTOOLBAR_DELETE');
        $user = Factory::getApplication()->getIdentity();
        if ($user->authorise('core.admin', 'com_cgchat')) {
            ToolbarHelper::preferences('com_cgchat');
        }
    }
}
