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

$this->p_tiempo = '
<p id="CGCHAT_tiempo_p" style="display:none">
	<span id="last">'.Text::_("COM_CGCHAT_LAST").'</span>
	<span id="CGCHAT_hace">'.Text::_("COM_CGCHAT_HACE").'</span>
	<span id="CGCHAT_tiempoK"></span>
	<span id="CGCHAT_ago">'.Text::_("COM_CGCHAT_AGO").'</span>
</p>';

?>

<div class="CGCHAT_div" id="CGCHAT_div"<?php if (Factory::getApplication()->getInput()->get('tmpl') == "component") echo ' style="padding:10px"'; ?>>
	<form id="kideForm" name="kideForm" method="post" onsubmit="return false" action="">
		<?php 	
		if ($this->user->can_read) {
			$this->display("botones");
            if ($this->show_sessions) {
                echo '<div id="CGCHAT_users_top">';
                echo '<div id="CGCHAT_users"></div>';
                echo '</div>';
            }
			$this->display("msgs");
			$this->display("msgs_private");
			$this->display("mostrar");
		}
		$this->display("form");  
		?>
	</form>
	<span id="CGCHAT_msg_sound"></span>
</div>

<?php $this->display("extra"); ?>

<?php if ($this->user->can_read) : ?>
<script type="text/javascript">
<!--
cgchat.onLoad(function() {
	cgchat.$('CGCHAT_msgs').onmousedown = function() { cgchat.scrolling = true };
	cgchat.$('CGCHAT_msgs').onmouseup = function() { cgchat.scrolling = false };
	if (cgchat.$('privado_full_x')) {
		cgchat.$('privado_full_x').onmousedown = function() { cgchat.scrolling_privados = true };
		cgchat.$('privado_full_x').onmouseup = function() { cgchat.scrolling_privados = false };
	}
	<?php if ($this->autoiniciar) : ?>
	cgchat.iniciar();
	<?php else : ?>
	cgchat.$("CGCHAT_div").onmouseover = function() {
		cgchat.iniciar();
		cgchat.$("CGCHAT_div").onmouseover = '';
	};
	<?php endif; ?>
	cgchat.tiempo(cgchat.last_time);
	cgchat.ajustar_scroll();
});
//-->
</script>
<?php endif; ?>