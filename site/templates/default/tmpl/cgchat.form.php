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

if ($this->user->row == 4) {
	echo "<br />".str_replace("%s", gmdate($this->fecha, $this->user->bantime + $this->user->gmt*3600), Text::_("COM_CGCHAT_BANNED")); 
}
elseif ($this->user->row == 3 && !$this->user->can_write) {
	$l = CGChatLinks::getLoginURL();
	$r = CGChatLinks::getRegisterURL();
	echo "<br />". str_replace("%s1", $r, str_replace("%s2", $l, Text::_("COM_CGCHAT_CHAT_PARA_SOLO_REGISTRADOS")));
}
else {
?>
	<?php 
	if (!$this->user->captcha) {
		echo '<div id="KIDE_catpcha">';
		echo recaptcha_get_html($this->recaptcha_public);
		echo '<br /><button onclick="cgchat.captcha.check()">'.Text::_('COM_CGCHAT_CAPTCHA_VALIDATE').'</button>';
		echo '</div>';
	}
	?>
	<div id="KIDE_form"<?php if (!$this->user->captcha) echo ' style="display:none"'; ?>>
		<br />
		<div>
			<?php echo Text::_("COM_CGCHAT_NOMBRE"); ?>: 
			<?php if ($this->user->id) : ?>
			<em id="KIDE_my_name"><?php echo stripslashes($this->user->name); ?></em>
			<?php else : ?>
			<input maxlength="20" size="15" type="text" name="KIDE_nuevo_nick" onkeyup="return cgchat.change_name_keyup(event, this)" onblur="cgchat.change_name(this)" value="<?php echo stripslashes($this->user->name); ?>" />
			<?php endif; ?>
		</div>
		
		<div><?php echo JText::_("COM_CGCHAT_MENSAJE"); ?>: <img style="display:none" id="KIDE_img_ajax" alt="<?php echo JText::_("COM_CGCHAT_LOADING"); ?>" src="<?php echo $this->include_html("otras", "ajax.gif"); ?>" class="KIDE_icono"/></div>
		<textarea <?php echo $this->maxlength; ?> class="<?php echo CGChatHelper::getRow($this->user->row, 'KIDE_dc_'); ?>" id="KIDE_txt" cols="50" rows="4" name="txt" onkeypress="return cgchat.pressedEnter(event, false)" onkeydown="cgchat.check_shift(event, false, false)" onkeyup="cgchat.check_shift(event, true, false)"></textarea>
		<?php if ($this->button_send) : ?>
		<br /><button id="KIDE_button_send" onclick="cgchat.sm()"><?php echo Text::_("COM_CGCHAT_SEND"); ?></button>
		<?php endif; ?>
		<br /><br />
		<div id="KIDE_iconos" style="display:<?php echo $this->user->icons_hidden ? 'none' : 'block'; ?>">
			<?php echo CGChatHelper::smilies_html($this->com).CGChatHelper::moreSmileys($this->com); ?>
		</div>
	</div>
<?php 
}
?>
