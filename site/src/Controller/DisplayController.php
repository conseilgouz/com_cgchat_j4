<?php
/**
* CG Chat Component  - Joomla 4.x Component 
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2023 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From Kide ShoutBox
*/
namespace ConseilGouz\Component\CGChat\Site\Controller;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatUser;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatHelper;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatLinks;

class DisplayController extends BaseController {
    public function display($cachable = false, $urlparams = false) {
        Factory::getApplication()->input->set('view', 'cgchat');
        Factory::getApplication()->input->set('layout', 'default');
		this->default_view = "cgchat";
        parent::display($cachable, $urlparams);
        return $this;
	}
/*	public function add() {
	    if (!\JSession::checkToken('get'))
	    {
	        echo new \JResponseJson(null, Text::_('JINVALID_TOKEN'), true);
	    }
	    else
	    {
	        parent::display();
	    }
	    
	}
	*/
}
