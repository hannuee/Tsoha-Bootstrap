<?php

class CorporateCustomerController extends BaseController{
    
    public static function index(){
        View::make('customerpage.html');
    }
    
    public static function admin(){
        View::make('customerpageAdmin.html');
    }
    
}

