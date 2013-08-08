<?php
/**
 * This is the model class for table "channel".
 *
 * The followings are the available columns in table 'channel':
 * @property integer $id
 * @property string $title
 * @property string $link
 * @property string $last_build_date
 * @property string $created
 * @property string $modified
 * @property string $etag
 */
class Channel extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Channel the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'channel';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, link, last_build_date, created', 'required'),
			array('title, link', 'length', 'max'=>255),
			array('modified, etag, last_build_date', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, link, last_build_date, created, modified, etag', 'safe', 'on'=>'search'),
			array('created','default', 'value'=>new CDbExpression('NOW()'), 'setOnEmpty'=>false,'on'=>'insert'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => 'Title',
			'link' => 'Link',
			'last_build_date' => 'Last Build Date',
			'created' => 'Created',
			'modified' => 'Modified',
			'etag' => 'Etag',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('link',$this->link,true);
		$criteria->compare('last_build_date',$this->last_build_date,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('modified',$this->modified,true);
		$criteria->compare('etag',$this->etag,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getFeed($url)
	{
		$feed = new Feed;
		$item_count = $feed->get($url);
		if ($item_count < 1) return false;
	
		$channel = Channel::model()->findByAttributes(array(
			'title' => $feed->getTitle(), 'link' => $feed->getLink()
		));
		if (!$channel) $channel = new Channel;
	
		//save this channel information
		$channel->title = $feed->getTitle();
		$channel->link = $feed->getLink();
		$channel->feed_link = $url;
		$channel->last_build_date = $feed->getLastBuildDate();
	
		//following needs to modify to use last modified dates
		$channel->created = date('Y-m-d H:i:s');
		$channel->modified = $feed->getLastModified();
		if (!$channel->save())
		{
			var_dump($channel->getErrors());
			exit;
		}
	
		//get all items
		$items = $feed->getItems();
		foreach ($items as $line)
		{	
			$item = Item::model()->findByAttributes(array(
				'channel_id' => $channel->id, 'title' => $line->title,
				'link' => $line->link,
			));
	
			if (!$item) $item = new Item();
	
			$item->channel_id = $channel->id;
			$item->title = $line->title;
			$item->link = $line->link;
			$item->desc = $line->description;
			$item->short_desc = $line->short_desc;
			$item->comments = $line->comments;
			$item->author = $line->author;
			$item->pud_date = $line->pubDate;
			$item->category = $line->category;
			$item->guid = $line->guid;
			$item->created = date('Y-m-d H:i:s');
			if (!$item->save())
			{
				var_dump($item->getErrors());
				exit;
			}
		}
	
		//return $item_count;
		return $channel;
	
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::update()
	 * checks for feed updates
	 */
	public function updateFeed()
	{ 
		//get channel url from its id
		//$channel = Channel::model()->findByPk($channel_id);
	//	if(!$channel)
	//		return false;
		$url = $this->feed_link;
		
		//download the feeds
		$feed = new Feed;
		$feed->setLastModified($this->modified);
		$feed->setETag($this->etag);
		$item_count = $feed->get($url);		
		
		if ($item_count < 1) return false;
		
		//set channels update time (current time) to avoid immediate updates
		$this->modified = $feed->getLastModified();
		$this->etag = $feed->getETag();
		$this->save();
		
		//insert them into db and count the number of new items 
		$items = $feed->getItems();
		
		$new_items = array();
		foreach ($items as $line)
		{
			$item = Item::model()->findByAttributes(array(
				'channel_id' => $this->id, 'title' => $line->title,
				'link' => $line->link,
			));
		
			if (!$item) $item = new Item();
		
			$item->channel_id = $this->id;
			$item->title = $line->title;
			$item->link = $line->link;
			$item->desc = $line->description;
			$item->short_desc = $line->short_desc;
			$item->comments = $line->comments;
			$item->author = $line->author;
			$item->pud_date = $line->pubDate;
			$item->category = $line->category;
			$item->guid = $line->guid;
			$item->created = date('Y-m-d H:i:s');
			$isNewRecord = $item->isNewRecord;
			if (!$item->save())
			{
				var_dump($item->getErrors());
				continue;
			}
			if($isNewRecord)
				$new_items[] = $item->id;
		}
		
		//check for number of new items if its 0 return 0
		if(count($new_items) < 1)	return 0;
		
		//check for subscribed users and insert new items to user_unread_items
		$sql = 'SELECT user_id FROM  user_subscription WHERE channel_id = :channel_id';
		$channl_id = $this->id;
		$subscribers = Yii::app()->db->createCommand($sql)
			->bindParam(':channel_id', $channl_id)
			->queryColumn(); 
		
		$sql = 'INSERT INTO user_unread_item (user_id, item_id, channel_id) VALUES ';
		$values = array();
		
		foreach($subscribers as $subscriber)
		{
			foreach ($new_items as $item)
			{
				$values[] = '('.$subscriber.','. $item.','.$this->id.')';
			}
		}
		
		$sql .= implode(', ', $values);
		
		//insert them into db
		Yii::app()->db->createCommand($sql)->execute();
	}
}