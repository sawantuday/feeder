<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
  	<link type="text/css" rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/reader.css">
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="main" id="page">

	<?php echo $content; ?>

	<div class="clear"></div>

	<div id="footer">
		<!--Copyright &copy; <?php echo date('Y'); ?> by My Company.<br/>
		All Rights Reserved.<br/>-->
		Inspired by Google Reader. <br/>
		<?php echo Yii::powered(); ?>
	</div>
</div>
</body>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery-1.9.1.min.js', CClientScript::POS_END)?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/application.js', CClientScript::POS_END)?>
</html>