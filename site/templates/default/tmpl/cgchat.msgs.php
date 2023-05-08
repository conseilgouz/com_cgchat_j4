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

$p_tiempo = '
<p id="KIDE_tiempo_p" style="display:none">
	<span id="last">'.Text::_("COM_CGCHAT_LAST").'</span>
	<span id="KIDE_hace">'.Text::_("COM_CGCHAT_HACE").'</span>
	<span id="KIDE_tiempoK"></span>
	<span id="KIDE_ago">'.Text::_("COM_CGCHAT_AGO").'</span>
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
				echo '<span style="cursor: pointer" title="'.$tiempo.'" onclick="cgchat.mensaje(\''.addslashes($r->name).'\','.$r->userid.','.$r->id.',\''.$r->url.'\',\''.$tiempo.'\',\''.$r->session.'\','.$r->row.',\''.$r->img.'\')" class="'.CGChatHelper::getRow($r->row, 'KIDE_').'">';
				echo $r->name;
				echo "</span>"; 
				$c = $r->color === '' ? 'class="'.CGChatHelper::getRow($r->row, 'KIDE_dc_').' KIDE_msg"' : 'style="color:#'.$r->color.'"';
				echo ': <span '.$c.'>'.$r->text.'</span></div>'; 	
			} 
		}
		?>
	</div>
	<?php echo $this->order=='top'?$this->copy:$p_tiempo; ?>
</div>