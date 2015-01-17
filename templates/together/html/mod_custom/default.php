<?php defined('_JEXEC') or die; ?>
<?php if ($moduleclass_sfx) : ?>
<div class="<?php echo $moduleclass_sfx; ?>">
<?php endif; ?>
<?php echo $module->content;?>
<?php if ($moduleclass_sfx) : ?>
</div>
<?php endif; ?>