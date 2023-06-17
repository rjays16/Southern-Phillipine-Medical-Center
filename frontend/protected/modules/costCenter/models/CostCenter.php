<?php
namespace SegHis\modules\costCenter\models;

/**
 * Class CostCenter
 * @package SegHis\modules\costCenter\models
 * @author Nick B. Alcala 3-28-2016
 */
abstract class CostCenter
{

    public $parameters;

    public $target;

    public $allowDelete;

    public $allowModify;

    public $messageHasFinalBill = "The request has final bill.";

    public $warningHasFinalBill = "The request has final bill.";

    public $messageHasServedNoPermission = "The request has served item.";

    public $warningHasServed = "The request has served item.";

    public $messageItemHasSample = "This item has sample already.";  #Added by Christian 12-31-19

    public $messageItemHasServe = "This item is served already.";  #Added by Christian 12-31-19

    private $message = '';

    private $warning = '';

    public static function search($parameters, $lazy = false)
    {
        $obj = new static();
        $obj->parameters = $parameters;
        $obj->init($parameters);

        if (!$lazy) {
            $obj->allowDelete();
            $obj->allowModify();
        }

        return $obj;
    }

    #Added by Christian 12-31-19
    public function getItemStatus($ref_source)
    {
        switch ($ref_source) {

            case 'LB':
                return $this->messageItemHasSample;
                break;

            case 'SPL':
                return $this->messageItemHasServe;
                break;

            case 'RD':
                return $this->messageItemHasServe;
                break;

            case 'BB':
                return $this->messageItemHasServe;
                break;

        }
    }
    #End Christian 12-31-19

    public function getMessage()
    {
        return $this->message;
    }

    public function getWarning()
    {
        return $this->warning;
    }

    /**
     * Allow delete if request
     * has no final bill
     * has no served item
     * has served but has permission
     * @return bool
     */
    public function allowDelete()
    {
        if ($this->isFinalBill()) {
            $this->allowDelete = false;
            $this->message = $this->messageHasFinalBill;
            $this->warning = $this->warningHasFinalBill;
            return false;
        }

        if ($this->isServed() && $this->hasPermission()) {
            $this->allowDelete = true;
            $this->warning = $this->warningHasServed;
            return true;
        }

        if ($this->isServed() && !$this->hasPermission()) {
            $this->allowDelete = false;
            $this->message = $this->messageHasServedNoPermission;
            $this->warning = $this->warningHasServed;
            return false;
        }

        $this->allowDelete = true;
        return true;
    }

    /**
     * Allow modify if request
     * has no final bill
     * has no served item
     * has served but has permission
     * @return bool
     */
    public function allowModify()
    {
        if ($this->isFinalBill()) {
            $this->allowModify = false;
            $this->message = $this->messageHasFinalBill;
            $this->warning = $this->warningHasFinalBill;
            return false;
        }

        if ($this->isServed() && $this->hasPermission()) {
            $this->allowModify = true;
            $this->warning = $this->warningHasServed;
            return true;
        }

        if ($this->isServed() && !$this->hasPermission()) {
            $this->allowModify = false;
            $this->message = $this->messageHasServedNoPermission;
            $this->warning = $this->warningHasServed;
            return false;
        }

        $this->allowModify = true;
        return true;
    }

    abstract public function init($parameters);

    abstract public function isServed();

    abstract public function isFinalBill();

    // pede pud static
    abstract public function hasPermission();

}