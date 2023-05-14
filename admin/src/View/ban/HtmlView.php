<?php
/**
* CG Chat Component  - Joomla 4.x Component 
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2023 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From Kide ShoutBox
*/
namespace ConseilGouz\Component\CGChat\Administrator\View\Ban;

// No direct access
\defined('_JEXEC') or die;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView {

	protected $state;
	protected $item;
	protected $form;
	public function display($tpl = null) {
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new GenericDataException(implode("\n", $errors), 500);
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}
	protected function addToolbar()	{
		$user		= Factory::getUser();
		$isNew		= ($this->item->id == 0);

		ToolBarHelper::title(Text::_('COM_CGCHAT_MANAGER_BAN'));

		ToolBarHelper::apply('ban.apply', 'JTOOLBAR_APPLY');
		ToolBarHelper::save('ban.save', 'JTOOLBAR_SAVE');
		if (empty($this->item->id)) {
			ToolBarHelper::cancel('ban.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			ToolBarHelper::cancel('ban.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
