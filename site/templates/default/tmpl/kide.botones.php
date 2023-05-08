<?php 
/*
 * @component Kide Shoutbox
 * @copyright Copyright (C) 2013 - JoniJnm.es
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die(); 

$url = JRequest::getCmd('tmpl')=="component" ? JRoute::_(KIDE_URL.'&view=kide&tmpl=component') : 'javascript:void(0)';
$onclick = JRequest::getCmd('tmpl')=="component" ? '' : ' onclick="kide.open_popup()"';
?>

<div id="KIDE_botones">
	<img alt="<?php echo JText::_("COM_KIDE_TURN"); ?>" id="encendido" src="<?php echo $this->include_html("botones", "encendido_0.gif"); ?>" />
	<?php if ($this->user->sound != -1) : ?><a title="<?php echo JText::_("COM_KIDE_SOUND"); ?>" href="javascript:kide.sonido()"><img id="sound" alt="<?php echo JText::_("COM_KIDE_SOUND"); ?>" src="<?php echo $this->include_html("botones", "sound_".($this->user->sound ? "on" : "off").".png"); ?>" /></a><?php endif; ?>
	<?php if ($this->user->can_write) : ?><a title="<?php echo JText::_("COM_KIDE_OPTIONS"); ?>" href="javascript:kide.mostrar_opciones()"><img alt="<?php echo JText::_("COM_KIDE_OPTIONS"); ?>" src="<?php echo $this->include_html("botones", "tools.png"); ?>" /></a><?php endif; ?>
	<a title="<?php echo JText::_("COM_KIDE_ICONOS"); ?>" href="javascript:kide.mostrar_iconos()"><img alt="<?php echo JText::_("COM_KIDE_ICONOS"); ?>" src="<?php echo $this->include_html("botones", "iconos.png"); ?>" /></a>
	<a title="<?php echo JText::_("COM_KIDE_HISTORY"); ?>" href="<?php echo JRoute::_(KIDE_URL_HISTORY.'&page=1'); ?>"><img alt="<?php echo JText::_("COM_KIDE_HISTORY"); ?>" src="<?php echo $this->include_html("botones", "history.png"); ?>" /></a>
	<a title="<?php echo JText::_("COM_KIDE_FAQ"); ?>" href="javascript:kide.show('KIDE_rangos')"><img alt="<?php echo JText::_("COM_KIDE_FAQ"); ?>" src="<?php echo $this->include_html("botones", "faq.png"); ?>" /></a>
	<a title="Kide Chat"<?php echo $onclick; ?> href="<?php echo $url; ?>"><img alt="Kide Chat" src="<?php echo $this->include_html("botones", "chat.png"); ?>" /></a>
</div>