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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatLinks;
use ConseilGouz\Component\CGChat\Site\Helper\CGChatHelper;

$return = Factory::getApplication()->getInput()->get('return');

$params = ComponentHelper::getParams('com_cgchat');

?>

<div style="font-weight: bold; padding-bottom: 15px"><a href="<?php echo base64_decode($return); ?>"><?php echo Text::_("COM_CGCHAT_BACK"); ?></a></div>

<div align="center"><?php echo $this->pags; ?></div>

<table width="100%" border="1">
	<?php foreach ($this->msgs as $r) : ?>
	<tr>
		<td class="CGCHAT_history_td"><?php echo gmdate($this->fecha, $r->time + $this->user->gmt * 3600); ?></td>
		<td class="CGCHAT_history_td">
			<?php $url = CGChatLinks::getUserLink($r->userid); ?>
			<?php if ($url) : ?>
			<a href="<?php echo $url; ?>">
			<?php endif; ?>
				<span class="<?php echo CGChatHelper::getRow($r->row, 'CGCHAT_'); ?>">
					<?php echo $r->name; ?>
				</span>
             <?php
                if (($params->get('countryinfo') > 0) && ($params->get('flag', 0) == 1)) { // flags on session ?
                    echo HTMLHelper::_('image', 'com_cgchat/' . strtolower($r->country) . '.png', $r->country, "title=$r->country", true);
                }
                ?>
			<?php if ($url) : ?>
			</a>
			<?php endif; ?>
		</td>
		<td <?php echo $r->color ? 'style="color:#'.$r->color.'"' : 'class="'.CGChatHelper::getRow($r->row, 'CGCHAT_dc_').'"'; ?>>
			<?php echo str_replace('\"', '"', $r->text); ?>
		</td>
	</tr>
	<?php endforeach; ?>
</table>

<div align="center"><?php echo $this->pags; ?></div>

<div style="font-weight: bold; padding-top: 15px"><a href="<?php echo base64_decode($return); ?>"><?php echo Text::_("COM_CGCHAT_BACK"); ?></a></div>
