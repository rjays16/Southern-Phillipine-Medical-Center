<?php

use SegHis\modules\InventoryAPIServices\components\urlconfig\urlconfig;
use SegHis\modules\InventoryAPIServices\components\api\APIservices;
class ServiceController extends Controller
{
	public $layout = '/layouts/main';
	public function filters()
    {
        return array(
            array('bootstrap.filters.BootstrapFilter')
        );
    }
    public function setUpURL(){
    	/*HTTP default to 'http://'
		 you may change by instance of the class in given arguments that passed to the class constructor
		 e.g: urlconfig('https://')*/
		 $DaiUrl =  new urlconfig();
    	return $DaiUrl->getLink();
    }
	
	public function actionIndex()
	{	
		 $rest = new APIservices();
		 $data = $rest->getItemsFromDAI("8fd001e3bf76b2a1306bf123efc9a8a0",'GET_TOTAL_ITEMS');
		
		 $this->render('index', array(
            'data' => $data
        ));
	}
}