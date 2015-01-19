<?php defined('_JEXEC') or die; ?>
<nav id="nav" class="navbar navbar-default navbar-fixed-top hidden-print hidden-xs hidden-sm">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<span id="logo"></span>
		</div>
		<div class="collapse navbar-collapse" id="menu">
			<ul class="nav navbar-nav">
				<?php foreach ($list as $i => &$item) : ?>
				<?php 
					$linktype = $item->title;
					$flink = JFilterOutput::ampReplace(htmlspecialchars($item->flink));
					$clase = '';
					if (($item->id == $active_id) OR ($item->type == 'alias' AND $item->params->get('aliasoptions') == $active_id)) {
						$clase .= 'current ';
					}
					if (in_array($item->id, $path) && !$item->shallower) {
						$clase .= 'active ';
					} elseif ($item->type == 'alias') {
						$aliasToId = $item->params->get('aliasoptions');
						if (count($path) > 0 && $aliasToId == $path[count($path) - 1] && !$item->shallower) {
							$clase .= 'active ';
						} elseif (in_array($aliasToId, $path)) {
							$clase .= 'active-parent ';
						}
					}
					if ($item->deeper) {
						$clase .= 'dropdown ';
					}
				?>
				<li class="<?php echo trim($clase); ?>">
					<a<?php if ($item->deeper) : ?> href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"<?php else : ?> href="<?php echo $flink; ?>"<?php endif; ?>>
						<?php echo $linktype; ?><?php if ($item->deeper) : ?> <span class="caret"></span><?php endif; ?>
					</a>
					<?php 
					if ($item->deeper) {
						echo '<ul class="dropdown-menu" role="menu">';
					} elseif ($item->shallower) {
						// The next item is shallower.
						echo '</li>';
						echo str_repeat('</ul></li>', $item->level_diff);
					} else {
						// The next item is on the same level.
						echo '</li>';
					}
					?>
				<?php endforeach; ?>
			</ul>
			<?php if (JDocumentHTML::countModules('login')) : ?>
			<button id="login-btn" type="button" class="btn btn-default navbar-btn navbar-right" data-toggle="modal" data-target="#loginModal">
				<?php echo JText::_('JLOGIN'); ?>
			</button>
			<?php endif; ?>
		</div>
	</div>
</nav>