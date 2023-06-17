<?php

Yii::import('bootstrap.widgets.TbModal');

/**
 * TRACER re-implementation of yii-bootstrap BootModal
 *
 * @author Gerard Angelo Baluyot
 * @copyright Copyright &copy; 2012. Segworks Technologies Corporation
 * @package Tracer.widgets
 */
class Modal extends TbModal
{

    /**
     *
     * @var string the route string (controller/action) to fetch the modal's contents from
     */
    public $url = null;

//    /**
//     *
//     * @var array list of parameters passed to
//     */
//    public $urlParams = array();


    /**
     * Initializes the widget.
     *
     * @return void
     */
    public function init()
    {
        /* @var $cs CClientScript */
        $cs = Yii::app()->getClientScript();
        $cs->registerScriptFile(Yii::app()->getBaseUrl() . '/js/jquery.blockUI.js', CClientScript::POS_END)
            ->registerScriptFile(Yii::app()->getBaseUrl() . '/js/frontend/modal.js', CClientScript::POS_END);

        if (!is_array($this->events)) {
            $this->events = array();
        }
        $this->events['show'] =
            'js:function(e) {
	var $this = $(this),
		$body = $this.find(\'.modal-body\');
	$this.css({
		\'margin-left\': function () {
			return -($(this).width() / 2);
		}
	});
//	tracer.modal.load($body, $this.data(\'), [], function(e){$(this).unblock()});
}';
        parent::init();
    }

}