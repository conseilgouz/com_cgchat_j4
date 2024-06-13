<?php
/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Package			: CG Chat
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From Kide ShoutBox
*/

namespace ConseilGouz\Component\CGChat\Site\Helper;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatHelper;

class CGChatUser
{
    //ban user if post 4 messages in 5 seconds or less
    //TODO: move to kide admin config
    public const BAN_TOTAL = 4;
    public const BAN_TIME = 5;

    public $color;
    public $session;

    /*
        0: systema
        1: administrador
        2: registrado
        3: invitado
        4: baneado
    */
    public $row;
    public $id;
    public $gmt;
    public $retardo;
    public $name = "";
    public $sound;
    public $icons_hidden;
    public $token;
    public $img;
    public $hidden_session;
    public $private;
    public $template;
    public $key;
    public $bantime;
    public $can_read;
    public $can_write;

    public function __construct()
    {
        self::saveNewOptions();
        $app = Factory::getApplication();
        $session = $app->getSession();
        $user = $app->getIdentity();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $params = ComponentHelper::getParams('com_cgchat');
        $this->session = $session->get('session', 0, 'cgchat');
        $this->key = $session->get('key', 0, 'cgchat');
        $oldid = $session->get('userid', 0, 'cgchat');

        if (!$this->session || !$this->key || ($user->id != $oldid)) {
            if ($this->session) {
                $or = '';
                if ($oldid > 0) {
                    $or = ' OR userid='.$oldid;
                }
                if ($user->id > 0) {
                    $or = ' OR userid='.$user->id;
                }
                $query = $db->getQuery(true);
                $query->delete($db->quoteName('#__cgchat_session'));
                $query->where('session="'.$this->session.'"'.$or);
                $db->setQuery($query);
                $db->execute();
            }
            $this->session = md5(mt_rand());
            $session->set('session', $this->session, 'cgchat');
            $this->key = rand(1000000, 9999999);
            $session->set('key', $this->key, 'cgchat');
        }
        $this->id = $user->id;
        $session->set('userid', $this->id, 'cgchat');

        if (!$this->id) {
            $this->row = 3;
        } elseif (CGChatHelper::isAdmin()) {
            $this->row = 1;
        } else {
            $this->row = 2;
        }
        if ($this->row != 1) {
            $query = $db->getQuery(true);
            $query->select('*')
            ->from('#__cgchat_bans')
            ->where('time > '.time());
            $ip = $_SERVER['REMOTE_ADDR'] ;
            if (($ip == '::1') || ($ip == '127.0.0.1')) {// localhost : ignore address
                $query->where($db->qn('session') .'='.$db->q($this->session));
            } else {
                $query->where("(".$db->qn('session') .'='.$db->q($this->session)." OR ip=".$db->q($_SERVER['REMOTE_ADDR']).")");
            }
            $db->setQuery($query);
            $ban = $db->loadObject();
            if ($ban) {
                if (!(($ip == '::1') || ($ip == '127.0.0.1'))) {// localhost : ignore address
                    if ($ban->ip != $_SERVER['REMOTE_ADDR']) {
                        $query = $db->getQuery(true);
                        $fields = array($db->qn('ip') . ' = ' . $db->q($_SERVER['REMOTE_ADDR']));
                        $conditions = array($db->qn('id') . ' = '.$ban->id);
                        $query->update($db->quoteName('#__cgchat_bans'))->set($fields)->where($conditions);
                        $db->setQuery($query);
                        $result = $db->execute();
                    }
                }
                $time = (int)$ban->time;
                if ($time > 0) {
                    $this->row = 4;
                    $this->bantime = $time;
                }
            }
        }
        if ($user->id) {
            $username = $params->get("username", true) ? $user->username : $user->name;
        } elseif ($session->get("name", '', 'cgchat')) {
            $username = $session->get("name", '', 'cgchat');
        } else { // not defined yet : create a new user name
            $username = Text::_("COM_CGCHAT_INVITADO")."_".rand(1000, 9999);
            $session->set("name", $username, 'cgchat');
        }
        $username = substr($username, 0, 20); //20 max char nick length
        $username = htmlspecialchars($username);
        $this->name = $username;

        $this->icons_hidden =  $session->get("icons_hidden", $params->get("icons_hidden", false), 'cgchat');
        $this->template =  $session->get("template", $params->get("template", 'default'), 'cgchat');
        $this->can_read = ($this->row < 3 || $params->get("guest_can", 2) >= 1) ? 1 : 0;
        $this->can_write = ($this->row < 3 || ($this->row == 3 && $params->get("guest_can", 2) >= 2)) ? 1 : 0;
        $this->sound = $params->get("sound", 1) ? $session->get("sound", 0, 'cgchat') : -1;
        $this->color =  $session->get("color", "", 'cgchat');
        $input = Factory::getApplication()->input;
        $this->token = $input->get('token', rand(), "POST");
        $this->gmt =  $session->get("gmt", 0, 'cgchat');
        $this->retardo = $session->get("retardo", 0, 'cgchat');
        $this->hidden_session = $session->get("hidden_session", 0, 'cgchat');
        $this->private = CGChatHelper::checkPrivate($user->id);
        $this->img = CGChatLinks::getAvatar();
    }

    public function checkBan()
    {
        if ($this->row <= 1) {
            return false;
        }
        $banned = false;
        $limit = self::BAN_TOTAL + 2;
        $session = Factory::getApplication()->getSession();
        $ban = $session->get('cgchat_ban', array(), 'cgchat');
        if (count($ban) != self::BAN_TOTAL + 3 || $ban[self::BAN_TOTAL + 1] != self::BAN_TOTAL || $ban[self::BAN_TOTAL + 2] != self::BAN_TIME) {
            $ban = array();
        }
        if (!count($ban)) {
            for ($i = 0; $i <= self::BAN_TOTAL; $i++) {
                $ban[$i] = $i == 0 ? 1 : 0;
            }
            $ban[self::BAN_TOTAL + 1] = self::BAN_TOTAL;
            $ban[self::BAN_TOTAL + 2] = self::BAN_TIME;
        }

        if ($ban[0] > self::BAN_TOTAL) {
            for ($i = 1; $i < self::BAN_TOTAL; $i++) {
                $ban[$i] = $ban[$i + 1];
            }
            $ban[self::BAN_TOTAL] = time();
            $aux = $ban[self::BAN_TOTAL] - $ban[1];
            if ($aux < self::BAN_TIME) {
                $this->banear();
                $banned = true;
            }
        } else {
            $ban[$ban[0]] = time();
            $ban[0]++;
        }
        $session->set('cgchat_ban', $ban, 'cgchat');
        return $banned;
    }
    public static function isBanned($session)
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select($db->qn('time'))
            ->from('#__cgchat_bans')
            ->where($db->qn('session') .'='.$db->q($session))
            ->where($db->qn('time').'>'.time());
        $db->setQuery($query);
        return $db->loadResult();
    }
    public function banear()
    {
        $this->row = 4;
        $params = ComponentHelper::getParams('com_cgchat');
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $session = Factory::getApplication()->getSession();
        $tiempo = time() + $params->get("banear_minutos", 5) * 60;
        $query = $db->getQuery(true);
        $columns = array('ip','session','time');
        $values = array($db->q($_SERVER['REMOTE_ADDR']),$db->q($session),$tiempo);
        $query->insert($db->quoteName('#__cgchat_bans'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));
        $db->setQuery($query);
        $db->execute();
    }
    public static function getInstance()
    {
        static $instance;
        if (!is_object($instance)) {
            $instance = new CGChatUser();
        }
        return $instance;
    }

    public function saveNewOptions()
    {
        $config = self::getCookieConfigArray();
        //        if (!headers_sent()) {
        //            setcookie('cgchat_config', '', 0, '/');
        //        }
        $session = Factory::getApplication()->getSession();
        foreach ($config as $k => $v) {
            $session->set($k, $v, 'cgchat');
        }
    }

    public function getCookieConfigArray()
    {
        $config = isset($_COOKIE['cgchat_config']) ? $_COOKIE['cgchat_config'] : '';
        $aux = array();
        if (strlen($config)) {
            $opciones = explode(";", $config);
            foreach ($opciones as $opcion) {
                $opcion = explode("=", $opcion);
                if ($opcion[0]) {
                    $aux[$opcion[0]] = isset($opcion[1]) ? $opcion[1] : '';
                }
            }
        }
        return $aux;
    }
}
