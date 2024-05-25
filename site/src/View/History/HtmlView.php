<?php
/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : Kide ShoutBox
*/

namespace ConseilGouz\Component\CGChat\Site\View\History;

defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Component\ComponentHelper;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatUser;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatTemplate;

class HtmlView extends BaseHtmlView
{
    public function display($tmpl = null)
    {
        $kuser = CGChatUser::getInstance();
        $model = $this->getModel();
        $params = ComponentHelper::getParams('com_cgchat');
        $tpl = CGChatTemplate::getInstance();

        $msgs = $model->getMsgs();
        $pags = $model->getPags();
        $fecha = $params->get("formato_fecha", "j-n G:i:s");

        $tpl->assignRef('user', $kuser);
        $tpl->assignRef('msgs', $msgs);
        $tpl->assignRef('pags', $pags);
        $tpl->assignRef('fecha', $fecha);

        $tpl->display();
    }
    /**
     * Prepares the document
     */
    protected function _prepareDocument()
    {
        $app              = Factory::getApplication();
        $menu             = $app->getMenu()->getActive();
        $pathway          = $app->getPathway();
        $title            = '';

        // Highest priority for "Browser Page Title".
        if ($menu) {
            $title = $menu->getParams()->get('page_title', '');
        }

        $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        $title = $title ?: $this->params->get('page_title', $menu->title);

        $this->setDocumentTitle($title);
        $pathway->addItem($title);


    }

}
