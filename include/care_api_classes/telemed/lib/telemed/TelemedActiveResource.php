<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__.'/TelemedRoutes.php';

class TelemedActiveResource extends TelemedRoutes
{



    public function __construct($personelID, $personelUname) {
        parent::__construct($personelID, $personelUname);
    }


}