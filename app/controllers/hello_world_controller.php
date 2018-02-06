<?php

  class HelloWorldController extends BaseController{

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	  echo 'Tämä on sivu';
    }

    public static function sandbox(){
      $olutera = new Olutera(array(
    'oluen_nimi' => '',
    'valmistuminen' => '2018-08-21',
    'eran_koko' => '400',
    'vapaana' => '400',
    'hinta' => '555'
          ));
      
      $olutera2 = new Olutera(array(
    'oluen_nimi' => 'Olvi',
    'valmistuminen' => '2018-08-21',
    'eran_koko' => '-400',
    'vapaana' => '400',
    'hinta' => 'a'
          ));
      
      Kint::dump($olutera->errors());
      Kint::dump($olutera2->errors());
    }
    
    public static function frontpage(){
      // Testaa koodiasi täällä
      View::make('frontpage.html');
    }
    
    public static function frontpageAdmin(){
      // Testaa koodiasi täällä
      View::make('frontpageAdmin.html');
    }
    
    public static function customerpage(){
      // Testaa koodiasi täällä
      View::make('customerpage.html');
    }
    
    public static function customerpageAdmin(){
      // Testaa koodiasi täällä
      View::make('customerpageAdmin.html');
    }
    
    public static function orderpage(){
      // Testaa koodiasi täällä
      View::make('orderpage.html');
    }
    
    public static function orderpageAdmin(){
      // Testaa koodiasi täällä
      View::make('orderpageAdmin.html');
    }
    
    public static function patchpageAdmin(){
      // Testaa koodiasi täällä
      View::make('patchpageAdmin.html');
    }
    
  }
