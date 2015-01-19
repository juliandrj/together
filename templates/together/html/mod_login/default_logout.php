<?php defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="logout-form">
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.logout" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<script type="text/javascript">
$(document).ready(function() {
	$('#menu').append('<p class="navbar-text navbar-right"><?php if ($params->get('name') == 0) : ?><?php echo JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('name'))); ?><?php else : ?><?php echo JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('username'))); ?><?php endif; ?></p>');
	$('#login-btn').text('<?php echo JText::_('JLOGOUT'); ?>');
	$('#login-btn').click(function () {
		$('#logout-form').submit();
	});
});
</script>
