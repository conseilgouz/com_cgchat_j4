<?php
/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : Kide ShoutBox
*/
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;

$comfield	= 'media/com_cgchat/';

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();

// $wa->registerAndUseStyle('iso',$comfield.'css/isotope.css');
$wa->registerAndUseScript('cgchat', $comfield.'js/base.js');
