<?php 
/**
* @Copyright Copyright (C) 2013 - JoniJnm.es
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

defined('_JEXEC') or die(); 

?>

<div class="KIDE_div" id="KIDE_div"<?php if (JRequest::getCmd('tmpl') == "component") echo ' style="padding:10px"'; ?>>
	<form id="kideForm" name="kideForm" method="post" onsubmit="return false" action="">
		<?php 	
		if ($this->user->can_read) {
			$this->display("botones");
			$this->display("msgs");
			$this->display("mostrar");
		}
		$this->display("form");  
		?>
	</form>
	<span id="KIDE_msg_sound"></span>
</div>

<?php $this->display("extra"); ?>

<?php if ($this->user->can_read) : ?>
<script type="text/javascript">
<!--
kide.onLoad(function() {
	kide.$('KIDE_msgs').onmousedown = function() { kide.scrolling = true };
	kide.$('KIDE_msgs').onmouseup = function() { kide.scrolling = false };
	if (kide.$('privado_full_x')) {
		kide.$('privado_full_x').onmousedown = function() { kide.scrolling_privados = true };
		kide.$('privado_full_x').onmouseup = function() { kide.scrolling_privados = false };
	}
	<?php if ($this->autoiniciar) : ?>
	kide.iniciar();
	<?php else : ?>
	kide.$("KIDE_div").onmouseover = function() {
		kide.iniciar();
		kide.$("KIDE_div").onmouseover = '';
	};
	<?php endif; ?>
	kide.tiempo(kide.last_time);
	kide.ajustar_scroll();
});
//-->
</script>
<?php endif; ?>