<?php

namespace SegHis\components\notification;

use \CFormModel;
/**
 * This class will extends CModel white initially providing you features from FisActiveResource.
 * This will help you organize FIS related data and functionalities into an OOP design.
 */

abstract class NotificationRestRecord extends CFormModel
{
    public static $_models; // HIDE ME!!!

    /**
     * @param string $className
     * @return $this
     */
    public static function model($className=__CLASS__)
    {
        if(isset(self::$_models[$className]))
            return self::$_models[$className];
        else
        {
            $model=self::$_models[$className]=new $className(null);
            $model->attachBehaviors($model->behaviors());
            return $model;
        }
    }

    public function getRest()
    {
        return \Yii::app()->FISRest;
    }

    /**
     * @todo hmm. RestCriteria?
     * @param  array $urlData url data, that would be inserted to the resource URL.
     * @param  array  $data Data that would be appended after "?"
     */
    public function find($resource, $urlData = array(), $data = array())
    {
        try {
            $data = $this->getRest()->get($resource, $urlData, $data);
        } catch (\Pest_Exception $e) {

        }
        if(empty($data))
            return null;
        $class = get_class($this);
        $model = new $class;
        $model->setAttributes($data, false);
        return $model;
    }

    public function findAll($resource, $urlData = array(), $data = array())
    {
        try {
            $data = $this->getRest()->get($resource, $urlData, $data);
        } catch (\Pest_Exception $e) {

        }
        if(empty($data))
            return null;
        $class = get_class($this);
        foreach($data as $index => $raw) {
            $model = new $class;
            $model->setAttributes($raw, false);
            $models[] = $model;
        }
        return empty($models) ? array() : $models;
    }
}