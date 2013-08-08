<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl?>/css/main.css" />
	<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl?>/css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl?>/css/bootstrap-responsive.min.css" />
	<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl?>/css/matrix-style.css" />
	<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl?>/css/matrix-media.css" />
</head>
<body>
	<div id="header">
    	<h1><a href="javascript:void(0)"><?php echo CHtml::encode(Yii::app()->name); ?></a></h1>
	</div><!-- header -->
	<!-- User nav -->
	<div id="user-nav" class="navbar navbar-inverse">
		<?php if($this->action->getId()!='index'){ ?>
	  	<ul class="nav" style="width: auto; margin: 0px;">
	  	<button class="btn" id="reload" data-url="#"><i class="icon icon-refresh"></i> Reload</button>
	  	<div class="btn-group" id="view-filter">
            <a class="btn" href="#show-all">View All</a>
            <button class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
			<ul class="dropdown-menu">
              	<li><a href="#show-all">View All</a></li>
              	<li><a href="#show-unread">View Unread</a></li>
        	</ul>
		</div>
		<a class="btn" id="mark_read" href="<?php echo $this->createUrl('markRead') ?>"><i class="icon icon-ok"></i> Mark All as Read</a>
		<!-- 
		<div class="btn-group">
            <button class="btn">Mark All as Read</button>
            <button class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
			<ul class="dropdown-menu">
              	<li><a href="#">All</a></li>
              	<li><a href="#">Older than day</a></li>
              	<li><a href="#">Older than week</a></li>
        	</ul>
		</div>
		 -->
		 <?php } ?>
	    <button class="btn btn-success" onclick="$('#subs-form').show();"><i class="icon-plus icon-white"></i> Subscribe</button>   
	  </ul>
	</div>
	<!-- User nav -->
	
	<!--start-top-serch-->
	<div id="search">
		<!-- <input type="text" placeholder="Search here..."/>
		<button type="submit" class="btn" title="Search"><i class="icon-search"></i></button> 
		<a style="margin: -10px 0 0 10px;" class="btn" href="<?php echo $this->createUrl('logout') ?>"><i class="icon-off"></i>&nbsp;Logout</a>-->
		<a class="btn" href="<?php echo $this->createUrl('logout') ?>"><i class="icon-off"></i>&nbsp;Logout</a>
	</div><!--close-top-serch-->

	<?php echo $content; ?>

	<div class="row-fluid">
    	<div id="footer" class="span12"><?php echo Yii::powered() ?></div>
  	</div>
  	
  	<div id="subs-form" style="display:none;">
  	<div id="modal-box">
  	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" onclick="$('#subs-form').hide();">&times;</button>
		<h3>Add new Subscription</h3>
	</div>
		<form action="<?php echo $this->createUrl('subscribe') ?>" method="POST">
			<span class="control-label">Enter Feed Url</span>
			<input class="control" type="text" name="data[url]" />
			<input type="submit" class="btn btn-primary" value="Add">
		</form>
	</div><!-- modal-box-->
	</div><!-- Subs-form-->
	

	<script src="<?php echo Yii::app()->theme->baseUrl?>/js/jquery.min.js"></script>
	<script src="<?php echo Yii::app()->theme->baseUrl?>/js/plugins.js"></script>
	<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/application.js', CClientScript::POS_END)?>
</body>
</html>