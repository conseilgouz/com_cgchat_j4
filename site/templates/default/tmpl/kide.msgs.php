<?php 
/*
 * @component Kide Shoutbox
 * @copyright Copyright (C) 2013 - JoniJnm.es
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die(); 

$p_tiempo = '
<p id="KIDE_tiempo_p" style="display:none">
	<span id="last">'.JText::_("COM_CGCHAT_LAST").'</span>
	<span id="KIDE_hace">'.JText::_("COM_CGCHAT_HACE").'</span>
	<span id="KIDE_tiempoK"></span>
	<span id="KIDE_ago">'.JText::_("COM_CGCHAT_AGO").'</span>
</p>';
?>
<?php if ($this->show_sessions) : ?>
<div id="KIDE_usuarios_top">
	<div id="KIDE_usuarios"></div>
</div>
<?php endif; ?>
<div id="KIDE_msgs">
	<?php echo $this->order=='top'?$p_tiempo:$this->copy; ?>
	<div id="KIDE_output">
		<?php		
		if (!count($this->msgs))
			echo '<span></span>';
		else {
			foreach ($this->msgs as $r) {													
				$tiempo = gmdate($this->fecha, $r->time + $this->user->gmt*3600);
				echo '<div id="KIDE_id_'.$r->id.'" class="KIDE_msg_top">';
				if ($this->show_hour) echo '<span class="KIDE_msg_hour">'.gmdate($this->formato_hora, $r->time + $this->user->gmt*3600).'</span> ';
				if ($r->img && $this->show_avatar) {
					$style = $this->avatar_maxheight ? 'style="max-height:'.$this->avatar_maxheight.'" ' : '';
					echo '<img '.$style.'src="'.$r->img.'" class="KIDE_icono" alt="" /> ';
				}
				echo '<span style="cursor: pointer" title="'.$tiempo.'" onclick="kide.mensaje(\''.addslashes($r->name).'\','.$r->userid.','.$r->id.',\''.$r->url.'\',\''.$tiempo.'\',\''.$r->sesion.'\','.$r->rango.',\''.$r->img.'\')" class="'.KideHelper::getRango($r->rango, 'KIDE_').'">';
				echo $r->name;
				echo "</span>"; 
				$c = $r->color === '' ? 'class="'.KideHelper::getRango($r->rango, 'KIDE_dc_').' KIDE_msg"' : 'style="color:#'.$r->color.'"';
				echo ': <span '.$c.'>'.$r->text.'</span></div>'; 	
			} 
		}
		?>
	</div>
	<?php echo $this->order=='top'?$this->copy:$p_tiempo; ?>
</div>