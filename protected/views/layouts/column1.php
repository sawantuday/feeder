<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
<?php $this->widget('application.widgets.navigator'); ?>
<div id="content">
  <div id="view-container">
    <div id="view-header">
      <span>Reload</span>
      <span>All Items</span>
      <span>Mark as read</span>
      <span style="float:right;"><a href="<?php echo $this->createUrl('logout') ?>">Logout</a></span>
    </div>
    <?php echo $content; ?>
  </div>
</div><!-- content -->
<?php $this->endContent(); ?>