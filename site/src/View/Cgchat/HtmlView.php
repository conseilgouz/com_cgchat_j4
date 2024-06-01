<?php
/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : Kide ShoutBox
*/

namespace ConseilGouz\Component\CGChat\Site\View\CGChat;

defined('_JEXEC') or die();
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\Filesystem\Folder;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatHelper;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatUser;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatHead;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatTemplate;

class HtmlView extends BaseHtmlView
{
    public function display($tmpl = null)
    {
        DEFINE('CGCHAT_LOADED', true);
        $kuser = CGChatUser::getInstance();
        $params = ComponentHelper::getParams('com_cgchat');
        PluginHelper::importPlugin('cgchat');
        $response = false;
        $contentEventArguments = [
            'context' => 'com_cgchat.cgchat',
            'params'  => $params,
            'response'    => &$response,
        ];
        Factory::getApplication()->triggerEvent('onCGChatStart', $contentEventArguments);
        if ($response) { // error found in plugins
            echo $response;
            return false;
        }
        CGChatHead::addScript("
		cgchat.show_hour = 1;
		cgchat.show_sessions = 1;
		cgchat.autostart = 1");
        $this->preparar();
        $tpl = CGChatTemplate::getInstance();
        $tpl->display();
    }
    public static function preparar()
    {
        CGChatHead::add_tags();

        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $kuser = CGChatUser::getInstance();
        $params = ComponentHelper::getParams('com_cgchat');
        $tpl = CGChatTemplate::getInstance();
        $tpl->include_html("css", "cgchat");

        $max_strlen = $params->get('msgs_max_strlen', 3000);
        $order = $params->get('order', 'bottom');
        $fecha = $params->get("formato_fecha", "j-n G:i:s");
        $formato_hora = $params->get("formato_hora", "G:i--");
        $copy = CGChatHelper::getCopy();

        $db->setQuery("SELECT * FROM #__cgchat ORDER BY id DESC LIMIT ".$params->get("msgs_limit", 36));
        $msgs = $db->loadObjectList();
        if ($order == 'bottom') {
            krsort($msgs);
        }
        $query = $db->getQuery(true);
        $query->select('*')->from('#__cgchat_private')
        ->where('('.$db->qn('fid').' = '.$db->q($kuser->id).' AND '.$db->qn('tid').' = '.$db->q($kuser->private).') OR ('.$db->qn('fid').' = '.$db->q($kuser->private).' AND '.$db->qn('tid').' = '.$db->q($kuser->id).')')
        ->order('id DESC')
        ->setLimit($params->get("msgs_limit", 36));
        $db->setQuery($query);
        $msgs_private = $db->loadObjectList();
        if ($order == 'bottom') {
            krsort($msgs_private);
        }
        // $msgs_private = [];
        $folders = Folder::folders(JPATH_ROOT.'/components/com_cgchat/templates');
        $s = array();
        foreach ($folders as $f) {
            $s[] = (object)array('text' => $f);
        }
        $templates = HTMLHelper::_('select.genericlist', $s, 'CGCHAT_template', 'class="inputbox"', 'text', 'text', $kuser->template);

        $tpl->assign('com', 'com');
        $tpl->assign('show_hour', 1);
        $tpl->assign('show_sessions', 1);
        $tpl->assign('autostart', 1);
        $tpl->assign('button_send', $params->get('button_send', 0));
        $tpl->assign('show_avatar', $params->get("show_avatar", 0));
        $tpl->assign('avatar_maxheight', $params->get("avatar_maxheight", '30px'));
        $tpl->assign('maxlength', $max_strlen > 0 ? 'maxlength="'.$max_strlen.'"' : '');
        $input = Factory::getApplication()->input;
        $tpl->assign('popup', $input->get('tmpl') == 'component');
        $tpl->assignRef('order', $order);
        $tpl->assignRef('copy', $copy);
        $tpl->assignRef('msgs', $msgs);
        $tpl->assignRef('msgs_private', $msgs_private);
        $tpl->assignRef('user', $kuser);
        $tpl->assignRef('fecha', $fecha);
        $tpl->assignRef('formato_hora', $formato_hora);
        $tpl->assignRef('templates', $templates);
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
