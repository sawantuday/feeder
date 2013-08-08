<div class="entry<?php if(!$item->isUnread) echo ' read'?>" id="<?php echo $item->id ?>">
  <div class="collapsed">
    <div class="entry-icons">
      <span class="icon-star<?php if(!in_array($item->id, $favorites)) echo '-empty' ?> star"></span>
    </div>
    <div class="entry-date pull-right" title="Published on : <?php echo date('M d Y H:i:s', strtotime($item->pud_date)); ?>">
    	<?php echo date('M d', strtotime($item->created)); ?>
    </div>
    <div class="entry-main">
      <?php //if($showTitle): ?>
      <span class="entry-source-title">
        <?php echo Channel::model()->findByPk($item->channel_id)->title ?>
      </span>
      <?php //endif; ?>
      <div class="entry-secondary">
        <h2 class="entry-title"><?php echo $item->title ?></h2> -
        <span class="snippet"><?php echo $item->short_desc ?></span>
      </div>
    </div>    <!-- entry-main -->
  </div>  <!-- collapsed -->
</div><!-- entry -->