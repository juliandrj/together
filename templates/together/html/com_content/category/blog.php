<?php defined('_JEXEC') or die;
$app = JFactory::getApplication();
$templateparams = $app->getTemplate(true)->params;
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
JHtml::_('behavior.caption');
$pageClass = $this->params->get('pageclass_sfx'); ?>
<section>
<?php if ($this->params->get('show_page_heading')) : ?>
<?php if ($this->params->get('show_page_heading') and ($this->params->get('show_category_title') or $this->params->get('page_subheading'))) : ?>
<hgroup>
<?php endif; ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>

<?php if ($this->params->get('show_category_title') or $this->params->get('page_subheading')) : ?>
<h2>
	<?php echo $this->escape($this->params->get('page_subheading')); ?>
	<?php if ($this->params->get('show_category_title')) : ?>
	<?php echo JHtml::_('content.prepare', $this->category->title, '', 'com_content.category.title'); ?>
	<?php endif; ?>
</h2>
<?php if ($this->params->get('show_page_heading') and ($this->params->get('show_category_title', 1) or $this->params->get('page_subheading'))) : ?>
</hgroup>
<?php endif; ?>
<?php endif; ?>
<?php if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
	<article>
	<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
		<span class="pull-left margen-derecho"><img class="img-rounded" src="<?php echo $this->category->getParams()->get('image'); ?>"/></span>
	<?php endif; ?>
	<?php if ($this->params->get('show_description') && $this->category->description) : ?>
		<?php echo JHtml::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
	<?php endif; ?>
	</article>
<?php endif; ?>
	<div class="container-fluid">
		<?php echo $this->loadTemplate('articles'); ?>
	</div>
<?php if (is_array($this->children[$this->category->id]) && count($this->children[$this->category->id]) > 0 && $this->params->get('maxLevel') != 0) : ?>
	<div class="container-fluid">
	<?php if ($this->params->get('show_category_heading_title_text', 1) == 1) : ?>
		<h2><?php echo JTEXT::_('JGLOBAL_SUBCATEGORIES'); ?></h2>
	<?php endif; ?>
	<?php echo $this->loadTemplate('children'); ?>
	</div>
<?php endif; ?>
</section>
