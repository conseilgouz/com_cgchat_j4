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
<div id="CGCHAT_msgs">
	<?php echo $this->order=='top'?$this->n_tiempo:$this->copy; ?>
	<div id="CGCHAT_output">
		<?php		
		if (!count($this->msgs))
			echo '<span></span>';
		else {
			foreach ($this->msgs as $r) {
				$tiempo = gmdate($this->fecha, $r->time + $this->user->gmt*3600);
				echo '<div id="CGCHAT_id_'.$r->id.'" class="CGCHAT_msg_top">';
				if ($this->show_hour) echo '<span class="CGCHAT_msg_hour">'.gmdate($this->formato_hora, $r->time + $this->user->gmt*3600).'</span> ';
				if ($r->img && $this->show_avatar) {
					$style = $this->avatar_maxheight ? 'style="max-height:'.$this->avatar_maxheight.'" ' : '';
					echo '<img '.$style.'src="'.$r->img.'" class="CGCHAT_icono" alt="" /> ';
				}
				echo '<span style="cursor: pointer" title="'.$tiempo.'" onclick="cgchat.mensaje(\''.addslashes($r->name).'\',\''.$r->country.'\','.$r->userid.','.$r->id.',\''.$r->url.'\',\''.$tiempo.'\',\''.$r->session.'\','.$r->row.',\''.$r->img.'\')" class="'.CGChatHelper::getRow($r->row, 'CGCHAT_').'">';
				echo $r->name;
                echo ($r->country) ? $r->country : '';
				echo "</span>"; 
				$c = $r->color === '' ? 'class="'.CGChatHelper::getRow($r->row, 'CGCHAT_dc_').' CGCHAT_msg"' : 'style="color:#'.$r->color.'"';
				echo ': <span '.$c.'>'.str_replace('\"','"',$r->text).'</span></div>'; 	
			} 
		}
		?>
	</div>
	<?php echo $this->order=='top'?$this->copy:$this->n_tiempo; ?>
</div>