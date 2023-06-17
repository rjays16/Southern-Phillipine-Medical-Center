<?php

namespace SegHis\modules\article\models;

/**
 * This is the model class for table "care_news_article".
 *
 * The followings are the available columns in table 'care_news_article':
 * @property integer $nr
 * @property string $lang
 * @property integer $dept_nr
 * @property string $category
 * @property string $status
 * @property string $title
 * @property string $preface
 * @property string $body
 * @property string $pic
 * @property string $pic_mime
 * @property integer $art_num
 * @property string $head_file
 * @property string $main_file
 * @property string $pic_file
 * @property string $author
 * @property string $submit_date
 * @property string $encode_date
 * @property string $publish_date
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 */
class Article extends \CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'care_news_article';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('title, category, preface, body,
            author, publish_date, modify_time', 'required'),
            array('dept_nr, art_num', 'numerical', 'integerOnly' => true),
            array('lang, status', 'length', 'max' => 10),
            array('title', 'length', 'max' => 255),
            array('pic_mime', 'length', 'max' => 4),
            array('author, modify_id, create_id', 'length', 'max' => 30),
            array('pic, submit_date, encode_date, publish_date, create_time', 'safe'),

            array('file_name', 'file', 'types'=>'jpg,gif,png,txt,pdf,doc,docx', 'allowEmpty'=>true, 'maxSize'=>2097152, 'tooLarge'=>'File has to be smaller than 2MB'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('nr, lang, dept_nr, category, status, title, preface, body, pic, pic_mime, art_num, head_file, main_file, pic_file,file_name, author, submit_date, encode_date, publish_date, modify_id, modify_time, create_id, create_time', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'nr' => 'Nr',
            'lang' => 'Lang',
            'dept_nr' => 'Dept Nr',
            'category' => 'Category',
            'status' => 'Status',
            'title' => 'Title',
            'preface' => 'Preface',
            'body' => 'Body',
            'pic' => 'Pic',
            'pic_mime' => 'Pic Mime',
            'art_num' => 'Section',
            'head_file' => 'Head File',
            'main_file' => 'Main File',
            'pic_file' => 'Picture',
            'file_name' => 'Attach a file 2MB max size',
            'author' => 'Author',
            'submit_date' => 'Submit Date',
            'encode_date' => 'Encode Date',
            'publish_date' => 'Publish Date',
            'modify_id' => 'Modify',
            'modify_time' => 'Modify Time',
            'create_id' => 'Create',
            'create_time' => 'Create Time',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return \CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria;

        $criteria->compare('title', $this->title, true);
        $criteria->compare('preface', $this->preface, true);
        $criteria->compare('body', $this->body, true);
        $criteria->compare('author', $this->author, true);

        if (strtotime($this->publish_date) !== false) {
            $criteria->addCondition('STR_TO_DATE(publish_date,"%Y-%m-%d") = STR_TO_DATE(:date,"%Y-%m-%d")');
            $criteria->params = \CMap::mergeArray($criteria->params, array(
                ':date' => date('Y-m-d', strtotime($this->publish_date))
            ));
        }

        $dataProvider = new \CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array(
                'defaultOrder' => 'publish_date DESC'
            )
        ));

        return $dataProvider;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Article the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @inheritdoc
     */
    protected function beforeSave()
    {
        if ($this->isNewRecord)
            $this->submit_date = date('m-d-Y H:i:s');

        return parent::beforeSave();
    }

    public function getPictureFile()
    {
        return $this->pic_file . '.' . $this->pic_mime;
    }

    public function getDocFile() /*added By MARK 2016-09-11*/
    {
        return $this->file_name;
    }
    public function getPictureFileFullPath()
    {
        return \Config::get('news_fotos_path') . $this->pic_file . '.' . $this->pic_mime;
    }

     public function getDOcFileFullPath() /*added By MARK 2016-09-11*/
    {
        return \Config::get('news_fotos_path').$this->file_name ;
    }

}
