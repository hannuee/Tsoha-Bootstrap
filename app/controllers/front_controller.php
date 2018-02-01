<?php

class FrontController extends BaseController{
    
    public static function index(){
        $oluterat = Oluterat::all();
        View::make('');
    }
    
}
