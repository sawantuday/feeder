<?php
class SiteController extends Controller
{
   	//public $defaultAction = 'login';
   	private $_user_id;
   
	public function init()
   	{
    	$this->_user_id = Yii::app()->user->getId();
      	parent::init();
   	}
   	
   /*
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}
    
  	
  	public function beforeaction($action)
  	{   
    	$allowed = array('login', 'logout');
    	if(Yii::app()->user->isGuest && !in_array(strtolower($action->getId()), $allowed))
			$this->redirect($this->createUrl('login'));
    	
    	return true;
  	}
  	  */
   	
  	public function filters()
  	{
  		return array(
			'accessControl', // perform access control for CRUD operations
  		);
  	}
  	
  	/**
  	 * Specifies the access control rules.
  	 * This method is used by the 'accessControl' filter.
  	 * @return array access control rules
  	 */
  	public function accessRules()
  	{
  		return array(
  				array('allow',  // allow all users to access 'index' and 'view' actions.
  						'actions'=>array('login','logout', 'signup'),
  						'users'=>array('*'),
  				),
  				array('allow', // allow authenticated users to access all actions
  						'users'=>array('@'),
  				),
  				array('deny',  // deny all users
  						'users'=>array('*'),
  				),
  		);
  	}

	public function actionIndex()
	{     
      	//this is a home/landing page for user
      	//show him the list of unread channels
      	$sql = 'SELECT c.title, count(item_id) as count, channel_id 
				FROM `user_unread_item` ui left join channel c 
              	on c.id = ui.channel_id
              	WHERE ui.user_id = :user_id
              	group by channel_id'; 
              
      	$list = Yii::app()->db->createCommand($sql)
                ->bindParam(':user_id', $this->_user_id)
                ->queryAll();

      	$this->render('index', array('list'=>$list));
	}
  
  	public function actionView($channel_id=false, $limit=25, $offset=0, $favorites=false)
  	{	            
      	//show a list of all feeds
      	if($channel_id && is_numeric($channel_id))
      	{
          	$subscription = UserSubscription::model()->exists(array(
            	'condition'=>'user_id=:user_id and channel_id=:channel_id', 
             	'params'=>array(':user_id'=>$this->_user_id, ':channel_id'=>$channel_id)
          	));
          	
          	if(!$subscription)
            	throw new CException('You are not subscribed to this channel');
          	
          	$channel_ids[] = $channel_id;
          	$title = Channel::model()->findByPk($channel_id)->title;
      	}
      	else
      	{
        	$sql = 'SELECT channel_id FROM user_subscription WHERE user_id = :user_id';       
        	$channel_ids = Yii::app()->db->createCommand($sql)
           		->bindParam(':user_id', $this->_user_id)
           	 	->queryColumn();
        	$title = 'All Items';
      	}
      	//var_dump($_POST, $_GET);exit;
      	$unreadOnly = Yii::app()->request->getParam('unreadOnly');
      	if($favorites)
      	{
      		$items = Item::model()->getStarredItems($this->_user_id, $limit, $offset, $unreadOnly);
      		$title = 'Favorite Items';
      	}
      	else
      	{
      		$items = Item::model()->getItems($channel_ids, $this->_user_id, $limit, $offset, $unreadOnly);
      	}

        $session = Yii::app()->session;
        $favorites = $session['favorites'];
		      
		//check if this is an ajax call
		if(Yii::app()->request->isAjaxRequest)
		{
    		if(count($items) > 0)
    			foreach ($items as $item)
    				$this->renderPartial('_item', array(
		            	'item'=>(object)$item, 
		                'showTitle'=>$title=='All Items', 
		                'favorites'=>$favorites
					));
    		Yii::app()->end();
		}
    		
    	$this->render('view', array(
        	'items'=>$items, 
        	'title'=>$title, 
        	'favorites'=>$favorites
        ));
  	}
  
  	public function actionTest()
  	{
  		$str = file_get_contents('codeCoverage');
  		$arr = unserialize($str);
  		var_dump($arr); 
  		
  	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
  		if($error=Yii::app()->errorHandler->error)
  		{
  			if(Yii::app()->request->isAjaxRequest)
  				echo $error['message'];
  			else
  				$this->render('error', $error);
  		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}     

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		//log visitors
		$line = date('Y-m-d H:i:s') . " - $_SERVER[REMOTE_ADDR]";
		file_put_contents('visitors.log', $line . PHP_EOL, FILE_APPEND);
		
		$this->layout = 'blank';
		
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
			{
        		$this->_user_id = Yii::app()->user->getId();
        		User::model()->afterLogin($this->_user_id);
				$this->redirect(Yii::app()->user->returnUrl);
      		}
		}
		
		// display the login form
		$this->render('login',array('model'=>$model));
	}
	
	/**
	 *  Sign up for the application 
	 *  */
	public function actionSignup()
	{
		$this->layout = 'blank';
		
		$model = new User;
		
		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='User')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		// collect user input data
		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			$model->name = ucwords($model->name);
			$model->password = md5($model->password);
			
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->save())
			{
				//set success msg and redirect to login page
				Yii::app()->user->setFlash('signup', 'You have registered successfully. Login with your Username password.');
				$this->redirect($this->createUrl('login'));
			}
			else{var_dump($model->getErrors());}
		}
		
		$this->render('signup',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		//TODO - Possibally need to destroy the session
		Yii::app()->user->logout();
		//$this->redirect(Yii::app()->homeUrl);
		$this->redirect($this->createUrl('login'));
	}
	
	public function actionGetItemData($item_id, $isRead=false)
	{
		$item = Item::model()->findByPk($item_id);
		if(!$item)
		{
			echo false;
			Yii::app()->end();
		}
		
		echo json_encode($item->attributes);
		
		//if this item is unread mark it as read.
		if($isRead)
		{
			$attributes = array('user_id'=>$this->_user_id, 'item_id'=>$item_id, 'channel_id'=>$item->channel_id);
			UserUnreadItem::model()->deleteAllByAttributes($attributes);
		}
	}
	
	public function actionMarkRead($id)
	{
		if(!is_numeric($id))	return false;
		$attributes = array('user_id'=>$this->_user_id);
		
		if($id > 0)
		{	//this is for specific channel
			$attributes['channel_id'] = $id;
			UserUnreadItem::model()->deleteAllByAttributes($attributes);
		}
		elseif($id == 0)
		{	//this is for all channels, clear all unread items
			UserUnreadItem::model()->deleteAllByAttributes($attributes);
		}
		elseif($id == -1)
		{	//this is for favorites items, get unread favorites and remove them from unread items
			$sql = 'DELETE ui
					FROM user_unread_item ui join user_favorites uf
					on ui.user_id = uf.user_id and ui.item_id = uf.item_id
					where ui.user_id = :user_id';
			Yii::app()->db->createCommand($sql)
				->bindParam(':user_id', $this->_user_id)
				->execute();
		}
		echo true;		
	}
	
	/*
	public function actionReload($channel_id=0)
	{
		//$channel = Channel::model()->findByPk(2);
		//$channel->update();
		//$this->redirect($_SERVER['HTTP_REFERER']);
	}*/
	
	public function actionSubscribe()
	{
		if(isset($_POST['data']) && $_POST['data']['url'] != '')
		{
			$url = $_POST['data']['url'];
			$channel = Channel::model()->findByAttributes(array('link'=>$url));
			if(!$channel)
			{	//channel not availabel in db. lets fetch it
				$channel = Channel::model()->getFeed($url);
			}
			
			//add this channel to users subscription
			//check if it already exists 
			$subscription = UserSubscription::model()->exist(
				'user_id=:user_id and channel_id=:channel_id',
				array(':user_id'=>$this->_user_id, ':channel_id'=>$channel->id)
			);
			if(!$subscription)
			{
				$subscription = new UserSubscription;
				$subscription->user_id = $this->_user_id;
				$subscription->channel_id = $channel->id;
				$subscription->save();
			}
			
			//add latest 10 items to users unread list
			$sql = 'SELECT id FROM  item WHERE channel_id = :channel_id ORDER BY  item.pud_date DESC LIMIT 10';
			$item_ids = Yii::app()->db->createCommand($sql)->bindParam(':channel_id', $channel->id)->queryColumn();
			foreach($item_ids as $id)
			{
				$unread_item = new UserUnreadItem;
				$unread_item->user_id = $this->_user_id;
				$unread_item->channel_id = $channel->id;
				$unread_item->item_id = $id;
				$unread_item->save();
			}
			
			//set flash message
			Yii::app()->user->setFlash('subscribed', 'You have successfully subscribed to '.$url);
			if(Yii::app()->request->isAjaxRequest)
			{
				echo 'success';
				Yii::app()->end();
			}
		}
		
		$this->redirect($this->createUrl('index'));		
	}
	
	public function actionUnsubscribe($channel_id)
	{
		$channel = UserSubscription::model()->findByAttributes(array(
			'user_id'=>$this->_user_id,
			'channel_id'=>$channel_id
		));
		
		if(!$channel)
		{
			$msg = 'feed not found';
		}
		else
		{
			$msg = 'You have successfully unsuscribed';
			$channel->delete();
		}
		
		//set flash
		Yii::app()->user->setFlash('unsubscribed', $msg);
		
		$this->redirect($this->createUrl('index'));
	}
	
	public function actionAddFavorite($item_id)
	{
	    //check if this item already exist in favorites 
	    $exists = UserFavorites::model()->exists(
	        'item_id=:item_id AND user_id=:user_id',
	        array(':item_id'=>$item_id, ':user_id'=>$this->_user_id)
	    );
	    
	    if($exists)
	    {
	        echo true;
	        Yii::app()->end();
	    }
	    
	    //item not in the favorite list. add it to list	
		$favorite = new UserFavorites;
		$favorite->user_id = $this->_user_id;
		$favorite->item_id = $item_id;
		if($favorite->save())
	    {
	        //add this to user session favorites
	        $session = Yii::app()->session;
	        $arrTemp = $session['favorites'];
	        $arrTemp[] = $favorite->item_id;
	        $session['favorites'] = $arrTemp;
	        echo true;
	    }
	    else
	    {
	        echo false;
	    }
		//if($favorites->save())
			//Yii::app()->user->setFlash('favorite', 'Item added to your favorite list');
		
		//$this->redirect($this->createUrl('index'));
	}
	
	public function actionRemoveFavorite($item_id)
	{
		$favorites = UserFavorites::model()->deleteAllByAttributes(array(
			'user_id'=>$this->_user_id,
			'item_id'=>$item_id
		));
    
	    if($favorites)
	    {
	        //remove this item from session favorites
	        $session = Yii::app()->session;
	        $arrTemp = $session['favorites'];
	        $key = array_search($item_id, $arrTemp); 
	        if($key!==false)
	        {
	            unset($arrTemp[$key]);
	            $session['favorites'] = $arrTemp;
	        } 
	        echo true;
	    }
	    else
	    {
	        echo false;
	    }
		   /*
		if(!$favorites)
		{
			$msg = 'Item not found';
		}
		else
		{
			$msg = 'Item removed from your favorite';
			$favorites->delete();
		}
		
		//set flash
		Yii::app()->user->setFlash('removefavorite', $msg);
		
		$this->redirect($this->createUrl('index'));     */
    
	}
}