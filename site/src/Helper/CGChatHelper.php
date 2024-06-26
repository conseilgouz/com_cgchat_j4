<?php
/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Package			: CG Chat
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : Kide ShoutBox
*/

namespace ConseilGouz\Component\CGChat\Site\Helper;

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseInterface;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatUser;

class CGChatHelper
{
    public function htmlInJs($html)
    {
        return addslashes(str_replace(array("\n", "\r"), array('',''), $html));
    }
    // JSON : replace @ by / , ~ by <br />
    public static function convertText($txt, $id)
    {
        $params = ComponentHelper::getParams('com_cgchat');
        $max_strlen = $params->get('msgs_max_strlen', 3000);
        if ($max_strlen > 0 && strlen($txt) > $max_strlen) {
            $txt = substr($txt, 0, $max_strlen);
        }
        $txt = ' '.trim($txt).' ';
        $txt = str_replace("@", "/", $txt);
        $txt = str_replace("\\x27", "'", $txt);
        $txt = htmlspecialchars($txt, ENT_NOQUOTES);
        $txt = self::make_links($txt);
        $txt = self::convert_smilies($txt);
        $txt = str_replace(array("\n"," ~ ","\r"), array("<br />","<br />", ""), $txt);

        if (($params->get('countryinfo') > 0) && ($params->get('flag', 0) == 2)) { // flags on messages ?
            $kuser = CGChatUser::getInstance();
            $img = HTMLHelper::_('image', 'com_cgchat/' . strtolower($kuser->country) . '.png', $kuser->country, "title=$kuser->country", true);
            $txt = $img.' '.$txt;
        }


        return $txt;
    }
    public static function opciones($cantidad)
    {
        for ($i = 1; $i <= $cantidad; $i++) {
            echo '<option value="'.$i.'">'.$i.'</option>';
        }
    }
    public static function getRow($row, $b = "")
    {
        $rows = array("special", "admin", "registered", "guest", "guest");
        return $b.$rows[$row];
    }
    public static function getRows()
    {
        return array("special", "admin", "registered", "guest", "guest");
    }
    public static function getRowTitles()
    {
        return array(Text::_('COM_CGCHAT_ESPECIAL'),Text::_('COM_CGCHAT_ADMINISTRADOR'),Text::_('COM_CGCHAT_REGISTRADO'), Text::_('COM_CGCHAT_INVITADO'),Text::_('COM_CGCHAT_ISBANNED'));
    }
    public static function isAdmin($id = 0)
    {
        if ($id) {
            $user = Factory::getApplication()->getIdentity($id);
        } else {
            $user = Factory::getApplication()->getIdentity();
        }
        if (substr(JVERSION, 0, 3) == 1.5) {
            if ($user->usertype == "Super Administrator" || $user->usertype == "Administrator") {
                return true;
            }
        } elseif ($user->authorise('core.manage', 'com_cgchat')) {
            return true;
        }
        return false;
    }
    public static function getLastTime($private = 0)
    {
        $table = '#__cgchat';
        if ($private) {
            $table = '#__cgchat_private';
        }
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $db->setQuery("SELECT time FROM ".$table." ORDER BY id DESC LIMIT 1");
        return (int)$db->loadResult();
    }
    public static function updateSession()
    {
        $kuser = CGChatUser::getInstance();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $params = ComponentHelper::getParams('com_cgchat');
        $time = time() - $params->get("session_time", 200);
        $query = $db->getQuery(true);
        $query->clear()->delete($db->quoteName('#__cgchat_session'));
        $query->where('time< '.$time);
        $db->setQuery($query);
        $db->execute();
        if ($kuser->private) { // private discussion : check user still connected
            if (!self::stillActive($kuser->private)) {
                $kuser->private = 0;
            }
        }
        // if ($kuser->can_write) {
        $query = $db->getQuery(true);
        $columns = array('name','userid','row','time','session','img','private','hidden','key','ip','country');
        $values = array($db->q($kuser->name),$kuser->id,$kuser->row,$db->q(time()),$db->q($kuser->session),$db->q($kuser->img),$db->q($kuser->private),$db->q($kuser->hidden_session),$db->q($kuser->key),$db->q($kuser->ip),$db->q($kuser->country));
        $query->insert($db->quoteName('#__cgchat_session'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));
        $query .= " ON DUPLICATE KEY UPDATE name=".$db->q($kuser->name).",time=".time().",hidden=".$kuser->hidden_session.",img=".$db->q($kuser->img).",country=".$db->q($kuser->country);
        $db->setQuery($query);
        $db->execute();
        //  }
        // delete obsolete bans
        $query = $db->getQuery(true);
        $fields = array($db->qn('state') . ' = 2', $db->qn('time_off'). ' = '.time());
        $conditions = array($db->qn('time'). ' < '.time() );
        $query->update($db->quoteName('#__cgchat_bans'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $db->execute();

    }
    public static function stillActive($userid)
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('s.key')->from('#__cgchat_session s')
        ->where($db->qn('userid').' = '.$db->q($userid));
        $db->setQuery($query);
        return $db->loadResult();
    }
    public static function getPrivate($userid)
    {
        if (!$userid) { // not registered => no private
            return 0;
        }
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('private')->from('#__cgchat_session')
        ->where($db->qn('userid').' = '.$db->q($userid));
        $db->setQuery($query);
        $result = $db->loadResult();
        if ($result) {
            return $result;
        }
        return 0;
    }
    public static function resetPrivate($userid)
    {
        if (!$userid) { // not registered => no private
            return 0;
        }
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $fields = array($db->qn('private') . ' = 0');
        $conditions = array($db->qn('userid') . ' = '.$userid);
        $query->update($db->quoteName('#__cgchat_session'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $db->execute();
    }
    public static function checkPrivate($userid)
    {
        if (!$userid) { // not registered => no private
            return 0;
        }
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('userid')->from('#__cgchat_session')
        ->where($db->qn('private').' = '.$db->q($userid));
        $db->setQuery($query);
        $result = $db->loadResult();
        if ($result) {
            return $result;
        }
        return 0;
    }
    public static function getUserPerSession($session)
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('name')->from('#__cgchat_session s')
        ->where($db->qn('session').' LIKE '.$db->q($session));
        $db->setQuery($query);
        $name = $db->loadResult();
        if (!$name) {
            $name = "user not found";
        }
        return $name;
    }
    public static function getUserPerId($id)
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('name')->from('#__cgchat_session s')
        ->where($db->qn('userid').' = '.$db->q($id));
        $db->setQuery($query);
        $name = $db->loadResult();
        if (!$name) {
            $name = "user not found";
        }
        return $name;
    }
    public static function make_links($text)
    {
        $kuser = CGChatUser::getInstance();
        $params = ComponentHelper::getParams('com_cgchat');
        $urls = $params->get("urls_text", "text");
        if (($urls == "text") || (($urls == 'regtext') && ($kuser->row < 3))) {
            return preg_replace("/(\n| )(http[^ |\n]+)/", '\1<a rel="nofollow" target="_blank" href="\2">'.$params->get("urls_text_personalized", Text::_('COM_CGCHAT_LINK')).'</a>', $text);
        } elseif (($urls == "link") || (($urls == 'reglink') && ($kuser->row < 3))) {
            return preg_replace("/(\n| )(http[^ |\n]+)/", '\1<a rel="nofollow" target="_blank" href="\2">\2</a>', $text);
        }
        return $text;
    }
    public static function moreSmileys($com)
    {
        $params = ComponentHelper::getParams('com_cgchat');
        $show = $params->get("icons_show_".$com, $com == 'com' ? 0 : 14);
        if (!$show) {
            return "";
        }

        $smilies = self::getSmileys();
        $aux = array();
        foreach ($smilies as $s) {
            if (!in_array($s, $aux)) {
                $aux[] = $s;
            }
        }

        if (count($aux) > $show) {
            if ($params->get('icons_window', 'popup') == "no_window") {
                return ' <a href="javascript:cgchat.show(\'CGCHAT_mas_iconos\')">'.Text::_('COM_CGCHAT_MAS_ICONOS').'</a>';
            } else {
                $ajax_url = URI::base(true).'/index.php?option=com_cgchat&no_html=1&tmpl=component';
                $xy = explode('x', $params->get('icons_popup_size', '500x500'));
                if (!($xy[0] > 0)) {
                    $xy[0] = 500;
                }
                if (!isset($xy[1])) {
                    $xy[1] = 500;
                }
                if ($params->get('icons_window', 'popup') == 'popup') {
                    $size = ',width='.$xy[0].',height='.$xy[1];
                    $onclick = "cgchat.open_popup_smileys('".$size."');return false;";
                    return ' <a href="'.Route::_($ajax_url.'&task=more_smileys').'" onclick="'.$onclick.'">'.Text::_('COM_CGCHAT_MAS_ICONOS').'</a>';
                } else {
                    $rel = "{handler: 'iframe', size: {x: ".$xy[0].", y: ".$xy[1]."}, onClose: function() {}}";
                    return ' <a class="modal" href="'.Route::_($ajax_url.'&task=more_smileys').'" rel="'.$rel.'">'.Text::_('COM_CGCHAT_MAS_ICONOS').'</a>';
                }
            }
        }
        return '';
    }
    public static function smilies_html($com, $window = null)
    {
        $params = ComponentHelper::getParams('com_cgchat');
        $hide = $params->get('icons_window') == 'no_window';
        $show = $com == 'ajax' ? 0 : $params->get("icons_show_".$com, 14);
        $smilies = self::getSmileys();
        $aux = array();
        $return = "";
        $count = 0;
        if ($com == 'ajax') {
            $parent = $window ? 'window.opener.parent.' : 'parent.';
        } else {
            $parent = '';
        }
        foreach ($smilies as $k => $s) {
            if (!in_array($s, $aux)) {
                $count++;
                $k = str_replace('"', '&quot;', $k);
                $return .= "<a href=\"javascript:{$parent}cgchat.insertSmile('".addslashes($k)."')\"><img title=\"$k\" alt=\"$k\" src=\"$s\" /></a>\n";
                $aux[] = $s;
            }
            if ($show == $count) {
                if ($hide && $com != "ajax") {
                    $return .= '<span id="CGCHAT_mas_iconos" style="display:none">';
                } else {
                    break;
                }
            }
        }
        if ($count >= $show && $show > 0 && $hide && $com != "ajax") {
            $return .= '</span>';
        }
        return $return;
    }
    public static function smilies_js()
    {
        $smilies = self::getSmileys(true);
        $return = [];
        foreach ($smilies as $k => $s) {
            $return[addslashes($k)] = addslashes($s);
        }
        return $return;
    }
    public static function getSmileys($length = false)
    {
        static $icons;
        $tmp = CGChatTemplate::getInstance();
        $id = $length ? 1 : 0;
        if (!is_array($icons)) {
            $icons = array();
        }
        if (isset($icons[$id])) {
            return $icons[$id];
        }
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        if ($id) {
            $db->setQuery("SELECT code,img FROM #__cgchat_icons order by LENGTH(code) DESC");
        } else {
            $db->setQuery("SELECT code,img FROM #__cgchat_icons order by `ordering`");
        }
        $data = $db->loadObjectList();
        $icons[$id] = array();
        foreach ($data as $r) {
            $icons[$id][$r->code] = $tmp->include_html("icons", $r->img);
        }
        return $icons[$id];
    }
    public static function convert_smilies($text)
    {
        $smilies = self::getSmileys(true);
        foreach ($smilies as $k => $s) {
            $k = str_replace('"', '&quot;', $k);
            $text = str_ireplace(" ".$k, ' <img alt="'.$k.'" src="'.$s.'" title="'.$k.'" class="cgchat_icon" /> ', $text);
        }
        return $text;
    }
    public static function getCopy()
    {
        return '';
    }
    public static function check_country($ip, $params)
    {
        $iplocate = 'https://www.iplocate.io/api/lookup/';
        $apikey = "e468c23c8daf64701f9d96e16b677e6f";
        $app = Factory::getApplication();
        $session = $app->getSession();
        $session->set("country", '', 'cgchat');    // assume not found
        if (($ip == '::1') || ($ip == '127.0.0.1')) { // local host
            return true;
        }
        if ($params->get('apikey')) { // api key defined : use it, else use mine
            $apikey = $params->get('apikey');
        }
        $response = CGChatHelper::getIPLocate_via_curl($iplocate.$ip.'?apikey='.$apikey);
        if ($response) { // IPLocate OK
            $json_array = json_decode($response);
            if ($json_array->country_code == "") { // IPLocate perdu : on suppose hackeur
                echo sprintf(Text::_('COM_CGCHAT_COUNTRY_NOTFOUND'), $ip);
                return false;
            }
            $country = $json_array->country_code;
            if (!$params->get('allow')) {
                $countries = '*';
            } else {
                $countries = $params->get('allow');
            }
            $pays_autorise = explode(',', $countries);
            if (($countries != '*') && (!in_array($country, $pays_autorise))) {
                echo Text::_('COM_CGCHAT_COUNTRY_INVALID_COUNTRY');
                return false;
            }
            if (!$params->get('block')) {
                $blocks = '*';
            } else {
                $blocks = $params->get('block');
            }
            $block = explode(',', $blocks);
            if (($blocks != '*') && (in_array($country, $block))) {
                echo Text::_('COM_CGCHAT_COUNTRY_INVALID_COUNTRY');
                return false;
            }
            $session->set("country", $country, 'cgchat');
            return true;
        } else { // IPLocate error
            echo Text::_('PLG_CGCHAT_COUNTRY_LOCATE_ERROR');
            return false;
        }
    }

    // get country using IPLocate
    public static function getIPLocate_via_curl($url)
    {
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, $url);
            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        } catch (\RuntimeException $e) {
            return null;
        }
    }

}
