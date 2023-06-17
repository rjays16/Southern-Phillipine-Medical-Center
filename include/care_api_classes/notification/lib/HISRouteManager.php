<?php
/**
 * Created by PhpStorm.
 * User: Deboner Dulos <deboner.dulos.wise@gmail.com>
 * Date: 3/5/2019
 * Time: 7:01 PM
 */

include_once __DIR__ .'/NotificationCurl.php';
class HISRouteManager extends NotificationCurl
{
    private static $_router;

    function __construct()
    {
        parent::__construct();
    }

    public static function init(){
        if (static::$_router === null) {
            static::$_router = new HISRouteManager();
            static::$_router->_initRoutes();
//            echo ' <b>HISRouteManager</b> ';
        }
        return static::$_router;
    }



    private function _initRoutes(){
        $ehrEndpoint_dic = array(
            //fn name => url path
            'getRecentEncounter' => array(
                'url' => '/api/encounter/recent',
                'method' => 'GET'
            ),
            'getpatientdatacf4' => array(
                'url' => '/api/patient/getpatientdatacf4',
                'method' => 'GET'
            )
        );


        foreach ($ehrEndpoint_dic  as $key => $ent){
            /*
             * @var Array $params => [
             *      'get' => Array [ key => value], <optional>
             *      'post' => Array [ key => value],<optional>
             * ]
             * */
            $ent['parent'] = $this;
            if($ent['method'] == 'POST'){
                $this->{$key} = function ($postArray = array() , $getArray = array()) use ($ent){
                    return $ent['parent']->rest_post($ent['url'], $postArray, $getArray);
                };
            }
            else
            {
                $this->{$key} = function ($getArray = array()) use ($ent){
                    return $ent['parent']->rest_get($ent['url'], $getArray);
                };
            }


        }

    }
}