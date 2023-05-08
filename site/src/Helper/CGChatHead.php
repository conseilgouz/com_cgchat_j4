<?php
/**
* CG Chat Component  - Joomla 4.x Component 
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2023 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From Kide ShoutBox
*/
namespace ConseilGouz\Component\CGChat\Site\Helper;

defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatUser;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

class CGChatHead {

	static function add_tags() {

		$base_html = URI::base(true).'/components/com_cgchat/';
		$kuser = CGChatUser::getInstance();
		$tpl = CGChatTemplate::getInstance();
		$db = Factory::getDBO();
		$params = ComponentHelper::getParams('com_cgchat');
		$session = Factory::getSession();
		$order = $params->get('order', 'bottom');
		$doc = Factory::getDocument();
		$comfield	= 'media/com_cgchat/';
		$app = Factory::getApplication();
		$com_id = $app->input->getInt('Itemid');
		/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
		$wa = Factory::getDocument()->getWebAssetManager();
// $wa->registerAndUseStyle('iso',$comfield.'css/isotope.css');
		$wa->registerAndUseScript('cgchat',$comfield.'js/base.js');
		$tpl->include_html("js", "cgchat");
		
		$db->setQuery("SELECT id FROM #__cgchat ORDER BY id DESC LIMIT 1");
		$id = $db->loadResult();
		
		$refresh_time_session = intval($params->get("refresh_time_session", 30));
		if ($refresh_time_session < 5) $refresh_time_session = 5;
		$refresh_time_session *= 1000;

		self::addScript('
	cgchat.img_encendido = ["'.$tpl->include_html("botones", "encendido_0.gif").'", "'.$tpl->include_html("botones", "encendido_1.gif").'", "'.$tpl->include_html("botones", "encendido_2.gif").'"];
	cgchat.sound_on = "'.$tpl->include_html("botones", "sound_on.png").'";
	cgchat.sound_off = "'.$tpl->include_html("botones", "sound_off.png").'";
	cgchat.sound_src = "'.$tpl->include_html("sound", "msg.mp3").'";
	cgchat.img_blank = "'.$tpl->include_html("otras", "blank.png").'";
	cgchat.ajax_url = "'.URI::base(true).'/index.php?option=com_cgchat&no_html=1&tmpl=component'.'";
	cgchat.url = "'.CGChatLinks::getUserLink($kuser->id).'";
	cgchat.popup_url = "'.Route::_("index.php?option=com_cgchat&view=cgchat").'";
	cgchat.order = "'.$order.'";
	cgchat.formato_hora = "'.$params->get("formato_hora", "G:i--").'";
	cgchat.formato_fecha = "'.$params->get("formato_fecha", "j-n G:i:s").'";
	
	cgchat.template = "'.$kuser->template.'";
	cgchat.gmt = "'.$kuser->gmt.'";
	cgchat.token = '.$kuser->token.';
	cgchat.session = "'.$kuser->session.'";
	cgchat.row = '.$kuser->row.';
	cgchat.rowss = ["'.implode('","', CGChatHelper::getRows()).'"];
	cgchat.can_read = '.($kuser->can_read?'true':'false').';
	cgchat.can_write = '.($kuser->can_write?'true':'false').';
	cgchat.show_avatar = '.($params->get("show_avatar", 0) ? 'true' : 'false').';
	cgchat.avatar_maxheight = "'.$params->get('avatar_maxheight', '30px').'";
	cgchat.refresh_time_session = '.$refresh_time_session.';
	cgchat.boton_enviar = '.($params->get('button_send', 0)?'true':'false').';
	cgchat.refresh_time = '.$params->get('refresh_time', 6).'*1000;
	cgchat.refresh_time_privates = '.$params->get('refresh_time_privates', 6).'*1000;
	
	cgchat.n = '.(int)$id.';
	cgchat.name = "'.$kuser->name.'";
	cgchat.userid = '.$kuser->id.';
	cgchat.sound = '.$kuser->sound.';
	cgchat.color = "'.$kuser->color.'";
	cgchat.retardo = '.(int)$kuser->retardo.';
	cgchat.last_time = '.CGChatHelper::getLastTime().';

	cgchat.msg = {
		espera_por_favor: \''.addslashes(Text::_("COM_CGCHAT_ESPERA_POR_FAVOR")).'\',
		mensaje_borra: \''.addslashes(Text::_("COM_CGCHAT_MENSAJE_BORRAR")).'\',
		retardo_frase: \''.addslashes(Text::_("COM_CGCHAT_RETARDO_FRASE")).'\',
		lang: [\''.addslashes(Text::_("COM_CGCHAT_MONTH")).'\', \''.addslashes(Text::_("COM_CGCHAT_MONTHS")).'\', \''.addslashes(Text::_("COM_CGCHAT_DAY")).'\', \''.addslashes(Text::_("COM_CGCHAT_DAYS")).'\', \''.addslashes(Text::_("COM_CGCHAT_HOUR")).'\', \''.addslashes(Text::_("COM_CGCHAT_HOURS")).'\', \''.addslashes(Text::_("COM_CGCHAT_MINUTE")).'\', \''.addslashes(Text::_("COM_CGCHAT_MINUTES")).'\', \''.addslashes(Text::_("COM_CGCHAT_SECOND")).'\', \''.addslashes(Text::_("COM_CGCHAT_SECONDS")).'\'],
		privados_usuario_cerrado: \''.addslashes(Text::_("COM_CGCHAT_PRIVADOS_USUARIO_CERRADO")).'\',
		privados_nuevos: \''.addslashes(str_replace("%url", Route::_("index.php?option=com_cgchat&view=cgchat"), Text::_("COM_CGCHAT_PRIVADOS_NUEVOS"))).'\',
		privados_need_login: \''.addslashes(Text::_('COM_CGCHAT_PRIVADOS_NEED_LOGIN')).'\'
	};
	cgchat.smilies = [
		'.CGChatHelper::smilies_js().'
	];
	');
	  
		$doc->addStyleDeclaration('
	'.($kuser->color?'#KIDE_txt { color: #'.$kuser->color.'; }':'').'
	#KIDE_usuarios_td { vertical-align: '.$order.' }');
			
		if ($session->get('gmt', null, 'kide') === null)
			self::addScript('
	var tiempo = new Date();
	cgchat.save_config("gmt", (tiempo.getTimezoneOffset()/60)*-1);');
			
		if($session->get('retardo', null, 'kide') === null)
			self::addScript('cgchat.ajax("retardo");');
	}
	
	static function addScript($str) {
		$doc = Factory::getDocument();
		$doc->addCustomTag("<script type=\"text/javascript\">\n/*<![CDATA[*/\n".$str."\n/*]]>*/\n</script>");
	}
}
