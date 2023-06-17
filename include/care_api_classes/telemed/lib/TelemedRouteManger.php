<?php
/**
 * Created by PhpStorm.
 * User: Deboner Dulos <deboner.dulos.wise@gmail.com>
 * Date: 3/4/2019
 * Time: 3:53 AM
 */

include_once __DIR__ .'/Route.php';
class TelemedRouteManger extends Route
{



    public function group($callback){
        if($this->isAuthorized()){
            $callback($this);
            $this->onExit();
        }
    }

    public  function get($url, $fn){
        if($this->foundRoute($url, 'GET')){
            echo $this->execute($fn);
        }
    }

    public  function post($url, $fn){
        if($this->foundRoute($url, 'POST')){
            echo $this->execute($fn);
        }
    }


}