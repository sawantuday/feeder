<?php
class navigator extends CWidget
{
  public function run()
  {
    $user_id = Yii::app()->user->getId();
    $sql = 'SELECT c.title, c.id
            FROM user_subscription us
            LEFT JOIN channel c ON c.id = us.channel_id
            WHERE us.user_id = :id
            ORDER BY  `c`.`title` ASC ';
    $subscriptions = Yii::app()->db->createCommand($sql)
          ->bindParam(':id', $user_id)
          ->queryAll();
    
    $sql = 'SELECT channel_id, count(item_id) as count FROM user_unread_item WHERE user_id=:id group by channel_id';
    $items = Yii::app()->db->createCommand($sql)
          ->bindParam(':id', $user_id)
          ->queryAll();

    $unread = array();
    foreach ($items as $val)
    {
		$unread[$val['channel_id']]=$val['count'];
    }
    
    unset($items);

    $this->render('_navigator', array('subs'=>$subscriptions, 'unread'=>$unread));  
  }
}