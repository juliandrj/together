<?php defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
?>
	<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
		<div class="modal-dialog">
    		<div class="modal-content">
    			<div class="modal-header">
    				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    				<h4 class="modal-title" id="loginModalLabel"><?php echo JText::_('JLOGIN'); ?></h4>
    			</div>
    			<div class="modal-body">
					<?php if ($params->get('pretext')) : ?>
						<p><?php echo $params->get('pretext'); ?></p>
					<?php endif; ?>
					<form class="form-horizontal" action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" >
						<div class="form-group">
							<label class="col-sm-4 control-label" for="modlgn-username"><?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?></label>
							<div class="col-sm-8">
								<input id="modlgn-username" type="text" name="username" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="modlgn-passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
							<div class="col-sm-8">
								<input id="modlgn-passwd" type="password" name="password" class="form-control" />
							</div>
						</div>
						<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
						<div class="col-sm-offset-4 col-sm-8">
							<div class="checkbox">
								<label>
									<input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
									<?php echo JText::_('MOD_LOGIN_REMEMBER_ME') ?>
								</label>
							</div>
						</div>
						<?php endif; ?>
						<div class="col-sm-offset-4 col-sm-8">
							<button type="submit" class="btn btn-default"><?php echo JText::_('JLOGIN') ?></button>
						</div>
						<input type="hidden" name="option" value="com_users" />
						<input type="hidden" name="task" value="user.login" />
						<input type="hidden" name="return" value="<?php echo $return; ?>" />
						<?php echo JHtml::_('form.token'); ?>
					</form>
					<ul class="list-inline">
						<li>
							<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
							<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
						</li>
						<li>
							<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
							<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
						</li>
						<?php $usersConfig = JComponentHelper::getParams('com_users'); ?>
						<?php if ($usersConfig->get('allowUserRegistration')) : ?>
							<li>
								<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
									<?php echo JText::_('MOD_LOGIN_REGISTER'); ?></a>
							</li>
						<?php endif; ?>
					</ul>
					<?php if ($params->get('posttext')) : ?>
						<p><?php echo $params->get('posttext'); ?></p>
					<?php endif; ?>
    			</div>
    		</div>
    	</div>
	</div>