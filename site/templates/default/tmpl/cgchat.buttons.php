<?php
/**
* CG Chat Component  - Joomla 4.x Component
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2023 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From Kide ShoutBox
*/
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$uri = Uri::getInstance();
$url = Factory::getApplication()->getInput()->get('tmpl') == "component" ? Route::_("index.php?option=com_cgchat&tmpl=component") : 'javascript:void(0)';
$onclick = Factory::getApplication()->getInput()->get('tmpl') == "component" ? '' : ' onclick="cgchat.open_popup()"';
?>

<div id="CGCHAT_buttons">
	<img alt="<?php echo Text::_("COM_CGCHAT_TURN"); ?>" id="starting" src="<?php echo $this->include_html("buttons", "starting_0.gif"); ?>" />
	<?php if ($this->user->sound != -1) : ?><a title="<?php echo Text::_("COM_CGCHAT_SOUND"); ?>" href="javascript:cgchat.sonido()"><img id="sound" alt="<?php echo Text::_("COM_CGCHAT_SOUND"); ?>" src="<?php echo $this->include_html("buttons", "sound_".($this->user->sound ? "on" : "off").".png"); ?>" /></a><?php endif; ?>
	<?php if ($this->user->can_write) : ?><a title="<?php echo Text::_("COM_CGCHAT_OPTIONS"); ?>" href="javascript:cgchat.mostrar_opciones()"><img alt="<?php echo Text::_("COM_CGCHAT_OPTIONS"); ?>" src="<?php echo $this->include_html("buttons", "tools.png"); ?>" /></a><?php endif; ?>
	<a title="<?php echo Text::_("COM_CGCHAT_ICONOS"); ?>" href="javascript:cgchat.mostrar_iconos()"><img alt="<?php echo Text::_("COM_CGCHAT_ICONOS"); ?>" src="<?php echo $this->include_html("buttons", "iconos.png"); ?>" /></a>
	<a title="<?php echo Text::_("COM_CGCHAT_HISTORY"); ?>" href="<?php echo Route::_('index.php?option=com_cgchat&view=history&page=1&return='.base64_encode($uri)); ?>"><img alt="<?php echo Text::_("COM_CGCHAT_HISTORY"); ?>" src="<?php echo $this->include_html("buttons", "history.png"); ?>" /></a>
	<a title="<?php echo Text::_("COM_CGCHAT_FAQ"); ?>" href="javascript:cgchat.show('CGCHAT_rangos')"><img alt="<?php echo Text::_("COM_CGCHAT_FAQ"); ?>" src="<?php echo $this->include_html("buttons", "faq.png"); ?>" /></a>
	<a title="CG Chat"<?php echo $onclick; ?> href="<?php echo $url; ?>"><img alt="CG Chat" src="<?php echo $this->include_html("buttons", "chat.png"); ?>" /></a>
	<a id="module_width" title="?php echo Text::_('COM_CGCHAT_MODULE_WIDTH');?>" href="javascript:cgchat.module_width()" style="display:none" class="left-button"></a>
    <a href="javascript:cgchat.close_private()" title="<?php echo Text::_('COM_CGCHAT_CLOSE_PRIVATE');?>"><span id="private_txt" style="display:none"><?php echo Text::_('COM_CGCHAT_PRIVATE');?></span></a>

</div>