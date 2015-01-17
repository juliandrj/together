<?php defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
JHtml::_('behavior.caption');
$allItems = array_merge($this->lead_items, $this->intro_items);
?>
<?php if (!empty($allItems)) : ?>
<nav id="destacados" class="navbar navbar-inverse menu-destacados navbar-fixed-bottom">
	<div class="container-fluid">
		<ul class="nav navbar-nav">
			<li class="active">
				<a class="text-center" href="#inicio">
					<span class="glyphicon glyphicon-home" aria-hidden="true"></span><br/>&nbsp;
				</a>
			</li>
		<?php foreach ($allItems as $i => &$item) : ?>
			<?php $attribs = json_decode($item->attribs); ?>
			<?php if ($attribs->show_title) : ?>
			<li>
				<a class="text-center" href="#destacado_<?php echo $item->id; ?>">
					<span class="glyphicon <?php echo empty($item->alternative_readmore) ? 'glyphicon-cog' : $item->alternative_readmore; ?>" aria-hidden="true"></span><br/>
					<?php echo $item->title; ?>
				</a>
			</li>
			<?php endif; ?>
		<?php endforeach; ?>
			<li>
				<a class="text-center" href="#mapa">
					<span class="glyphicon glyphicon-globe" aria-hidden="true"></span><br/>Red nacional
				</a>
			</li>
		</ul>
	</div>
</nav>
<?php endif; ?>
<section>
<?php if ( $this->params->get('show_page_heading') != 0) : ?>
	<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
<?php endif; ?>
<?php $leadingcount = 0; ?>
<?php if (!empty($allItems)) : ?>
<?php foreach ($allItems as &$item) : ?>
<div class="row">
	<div class="col-md-12">
		<?php
			$this->item = &$item;
			echo $this->loadTemplate('item');
		?>
	<?php $leadingcount++; ?>
	</div>
</div>
<?php endforeach; ?>
<?php endif; ?>
</section>


