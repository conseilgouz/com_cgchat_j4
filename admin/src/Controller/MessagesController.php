<?php
/**
* CG Chat Component  - Joomla 4.x Component 
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2023 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From              : Kide ShoutBox
*/
namespace ConseilGouz\Component\CGChat\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\String\StringHelper;

class MessagesController extends AdminController
{
	public function getModel($name = 'Message', $prefix = 'Administrator', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}