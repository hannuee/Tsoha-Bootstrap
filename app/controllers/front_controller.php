<?php

class FrontController extends BaseController{
    
    public static function index(){
        $oluterat = Olutera::allAvailableWithMargin(400);
        View::make('frontpage.html', array('oluterat' => $oluterat));
    }
    
}
