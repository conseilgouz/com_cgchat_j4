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

<div id="KIDE_opciones" class="KIDE_mostrar" style="display: none">
	<div><?php echo Text::_("COM_CGCHAT_OCULTAR_SESION"); ?> <input type="checkbox" value="1" name="private_session" id="private_session" <?php if ($this->user->private_session) echo 'checked="checked" '; ?> style="vertical-align:middle" /></div>
	<div><?php echo Text::_('COM_CGCHAT_TEMPLATE'); ?>: <?php echo $this->templates; ?></div>
	<div id="KIDE_opciones_colores"></div>
	<br />
	<button onclick="cgchat.save_options()"><?php echo Text::_("COM_CGCHAT_SAVE"); ?></button> <button onclick="cgchat.retardo_input()"><?php echo JText::_("COM_CGCHAT_RETARDO_INPUT"); ?></button>
</div>

<div id="KIDE_mensaje" class="KIDE_mostrar" style="display: none">
	<table width="100%">
		<tr>
			<td>
				· <span id="KIDE_mensaje_username"></span>
				<br />
				· <span id="KIDE_tiempo_msg"></span>
				<span id="KIDE_mensaje_perfil_span">
					<br />
					· <a target="_blank" id="KIDE_mensaje_perfil" href="javascript:void(0)"><?php echo Text::_("COM_CGCHAT_VERPERFIL"); ?></a>
				</span>
				<span id="KIDE_mensaje_borrar_span">
					<br />
					· <a id="KIDE_mensaje_borrar" href="javascript:void(0)"><?php echo Text::_("COM_CGCHAT_BORRARMENSAJE"); ?></a>
				</span>
				<span id="KIDE_mensaje_ocultar_span">
					<br />
					· <a id="KIDE_mensaje_ocultar" href="javascript:void(0)"><?php echo Text::_("COM_CGCHAT_HIDE_MESSAGE"); ?></a>
				</span>
				<?php if ($this->user->row == 1) : ?>
				<span id="KIDE_mensaje_banear_span1">
					<br />
					· <a href="javascript:cgchat.show('KIDE_mensaje_banear_span')"><?php echo Text::_("COM_CGCHAT_MENSAJE_BANEAR"); ?></a>
					<span id="KIDE_mensaje_banear_span" style="display: none">
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
						<button style="padding:0" id="KIDE_mensaje_banear"><?php echo Text::_("COM_CGCHAT_MENSAJE_BANEAR_MIN"); ?></button>
					</span>
				</span>
				<?php endif; ?>
				<br />
				<a href="" id="KIDE_mensaje_img_enlace"><img style="border:0" id="KIDE_mensaje_img" src="<?php echo $this->include_html('otras', 'blank.png'); ?>" alt="" class="KIDE_avatar" /></a>
			</td>
			<td style="text-align: right; vertical-align: top">
				<a href="javascript:cgchat.show('KIDE_mensaje',false)" class="KIDE_cerrar_x">X</a>
			</td>
		</tr>
	</table>
</div>

<div id="KIDE_usuario" class="KIDE_mostrar" style="display: none">
	<table width="100%">
		<tr>
			<td>
				· <span id="KIDE_usuario_name"></span>
				<span id="KIDE_usuario_perfil_mostrar">
					<br />
					· <a target="_blank" id="KIDE_usuario_perfil" href="javascript:void(0)"><?php echo Text::_("COM_CGCHAT_VERPERFIL"); ?></a>
				</span>
				<?php if ($this->user->row == 1) : ?>
				<span id="KIDE_usuario_banear_span1">
					<br />
					· <a href="javascript:cgchat.show('KIDE_usuario_banear_span')"><?php echo Text::_("COM_CGCHAT_MENSAJE_BANEAR"); ?></a>
					<span id="KIDE_usuario_banear_span" style="display: none">
						<select name="kide_usuario_banear_dias" style="padding:0">
							<option value="0"><?php echo ucfirst(JText::_("COM_CGCHAT_DAYS")); ?></option>
							<?php echo CGChatHelper::opciones(15); ?>
						</select>
						<select name="kide_usuario_banear_horas" style="padding:0">
							<option value="0"><?php echo ucfirst(JText::_("COM_CGCHAT_HOURS")); ?></option>
							<?php echo CGChatHelper::opciones(24); ?>
						</select>
						<select name="kide_usuario_banear_minutos" style="padding:0">
							<option value="0"><?php echo ucfirst(JText::_("COM_CGCHAT_MINUTES")); ?></option>
							<?php echo CGChatHelper::opciones(60); ?>
						</select>
						<button style="padding:0" id="KIDE_usuario_banear"><?php echo JText::_("COM_CGCHAT_MENSAJE_BANEAR_MIN"); ?></button>
					</span>
				</span>
				<?php endif; ?>
				<br />
				<a href="" id="KIDE_usuario_img_enlace"><img style="border:0" id="KIDE_usuario_img" src="<?php echo $this->include_html('otras', 'blank.png'); ?>" alt="" class="KIDE_avatar" /></a>
			</td>
			<td style="text-align: right; vertical-align: top">
				<a href="javascript:cgchat.show('KIDE_usuario',false)" class="KIDE_cerrar_x">X</a>
			</td>
		</tr>
	</table>
</div>
	
<div id="KIDE_rangos" class="KIDE_mostrar" style="display: none">
	<?php echo JText::_("COM_CGCHAT_RANGOS"); ?>: <br />
	<img class="KIDE_r KIDE_bg_admin" src="<?php echo $this->include_html("otras", "blank.png"); ?>" alt="" /> &nbsp; <?php echo Text::_("COM_CGCHAT_ADMINISTRADOR"); ?><br />
	<img class="KIDE_r KIDE_bg_registered" src="<?php echo $this->include_html("otras", "blank.png"); ?>" alt="" /> &nbsp; <?PHP echo Text::_("COM_CGCHAT_REGISTRADO") ;?><br />
	<img class="KIDE_r KIDE_bg_guest" src="<?php echo $this->include_html("otras", "blank.png"); ?>" alt="" /> &nbsp; <?php echo Text::_("COM_CGCHAT_INVITADO"); ?><br />
	<img class="KIDE_r KIDE_bg_special" src="<?php echo $this->include_html("otras", "blank.png"); ?>" alt="" /> &nbsp; <?php echo Text::_("COM_CGCHAT_ESPECIAL"); ?><br />
</div>