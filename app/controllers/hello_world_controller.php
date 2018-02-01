<?php

require 'app/models/olutera.php';

  class HelloWorldController extends BaseController{

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	  echo 'Tämä on sivu';
    }

    public static function sandbox(){
      $oluterat = Olutera::findAll();
      $olutera = Olutera::find(1);
      
      Kint::dump($oluterat);
      Kint::dump($olutera);
    }
    
    public static function frontpage(){
      // Testaa koodiasi täällä
      View::make('suunnitelmat/frontpage.html');
    }
    
    public static function frontpageAdmin(){
      // Testaa koodiasi täällä
      View::make('suunnitelmat/frontpageAdmin.html');
    }
    
    public static function customerpage(){
      // Testaa koodiasi täällä
      View::make('suunnitelmat/customerpage.html');
    }
    
    public static function customerpageAdmin(){
      // Testaa koodiasi täällä
      View::make('suunnitelmat/customerpageAdmin.html');
    }
    
    public static function orderpage(){
      // Testaa koodiasi täällä
      View::make('suunnitelmat/orderpage.html');
    }
    
    public static function orderpageAdmin(){
      // Testaa koodiasi täällä
      View::make('suunnitelmat/orderpageAdmin.html');
    }
    
    public static function patchpageAdmin(){
      // Testaa koodiasi täällä
      View::make('suunnitelmat/patchpageAdmin.html');
    }
    
  }
