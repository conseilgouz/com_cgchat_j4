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
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatUser;

class CGChatHelper {
	function htmlInJs($html) {
		return addslashes(str_replace(array("\n", "\r"), array('',''), $html));
	}
	static function convertText($txt, $id) {
		$params = ComponentHelper::getParams('com_cgchat');
		$max_strlen = $params->get('msgs_max_strlen', 3000);
		if ($max_strlen > 0 && strlen($txt) > $max_strlen) 
			$txt = substr($txt, 0, $max_strlen);
		$txt = ' '.trim($txt).' ';
		$txt = htmlspecialchars($txt, ENT_NOQUOTES);
		$txt = self::make_links($txt);
		$txt = self::convert_smilies($txt);
		$txt = str_replace(array("\n", "\r"), array("<br />", ""), $txt);
		return $txt;
	}
	static function opciones($cantidad) {
		for ($i=1; $i<=$cantidad; $i++) 
			echo '<option value="'.$i.'">'.$i.'</option>';
	}
	static function getRow($row, $b="") {
		$rows = array("special", "admin", "registered", "guest", "guest");
		return $b.$rows[$row];
	}
	static function getRows() {
		return array("special", "admin", "registered", "guest", "guest");
	}
	static function isAdmin($id=0) {
		if ($id)
			$user = Factory::getUser($id);
		else
			$user = Factory::getUser();
		if (substr(JVERSION, 0, 3) == 1.5) {
			if ($user->usertype == "Super Administrator" || $user->usertype == "Administrator")
				return true;
		}
		elseif ($user->authorise('core.manage', 'com_cgchat')) 
			return true;
		return false;
	}
	static function getLastTime() {
		$db = Factory::getDBO();
		$db->setQuery("SELECT time FROM #__cgchat ORDER BY id DESC LIMIT 1");
		return (int)$db->loadResult();
	}
	static function updateSession() {
		$kuser = CGChatUser::getInstance();
		$db = Factory::getDBO();
		$params = ComponentHelper::getParams('com_cgchat');
		$time = time() - $params->get("session_time", 200);
		$query = $db->getQuery(true);
		$query->clear()->delete($db->quoteName('#__cgchat_session'));
		$query->where('time< '.$time);
		$db->setQuery($query);
		$db->execute();
		if ($kuser->can_write) {
			$query = $db->getQuery(true);
			$columns = array('name','userid','row','time','session','img','private','hidden','key');
			$values = array($db->quote($kuser->name),$kuser->id,$kuser->row,$db->quote(time()),$db->quote($kuser->session),$db->quote($kuser->img),$db->quote($kuser->private_session),0,$db->quote($kuser->key));			
			$query->insert($db->quoteName('#__cgchat_session'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));
			$query .= " ON DUPLICATE KEY UPDATE name=".$db->quote($kuser->name).",time=".time().",private=".$kuser->private_session.",img=".$db->quote($kuser->img);
			$db->setQuery($query);
			$db->execute();
		}
	}
	static function make_links($text) {
		$params = ComponentHelper::getParams('com_cgchat');
		$urls = $params->get("urls_text", "text");
		if ($urls == "text")
			return preg_replace("/(\n| )(http[^ |\n]+)/", '\1<a rel="nofollow" target="_blank" href="\2">'.$params->get("urls_text_personalized", "«link»").'</a>', $text);
		elseif ($urls == "link")
			return preg_replace("/(\n| )(http[^ |\n]+)/", '\1<a rel="nofollow" target="_blank" href="\2">\2</a>', $text);
		return $text;
	}
	static function moreSmileys($com) {
		$params = ComponentHelper::getParams('com_cgchat');
		$show = $params->get("icons_show_".$com, $com=='com'?0:14);
		if (!$show)
			return "";
		
		$smilies = self::getSmileys();
		$aux = array();
		foreach ($smilies as $s) {
			if (!in_array($s,$aux))
				$aux[] = $s;
		}

		if (count($aux) > $show) {
			if ($params->get('icons_window','popup') == "no_window") {
				return ' <a href="javascript:cgchat.show(\'KIDE_mas_iconos\')">'.JText::_('COM_CGCHAT_MAS_ICONOS').'</a>';
			}
			else{
				$xy = explode('x', $params->get('icons_popup_size', '500x500'));
				if (!($xy[0]>0)) $xy[0] = 500;
				if (!isset($xy[1])) $xy[1] = 500;
				if ($params->get('icons_window','popup') == 'popup') {
					$size = ',width='.$xy[0].',height='.$xy[1];
					$onclick = "cgchat.open_popup_smileys('".$size."');return false;";
					return ' <a href="'.Route::_(KIDE_AJAX.'&task=more_smileys').'" onclick="'.$onclick.'">'.JText::_('COM_CGCHAT_MAS_ICONOS').'</a>';
				}
				else {
					$rel = "{handler: 'iframe', size: {x: ".$xy[0].", y: ".$xy[1]."}, onClose: function() {}}";
					return ' <a class="modal" href="'.Route::_(KIDE_AJAX.'&task=more_smileys').'" rel="'.$rel.'">'.JText::_('COM_CGCHAT_MAS_ICONOS').'</a>';
				}
			}
		}
		return '';
	}
	static function smilies_html($com, $window=null) {	
		$params = ComponentHelper::getParams('com_cgchat');
		$hide = $params->get('icons_window')=='no_window';
		$show = $com =='ajax' ? 0 : $params->get("icons_show_".$com, 14);
		$smilies = self::getSmileys();
		$aux = array();
		$return = "";
		$count = 0;
		if ($com=='ajax') 
			$parent = $window ? 'window.opener.parent.' : 'parent.';
		else
			$parent = '';
		foreach ($smilies as $k=>$s) {
			if (!in_array($s,$aux)) {
				$count++;
				$k = str_replace('"', '&quot;', $k);
				$return .= "<a href=\"javascript:{$parent}cgchat.insertSmile('".addslashes($k)."')\"><img title=\"$k\" alt=\"$k\" src=\"$s\" /></a>\n";
				$aux[] = $s;
			}
			if ($show == $count) {
				if ($hide && $com != "ajax")
					$return .= '<span id="KIDE_mas_iconos" style="display:none">';
				else
					break;
			}
		}
		if ($count >= $show && $show>0 && $hide && $com != "ajax")
			$return .= '</span>';
		return $return;
	}
	static function smilies_js() {
		$smilies = self::getSmileys(true);
		$return = "";
		foreach ($smilies as $k=>$s)
			$return .= "['".addslashes($k)."', 	'".addslashes($s)."'],\n";
		return substr($return, 0, -2);
	}
	static function getSmileys($length=false) {
		static $icons;
		$tmp = CGChatTemplate::getInstance();
		$id = $length ? 1 : 0;
		if (!is_array($icons)) $icons = array();
		if (isset($icons[$id])) return $icons[$id];
		$db = Factory::getDBO();
		if ($id)
			$db->setQuery("SELECT code,img FROM #__cgchat_icons order by LENGTH(code) DESC");
		else
			$db->setQuery("SELECT code,img FROM #__cgchat_icons order by `ordering`");
		$data = $db->loadObjectList();
		$icons[$id] = array();
		foreach ($data as $r) 
			$icons[$id][$r->code] = $tmp->include_html("icons", $r->img);
		return $icons[$id];
	}
	static function convert_smilies($text) {
		$smilies = self::getSmileys(true);
		foreach ($smilies as $k=>$s) {
			$k = str_replace('"', '&quot;', $k);
			$text = str_ireplace(" ".$k, ' <img alt="'.$k.'" src="'.$s.'" title="'.$k.'" class="cgchat_icon" /> ', $text);
		}
		return $text;
	} 
	static function getCopy() {
		return '';
	}
}
