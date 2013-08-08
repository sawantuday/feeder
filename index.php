<?php
if($_SERVER['HTTP_HOST'] == 'localhost')
{	//this is a dev environment
	$yii=dirname(__FILE__).'/../../yii/yii-1.1.11/framework/yii.php';
	$config=dirname(__FILE__).'/protected/config/main.php';
	
	defined('YII_DEBUG') or define('YII_DEBUG',true);
	defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
}
else 
{	//this is a live site
	$yii=dirname(__FILE__).'/../../data/yii/framework/yii.php';
	$config=dirname(__FILE__).'/protected/config/live.php';
}

require_once($yii);
Yii::createWebApplication($config)->run();