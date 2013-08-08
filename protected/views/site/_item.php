<div class="entry<?php if(!$item->isUnread) echo ' read'?>" id="<?php echo $item->id ?>">                 
  <div class="collapsed">                     
    <div class="entry-icons">                         
      <div class="item-star<?php if(in_array($item->id, $favorites)) echo '-active' ?> star"></div>                     
    </div>                     
    <div class="entry-date"><?php echo date('M d', strtotime($item->created)); ?></div>                     
    <div class="entry-main">                         
      <!-- <a class="entry-original" target="_blank" href="<?php echo $item->link ?>"></a>  -->
      <?php //if($showTitle): ?>                         
      <span class="entry-source-title">
        <?php echo Channel::model()->findByPk($item->channel_id)->title ?>                          
      </span>            
      <?php //endif; ?>             
      <div class="entry-secondary">                             
        <h2 class="entry-title">              
          <?php echo $item->title ?></h2>                             
        <span class="entry-secondary-snippet"> -
          <span class="snippet">                                       
            <?php //echo substr($item->desc, 0, 25) ?>    
            <?php echo $item->desc ?> 
          </span>                             
        </span>                         
      </div>                     
    </div>                 
  </div>             
</div>