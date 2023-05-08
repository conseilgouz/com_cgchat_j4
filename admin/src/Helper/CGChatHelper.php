<?php
/**
* CG Chat Component  - Joomla 4.x Component 
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2023 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From              : Kide ShoutBox
*/
namespace ConseilGouz\Component\CGChat\Administrator\Helper;

defined('_JEXEC') or die;

class CGChatHelper {
	public static function addSubmenu($vName = 'messages')
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_CGCHAT_MANAGER_MESSAGES'),
			'index.php?option=com_cgchat&view=messages',
			$vName == 'messages'
		);
		JSubMenuHelper::addEntry(
			Text::_('COM_CGCHAT_MANAGER_ICONS'),
			'index.php?option=com_cgchat&view=icons',
			$vName == 'icons'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_CGCHAT_MANAGER_BANS'),
			'index.php?option=com_cgchat&view=bans',
			$vName == 'bans'
		);
	}
}
