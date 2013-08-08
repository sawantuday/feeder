<?php

/**
 * This is the model class for table "item".
 *
 * The followings are the available columns in table 'item':
 * @property integer $id
 * @property integer $channel_id
 * @property string $title
 * @property string $link
 * @property string $desc
 * @property string $comments
 * @property string $pud_date
 * @property string $author
 * @property string $category
 * @property string $guid
 * @property string $created
 */
class Item extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Item the static model class
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
		return 'item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('channel_id, title, link, desc, comments, pud_date, author, category, guid, created', 'required'),
			array('channel_id', 'numerical', 'integerOnly'=>true),
			array('title, link, author, category, guid', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, channel_id, title, link, desc, comments, pud_date, author, category, guid, created', 'safe', 'on'=>'search'),
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
			'channel_id' => 'Channel',
			'title' => 'Title',
			'link' => 'Link',
			'desc' => 'Desc',
			'comments' => 'Comments',
			'pud_date' => 'Pud Date',
			'author' => 'Author',
			'category' => 'Category',
			'guid' => 'Guid',
			'created' => 'Created',
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
		$criteria->compare('channel_id',$this->channel_id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('link',$this->link,true);
		$criteria->compare('desc',$this->desc,true);
		$criteria->compare('comments',$this->comments,true);
		$criteria->compare('pud_date',$this->pud_date,true);
		$criteria->compare('author',$this->author,true);
		$criteria->compare('category',$this->category,true);
		$criteria->compare('guid',$this->guid,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
  
  	public function getLatestByChannel($channel_id)
  	{
    	return Item::model()->find(array(
	        'select'=>'title, `desc`',
	        'order'=>'created DESC',
	        'condition'=>'channel_id=:id',
	        'params'=>array(':id'=>$channel_id) 
	    ));  
  	}
  
	public function getItems($channel_ids, $user_id, $limit, $offset, $unreadOnly=false)
	{ 
		$join = $unreadOnly ? 'JOIN' : 'LEFT JOIN';
		$sql = 'SELECT id, i.channel_id, title, link, short_desc, comments, i.created, pud_date, ui.item_id as isUnread
				FROM  `item` i
				'.$join.' user_unread_item ui ON ui.item_id = i.id AND ui.user_id = :user_id
				WHERE i.channel_id IN ('.implode(',', $channel_ids).')
				ORDER BY  `i`.`pud_date` DESC LIMIT :offset, :limit';
		
		return Yii::app()->db->createCommand($sql)->bindValues(array(
				':offset'=>(int)$offset,
				':limit'=>(int)$limit,
				':user_id'=>$user_id,
		))->queryAll();
	}
	
	public function getStarredItems($user_id, $limit, $offset, $unreadOnly=false)
	{
		$join = $unreadOnly ? 'JOIN' : 'LEFT JOIN';
		$sql = 'SELECT id, i.channel_id, title, link, short_desc, comments, i.created, pud_date, ui.item_id as isUnread
				FROM user_favorites uf 
				LEFT JOIN item i on uf.item_id = i.id
				'.$join.' user_unread_item ui ON ui.item_id = i.id
				WHERE uf.user_id = :user_id
				ORDER BY  `i`.`pud_date` DESC LIMIT :offset, :limit';
		
		return Yii::app()->db->createCommand($sql)->bindValues(array(
			':offset'=>(int)$offset,
			':limit'=>(int)$limit,
			':user_id'=>$user_id,
		))->queryAll();
	}
	
}