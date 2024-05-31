<?php
/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : Kide ShoutBox
*/

namespace ConseilGouz\Component\CGChat\Site\Controller;

\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

class DisplayController extends BaseController
{
    public function display($cachable = false, $urlparams = false)
    {
        $view = Factory::getApplication()->input->getCmd('view', 'cgchat');
        if (($view != 'history') && ($view != 'cgchat')) {
            $view = 'cgchat';
        }
        $return = Factory::getApplication()->input->getCmd('return', '');
        Factory::getApplication()->input->set('view', $view);
        Factory::getApplication()->input->set('layout', 'default');
        Factory::getApplication()->input->set('return', $return);
        $this->default_view = "cgchat";
        parent::display($cachable, $urlparams);
        return $this;
    }
}
