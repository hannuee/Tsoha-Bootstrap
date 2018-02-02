<?php

class OrderController extends BaseController{
    
    public static function index($id){
        $olutera = Olutera::oneAvailableWithMargin($id, 400);
        View::make('orderpage.html', array('oluterat' => $olutera));
    }
    
}