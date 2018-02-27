<?php

  class HelloWorldController extends BaseController{

    public static function index(){
        // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	echo 'Tämä on sivu';
    }

    public static function sandbox(){
        // Kint::dump();
    }
    
  }
