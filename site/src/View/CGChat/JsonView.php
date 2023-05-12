<?php
/**
* CG Chat Component  - Joomla 4.x Component 
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2023 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From Kide ShoutBox
*/
namespace ConseilGouz\Component\CGChat\Site\View\CGChat;

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Access\Access;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Component\Content\Site\Model\ArticleModel; // 4.0. compatibility
use Joomla\CMS\MVC\View\JsonView as BaseHtmlView;
use Joomla\CMS\Language\Text;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatUser;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatHelper;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatLinks;

class JsonView extends BaseHtmlView {

	function display($tpl = null)
	{
		$input = Factory::getApplication()->input;
		$task = $input->get('task');
		if ($task == 'add') echo json_encode(self::add());
		if ($task == 'sessions') echo json_encode(self::sessions());
		if ($task == 'borrar') echo json_encode(self::borrar());
		if ($task == 'banear') echo json_encode(self::banear());
		if ($task == 'reload') echo json_encode(self::reload());
		if ($task == 'retardo') echo json_encode(self::retardo());
	}
	function borrar() {
        $input = Factory::getApplication()->input;
		$id = $input->get('id');
		$kuser = CGChatUser::getInstance();
		$db = Factory::getDBO();
		
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
		        $conditions = array($db->qn('id'). ' = '.$id,$db-qn('session') .'='.$db->q('session') );
		        $query->delete($db->quoteName('#__cgchat'));
		        $query->where($conditions);
		        $db->setQuery($query);
		        $db->execute();
		    }
		} 
		return [];
	}
	function banear() {
        $input = Factory::getApplication()->input;
		$kuser = CGChatUser::getInstance();
		$db = Factory::getDBO();
		$params = ComponentHelper::getParams('com_cgchat');
		$result = [];
		if ($kuser->row == 1) {
			$session = $input->get('session');
			$dias = $input->get('dias');
			$horas = $input->get('horas');
			$minutos = $input->get('minutos');
			$t = (($dias*24+$horas)*60+$minutos)*60;
			if ($t > 0 && $session) {
				$t += time();
				$query = $db->getQuery(true);
				$conditions = array($db->qn('time'). ' < '.time() );
				$query->delete($db->quoteName('#__cgchat_bans'));
				$query->where($conditions);
				$db->setQuery($query);
				$result = $db->execute();
				$query = $db->getQuery(true);
				$query->select('id')->from('#__cgchat_bans')->where($db->qn('session').' = '.$db->q($session));
				$db->setQuery($query);
				$id = $db->loadResult();
				$query = $db->getQuery(true);
				if ($id) {
				    $fields = array($db->qn('time') . ' = ' . $db->q($t));
				    $conditions = array($db->qn('id') . ' = '.$id);
				    $query->update($db->quoteName('#__cgchat_bans'))->set($fields)->where($conditions);
				    $db->setQuery($query);
				    $result = $db->execute();
				} else {
				    $query = $db->getQuery(true);
				    $columns = array('session','time');
				    $values = array($db->q($session),$t);
				    $query
				    ->insert($db->quoteName('#__cgchat_bans'))
				    ->columns($db->quoteName($columns))
				    ->values(implode(',', $values));
				    $db->setQuery($query);
				    $db->execute();
				}
				$result[] =  str_replace("%s1", $session, str_replace("%s2", gmdate($params->get("formato_fecha", "j-n G:i:s"), $t+ $kuser->gmt *3600), Text::_("COM_CGCHAT_IP_BANEADA")));
			}
		}
		return $result;
	}
	function more_smileys() {
        $input = Factory::getApplication()->input;
		echo '<style>img {border:0}</style>'.CGChatHelper::smilies_html('ajax', $input->get('window'));
	}
	function sessions() {
		CGChatHelper::updateSession();
		$result = [];
        $input = Factory::getApplication()->input;
		if (!$input->get('show_sessions')) return $result;
		$db = Factory::getDBO();
		$id = $input->get('id');
		$params = ComponentHelper::getParams('com_cgchat');
		$query = $db->getQuery(true);
		$query->select('*')
			->from ('#__cgchat_session')
			->where ("hidden=0 AND userid != 0  AND time>".(time() - $params->get("session_time", 200)))
			->order("name ASC");
		$db->setQuery($query);
		$users = $db->loadObjectList();
		if ($users) {
			foreach ($users as $u)
			    $one = [];
			    $one['row'] =  $u->row;
			    $one['name'] = htmlspecialchars($u->name);
			    $one['class'] = CGChatHelper::getRow($u->row, 'CGCHAT_');
			    $one['session'] = $u->session;
			    $one['profile'] = CGChatLinks::getUserLink($u->userid);
			    $row['userid'] = $u->userid;
			    $row['img'] = $u->img;
			    $result[] = $one;
		}
		$query = $db->getQuery(true);
		$query->select('*')
			->from ('#__cgchat_session')
			->where ("hidden=0 AND userid = 0  AND time>".(time() - $params->get("session_time", 200)))
			->order("name ASC");
		$db->setQuery($query);
		$users = $db->loadObjectList();
		if ($users) {
		    foreach ($users as $u) {
                $one = [];
			    $one['row'] =  $u->row;
			    $one['name'] = htmlspecialchars($u->name);
			    $one['class'] = CGChatHelper::getRow($u->row, 'CGCHAT_');
			    $one['session'] = $u->session;
			    $one['profile'] ="";
			    $row['userid'] ="0";
			    $row['img'] = $u->img;
			    $result[] = $one;
		    }
		}
		return $result;
	}
	function reload() {
		$result = [];
		$db = Factory::getDBO();
		$kuser = CGChatUser::getInstance();
		if (!$kuser->can_read) return $result;;
		$params = ComponentHelper::getParams('com_cgchat');
		$refresh = intval($params->get("refresh_time", 6));
		if ($refresh < 2) $refresh = 2;
        $input = Factory::getApplication()->input;
		$query = $db->getQuery(true);
		$query->select('*')
		->from($db->quoteName('#__cgchat'))
		->where('id>'.$input->get('id').' AND token!='.$kuser->token)
		->order('id ASC');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if (!$rows) return $result;
		if ($params->get('order', 'bottom') == 'top')
			krsort($rows);
		if ($rows) {
			$result['last_id'] = $rows[count($rows)-1]->id;
			$result['last_time'] =$rows[count($rows)-1]->time;
			$messages = [];
			foreach ($rows as $row) {
				$one = [];
				$one['uid'] = $row->userid;
				$one['img'] =$row->img;
				$one['time'] = $row->time;
				$one['id'] = $row->id;
				$one['hora'] = gmdate($params->get("formato_hora", "G:i--"), $row->time + $kuser->gmt*3600);
				$one['name'] = htmlspecialchars($row->name);
				$one['url'] = htmlspecialchars($row->url);
				$one['date'] = gmdate($params->get("formato_fecha", "j-n G:i:s"), $row->time + $kuser->gmt*3600);
				$one ['color'] = $row->color;
				$one['row'] = $row->row;
				$one['session'] = $row->session;
				$one['text'] = str_replace('\"','"',$row->text);
				$messages[] = $one;
			}
			$result['messages'] = $messages;
		}
		return $result;
	}
	function add() {
		$db = Factory::getDBO();
		$params = ComponentHelper::getParams('com_cgchat');
		$kuser = CGChatUser::getInstance();
		$result = [];
		
		if (!$kuser->can_write ) {
		    return $result;
		}
		if ($kuser->row == 4) {
			$result['banned'] = 1;
			return $result;
		}
		
		if ($kuser->checkBan()) {
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__cgchat_bans'));
			$query->where('time<"'.time());
			$db->setQuery($query);
			$db->execute();
			$db->setQuery('INSERT INTO #__cgchat (name,userid,text,time,color,row,session,token,img,url) VALUES ("System", 0, "'.str_replace("%name", $kuser->name, Text::_("COM_CGCHAT_USER_BANEADO")).'", '.time().', "'.$params->get('color_sp', '000').'", 0, 0, 0, "", "")');
			$db->query();
			$result['banned'] = 1;
			return $result;
		}
        $input = Factory::getApplication()->input;
		$txt = $input->getRaw('txt', '', 'post', 'string');

		$id = 0;
		if ($txt && $txt != Text::_("COM_CGCHAT_NOSPAM")) {
			$db->setQuery('SHOW TABLE STATUS LIKE "'.$db->getPrefix().'cgchat"');
			$status = $db->loadObject();
			$txt = CGChatHelper::convertText($txt, $status->Auto_increment);
			$query = $db->getQuery(true);
			$query->select('id,text,session,token')
				->from($db->quoteName('#__cgchat'))
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
				$result['hora'] = gmdate($params->get("formato_hora", "G:i--"), $t + $kuser->gmt*3600);
				$result['txt'] = $txt;
				return $result;
			}
			$query = $db->getQuery(true);
			$columns = array('name','userid','text','time','color','row','token','session','img','url','ip');
			$values = array($db->q($kuser->name),$db->q($kuser->id),$db->q(addslashes($txt)),$db->q(time()),$db->q($kuser->color),$db->q($kuser->row),$db->q($kuser->token),$db->q($kuser->session),$db->q($kuser->img),$db->q(CGChatLinks::getUserLink($kuser->id)),$db->q($_SERVER['REMOTE_ADDR']));
			$query->insert($db->quoteName('#__cgchat'))
			->columns($db->quoteName($columns))
			->values(implode(',', $values));
			$db->setQuery($query);
			$db->execute();
			
			$query = $db->getQuery(true);
			$query->select("id")->from($db->qn('#__cgchat'))->where($db->qn('token')." = ".$db->q($kuser->token))->order('id DESC');
			$db->setQuery($query);
			$id = $db->loadResult();

			$save = $params->get("msgs_saved", 500);
			if ($save > 0 && $lastmsg) {
				$var = $lastmsg->id + 1 - $save;
				if ($var > 0) {
					$query = $db->getQuery(true);
					$query->clear()->delete($db->quoteName('#__cgchat'));
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
		$result['hora'] = gmdate($params->get("formato_hora", "G:i--"), $t + $kuser->gmt*3600);
		$result['txt'] = '';
		if ($txt)
			$result['txt'] = $txt;
		return $result;
	}
	function retardo() {
	    $result = [];
	    $result['time'] =  time();
	    return $result;
	}

}