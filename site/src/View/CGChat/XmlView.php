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
//		$articleId = $input->get('article');
//		echo json_encode(self::getArticle((int)$articleId)); 
	}
	function borrar() {
        $input = Factory::getApplication()->input;
		$id = $input->get('id');
		$kuser = CGChatUser::getInstance();
		$db = Factory::getDBO();
		
		if ($id) {
			if ($kuser->row == 1)
				$db->setQuery("DELETE FROM #__cgchat WHERE id=".$id);
			else
				$db->setQuery("DELETE FROM #__cgchat WHERE id=".$id." AND session='".$kuser->session."'");
			$db->query();
		} 
	}
	function banear() {
        $input = Factory::getApplication()->input;
		$kuser = CGChatUser::getInstance();
		$db = Factory::getDBO();
		$params = ComponentHelper::getParams('com_cgchat');
		
		if ($kuser->row == 1) {
			$session = $input->get('session');
			$dias = $input->get('dias');
			$horas = $input->get('horas');
			$minutos = $input->get('minutos');
			$t = (($dias*24+$horas)*60+$minutos)*60;
			if ($t > 0 && $session) {
				$t += time();
				$db->setQuery("DELETE FROM #__cgchat_bans WHERE time<".time());
				$db->query();
				$db->setQuery("SELECT id FROM #__cgchat_bans WHERE session='".$session."'");
				$id = $db->loadResult();
				if ($id) 
					$db->setQuery("UPDATE #__cgchat_bans SET time=".$t." WHERE id=".$id);
				else
					$db->setQuery("INSERT INTO #__cgchat_bans (session, time) VALUES ('".$session."', ".$t.")");
				$db->query();
				echo str_replace("%s1", $session, str_replace("%s2", gmdate($params->get("formato_fecha", "j-n G:i:s"), $t+$kuser->gmt*3600), Text::_("COM_CGCHAT_IP_BANEADA")));
			}
		}
	}
	function more_smileys() {
        $input = Factory::getApplication()->input;
		echo '<style>img {border:0}</style>'.CGChatHelper::smilies_html('ajax', $input->get('window'));
	}
	function sessions() {
		CGChatHelper::updateSession();
        $input = Factory::getApplication()->input;
		if ($input->get('show_sessions')) {
			$db = Factory::getDBO();
			$id = $input->get('id');
			$params = ComponentHelper::getParams('com_cgchat');
			header("Content-type: text/xml");
			echo '<?xml version="1.0" encoding="UTF-8" ?>';
			echo '<xml>';
			$db->setQuery("SELECT * FROM #__cgchat_session WHERE hidden=0 AND userid!=0  AND time>".(time() - $params->get("session_time", 200)). " ORDER BY name ASC");
			$users = $db->loadObjectList();
			if ($users) {
				foreach ($users as $u)
					echo '<user row="'.$u->row.'" name="'.htmlspecialchars($u->name).'" class="'.CGChatHelper::getRow($u->row, 'CGCHAT_').'" session="'.$u->session.'" profile="'.CGChatLinks::getUserLink($u->userid).'" userid="'.$u->userid.'" img="'.$u->img.'" />';
			}
			$db->setQuery("SELECT * FROM #__cgchat_session WHERE hidden=0 AND userid=0 AND time>".(time() - $params->get("session_time", 200)). " ORDER BY name ASC");
			$users = $db->loadObjectList();
			if ($users) {
				foreach ($users as $u)
					echo '<user row="'.$u->row.'" name="'.htmlspecialchars($u->name).'" class="'.CGChatHelper::getRow($u->row, 'CGCHAT_').'" session="'.$u->session.'" profile="" userid="0" img="'.$u->img.'" />';
			}
			echo '</xml>';
		}
	}
	function reload() {
		$db = Factory::getDBO();
		$kuser = CGChatUser::getInstance();
		
		if (!$kuser->can_read) exit;
		
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
		if (!$rows) exit;
		
		if ($params->get('order', 'bottom') == 'top')
			krsort($rows);
		header("Content-type: text/xml");
		echo '<?xml version="1.0" encoding="UTF-8" ?>';
		echo '<xml>';
		if ($rows) {
			echo '<last_id>'.$rows[count($rows)-1]->id.'</last_id>';
			echo '<last_time>'.$rows[count($rows)-1]->time.'</last_time>';
			foreach ($rows as $row) {
				echo '<mensaje uid="'.$row->userid.'" img="'.$row->img.'" time="'.$row->time.'" id="'.$row->id.'" hora="'.gmdate($params->get("formato_hora", "G:i--"), $row->time + $kuser->gmt*3600).'" name="'.htmlspecialchars($row->name).'" url="'.htmlspecialchars($row->url).'" date="'.gmdate($params->get("formato_fecha", "j-n G:i:s"), $row->time + $kuser->gmt*3600).'" color="'.$row->color.'" row="'.$row->row.'" session="'.$row->session.'">';
				echo '<![CDATA['.$row->text.']]>';
				echo '</mensaje>';
			}
		}
		echo '</xml>';
	}
	function add() {
		header("Content-type: text/xml");
		$db = Factory::getDBO();
		$params = ComponentHelper::getParams('com_cgchat');
		$kuser = CGChatUser::getInstance();
		$result = "";
		$result .= '<?xml version="1.0" encoding="UTF-8" ?>';
		
		if (!$kuser->can_write || ($kuser->row == 3 && !$kuser->captcha)) {
			$result .= '<xml></xml>';
		    echo $result;
		}
		if ($kuser->row == 4) {
			$result .= '<xml banned="1"></xml>';
			echo $result;
		}
		
		if ($kuser->checkBan()) {
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__cgchat_bans'));
			$query->where('time<"'.time());
			$db->setQuery($query);
			$db->execute();
			$db->setQuery('INSERT INTO #__cgchat (name,userid,text,time,color,row,session,token,img,url) VALUES ("System", 0, "'.str_replace("%name", $kuser->name, JText::_("COM_CGCHAT_USER_BANEADO")).'", '.time().', "'.$params->get('color_sp', '000').'", 0, 0, 0, "", "")');
			$db->query();
			$result .= '<xml banned="1"></xml>';
			echo $result;
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
				$result .= '<xml banned="0" id="0" img="'.$kuser->img.'" tiempo="'.$t.'" hora="'.gmdate($params->get("formato_hora", "G:i--"), $t + $kuser->gmt*3600).'">';
				$result .= '<txt><![CDATA['.$txt.']]></txt>';
				$result .= '<img />';
				$result .= '</xml>';
				echo $result;
			}
			$query = $db->getQuery(true);
			$columns = array('name','userid','text','time','color','row','token','session','img','url','ip');
			$values = array($db->q($kuser->name),$db->q($kuser->id),$db->q(addslashes($txt)),$db->q(time()),$db->q($kuser->color),$db->q($kuser->row),$db->q($kuser->token),$db->q($kuser->session),$db->q($kuser->img),$db->q(CGChatLinks::getUserLink($kuser->id)),$db->q($_SERVER['REMOTE_ADDR']));
			$query->insert($db->quoteName('#__cgchat'))
			->columns($db->quoteName($columns))
			->values(implode(',', $values));
			$db->setQuery($query);
			$db->execute();
			
			$db->setQuery("SELECT id FROM #__cgchat WHERE token=".$kuser->token." ORDER BY id DESC");
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
		$result .= '<xml txt="1" banned="0" id="'.$id.'" img="'.$kuser->img.'" tiempo="'.$t.'" hora="'.gmdate($params->get("formato_hora", "G:i--"), $t + $kuser->gmt*3600).'">';
		if ($txt)
			$result .= '<txt><![CDATA['.$txt.']]></txt>';
		$result .= '</xml>';
		echo $result;
	}
	function retardo() {
		echo time()."|ok";
	}

}