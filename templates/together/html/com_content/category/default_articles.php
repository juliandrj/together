<?php defined('_JEXEC') or die;
$app = JFactory::getApplication();
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.framework');
$n = count($this->items);
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<?php if (empty($this->items)) : ?>
	<?php if ($this->params->get('show_no_articles', 1)) : ?>
		<p><?php echo JText::_('COM_CONTENT_NO_ARTICLES'); ?></p>
	<?php endif; ?>
<?php else : ?>

<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
	<?php if ($this->params->get('filter_field') != 'hide') : ?>
	<div class="row">
	<div class="col-md-12">
	<fieldset class="filters">
		<legend class="hidelabeltxt">
			<?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?>
		</legend>

		<div class="filter-search">
			<label class="filter-search-lbl" for="filter-search"><?php echo JText::_('COM_CONTENT_'.$this->params->get('filter_field').'_FILTER_LABEL').'&#160;'; ?></label>
			<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
		</div>
	<?php endif; ?>

	<?php if ($this->params->get('show_pagination_limit')) : ?>
		<div class="display-limit">
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	<?php endif; ?>

	<?php if ($this->params->get('filter_field') != 'hide') :?>
	</fieldset>
	</div>
	</div>
	<?php endif; ?>

			<?php foreach ($this->items as $i => &$article) : ?>
				<?php if ($i % 4 == 0) : ?>
				<div class="row">
				<?php endif; ?>
					<div class="col-md-4">
						<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid)); ?>">
							<div class="panel panel-default">
								<div class="panel-body text-center">
									<img class="img-circle" src="<?php echo json_decode($article->images)->image_intro; ?>" alt="<?php echo $this->escape($article->title); ?>" />
									<div class="carousel-caption">
										<?php echo $this->escape($article->title); ?>
										<?php if ($this->params->get('list_show_hits', 1)) : ?>
										<span class="badge">
											<?php echo $article->hits; ?>
										</span>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</a>
					</div>
				<?php if ($i + 1 % 4 == 0) : ?>
				</div>
				<?php endif; ?>
			<?php endforeach; ?>
<?php endif; ?>

<?php // Add pagination links ?>
<?php if (!empty($this->items)) : ?>
	<?php if (($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
	<div class="pagination">

		<?php if ($this->params->def('show_pagination_results', 1)) : ?>
		 	<p class="counter">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>
		<?php  endif; ?>

		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<?php endif; ?>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="filter_order" value="" />
		<input type="hidden" name="filter_order_Dir" value="" />
		<input type="hidden" name="limitstart" value="" />
	</div>
</form>
<?php endif; ?>
