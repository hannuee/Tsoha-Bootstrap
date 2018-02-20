<?php

class OrderController extends BaseController{
    
    public static function index($id){
        self::check_user_logged_in();
        
        $olutera = Olutera::oneAvailableWithMargin($id, 400);
        $pakkaustyypit = Pakkaustyyppi::allAvailable();
        
        View::make('order_new.html', array('olutera' => $olutera, 'pakkaustyypit' => $pakkaustyypit));
    }
 
    public static function admin($id){
        self::check_admin_logged_in();
        
        View::make('order_new_admin.html');
    }
    
    public static function saveNew(){
        self::check_user_logged_in();
        
        
    }
}