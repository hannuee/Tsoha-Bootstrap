<?php

class PackageTypeController extends BaseController{
    
    public static function admin(){
        self::check_admin_logged_in();
        
        View::make('package_type_admin.html');
    }
    
}