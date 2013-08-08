<!--sidebar-menu-->
<div id="sidebar">
	<ul>
    	<li class="active">
    		<a href="<?php echo Yii::app()->createUrl('site/index') ?>">
    			<i class="icon icon-home"></i>
    			<span>Dashboard</span>
    		</a>
    	</li>
    	<li class="">
    		<a href="<?php echo Yii::app()->createUrl('site/view') ?>">
    			<i class="icon icon-th-list"></i>
    			<span>All Items</span>
    		</a>
    	</li>
    	<li class="">
    		<a href="<?php echo Yii::app()->createUrl('site/view', array('favorites'=>1)) ?>">
    			<i class="icon icon-star"></i>
    			<span>Favorites</span>
    		</a>
    	</li>
    	<li class="submenu open">
    		<a href="#">
    			<i class="icon icon-folder-open"></i>
    			<span>Subscriptions</span>
    			<!-- <span class="label label-important"><?php echo count($subs)?></span> -->
    		</a>
	      	<ul style="overflow:auto;">
		        <?php foreach($subs as $item): ?>
	            <li>
	              <a href="<?php echo Yii::app()->createUrl('site/view', array('channel_id'=>$item['id'])) ?>">
	                <?php echo $item['title'] ?>
	                <span class="label"><?php echo @$unread[$item['id']] ?></span>
	              </a>
	            </li>
	          	<?php endforeach; ?>
	      	</ul>
    	</li>
	</ul>
</div><!--sidebar-menu-->