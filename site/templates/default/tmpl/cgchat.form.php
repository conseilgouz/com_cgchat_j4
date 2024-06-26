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
use Joomla\CMS\Language\Text;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatHelper;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatLinks;

if ($this->user->row == 3 && !$this->user->can_write) {
    $l = CGChatLinks::getLoginURL();
    $r = CGChatLinks::getRegisterURL();
    echo "<br />". str_replace("%s1", $r, str_replace("%s2", $l, Text::_("COM_CGCHAT_CHAT_PARA_SOLO_REGISTRADOS")));
} else {
    if ($this->user->row == 4) {
    echo "<div id='cgchat_banned'><br />".str_replace("%s", gmdate($this->fecha, $this->user->bantime + $this->user->gmt * 3600), Text::_("COM_CGCHAT_BANNED"))."</div>";
    echo '<div id="CGCHAT_form" style="display:none">';
    } else{ ?>
    <div id='cgchat_banned' style="display:none"><br /><?php echo Text::_("COM_CGCHAT_BANNED");?></div>
	<div id="CGCHAT_form" style="display:none">
    <?php } ?>
		<div>
			<?php echo Text::_("COM_CGCHAT_NAME"); ?>: 
			<em id="CGCHAT_my_name"><?php echo stripslashes($this->user->name); ?></em>
		</div>
		<div><?php echo Text::_("COM_CGCHAT_MESSAGE"); ?>: <img style="display:none" id="CGCHAT_img_ajax" alt="<?php echo Text::_("COM_CGCHAT_LOADING"); ?>" src="<?php echo $this->include_html("otras", "ajax.gif"); ?>" class="CGCHAT_icono"/></div>
		<textarea <?php echo $this->maxlength; ?> class="<?php echo CGChatHelper::getRow($this->user->row, 'CGCHAT_dc_'); ?>" id="CGCHAT_txt" cols="50" rows="4" name="txt" onkeypress="return cgchat.pressedEnter(event, false)" onkeydown="cgchat.check_shift(event, false, false)" onkeyup="cgchat.check_shift(event, true, false)"></textarea>
		<?php if ($this->button_send) : ?>
		<br /><button id="CGCHAT_button_send" onclick="cgchat.sm()"><?php echo Text::_("COM_CGCHAT_SEND"); ?></button>
		<?php endif; ?>
		<br />
		<div id="CGCHAT_iconos" style="display:<?php echo $this->user->icons_hidden ? 'none' : 'block'; ?>">
			<?php echo CGChatHelper::smilies_html($this->com).CGChatHelper::moreSmileys($this->com); ?>
		</div>
	</div>
<?php
}
?>
