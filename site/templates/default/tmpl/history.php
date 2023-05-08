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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatLinks;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatHelper;
?>

<div style="font-weight: bold; padding-bottom: 15px"><a href="<?php echo Route::_('index.php?option=com_cgchat'."&view=cgchat"); ?>"><?php echo Text::_("COM_CGCHAT_VOLVER"); ?></a></div>

<div align="center"><?php echo $this->pags; ?></div>

<table width="100%" border="1">
	<?php foreach ($this->msgs as $r) : ?>
	<tr>
		<td class="KIDE_history_td"><?php echo gmdate($this->fecha, $r->time + $this->user->gmt*3600); ?></td>
		<td class="KIDE_history_td">
			<?php $url = CGChatLinks::getUserLink($r->userid); ?>
			<?php if ($url) : ?>
			<a href="<?php echo $url; ?>">
			<?php endif; ?>
				<span class="<?php echo CGChatHelper::getRow($r->row, 'KIDE_'); ?>">
					<?php echo $r->name; ?>
				</span>
			<?php if ($url) : ?>
			</a>
			<?php endif; ?>
		</td>
		<td <?php echo $r->color ? 'style="color:#'.$r->color.'"' : 'class="'.CGChatHelper::getRow($r->row, 'KIDE_dc_').'"'; ?>>
			<?php echo $r->text; ?>
		</td>
	</tr>
	<?php endforeach; ?>
</table>

<div align="center"><?php echo $this->pags; ?></div>

<div style="font-weight: bold; padding-top: 15px"><a href="<?php echo Route::_('index.php?option=com_cgchat'."&view=cgchat"); ?>"><?php echo Text::_("COM_CGCHAT_VOLVER"); ?></a></div>
