<?php $this->pageTitle=Yii::app()->name; ?>
<?php if(count($list) < 1): ?>
<div id="content-header">
	<div id="breadcrumb">
    	<a href="#" class="tip-bottom">
    		<h3>You do not have any unread items.</h3>
    	</a>
    </div>
</div>
<!-- <div><h3>You do not have any unread items.</h3></div>  -->
<?php else: ?>
<div>
	<div id="breadcrumb">
    	<a href="#" class="tip-bottom"><b>New items in your subscriptions</b></a>
    </div>
    <div class="widget-box rss-list">
	  	<ul>
	  	<?php foreach($list as $channel): ?>
	    	<li>
	      		<div>
	       			<a href="<?php echo $this->createUrl('view', array('channel_id'=>$channel['channel_id'])) ?>">
	       				<h2><?php echo $channel['title'] ?> (<?php echo $channel['count'] ?>)</h2>
	       			</a>
	        		<?php $item = Item::model()->getLatestByChannel($channel['channel_id']); ?>
	        		<h4><?php echo $item->title ?></h4>
	        		<p><?php //echo substr($item->desc, 0, 50) ?></p>
	        		<p><?php echo $item->short_desc ?></p>
	      		</div>
	    	</li>
	  	<?php endforeach; ?>
	  	</ul>
  	</div>
</div>
<?php endif; ?>