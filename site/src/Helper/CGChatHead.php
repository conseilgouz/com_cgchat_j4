<?php
/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Package			: CG Chat
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : Kide ShoutBox
*/

namespace ConseilGouz\Component\CGChat\Site\Helper;

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseInterface;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatUser;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatHelper;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatLinks;

class CGChatHead
{
    public static function add_tags()
    {

        $base_html = URI::base(true).'/components/com_cgchat/';
        $kuser = CGChatUser::getInstance();
        $tpl = CGChatTemplate::getInstance();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $params = ComponentHelper::getParams('com_cgchat');
        $session = Factory::getApplication()->getSession();
        $order = $params->get('order', 'bottom');
        $doc = Factory::getApplication()->getDocument();
        $comfield	= 'media/com_cgchat/';
        $app = Factory::getApplication();
        $com_id = $app->input->getInt('Itemid');
        /** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        if ((bool)Factory::getConfig()->get('debug')) { // debug mode 
            Factory::getApplication()->getDocument()->addScript(''.URI::base(true).'/media/com_cgchat/js/base.js'); 
        } else { 
            $wa->registerAndUseScript('cgchat', $comfield.'js/base.min.js');
        }
        $tpl->include_html("js", "cgchat");

        $db->setQuery("SELECT id FROM #__cgchat ORDER BY id DESC LIMIT 1");
        $id = $db->loadResult();
        $db->setQuery("SELECT id FROM #__cgchat_private ORDER BY id DESC LIMIT 1");
        $last_private = $db->loadResult();

        $refresh_time_session = intval($params->get("refresh_time_session", 30));
        if ($refresh_time_session < 5) {
            $refresh_time_session = 5;
        }
        $refresh_time_session *= 1000;

        $msg = ['espera_por_favor' => addslashes(Text::_("COM_CGCHAT_ESPERA_POR_FAVOR")),
		'mensaje_borra' => addslashes(Text::_("COM_CGCHAT_MESSAGE_REMOVE")),
		'retardo_frase' => addslashes(Text::_("COM_CGCHAT_RETARDO_FRASE")),
		'privados_user_cerrado' => addslashes(Text::_("COM_CGCHAT_PRIVATES_USER_CERRADO")),
		'privados_nuevos' => addslashes(str_replace("%url", Route::_("index.php?option=com_cgchat"), Text::_("COM_CGCHAT_PRIVATES_NUEVOS"))),
		'privados_need_login' => addslashes(Text::_('COM_CGCHAT_PRIVATES_NEED_LOGIN')),
        'lang' => [addslashes(Text::_("COM_CGCHAT_MONTH")),addslashes(Text::_("COM_CGCHAT_MONTHS")),addslashes(Text::_("COM_CGCHAT_DAY")),addslashes(Text::_("COM_CGCHAT_DAYS")),addslashes(Text::_("COM_CGCHAT_HOUR")),addslashes(Text::_("COM_CGCHAT_HOURS")),addslashes(Text::_("COM_CGCHAT_MINUTE")),addslashes(Text::_("COM_CGCHAT_MINUTES")),addslashes(Text::_("COM_CGCHAT_SECOND")),addslashes(Text::_("COM_CGCHAT_SECONDS"))]
        ];
        $flag = ($params->get('countryinfo',0) > 0) ? $params->get('flag', 0) : 0;

        $is_component = DEFINED("CGCHAT_LOADED") ? 1 : 0;
        $doc = Factory::getApplication()->getDocument();
        $doc->addScriptOptions('cgchat',array(
                'img_starting' => [$tpl->include_html("buttons", "starting_0.gif"),$tpl->include_html("buttons", "starting_1.gif"),$tpl->include_html("buttons", "starting_2.gif")],
                'sound_on' => $tpl->include_html("buttons", "sound_on.png"),
                'sound_off' => $tpl->include_html("buttons", "sound_off.png"),
                'sound_src' => $tpl->include_html("sound", "msg.mp3"),
                'img_blank' => $tpl->include_html("otras", "blank.png"),
                'ajax_url'  => URI::base(true).'/index.php?option=com_cgchat&no_html=1&tmpl=component',
                'url'       => CGChatLinks::getUserLink($kuser->id),
                'popup_url' => Route::_("index.php?option=com_cgchat&tmpl=none"),
                'order'     => $order,
                'formato_hora'  => $params->get("formato_hora", "G:i--"),
                'formato_fecha' => $params->get("formato_fecha", "j-n G:i:s"),
                'template'  => $kuser->template,
                'gmt'       => $kuser->gmt,
                'token'     => $kuser->token,
                'session'   => $kuser->session,
                'row'       => $kuser->row,
                'rows'      => CGChatHelper::getRows(),
                'rowtitles' => CGChatHelper::getRowTitles(),
                'can_read'  => $kuser->can_read ? true : false,
                'can_write' => $kuser->can_write ? true : false,
                'show_avatar'   => $params->get("show_avatar", 0) ? true : false,
                'avatar_maxheight'  => $params->get('avatar_maxheight', '30px'),
                'refresh_time_session'  => $refresh_time_session,
                'boton_enviar'  => $params->get('button_send', 0) ? true : false,
                'refresh_time'  => $params->get('refresh_time', 6)*1000,
                'refresh_time_privates' => $params->get('refresh_time_privates', 6)*1000,
                'n'         => $id, 'p'     => (int)$last_private,
                'private'   => (int)$kuser->private,
                'name'      => $kuser->name,'userid'    => $kuser->id,
                'sound'     => $kuser->sound, 'color'   => $kuser->color,
                'retardo'   => (int)$kuser->retardo,
                'last_time' => CGChatHelper::getLastTime($kuser->private),
                'msg'       => $msg,
                'smilies'   => CGChatHelper::smilies_js(),
                'session_gmt'   => $session->get('gmt', null, 'cgchat') === null,
                'session_retardo'   => $session->get('retardo', null, 'cgchat') === null,
                'show_hour'     => $is_component,
                'show_sessions' => $is_component,
                'autostart'     => $is_component,
                'flag'          => $flag
                )
        ); // end of addScriptOptions
    }

}
