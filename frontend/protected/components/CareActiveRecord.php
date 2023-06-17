<?php
/**
 * CareActiveRecord.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

/**
 * Extension of Yii's CActiveRecord
 *
 * @package application.components
 */
class CareActiveRecord extends ActiveRecord {

    /**
     *
     * @return type
     */
    protected function beforeSave() {
        if ($this->isNewRecord) {
            if ($this->hasAttribute('create_time')) {
                $this->create_time = date('YmdHis');
            }
            if ($this->hasAttribute('create_dt')) {
                $this->create_dt = date('YmdHis');
            }
            if ($this->hasAttribute('create_id')) {
                $this->create_id = Yii::app()->user->getId();
            }
        }

        if ($this->hasAttribute('modify_time')) {
            $this->modify_time = date('YmdHis');
        }
        if ($this->hasAttribute('modify_dt')) {
            $this->modify_dt = date('YmdHis');
        }
        if ($this->hasAttribute('modify_id')) {
            $this->modify_id = Yii::app()->user->getId();
        }

        return parent::beforeSave();
    }

    protected function beforeValidate() 
    {
        if ($this->isNewRecord) {
            if ($this->hasAttribute('create_time')) {
                $this->create_time = date('YmdHis');
            }
            if ($this->hasAttribute('create_id')) {
                $this->create_id = Yii::app()->user->getId();
            }
        }

        if ($this->hasAttribute('modify_time')) {
            $this->modify_time = date('YmdHis');
        }
        if ($this->hasAttribute('modify_dt')) {
            $this->modify_dt = date('YmdHis');
        }
        if ($this->hasAttribute('modify_id')) {
            $this->modify_id = Yii::app()->user->getId();
        }

        return parent::beforeValidate();
    }

}
