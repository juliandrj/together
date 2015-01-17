<?php defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form">
<?php if ($params->get('greeting')) : ?>
	<?php if ($params->get('name') == 0) : ?>
		<?php echo JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('name'))); ?>
	<?php else : ?>
	 	<?php echo JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('username'))); ?>
	<?php endif; ?>
	<button type="submit" class="btn btn-danger">
		<?php echo JText::_('JLOGOUT'); ?>
	</button>
<?php endif; ?>
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.logout" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>