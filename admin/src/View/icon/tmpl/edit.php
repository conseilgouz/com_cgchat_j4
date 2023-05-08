<?php
/*
 * @component Kide Shoutbox
 * @copyright Copyright (C) 2013 - JoniJnm.es
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'icono.cancel' || document.formvalidator.isValid(document.id('kide-form'))) {
			Joomla.submitform(task, document.getElementById('kide-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
	function kide_show_img(img) {
		document.getElementById('kide_imagen').src = "<?php echo JURI::root().'components/com_kide/templates/default/images/icons/'; ?>"+img;
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_kide&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="kide-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::sprintf('COM_CGCHAT_EDIT_ICONO'); ?></legend>
			<ul class="adminformlist">
			<li><?php echo $this->form->getLabel('code'); ?>
			<?php echo $this->form->getInput('code'); ?></li>
			
			<li><?php echo $this->form->getLabel('img'); ?>
			<?php echo $this->imagenes; ?></li>
			
			<li><?php echo $this->form->getLabel('ordering'); ?>
			<?php echo $this->form->getInput('ordering'); ?></li>
			</ul>
		</fieldset>
		<br />
		<?php echo JText::_("COM_CGCHAT_ADD_IMAGES"); ?>
	</div>
	<div class="width-40 fltrt">
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	<div class="clr"></div>
</form>