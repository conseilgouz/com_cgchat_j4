<?php
/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Package			: CG Chat
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : Kide ShoutBox
*/

namespace ConseilGouz\Component\CGChat\Site\View\CGChat;

defined('_JEXEC') or die;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\JsonView as BaseHtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Session\Session;
use Joomla\Database\DatabaseInterface;
use Joomla\Utilities\IpHelper;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatUser;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatHelper;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatLinks;

class JsonView extends BaseHtmlView
{
    public function display($tpl = null)
    {
        if (!Session::checkToken('get')) {
            echo new JsonResponse(null, Text::_('JINVALID_TOKEN'), true);
            exit;
        }
        PluginHelper::importPlugin('cgchat');

        $input = Factory::getApplication()->input;
        $task = $input->get('task');
        if ($task == 'add') {
            echo json_encode(self::add());
            exit;
        }
        if ($task == 'sessions') {
            echo json_encode(self::sessions());
            exit;
        }
        if ($task == 'reload') {
            echo json_encode(self::reload());
            exit;
        }
        if ($task == 'borrar') { // delete a message
            echo json_encode(self::borrar());
            exit;
        }
        if ($task == 'ban') { // banned
            echo json_encode(self::ban());
            exit;
        }
        if ($task == 'retardo') {
            echo json_encode(self::retardo());
            exit;
        }
        if ($task == 'kill') {
            echo json_encode(self::kill());
            exit;
        }
        if ($task == 'askprivate') { // ask to one user to go to private
            echo json_encode(self::askprivate());
            exit;
        }
        if ($task == 'acceptprivate') { // accept go to private with another user
            echo json_encode(self::acceptprivate());
            exit;
        }
        if ($task == 'closeprivate') { // close/refuse private
            echo json_encode(self::closeprivate());
            exit;
        }
        exit;
    }
    // Delete a message
    public function borrar()
    {
        $input = Factory::getApplication()->input;
        $id = (int)$input->get('id');
        $kuser = CGChatUser::getInstance();
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        if ($id) {
            if ($kuser->row == 1) {
                $query = $db->getQuery(true);
                $conditions = array($db->qn('id'). ' = '.$id );
                $query->delete($db->quoteName('#__cgchat'));
                $query->where($conditions);
                $db->setQuery($query);
                $db->execute();
            } else {
                $query = $db->getQuery(true);
                $conditions = array($db->qn('id'). ' = '.$id,$db->qn('session') .'='.$db->q('session') );
                $query->delete($db->quoteName('#__cgchat'));
                $query->where($conditions);
                $db->setQuery($query);
                $db->execute();
            }
        }
        return [];
    }
    // ask to private messages
    public function askprivate()
    {
        $out = [];
        $input = Factory::getApplication()->input;
        $kuser = CGChatUser::getInstance();
        $flag = ($input->getString('private', '') == "false") ? false : true;
        $userid = (int)$input->get('user');
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        if ($flag) { // Want to talk private
            if (CGChatHelper::checkPrivate($kuser->id)) {
                $out['error'] = Text::_("COM_CGCHAT_ALREADY_PRIVATE_YOU");
                return $out;
            }
            if (CGChatHelper::checkPrivate($userid)) {
                $name = CGChatHelper::getUserPerId($userid);
                $out['error'] = sprintf(Text::_('COM_CGCHAT_ALREADY_PRIVATE'), $name);
                return $out;
            }
        }
        $query = $db->getQuery(true);
        $query->select($db->qn('key'))->from('#__cgchat_session')->where($db->qn('userid').' = '.$db->q($userid));
        $db->setQuery($query);
        $key = $db->loadResult();
        if ($key) {
            $query = $db->getQuery(true);
            if ($flag) {
                $fields = array($db->qn('private') . ' = ' . $kuser->id);
                $out['userid'] = $userid;
            } else {
                $fields = array($db->qn('private') . ' = 0');
                $out['userid'] = 0;
            }
            $conditions = array($db->qn('key') . ' = '.$key);
            $query->update($db->quoteName('#__cgchat_session'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $db->execute();
            if (!$flag) {
                // close user talking to me
                $query = $db->getQuery(true);
                $fields = array($db->qn('private') . ' = 0');
                $conditions = array($db->qn('userid') . ' = '.$kuser->id);
                $query->update($db->quoteName('#__cgchat_session'))->set($fields)->where($conditions);
                $db->setQuery($query);
                $db->execute();
            }
        }
        return $out;
    }
    // accept private messages
    public function acceptprivate()
    {
        $out = [];
        $kuser = CGChatUser::getInstance();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        // accept private messages from user talking to me
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select($db->qn('private'))->from('#__cgchat_session')->where($db->qn('userid').' = '.$db->q($kuser->id));
        $db->setQuery($query);
        $userid = $db->loadResult();
        $query = $db->getQuery(true);
        $fields = array($db->qn('private') . ' = '.$db->q($kuser->id));
        $conditions = array($db->qn('userid') . ' = '.$userid);
        $query->update($db->quoteName('#__cgchat_session'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $db->execute();
        $out['private'] = $userid;
        return $out;
    }
    // close to private messages
    public function closeprivate($userid = 0)
    {
        $out = [];
        if (!$userid) {
            $kuser = CGChatUser::getInstance();
            $userid = $kuser->id;
        }
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        // close my private
        $query = $db->getQuery(true);
        $query->select($db->qn('private'))->from('#__cgchat_session')->where($db->qn('userid').' = '.$db->q($userid));
        $db->setQuery($query);
        $private = $db->loadResult();
        $query = $db->getQuery(true);
        $fields = array($db->qn('private') . ' = 0');
        $out['userid'] = 0;
        $conditions = array($db->qn('userid') . ' = '.$userid);
        $query->update($db->quoteName('#__cgchat_session'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $db->execute();
        // close user talking to me
        $query = $db->getQuery(true);
        $fields = array($db->qn('private') . ' = 0');
        $conditions = array($db->qn('userid') . ' = '.$private);
        $query->update($db->quoteName('#__cgchat_session'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $db->execute();
        $out['userid'] = 0;
        return $out;
    }
    // Ban
    public function ban()
    {
        $input = Factory::getApplication()->input;
        $kuser = CGChatUser::getInstance();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $params = ComponentHelper::getParams('com_cgchat');
        $out = [];
        if ($kuser->row == 1) {
            $session    = $input->get('session', '', 'ALNUM');
            $flag       = (string)$input->get('flag');
            if ($flag == "false") {
                $fields = array($db->qn('state') . ' = 2', $db->qn('time_off'). ' = '.time());
                $conditions = array($db->qn('session').' LIKE '.$db->q($session));
                $query = $db->getQuery(true);
                $query->update($db->quoteName('#__cgchat_bans'))->set($fields)->where($conditions);
                $db->setQuery($query);
                $db->execute();
                $name = CGChatHelper::getUserPerSession($session);
                $out[] = self::add(sprintf(Text::_("COM_CGCHAT_IP_UNBANNED"), $name), '008000');
            } else { // ban
                $minutos = $params->get('baneado', 10);
                $t = $minutos * 60;
                if ($t > 0 && $session) {
                    $t += time();
                    $query = $db->getQuery(true);
                    $fields = array($db->qn('state') . ' = 2',$db->qn('time_off'). ' = '.time());
                    $conditions = array($db->qn('time'). ' < '.time() );
                    $query->update($db->quoteName('#__cgchat_bans'))->set($fields)->where($conditions);
                    $db->setQuery($query);
                    $db->execute();
                    $query = $db->getQuery(true);
                    $query->select('id')
                        ->from('#__cgchat_bans')
                        ->where($db->qn('session').' = '.$db->q($session))
                        ->where($db->qn('state') . '< 2');
                    $db->setQuery($query);
                    $id = $db->loadResult();
                    $query = $db->getQuery(true);
                    if ($id) {
                        $fields = array($db->qn('time') . ' = ' . $db->q($t));
                        $conditions = array($db->qn('id') . ' = '.$id);
                        $query->update($db->quoteName('#__cgchat_bans'))->set($fields)->where($conditions);
                        $db->setQuery($query);
                        $db->execute();
                    } else {
                        $query = $db->getQuery(true);
                        $columns = array('session','time','name');
                        $values = array($db->q($session),$t,$db->q($kuser->name));
                        $query->insert($db->quoteName('#__cgchat_bans'))
                            ->columns($db->quoteName($columns))
                            ->values(implode(',', $values));
                        $db->setQuery($query);
                        $db->execute();
                    }
                    $query = $db->getQuery(true);
                    $query->select('userid')->from('#__cgchat_session')->where($db->qn('session').' = '.$db->q($session));
                    $db->setQuery($query);
                    $userid = $db->loadResult();
                    self::closeprivate($userid);
                    $gmt =  $kuser->gmt;
                    $blocktime = (string) gmdate($params->get("formato_fecha", "j-n G:i:s"), $t + ($gmt * 3600));
                    $name = CGChatHelper::getUserPerSession($session);
                    $out[] = self::add(sprintf(Text::_("COM_CGCHAT_IP_BANNED"), $name, $blocktime), 'ff0000');
                }
            }
        }
        return $out;
    }
    public function more_smileys()
    {
        $input = Factory::getApplication()->input;
        echo '<style>img {border:0}</style>'.CGChatHelper::smilies_html('ajax', $input->get('window'));
    }
    public function sessions()
    {
        CGChatHelper::updateSession();
        $result = [];
        $input = Factory::getApplication()->input;
        if (!$input->get('show_sessions')) {
            return $result;
        }
        $kuser = CGChatUser::getInstance();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $id = (int)$input->get('id');
        $params = ComponentHelper::getParams('com_cgchat');
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__cgchat_session')
            ->where("hidden=0 AND userid != 0  AND time>".(time() - $params->get("session_time", 200)))
            ->order("name ASC");
        $db->setQuery($query);
        $users = $db->loadObjectList();
        if ($users) {
            foreach ($users as $u) {
                $one = [];
                $one['row'] =  $u->row;
                if ($time = CGChatUser::isBanned($u->session)) {
                    $one['row'] = 4; // banned
                    $one['banned'] = gmdate($params->get("formato_fecha", "j-n G:i:s"), $time + $kuser->gmt * 3600);
                }
                $one['name'] = htmlspecialchars($u->name);
                $one['class'] = CGChatHelper::getRow($u->row, 'CGCHAT_');
                $one['session'] = $u->session;
                $one['profile'] = CGChatLinks::getUserLink($u->userid);
                $one['userid'] = $u->userid;
                if (($u->private) && !CGChatHelper::stillActive($u->private)) {
                    // user not connected anymore
                    $one['private'] = 0;
                    CGChatHelper::resetPrivate($u->userid);
                } else {
                    $one['private'] = $u->private;
                }
                $one['img'] = $u->img;
                $one['country'] = $u->country;
                $result[] = $one;
            }
        }
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__cgchat_session')
            ->where("hidden=0 AND userid = 0  AND time>".(time() - $params->get("session_time", 200)))
            ->order("name ASC");
        $db->setQuery($query);
        $users = $db->loadObjectList();
        if ($users) {
            foreach ($users as $u) {
                $one = [];
                $one['row'] =  $u->row;
                if ($time = CGChatUser::isBanned($u->session)) {
                    $one['row'] = 4; // banned
                    $one['banned'] = gmdate($params->get("formato_fecha", "j-n G:i:s"), $time + $kuser->gmt * 3600);
                }
                $one['name'] = htmlspecialchars($u->name);
                $one['class'] = CGChatHelper::getRow($u->row, 'CGCHAT_');
                $one['session'] = $u->session;
                $one['profile'] = "";
                $one['userid'] = "0";
                $one['private'] = "0";
                $one['img'] = $u->img;
                $one['country'] = $u->country;
                $result[] = $one;
            }
        }
        return $result;
    }
    public function reload()
    {
        $result = [];
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $kuser = CGChatUser::getInstance();
        if (!$kuser->can_read) {
            return $result;
        };
        $kuser->private = CGChatHelper::getPrivate($kuser->id);
        $chkprivate = CGChatHelper::checkPrivate($kuser->id);
        if (!$chkprivate && $kuser->private) {// private request pending
            $result['privaterequest'] = $kuser->private;
        }
        if ($chkprivate && $kuser->private) {// private request pending
            $result['private'] = $kuser->private;
        }
        $params = ComponentHelper::getParams('com_cgchat');
        $refresh = intval($params->get("refresh_time", 6));
        if ($refresh < 2) {
            $refresh = 2;
        }
        $input = Factory::getApplication()->input;
        $privs = (int)$input->get('privs');
        $table = '#__cgchat';
        if ($privs > 0) {
            $table = '#__cgchat_private';
        }
        $query = $db->getQuery(true);
        $query->select('*')
        ->from($db->quoteName($table))
        ->where('id>'.(int)$input->get('id'));
        if ($privs > 0) {
            $query->where('(('.$db->qn('fid').' = '.$db->q($kuser->id).' AND '.$db->qn('tid').' = '.$db->q($kuser->private).') 
                        OR ('.$db->qn('fid').' = '.$db->q($kuser->private).' AND '.$db->qn('tid').' = '.$db->q($kuser->id).'))');
        }
        $query->order('id ASC');
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if (!$rows) {
            return $result;
        }
        if ($params->get('order', 'bottom') == 'top') {
            krsort($rows);
        }
        if ($rows) {
            $result['last_id'] = $rows[count($rows) - 1]->id;
            $result['last_time'] = $rows[count($rows) - 1]->time;
            $messages = [];
            foreach ($rows as $row) {
                $one = [];
                if ($privs > 0) { // private
                    $one['uid'] = $row->fid;
                    $one['name'] = $row->from;

                } else {
                    $one['url'] = htmlspecialchars($row->url);
                    $one['uid'] = $row->userid;
                    $one['name'] = htmlspecialchars($row->name);
                }
                $one['img'] = $row->img;
                $one['time'] = $row->time;
                $one['id'] = $row->id;
                $one['hora'] = gmdate($params->get("formato_hora", "G:i--"), $row->time + $kuser->gmt * 3600);
                $one['date'] = gmdate($params->get("formato_fecha", "j-n G:i:s"), $row->time + $kuser->gmt * 3600);
                $one ['color'] = $row->color;
                $one['row'] = $row->row;
                $one['session'] = $row->session;
                $one['text'] = str_replace('\"', '"', $row->text);
                $one['country'] = ($row->country) ? $row->country : '';
                $messages[] = $one;
            }
            $result['messages'] = $messages;
        }
        return $result;
    }
    public function add($message = "", $color = "")
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $params = ComponentHelper::getParams('com_cgchat');
        $kuser = CGChatUser::getInstance();
        $result = [];

        if (!$kuser->can_write) {
            return $result;
        }
        if ($kuser->row == 4) {
            $result['banned'] = 1;
            return $result;
        }

        if ($kuser->checkBan()) {
            $query = $db->getQuery(true);
            $fields = array($db->qn('state') . ' = 2', $db->qn('time_off'). ' = '.time());
            $conditions = array($db->qn('time'). ' < '.time() );
            $query->update($db->quoteName('#__cgchat_bans'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $db->execute();
            $db->setQuery('INSERT INTO #__cgchat (name,userid,text,time,color,row,session,token,img,url) VALUES ("System", 0, "'.str_replace("%name", $kuser->name, Text::_("COM_CGCHAT_USER_BANNED")).'", '.time().', "'.$params->get('color_sp', '000').'", 0, 0, 0, "", "")');
            $db->query();
            $result['banned'] = 1;
            return $result;
        }
        if ($message) {
            $txt = $message;
        } else {
            $input = Factory::getApplication()->input;
            $txt = $input->getRaw('txt', '', 'post', 'string');
            $color = $input->get('color', '', 'post', 'NUM');
            $private = $input->get('privs', '', 'NUM');
        }
        $table = '#__cgchat';
        if ($private > 0) {
            $table = '#__cgchat_private';
        }
        $id = 0;
        if ($txt && $txt != Text::_("COM_CGCHAT_NOSPAM")) {
            if ($private > 0) {
                $db->setQuery('SHOW TABLE STATUS LIKE "'.$db->getPrefix().'cgchat_private"');
            } else {
                $db->setQuery('SHOW TABLE STATUS LIKE "'.$db->getPrefix().'cgchat"');
            }
            $status = $db->loadObject();
            $txt = CGChatHelper::convertText($txt, $status->Auto_increment);

            // call CGChat plugin
            $response = false;
            $contentEventArguments = [
                'context'   => 'com_cgchat.cgchat',
                'params'    => $params,
                'user'      => $kuser,
                'txt'       => &$txt,
                'response'  => &$response,
            ];
            Factory::getApplication()->triggerEvent('onCGChatBeforeMsg', $contentEventArguments);
            if ($response) { // error found in plugins
                $result['comment'] = $response;
                return $result;
            }
            $query = $db->getQuery(true);
            $query->select('id,text,session,token')
                ->from($db->quoteName($table))
                ->order('id DESC')
                ->setLimit('1');
            $db->setQuery($query);
            $lastmsg = $db->loadObject();

            if ($lastmsg && $lastmsg->text == $txt && $lastmsg->token == $kuser->token) {
                $t = time();
                $result['banned'] = 0;
                $result['id'] = 0;
                $result['img'] = $kuser->img;
                $result['tiempo'] = $t;
                $result['hora'] = gmdate($params->get("formato_hora", "G:i--"), $t + $kuser->gmt * 3600);
                $result['txt'] = $txt;
                $result['country'] = $kuser->country;
                return $result;
            }
            $query = $db->getQuery(true);

            if ($private) {
                $name = CGChatHelper::getUserPerId($private);
                $columns = array('text','fid','from','tid','to','row','color','img','time','session','key','token','ip','country');
                $values = array($db->q($txt),$db->q($kuser->id),$db->q($kuser->name),$db->q($private),$db->q($name),$db->q($kuser->row),$db->q($color),$db->q($kuser->img),$db->q(time()),$db->q($kuser->session),$db->q($kuser->key),$db->q($kuser->token),$db->q($kuser->ip),$db->q($kuser->country));
            } else {
                $columns = array('name','userid','text','time','color','row','token','session','img','url','ip','country');
                $values = array($db->q($kuser->name),$db->q($kuser->id),$db->q($txt),$db->q(time()),$db->q($color),$db->q($kuser->row),$db->q($kuser->token),$db->q($kuser->session),$db->q($kuser->img),$db->q(CGChatLinks::getUserLink($kuser->id)),$db->q($kuser->ip),$db->q($kuser->country));
            }
            $query->insert($db->quoteName($table))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));
            $db->setQuery($query);
            $db->execute();

            $query = $db->getQuery(true);
            $query->select("id")->from($db->qn($table))->where($db->qn('token')." = ".$db->q($kuser->token))->order('id DESC');
            $db->setQuery($query);
            $id = $db->loadResult();

            $save = $params->get("msgs_saved", 500);
            if ($save > 0 && $lastmsg) {
                $var = $lastmsg->id + 1 - $save;
                if ($var > 0) {
                    $query = $db->getQuery(true);
                    $query->clear()->delete($db->quoteName($table));
                    $query->where('id< '.$var);
                    $db->setQuery($query);
                    $db->execute();
                }
            }
        }
        $t = time();
        $result['banned'] = 0;
        $result['id'] = $id;
        $result['img'] = $kuser->img;
        $result['tiempo'] = $t;
        $result['hora'] = gmdate($params->get("formato_hora", "G:i--"), $t + $kuser->gmt * 3600);
        $result['txt'] = '';
        if ($txt) {
            $result['txt'] = $txt;
        }
        $result['country'] = $kuser->country;
        return $result;
    }
    // Delay
    public function retardo()
    {
        $result = [];
        $result['time'] =  time();
        return $result;
    }
    public function kill()
    {

        $result = [];
        $input = Factory::getApplication()->input;
        $session = $input->get('session', '', 'ALNUM');
        $params = ComponentHelper::getParams('com_cgchat');

        // call CGChat plugin
        $response = false;
        $contentEventArguments = [
            'context'   => 'com_cgchat.cgchat',
            'params'    => $params,
            'session'   => $session,
            'response'  => &$response,
        ];
        Factory::getApplication()->triggerEvent('onCGChatKill', $contentEventArguments);
        if ($response) { // error found in plugins
            $result['comment'] = $response;
            return $result;
        }
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $conditions = array($db->qn('session'). ' = '.$db->q($session));
        $query->delete($db->quoteName('#__cgchat_session'));
        $query->where($conditions);
        $db->setQuery($query);
        $db->execute();
        return $result;
    }
}
