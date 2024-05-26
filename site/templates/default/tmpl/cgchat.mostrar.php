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
?>

<div id="CGCHAT_opciones" class="CGCHAT_mostrar" style="display: none">
	<div><?php echo Text::_("COM_CGCHAT_HIDE_SESSION"); ?> <input type="checkbox" value="1" name="hidden_session" id="hidden_session" <?php if ($this->user->hidden_session) echo 'checked="checked" '; ?> style="vertical-align:middle" /></div>
	<div><?php echo Text::_('COM_CGCHAT_TEMPLATE'); ?>: <?php echo $this->templates; ?></div>
	<div id="CGCHAT_opciones_colores"></div>
	<br />
	<button onclick="cgchat.save_options()"><?php echo Text::_("COM_CGCHAT_SAVE"); ?></button> <button onclick="cgchat.retardo_input()"><?php echo Text::_("COM_CGCHAT_RETARDO_INPUT"); ?></button>
</div>

<div id="CGCHAT_mensaje" class="CGCHAT_mostrar" style="display: none">
	<table width="100%">
		<tr>
			<td>
				· <span id="CGCHAT_mensaje_username"></span>
				<br />
				· <span id="CGCHAT_tiempo_msg"></span>
				<span id="CGCHAT_mensaje_profil_span">
					<br />
					· <a target="_blank" id="CGCHAT_mensaje_profil" href="javascript:void(0)"><?php echo Text::_("COM_CGCHAT_VERPROFIL"); ?></a>
				</span>
				<span id="CGCHAT_mensaje_borrar_span">
					<br />
					· <a id="CGCHAT_mensaje_borrar" href="javascript:void(0)"><?php echo Text::_("COM_CGCHAT_REMOVEMESSAGE"); ?></a>
				</span>
				<span id="CGCHAT_mensaje_ocultar_span">
					<br />
					· <a id="CGCHAT_mensaje_ocultar" href="javascript:void(0)"><?php echo Text::_("COM_CGCHAT_HIDE_MESSAGE"); ?></a>
				</span>
				<?php if ($this->user->row == 1) : ?>
				<span id="CGCHAT_mensaje_banear_span1">
					<br />
					· <a href="javascript:cgchat.show('CGCHAT_mensaje_banear_span')"><?php echo Text::_("COM_CGCHAT_MESSAGE_BAN"); ?></a>
					<span id="CGCHAT_mensaje_banear_span" style="display: none">
						<select name="kide_mensaje_banear_dias" style="padding:0">
							<option value="0"><?php echo ucfirst(Text::_("COM_CGCHAT_DAYS")); ?></option>
							<?php echo CGChatHelper::opciones(15); ?>
						</select>
						<select name="kide_mensaje_banear_horas" style="padding:0">
							<option value="0"><?php echo ucfirst(Text::_("COM_CGCHAT_HOURS")); ?></option>
							<?php echo CGChatHelper::opciones(24); ?>
						</select>
						<select name="kide_mensaje_banear_minutos" style="padding:0">
							<option value="0"><?php echo ucfirst(Text::_("COM_CGCHAT_MINUTES")); ?></option>
							<?php echo CGChatHelper::opciones(60); ?>
						</select>
						<button style="padding:0" id="CGCHAT_mensaje_banear"><?php echo Text::_("COM_CGCHAT_MESSAGE_BAN_MIN"); ?></button>
					</span>
				</span>
				<?php endif; ?>
				<br />
				<a href="" id="CGCHAT_mensaje_img_enlace"><img style="border:0" id="CGCHAT_mensaje_img" src="<?php echo $this->include_html('otras', 'blank.png'); ?>" alt="" class="CGCHAT_avatar" /></a>
			</td>
			<td style="text-align: right; vertical-align: top">
				<a href="javascript:cgchat.show('CGCHAT_mensaje',false)" class="CGCHAT_cerrar_x">X</a>
			</td>
		</tr>
	</table>
</div>

<div id="CGCHAT_user" class="CGCHAT_mostrar" style="display: none">
	<table width="100%">
		<tr>
			<td>
				· <span id="CGCHAT_user_name"></span>
				<span id="CGCHAT_user_profil_mostrar">
					<br />
					· <a target="_blank" id="CGCHAT_user_profil" href="javascript:void(0)"><?php echo Text::_("COM_CGCHAT_VERPROFIL"); ?></a>
				</span>
				<?php if ($this->user->row == 1) : ?>
				<span id="CGCHAT_user_banear_span1">
					<br />
					· <a href="javascript:cgchat.show('CGCHAT_user_banear_span')"><?php echo Text::_("COM_CGCHAT_MESSAGE_BAN"); ?></a>
					<span id="CGCHAT_user_banear_span" style="display: none">
						<select name="kide_user_banear_dias" style="padding:0">
							<option value="0"><?php echo ucfirst(Text::_("COM_CGCHAT_DAYS")); ?></option>
							<?php echo CGChatHelper::opciones(15); ?>
						</select>
						<select name="kide_user_banear_horas" style="padding:0">
							<option value="0"><?php echo ucfirst(Text::_("COM_CGCHAT_HOURS")); ?></option>
							<?php echo CGChatHelper::opciones(24); ?>
						</select>
						<select name="kide_user_banear_minutos" style="padding:0">
							<option value="0"><?php echo ucfirst(Text::_("COM_CGCHAT_MINUTES")); ?></option>
							<?php echo CGChatHelper::opciones(60); ?>
						</select>
						<button style="padding:0" id="CGCHAT_user_banear"><?php echo Text::_("COM_CGCHAT_MESSAGE_BAN_MIN"); ?></button>
					</span>
				</span>
				<?php endif; ?>
				<br />
				<a href="" id="CGCHAT_user_img_enlace"><img style="border:0" id="CGCHAT_user_img" src="<?php echo $this->include_html('otras', 'blank.png'); ?>" alt="" class="CGCHAT_avatar" /></a>
			</td>
			<td style="text-align: right; vertical-align: top">
				<a href="javascript:cgchat.show('CGCHAT_user',false)" class="CGCHAT_cerrar_x">X</a>
			</td>
		</tr>
	</table>
</div>
	
<div id="CGCHAT_rangos" class="CGCHAT_mostrar" style="display: none">
	<?php echo Text::_("COM_CGCHAT_ROWS"); ?>: <br />
	<img class="CGCHAT_r CGCHAT_bg_admin" src="<?php echo $this->include_html("otras", "blank.png"); ?>" alt="" /> &nbsp; <?php echo Text::_("COM_CGCHAT_ADMINISTRADOR"); ?><br />
	<img class="CGCHAT_r CGCHAT_bg_registered" src="<?php echo $this->include_html("otras", "blank.png"); ?>" alt="" /> &nbsp; <?PHP echo Text::_("COM_CGCHAT_REGISTRADO") ;?><br />
	<img class="CGCHAT_r CGCHAT_bg_guest" src="<?php echo $this->include_html("otras", "blank.png"); ?>" alt="" /> &nbsp; <?php echo Text::_("COM_CGCHAT_INVITADO"); ?><br />
	<img class="CGCHAT_r CGCHAT_bg_special" src="<?php echo $this->include_html("otras", "blank.png"); ?>" alt="" /> &nbsp; <?php echo Text::_("COM_CGCHAT_ESPECIAL"); ?><br />
</div>