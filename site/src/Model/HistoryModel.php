<?php
/**
* CG Chat Component  - Joomla 4.x Component 
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2023 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From Kide ShoutBox
*/
namespace ConseilGouz\Component\CGChat\Site\Model;
defined('_JEXEC') or die();
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

class HistoryModel extends ListModel {
	function getMsgs() {
		$db = Factory::getDBO();
		$app       = Factory::getApplication();
		$params = ComponentHelper::getParams('com_cgchat');
		$page = $app->input->getInt('page', 1);
		$limit = $params->get("msgs_history", 50);
		$db->setQuery("SELECT * FROM #__cgchat ORDER BY id DESC LIMIT ".(($page-1)*$limit).",".$limit);
		$msgs = $db->loadObjectList();
		return $msgs;
	}
	
	function getPags() {
		$db  = Factory::getDBO();
		$app = Factory::getApplication();
		$params = ComponentHelper::getParams('com_cgchat');
		$page = $app->input->getInt('page', 1);
		$limit = $params->get("msgs_history", 50);
		$limitpages = $params->get("pages_history", 5);
		
		$db->setQuery("SELECT count(*) FROM #__cgchat");
		$total = $db->loadResult();
		if ($limit > 0) {
			$tmp = $total/$limit;
			$pages = round($tmp);
			if ($tmp-$pages > 0)
				$pages++;
		}
		else 
			$pages = 1;
			
		if (!($limitpages > 0))
			$limitpages = $pages;
			
		$show = "";
		$cshow = 0;
		$mitad = round($limitpages/2);
		$ini = $page-$mitad;
		if ($ini <= 0) $ini = 1; 
		
		for ($i=$ini; $i<=$pages && $cshow<=$limitpages; $i++) {
			if ($i == $page) 
				$show .= " $i";
			else
				$show .= ' <a href="'.Route::_("index.php?option=com_cgchat&view=history&page=".$i).'">'.$i.'</a>';
			$cshow++;
		}
		
		return ($cshow > 1) ? $show : "";
	}
}
