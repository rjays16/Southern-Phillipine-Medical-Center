<?php

class DialysisTransaction {

    public $isNew;
    public $transactionNr;
    public $pid;
    public $dialyzerSerialNr;
    public $machineNr;
    public $transactionDate;
    public $createId;
    public $createTime;
    public $modifyId;
    public $modifyDate;
    public $history;
    public $dialyzerReuse;
    private $classPerson;
    private $classDialysis;
    public $dialyzerType;


    public function __construct($tnr, $pid) {
        $this->transactionNr = $tnr;
        $this->pid = $pid;
        $this->classPerson = new Person();
        $this->classDialysis = new SegDialysis();
        $this->machineNr = '';
        $this->setIsNew($tnr);
        if($this->isNew) {
           $dialyzer = $this->getLastDialyzer();
           //if dialyzer reuse
           if($dialyzer) {
               $this->dialyzerReuse = $dialyzer['reusex'];
               $this->dialyzerSerialNr = $dialyzer['dialyzer_serial_nr'];
               $this->dialyzerType = strtoupper($dialyzer['dialyzer_type']);
           }
        } else {
            //if update
            $transactionInfo = $this->getTransactionInfo();
            $this->transactionDate = new DateTime($transactionInfo['transaction_date']);
            $this->dialyzerReuse = $transactionInfo['dialyzer_reuse'];
            $this->dialyzerSerialNr = $transactionInfo['dialyzer_serial_nr'];
            $this->dialyzerType = strtoupper($transactionInfo['dialyzer_type']);
            $this->machineNr = $transactionInfo['machine_nr'];
        }
    }

    public function getPersonInfo() {
        return $this->classPerson->getPidInfo($this->pid);
    }

    public function hasReusableDialyzer() {
        if(isset($this->dialyzerSerialNr) && $this->dialyzerReuse < 8)
            return true;
        return false;
    }

    public function save() {

    }

    private function setIsNew($tnr) {
        $result = $this->classDialysis->getTransactionByTnr($tnr);
        $this->isNew = $result ? false : true;
    }

    public function getLastDialyzer() {
        $result = $this->classDialysis->getLastDialyzer($this->pid);
        return $result ? $result : false;
    }

    public function getTransactionInfo() {
        $result = $this->classDialysis->getDialyzerInfo($this->transactionNr);
        return $result ? $result : false;
    }

    public function isPhicTrxn(){
        return $this->classDialysis->isPhicTrxn($this->transactionNr);
    }
}