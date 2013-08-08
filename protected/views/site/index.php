<?php $this->pageTitle=Yii::app()->name; ?>
<div>
  <ul>
  <?php foreach($list as $channel): ?>
    <li>
      <div>
        <h2>
        	<a href="<?php echo $this->createUrl('view', array('channel_id'=>$channel['channel_id'])) ?>">
        		<?php echo $channel['title'] ?> (<?php echo $channel['count'] ?>)
        	</a>
        </h2>
        <?php $item = Item::model()->getLatestByChannel($channel['channel_id']); ?>
        <h4><?php echo $item->title ?></h4>
        <p><?php //echo substr($item->desc, 0, 50) ?></p>
        <p><?php echo $item->desc ?></p>
      </div>
    </li>
  <?php endforeach; ?>
  </ul>
</div>
