<?php
/*
 * @component Kide Shoutbox
 * @copyright Copyright (C) 2013 - JoniJnm.es
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die(); 

if ($this->user->rango == 4) {
	echo "<br />".str_replace("%s", gmdate($this->fecha, $this->user->bantime + $this->user->gmt*3600), JText::_("COM_CGCHAT_BANNED")); 
}
elseif ($this->user->rango == 3 && !$this->user->can_write) {
	$l = kideLinks::getLoginURL();
	$r = kideLinks::getRegisterURL();
	echo "<br />". str_replace("%s1", $r, str_replace("%s2", $l, JText::_("COM_CGCHAT_CHAT_PARA_SOLO_REGISTRADOS")));
}
else {
?>
	<?php 
	if (!$this->user->captcha) {
		echo '<div id="KIDE_catpcha">';
		echo recaptcha_get_html($this->recaptcha_public);
		echo '<br /><button onclick="kide.captcha.check()">'.JText::_('COM_CGCHAT_CAPTCHA_VALIDATE').'</button>';
		echo '</div>';
	}
	?>
	<div id="KIDE_form"<?php if (!$this->user->captcha) echo ' style="display:none"'; ?>>
		<br />
		<div>
			<?php echo JText::_("COM_CGCHAT_NOMBRE"); ?>: 
			<?php if ($this->user->id) : ?>
			<em id="KIDE_my_name"><?php echo stripslashes($this->user->name); ?></em>
			<?php else : ?>
			<input maxlength="20" size="15" type="text" name="KIDE_nuevo_nick" onkeyup="return kide.change_name_keyup(event, this)" onblur="kide.change_name(this)" value="<?php echo stripslashes($this->user->name); ?>" />
			<?php endif; ?>
		</div>
		
		<div><?php echo JText::_("COM_CGCHAT_MENSAJE"); ?>: <img style="display:none" id="KIDE_img_ajax" alt="<?php echo JText::_("COM_CGCHAT_LOADING"); ?>" src="<?php echo $this->include_html("otras", "ajax.gif"); ?>" class="KIDE_icono"/></div>
		<textarea <?php echo $this->maxlength; ?> class="<?php echo KideHelper::getRango($this->user->rango, 'KIDE_dc_'); ?>" id="KIDE_txt" cols="50" rows="4" name="txt" onkeypress="return kide.pressedEnter(event, false)" onkeydown="kide.check_shift(event, false, false)" onkeyup="kide.check_shift(event, true, false)"></textarea>
		<?php if ($this->button_send) : ?>
		<br /><button id="KIDE_button_send" onclick="kide.sm()"><?php echo JText::_("COM_CGCHAT_SEND"); ?></button>
		<?php endif; ?>
		<br /><br />
		<div id="KIDE_iconos" style="display:<?php echo $this->user->icons_hidden ? 'none' : 'block'; ?>">
			<?php echo kideHelper::smilies_html($this->com).kideHelper::moreSmileys($this->com); ?>
		</div>
	</div>
<?php 
}
?>
