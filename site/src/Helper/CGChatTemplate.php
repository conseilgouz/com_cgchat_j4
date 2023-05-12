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

defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatUser;

class CGChatTemplate {
	var $def = 'default';
	var $tuser,$view,$tpl_html,$tpl_php;
	var $com,$show_hour,$show_sessions,$show_privados;
	var $autoiniciar,$button_send,$show_avatar,$avatar_maxheight;
	var $maxlength,$popup,$order,$copy,$msgs,$user;
	var $fecha,$formato_hora,$templates;

	function __construct() {
		$ktuser = CGChatUser::getInstance();
		$this->tpl_html = URI::base(true).'/components/com_cgchat/templates/';
		$this->tpl_php = JPATH_COMPONENT.'/'.'templates/';
		$this->tuser = $ktuser->template;
		if (!file_exists($this->tpl_php.$this->tuser.'/'))
			$this->tuser = 'default';
		$this->view = Factory::getApplication()->getInput()->get('view', 'cgchat');
		$this->check_language();
	}
	function check_language() {
		if (file_exists($this->tpl_php.$this->tuser.'/template.xml')) {
			$xml = simplexml_load_file($this->tpl_php.$this->tuser.'/template.xml');
			if (isset($xml->languages) && isset($xml->languages[0])) {
				$folder = isset($xml->languages[0]['folder']) ? ((string)$xml->languages[0]['folder']).'/' : '';
				$path = $this->tpl_php.$this->tuser.'/'.$folder;
				$this->load_language($path);
			}
		}
	}
	function load_language($path, $default="en-GB") {
		$user = Factory::getUser();
		$language = Factory::getLanguage();
		if ($this->lc($path, $user->getParam("language")))
			return;
		elseif ($this->lc($path, $language->getTag()))
			return;
		else
			$this->lc($path, $default);
	}
	function lc($path, $tag) {
		if (file_exists($path.$tag.".ini")) {
			$language = Factory::getLanguage();
			$language->_load($path.$tag.".ini");
			return true;
		}
		return false;
	}
	static function getInstance() {
		static $class;
		if (!is_object($class)) 
			$class = new CGChatTemplate;
		return $class;
	}
	function assignRef($name, &$var) {
		$this->$name = $var;
	}
	function assign($name, $var) {
		$this->$name = $var;
	}
	function display($tmpl='') {
		$file = $tmpl ? $this->view.'.'.$tmpl.'.php' : $this->view.'.php';
		$tpl = file_exists($this->tpl_php.$this->tuser.'/tmpl/'.$file) ? $this->tuser : $this->def;
		include $this->tpl_php.$tpl.'/tmpl/'.$file;
	}
	function include_php($file) {
		$tpl = file_exists($this->tpl_php.$this->tuser.'/'.$file) ? $this->tuser : $this->def;
		require_once($this->tpl_php.$tpl.'/'.$file);
	}
	function include_html($folder, $file) {
		$document =Factory::getDocument();
		if ($folder == 'css' || $folder == 'js') $file .= '.'.$folder;
		if ($folder != 'css' && $folder != 'js' && $folder != 'sound') $f = 'images/'.$folder.'/';
		else $f = $folder.'/';
		$tpl = file_exists($this->tpl_php.$this->tuser.'/'.$f.$file) ? $this->tuser : $this->def;
		
		if ($folder == "css")
			$document->addStyleSheet($this->tpl_html.$tpl.'/'.$f.$file);
		elseif ($folder == "js")
			$document->addScript($this->tpl_html.$tpl.'/'.$f.$file);
		else
			return $this->tpl_html.$tpl.'/'.$f.$file;
	}
}