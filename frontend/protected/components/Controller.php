<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();


    public $pageSubTitle = null;
    public $pageIcon = null;
    public $showfooter = true;

    public $showFooter = null;


    /**
     *
     * @param string $text
     */
    public function setPageSubTitle($text) {
        $this->pageSubTitle = $text;
    }

    /**
     *
     * @param string $icon
     */
    public function setPageIcon($icon) {
        $this->pageIcon = $icon;
    }

    /**
     *
     * @param type $view
     * @param type $data
     * @param type $return
     */
    public function render($view, $data = null, $return = false) {
        $request = Yii::app()->getRequest();

        if ($request->isAjaxRequest) {
            $this->renderPartial($view, $data, $return, false);
        } else {
            parent::render($view, $data, $return);
        }
    }
    
    /**
     * Return data to browser as JSON and end application.
     * @param array $data
     */
    protected function renderJSON($data)
    {
        header('Content-type: application/json');
        echo CJSON::encode($data);

        foreach (Yii::app()->log->routes as $route) {
            if($route instanceof CWebLogRoute) {
                $route->enabled = false; // disable any weblogroutes
            }
        }
        Yii::app()->end();
    }  
}