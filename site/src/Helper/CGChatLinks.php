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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseInterface;

class CGChatLinks
{
    public $link;
    public $r;
    public $l;
    public $Itemid;
    public $v;
    public $params;

    public function __construct()
    {
        $this->params = ComponentHelper::getParams('com_cgchat');
        $u = URI::getInstance();
        $perfil = $this->params->get('perfil_link');
        if ($perfil == 'cb') {
            $this->setLink('com_comprofiler', 'task=userProfile&user=');
        } elseif ($perfil == "cbe") {
            $this->setLink("com_cbe", "task=userProfile&user=");
        } elseif ($perfil == 'cbe25') {
            $this->setLink('com_cbe', 'view=profile&userid=');
        } elseif ($perfil == 'js') {
            $this->setLink('com_community', 'view=profile&userid=');
        } elseif ($perfil == 'kunena') {
            $this->setLink('com_kunena', 'func=profile&userid=');
            //$this->setLink('com_kunena', 'func=fbprofile&task=showprf&userid='); //old kunena link
        } elseif ($perfil == 'aup') {
            $this->setLink('com_alphauserpoints', 'view=account&userid=');
        }

        $this->v = substr(JVERSION, 0, 3);
        $com = substr(JVERSION, 0, 3) == "1.5" ? 'user' : 'users';
        if (!$this->l) {
            $this->l = Route::_('index.php?option=com_'.$com.'&view=login&return='.base64_encode($u->toString()));
        }
        if (!$this->r) {
            $this->r = Route::_('index.php?option=com_'.$com.'&view='.(substr(JVERSION, 0, 3) == "1.5" ? 'register' : 'registration'));
        }
    }

    public static function getInstance()
    {
        static $instance;
        if (!is_object($instance)) {
            $instance = new CGChatLinks();
        }
        return $instance;
    }
    public static function getRegisterURL()
    {
        $class = self::getInstance();
        return $class->r;
    }
    public static function getLoginURL()
    {
        $class = self::getInstance();
        return $class->l;
    }
    public static function getUserLink($userid)
    {
        $class = self::getInstance();
        if (!(int)$userid || !$class->link) {
            return '';
        }
        return Route::_($class->link.$userid);
    }

    public function setLink($com, $link)
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $tmp = $this->v == '1.5' ? '=0' : '<=1';
        $db->setQuery("SELECT id FROM #__menu WHERE link LIKE 'index.php?option=".$com."%' AND access ".$tmp." AND published='1' LIMIT 1");
        $this->Itemid = $db->loadResult();

        $this->Itemid = $this->Itemid > 0 ? '&Itemid='.$this->Itemid : '';
        $this->link = 'index.php?option='.$com.$this->Itemid.'&'.$link;

        if ($com == 'com_comprofiler') {
            $this->l = Route::_('index.php?option=com_comprofiler&task=login'.$this->Itemid);
            $this->r = Route::_('index.php?option=com_comprofiler&task=registers'.$this->Itemid);
        } elseif ($com == 'com_community') {
            $this->l = Route::_('index.php?option=com_community&view=frontpage'.$this->Itemid);
            $this->r =  Route::_('index.php?option=com_community&view=register'.$this->Itemid);
        } elseif ($com == 'com_cbe') {
            $this->r = Route::_('index.php?option=com_cbe&task=registers'.$this->Itemid);
        }
    }
    public static function getAvatar()
    {
        $class = self::getInstance();
        return $class->_getAvatar();
    }
    public function _getAvatar()
    {
        static $avatar;
        if (!$avatar) {
            $user = Factory::getApplication()->getIdentity();
            if (!$avatar) {
                $avatar = $this->getAvatarJoomla();
            }
            if (!$avatar) {
                $avatar = 'https://www.gravatar.com/avatar/'.md5($user->id ? $user->email : (session_id() ? session_id() : rand())).'?s=50&d='.$this->params->get('gravatar_d', 'identicon');
            }
            $avatar = htmlspecialchars($avatar);
        }
        return $avatar;
    }
    public function getAvatarJoomla()
    {
        $user = Factory::getApplication()->getIdentity();
        $avatar = '';
        if ($user->id) {
            $db = Factory::getContainer()->get(DatabaseInterface::class);
            $perfil = $this->params->get('perfil_link');
            if ($perfil == 'js') {
                $db->setQuery('SELECT thumb FROM #__community_users WHERE userid='.$user->id);
                $tmp = $db->loadResult();
                if ($tmp && strpos($tmp, '/default_thumb.jpg') === false && file_exists(JPATH_ROOT.'/'.$tmp)) {
                    $avatar = URI::root().$tmp;
                }
            } elseif ($perfil == 'kunena') {
                $db->setQuery('SELECT avatar FROM #__kunena_users WHERE userid='.$user->id);
                $tmp = $db->loadResult();
                if ($tmp && file_exists(JPATH_ROOT.'/media/kunena/avatars/'.$tmp)) {
                    $avatar = URI::root().'media/kunena/avatars/'.$tmp;
                }
            } elseif ($perfil == 'cb') {
                $db->setQuery('SELECT avatar FROM #__comprofiler WHERE user_id='.$user->id);
                $tmp = $db->loadResult();
                if ($tmp && strpos($tmp, '/default_thumb.jpg') === false) {
                    if (file_exists(JPATH_ROOT.'/images/comprofiler/tn'.$tmp)) {
                        $avatar = URI::root().'images/comprofiler/tn'.$tmp;
                    } elseif (file_exists(JPATH_ROOT.'/images/comprofiler/'.$tmp)) {
                        $avatar = URI::root().'images/comprofiler/'.$tmp;
                    }
                }
            } elseif ($perfil == "cbe") {
                $db->setQuery("SELECT avatar FROM #__cbe WHERE user_id=".$user->id);
                $tmp = $db->loadResult();
                if ($tmp && strpos($tmp, "/default_thumb.jpg") === false) {
                    if (file_exists(JPATH_ROOT."/images/cbe/tn".$tmp)) {
                        $avatar = URI::root()."images/cbe/tn".$tmp;
                    } elseif (file_exists(JPATH_ROOT."/images/cbe/".$tmp)) {
                        $avatar = URI::root()."images/cbe/".$tmp;
                    }
                }
            } elseif ($perfil == 'cbe25') {
                $db->setQuery('SELECT thumb FROM #__cbe_users WHERE userid='.$user->id);
                $tmp = $db->loadResult();
                if ($tmp && strpos($tmp, '/default_thumb.jpg') === false && file_exists(JPATH_ROOT.'/'.$tmp)) {
                    $avatar = URI::root().$tmp;
                }
            } elseif ($perfil == 'aup') {
                $db->setQuery('SELECT avatar FROM #__alpha_userpoints WHERE userid='.$user->id);
                $tmp = $db->loadResult();
                if ($tmp && file_exists(JPATH_ROOT.'/components/com_alphauserpoints/assets/images/avatars/'.$tmp)) {
                    $avatar = URI::root().'components/com_alphauserpoints/assets/images/avatars/'.$tmp;
                }
            } elseif ($perfil == 'agora') {
                $db->setQuery('SELECT id FROM #__agora_users WHERE jos_id='.$user->id.' AND use_avatar = 1');
                $tmp = $db->loadResult();
                if ($tmp > 0) {
                    $avatar = URI::root().'components/com_agora/img/pre_avatars/'.$tmp;
                }
            }
        }
        return $avatar;
    }
}
