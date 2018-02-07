<?php

class OrderController extends BaseController{
    
    public static function index($id){
        $olutera = Olutera::oneAvailableWithMargin($id, 400);
        View::make('order.html', array('olutera' => $olutera));
    }
 
    public static function admin($id){
        View::make('orderpageAdmin.html');
    }
}