<?php
namespace SegHis\modules\collections;

/**
 * Class CollectionsModule
 * @author michelle 03-02-15
 */
class CollectionsModule extends \WebModule {
    public $defaultController='default';

    public function beforeControllerAction($controller, $action)
	{
        return parent::beforeControllerAction($controller, $action);
	}
}