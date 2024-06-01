<?php
/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Version			: 1.0.0
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
            $wa->registerAndUseScript('cgchat', $comfield.'js/base.js');
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

        self::addScript('
	cgchat.img_starting = ["'.$tpl->include_html("buttons", "starting_0.gif").'", "'.$tpl->include_html("buttons", "starting_1.gif").'", "'.$tpl->include_html("buttons", "starting_2.gif").'"];
	cgchat.sound_on = "'.$tpl->include_html("buttons", "sound_on.png").'";
	cgchat.sound_off = "'.$tpl->include_html("buttons", "sound_off.png").'";
	cgchat.sound_src = "'.$tpl->include_html("sound", "msg.mp3").'";
	cgchat.img_blank = "'.$tpl->include_html("otras", "blank.png").'";
	cgchat.ajax_url = "'.URI::base(true).'/index.php?option=com_cgchat&no_html=1&tmpl=component'.'";
	cgchat.url = "'.CGChatLinks::getUserLink($kuser->id).'";
	cgchat.popup_url = "'.Route::_("index.php?option=com_cgchat").'";
	cgchat.order = "'.$order.'";
	cgchat.formato_hora = "'.$params->get("formato_hora", "G:i--").'";
	cgchat.formato_fecha = "'.$params->get("formato_fecha", "j-n G:i:s").'";
	
	cgchat.template = "'.$kuser->template.'";
	cgchat.gmt = "'.$kuser->gmt.'";
	cgchat.token = '.$kuser->token.';
	cgchat.session = "'.$kuser->session.'";
	cgchat.row = '.$kuser->row.';
	cgchat.rows = ["'.implode('","', CGChatHelper::getRows()).'"];
	cgchat.rowtitles = ["'.implode('","', CGChatHelper::getRowTitles()).'"];
	cgchat.can_read = '.($kuser->can_read ? 'true' : 'false').';
	cgchat.can_write = '.($kuser->can_write ? 'true' : 'false').';
	cgchat.show_avatar = '.($params->get("show_avatar", 0) ? 'true' : 'false').';
	cgchat.avatar_maxheight = "'.$params->get('avatar_maxheight', '30px').'";
	cgchat.refresh_time_session = '.$refresh_time_session.';
	cgchat.boton_enviar = '.($params->get('button_send', 0) ? 'true' : 'false').';
	cgchat.refresh_time = '.$params->get('refresh_time', 6).'*1000;
	cgchat.refresh_time_privates = '.$params->get('refresh_time_privates', 6).'*1000;
	
	cgchat.n = '.(int)$id.';
    cgchat.p = '.(int)$last_private.';
    cgchat.private = '.(int)$kuser->private.';
	cgchat.name = "'.$kuser->name.'";
	cgchat.userid = '.$kuser->id.';
	cgchat.sound = '.$kuser->sound.';
	cgchat.color = "'.$kuser->color.'";
	cgchat.retardo = '.(int)$kuser->retardo.';
	cgchat.last_time = '.CGChatHelper::getLastTime($kuser->private).';

	cgchat.msg = {
		espera_por_favor: \''.addslashes(Text::_("COM_CGCHAT_ESPERA_POR_FAVOR")).'\',
		mensaje_borra: \''.addslashes(Text::_("COM_CGCHAT_MESSAGE_REMOVE")).'\',
		retardo_frase: \''.addslashes(Text::_("COM_CGCHAT_RETARDO_FRASE")).'\',
		lang: [\''.addslashes(Text::_("COM_CGCHAT_MONTH")).'\', \''.addslashes(Text::_("COM_CGCHAT_MONTHS")).'\', \''.addslashes(Text::_("COM_CGCHAT_DAY")).'\', \''.addslashes(Text::_("COM_CGCHAT_DAYS")).'\', \''.addslashes(Text::_("COM_CGCHAT_HOUR")).'\', \''.addslashes(Text::_("COM_CGCHAT_HOURS")).'\', \''.addslashes(Text::_("COM_CGCHAT_MINUTE")).'\', \''.addslashes(Text::_("COM_CGCHAT_MINUTES")).'\', \''.addslashes(Text::_("COM_CGCHAT_SECOND")).'\', \''.addslashes(Text::_("COM_CGCHAT_SECONDS")).'\'],
		privados_user_cerrado: \''.addslashes(Text::_("COM_CGCHAT_PRIVATES_USER_CERRADO")).'\',
		privados_nuevos: \''.addslashes(str_replace("%url", Route::_("index.php?option=com_cgchat"), Text::_("COM_CGCHAT_PRIVATES_NUEVOS"))).'\',
		privados_need_login: \''.addslashes(Text::_('COM_CGCHAT_PRIVATES_NEED_LOGIN')).'\'
	};
	cgchat.smilies = [
		'.CGChatHelper::smilies_js().'
	];
	');

        $doc->addStyleDeclaration('
	'.($kuser->color ? '#CGCHAT_txt { color: #'.$kuser->color.'; }' : '').'
	#CGCHAT_users_td { vertical-align: '.$order.' }');

        if ($session->get('gmt', null, 'cgchat') === null) {
            self::addScript('
	var tiempo = new Date();
	cgchat.save_config("gmt", (tiempo.getTimezoneOffset()/60)*-1);');
        }

        if($session->get('retardo', null, 'cgchat') === null) {
            self::addScript('cgchat.ajax("retardo");');
        }
    }

    public static function addScript($str)
    {
        $doc = Factory::getApplication()->getDocument();
        $doc->addCustomTag("<script type=\"text/javascript\">\n/*<![CDATA[*/\n".$str."\n/*]]>*/\n</script>");
    }
}
