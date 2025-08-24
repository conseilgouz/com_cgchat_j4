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
use Joomla\CMS\Uri\Uri;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

?>
<script type="text/javascript">
	function chat_show_img(img) {
		document.getElementById('cgchat_image').src = "<?php echo URI::root().'media/com_cgchat/templates/default/images/icons/'; ?>"+img;
	}
</script>

<form action="<?php echo Route::_('index.php?option=com_cgchat&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="kide-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo Text::sprintf('COM_CGCHAT_EDIT_ICONO'); ?></legend>
			<ul class="adminformlist">
			<li><?php echo $this->form->getLabel('code'); ?>
			<?php echo $this->form->getInput('code'); ?></li>
			
			<li><?php echo $this->form->getLabel('img'); ?>
			<?php echo $this->images; ?></li>
			
			<li><?php echo $this->form->getLabel('ordering'); ?>
			<?php echo $this->form->getInput('ordering'); ?></li>
			</ul>
		</fieldset>
		<br />
		<?php echo Text::_("COM_CGCHAT_ADD_IMAGES"); ?>
	</div>
	<div class="width-40 fltrt">
		<input type="hidden" name="task" value="" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
	<div class="clr"></div>
</form>