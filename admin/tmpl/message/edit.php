<?php
/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : Kide ShoutBox
*/
// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

?>
<form action="<?php echo Route::_('index.php?option=com_cgchat&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="kide-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo Text::sprintf('COM_CGCHAT_EDIT_MESSAGE'); ?></legend>
			<ul class="adminformlist">
			<li><?php echo $this->form->getLabel('name'); ?>
			<?php echo $this->form->getInput('name'); ?></li>
			
			<li><?php echo $this->form->getLabel('text'); ?>
			<?php echo $this->form->getInput('text'); ?></li>
			
			<li><?php echo $this->form->getLabel('name'); ?>
			<?php echo $this->form->getInput('name'); ?></li>
			
			<li><?php echo $this->form->getLabel('userid'); ?>
			<?php echo $this->form->getInput('userid'); ?></li>
			
			<li><?php echo $this->form->getLabel('row'); ?>
			<?php echo $this->form->getInput('row'); ?></li>

			<li><?php echo $this->form->getLabel('color'); ?>
			<?php echo $this->form->getInput('color'); ?></li>
			
			<li><?php echo $this->form->getLabel('img'); ?>
			<?php echo $this->form->getInput('img'); ?></li>
			
			<li><?php echo $this->form->getLabel('url'); ?>
			<?php echo $this->form->getInput('url'); ?></li>
			
			<li><?php echo $this->form->getLabel('time'); ?>
			<?php echo $this->form->getInput('time'); ?></li>
			
			<li><?php echo $this->form->getLabel('token'); ?>
			<?php echo $this->form->getInput('token'); ?></li>
			
			<li><?php echo $this->form->getLabel('ip'); ?>
			<?php echo $this->form->getInput('ip'); ?></li>

		</fieldset>
	</div>
	<div class="width-40 fltrt">
		<input type="hidden" name="task" value="" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
	<div class="clr"></div>
</form>