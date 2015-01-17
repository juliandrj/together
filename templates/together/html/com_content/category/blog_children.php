<?php defined('_JEXEC') or die;
$app = JFactory::getApplication();
$templateparams = $app->getTemplate(true)->params;
$i = 0;
?>
<?php if (count($this->children[$this->category->id]) > 0) :?>
	<?php foreach ($this->children[$this->category->id] as $id => $child) : ?>
		<?php if ($this->params->get('show_empty_categories') || $child->getNumItems(true) || count($child->getChildren())) : ?>
		<?php if ($i % 4 == 0) : ?>
		<div class="row">
		<?php endif; ?>
			<div class="col-md-3">
				<a href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($child->id));?>">
					<div class="panel panel-default">
						<div class="panel-body text-center">
								<img class="img-rounded" src="<?php echo $child->getParams()->get('image'); ?>" alt="<?php echo $this->escape($child->title); ?>" />
								<div class="carousel-caption">
									<h4><?php echo $this->escape($child->title); ?></h4>
								</div>
						</div>
						<?php if (count($child->getChildren()) > 0 ) :
							$this->children[$child->id] = $child->getChildren();
							$this->category = $child;
							$this->maxLevel--;
							if ($this->maxLevel != 0) :
								echo $this->loadTemplate('children');
							endif;
							$this->category = $child->getParent();
							$this->maxLevel++;
						endif; ?>
					<?php $i ++; ?>
					</div>
				</a>
			</div>
		<?php if ($i + 1 % 4 == 0) : ?>
		</div>
		<?php endif; ?>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>
