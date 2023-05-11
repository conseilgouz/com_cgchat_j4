<?php
/**
* CG Chat Component  - Joomla 4.x Component 
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2023 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From Kide ShoutBox
*/
// no direct access
defined('_JEXEC') or die;

$comfield	= 'media/com_cgchat/';

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getDocument()->getWebAssetManager();

// $wa->registerAndUseStyle('iso',$comfield.'css/isotope.css');
$wa->registerAndUseScript('cgchat',$comfield.'js/base.js');
?>