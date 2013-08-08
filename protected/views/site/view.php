<?php $this->pageTitle=Yii::app()->name; ?>
<div id="item-container">         
  <div id="title-holder">             
    <span id="chrome-title"><?php echo $title ?></span>
	<?php if(Yii::app()->user->hasFlash('subscribed')): ?>
		<span id=""><?php echo Yii::app()->user->getFlash('subscribed'); ?></span>
	<?php endif; ?>
	<span></span>                
    <div class="clear"></div>         
  </div>         
  <div id="entries" class="list">
  	<?php $showTitle = $title=='All Items'; ?>  
    <?php foreach($items as $item) :?>    
    <?php $this->renderPartial('_item', array(
    	'item'=>(object)$item, 
    	'showTitle'=>$showTitle, 
    	'favorites'=>$favorites
    )) ?>  
    <?php endforeach;?>
  </div>            
</div>
<div style="display:none;" id="templates">
<div id="item-open">
<div class="entry-container"><div class="entry-main"><h2 class="entry-title"><a class="entry-title-link" target="_blank" href="http://blog.nihilogic.dk/2009/01/genetic-mona-lisa.html">Genetic algorithms, Mona Lisa and JavaScript + Canvas<div class="entry-title-go-to"></div></a><span class="entry-icons-placeholder"></span></h2><div class="entry-body"><div><div class="item-body"><div><a href="https://news.ycombinator.com/item?id=5657926" target="_blank">Comments</a></div></div></div></div></div></div>
<div class="entry-actions"><span class="item-star star link unselectable" title="Add star"></span><span class="read-state-not-kept-unread read-state link unselectable" title="Keep unread">Keep unread</span></div>
</div>
</div>