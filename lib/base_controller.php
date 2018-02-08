<?php

  class BaseController{
      
    public static function get_user_logged_in(){
        if(isset($_SESSION['user'])){
          $id = $_SESSION['user'];
          $corporate_customer = Yritysasiakas::find($id);

          return $corporate_customer;
        }

        return null;
    }
    
    public static function get_admin_logged_in(){
        if(isset($_SESSION['admin'])){
          $id = $_SESSION['admin'];
          $corporate_customer = Yritysasiakas::find($id);

          return $corporate_customer;
        }

        return null;
    }

    public static function check_user_logged_in(){
        if(!isset($_SESSION['user'])){
            View::make('login.html', array('error' => 'Yrittämällesi sivulle pääsee vain sisäänkirjautuneet yritysasiakkaat!'));
        }
    }
    
    public static function check_admin_logged_in(){
        if(!isset($_SESSION['admin'])){
            View::make('login.html', array('error' => 'Yrittämällesi sivulle pääsee vain sisäänkirjautuneet työntekijät!'));
        }
    }

  }
