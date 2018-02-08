<?php

class CorporateCustomerController extends BaseController{
    
    public static function index(){
        self::check_user_logged_in();
        
        View::make('customerpage.html');
    }
    
    public static function admin(){
        self::check_admin_logged_in();
        
        View::make('customerpageAdmin.html');
    }
    
    public static function login(){
        View::make('login.html');
    }
    
    public static function handle_login(){
        $params = $_POST;
        
        $corporate_customer = Yritysasiakas::authenticate($params['email'], $params['password']);
        
        if(!$corporate_customer){
            View::make('login.html', array('error' => 'Väärä käyttäjätunnus tai salasana!', 'email' => $params['email']));
        } elseif ($corporate_customer->tyontekija == 1) {  // Jos kyseessä työntekijä.
            $_SESSION['admin'] = $corporate_customer->id;
            Redirect::to('/hallinnointi/oluterat', array('message' => 'Tervetuloa takaisin ' . $corporate_customer->yrityksen_nimi . '!'));
        } elseif ($corporate_customer->tyontekija == 0) {  // Jos kyseessä normaali yritysasiakas.
            $_SESSION['user'] = $corporate_customer->id;
            Redirect::to('/', array('message' => 'Tervetuloa takaisin ' . $corporate_customer->yrityksen_nimi . '!'));
        }
    }
    
}

