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

defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatUser;

class CGChatTemplate
{
    public $def = 'default';
    public $tuser;
    public $view;
    public $tpl_html;
    public $tpl_php;
    public $com;
    public $show_hour;
    public $show_sessions;
    public $show_privados;
    public $autoiniciar;
    public $button_send;
    public $show_avatar;
    public $avatar_maxheight;
    public $maxlength;
    public $popup;
    public $order;
    public $copy;
    public $msgs;
    public $user;
    public $fecha;
    public $formato_hora;
    public $templates;
    public $pags;

    public function __construct()
    {
        $ktuser = CGChatUser::getInstance();
        $this->tpl_html = URI::base(true).'/components/com_cgchat/templates/';
        $this->tpl_php = JPATH_ROOT.'/components/com_cgchat/templates/';
        $this->tuser = $ktuser->template;
        if (!file_exists($this->tpl_php.$this->tuser.'/')) {
            $this->tuser = 'default';
        }
        $this->view = Factory::getApplication()->getInput()->get('view', 'cgchat');
        $this->check_language();
    }
    public function check_language()
    {
        if (file_exists($this->tpl_php.$this->tuser.'/template.xml')) {
            $xml = simplexml_load_file($this->tpl_php.$this->tuser.'/template.xml');
            if (isset($xml->languages) && isset($xml->languages[0])) {
                $folder = isset($xml->languages[0]['folder']) ? ((string)$xml->languages[0]['folder']).'/' : '';
                $path = $this->tpl_php.$this->tuser.'/'.$folder;
                $this->load_language($path);
            }
        }
    }
    public function load_language($path, $default = "en-GB")
    {
        $user = Factory::getApplication()->getIdentity();
        $language = Factory::getApplication()->getLanguage();
        if ($this->lc($path, $user->getParam("language"))) {
            return;
        } elseif ($this->lc($path, $language->getTag())) {
            return;
        } else {
            $this->lc($path, $default);
        }
    }
    public function lc($path, $tag)
    {
        if (file_exists($path.$tag.".ini")) {
            $language = Factory::getApplication()->getLanguage();
            $language->_load($path.$tag.".ini");
            return true;
        }
        return false;
    }
    public static function getInstance()
    {
        static $class;
        if (!is_object($class)) {
            $class = new CGChatTemplate();
        }
        return $class;
    }
    public function assignRef($name, &$var)
    {
        $this->$name = $var;
    }
    public function assign($name, $var)
    {
        $this->$name = $var;
    }
    public function display($tmpl = '')
    {
        $file = $tmpl ? $this->view.'.'.$tmpl.'.php' : $this->view.'.php';
        $tpl = file_exists($this->tpl_php.$this->tuser.'/tmpl/'.$file) ? $this->tuser : $this->def;
        include $this->tpl_php.$tpl.'/tmpl/'.$file;
    }
    public function include_php($file)
    {
        $tpl = file_exists($this->tpl_php.$this->tuser.'/'.$file) ? $this->tuser : $this->def;
        require_once($this->tpl_php.$tpl.'/'.$file);
    }
    public function include_html($folder, $file)
    {
        $document = Factory::getApplication()->getDocument();
        if ($folder == 'css' || $folder == 'js') {
            $file .= '.'.$folder;
        }
        if ($folder != 'css' && $folder != 'js' && $folder != 'sound') {
            $f = 'images/'.$folder.'/';
        } else {
            $f = $folder.'/';
        }
        $tpl = file_exists($this->tpl_php.$this->tuser.'/'.$f.$file) ? $this->tuser : $this->def;

        if ($folder == "css") {
            $document->addStyleSheet($this->tpl_html.$tpl.'/'.$f.$file);
        } elseif ($folder == "js") {
            $document->addScript($this->tpl_html.$tpl.'/'.$f.$file);
        } else {
            return $this->tpl_html.$tpl.'/'.$f.$file;
        }
    }
}
