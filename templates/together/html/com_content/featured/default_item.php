<?php defined('_JEXEC') or die;
$canEdit = $this->item->params->get('access-edit');
$params = &$this->item->params;
$images = json_decode($this->item->images);
$attribs = json_decode($this->item->attribs);
$app = JFactory::getApplication();
$templateparams = $app->getTemplate(true)->params;
$clase = '';
if ($this->item->state == 0 || strtotime($this->item->publish_up) > strtotime(JFactory::getDate())
		|| ((strtotime($this->item->publish_down) < strtotime(JFactory::getDate())) && $this->item->publish_down != '0000-00-00 00:00:00' )) {
	$clase .= 'bg-warning';
}
if (!empty($this->item->created_by_alias)) {
	$clase .= $this->item->created_by_alias . ' ';
} else {
	$clase .= 'destacado ';
}
?>
<article id="destacado_<?php echo $this->item->id; ?>" class="<?php echo trim($clase); ?>">
	<?php if ($attribs->show_title) : ?>
	<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid)); ?>">
		<h2><span class="glyphicon <?php echo empty($this->item->alternative_readmore) ? 'glyphicon-cog' : $this->item->alternative_readmore; ?>" aria-hidden="true"></span> <?php echo htmlspecialchars($this->escape($this->item->title)); ?></h2>
	</a>
	<?php if ($params->get('show_hits')) : ?>
	<span class="badge">
		<?php echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $this->item->hits); ?>
	</span>
	<?php endif; ?>
	<?php endif; ?>
	<?php echo $this->item->introtext; ?>
</article>