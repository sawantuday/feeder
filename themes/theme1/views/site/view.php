<?php $this->pageTitle=Yii::app()->name; ?>
<!--breadcrumbs-->
<div id="content-header">
	<div id="breadcrumb">
    	<a href="#" title="<?php echo $title ?>" class="tip-bottom">
    		<i class="icon-home"></i>
    		<b><?php echo $title ?></b>
    	</a>
    </div>
</div><!--End-breadcrumbs-->
<div class="container-fluid">
	<div class="row-fluid">
    	<div class="span12">
        	<div class="widget-box rss-list">
          		<div class="widget-content">
            		<div id="entries" class="list">
            		<?php $showTitle = $title=='All Items'; ?>  
			    	<?php foreach($items as $item) :?>    
					    <?php $this->renderPartial('_item', array(
					      'item'=>(object)$item, 
					      'showTitle'=>$showTitle, 
					      'favorites'=>$favorites
					    )) ?>  
			    	<?php endforeach;?>
            		</div><!-- #entries -->
          		</div>
        	</div>
		</div>
	</div>
</div>

<div id="templates" style="display:none;">
<div id="template-item-container">
	<div class="entry-container">
	  <h2 class="entry-title">
	    <a href=":link" target="_blank" class="entry-title-link">:title</a>
	  </h2>
	  <div class="entry-author">
	  	<!-- 
	    <span class="entry-source-title-parent">From - 
	      <a target="_blank" class="entry-source-title" href="javascript:void(0)"></a>
	    </span> -->
	    <span class="entry-author-parent">By - 
	      <span class="entry-author-name">:author</span>
	    </span>
	  </div>
	  <div class="entry-body">:desc</div>
	</div><!-- entry-container -->
	<div class="entry-actions">
	  <span class="icon-star-empty star"></span>
	</div><!-- entry-actions -->
</div>
</div>