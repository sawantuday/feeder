<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
<?php $this->widget('application.widgets.navigator'); ?>
<!--main-container-part-->
<div id="content">
	<?php echo $content; ?>
</div>
<!--end-main-container-part-->
<?php $this->endContent(); ?>