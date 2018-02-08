<?php

class OrderController extends BaseController{
    
    public static function index($id){
        self::check_user_logged_in();
        
        $olutera = Olutera::oneAvailableWithMargin($id, 400);
        View::make('order.html', array('olutera' => $olutera));
    }
 
    public static function admin($id){
        self::check_admin_logged_in();
        
        View::make('orderpageAdmin.html');
    }
}