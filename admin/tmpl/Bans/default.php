<?php
/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : Kide ShoutBox
*/
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.multiselect');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'a.ordering';
?>

<form action="<?php echo Route::_('index.php?option=com_cgchat&view=bans'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="<?php if (version_compare(JVERSION, '3.0', '>=')) {
					    echo 'Joomla.';
					} ?>checkAll(this)" />
				</th>
				<th>
					<?php echo HTMLHelper::_('grid.sort', 'IP', 'a.ip', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo HTMLHelper::_('grid.sort', 'COM_CGCHAT_SESION', 'a.sesion', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo HTMLHelper::_('grid.sort', 'COM_CGCHAT_BAN_TIME', 'a.time', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
		    ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php echo $item->ip; ?>
				</td>
				<td>
					<?php echo $item->session; ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('date', $item->time); ?>
				</td>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
