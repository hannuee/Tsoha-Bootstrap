<?php

class CorporateCustomerController extends BaseController{
    
    // ASIAKASTIETOSIVUT:
    
    public static function index(){
        self::check_user_logged_in();
        
        View::make('customerpage.html');
    }
    
    public static function admin(){
        self::check_admin_logged_in();
        
        View::make('customerpageAdmin.html');
    }
    
    
    // KIRJAUTUMISSIVU:
    
    public static function login(){
        View::make('login.html');
    }
    
    public static function handle_login(){
        $params = $_POST;
        
        // Tarkistetaan että käyttäjä ei ole jo kirjautunut sisään jollakin tunnuksella.
        if(isset($_SESSION['user']) || isset($_SESSION['admin'])){
            Redirect::to('/kirjautuminen', array('error' => 'Olet jo kirjautunut sisään! Jos haluat kirjautua sisään jollakin toisella tunnuksella, kirjaudu ulos ensin!'));
        }
        
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
    
    public static function logout(){
        $_SESSION['user'] = null;
        $_SESSION['admin'] = null;
        Redirect::to('/kirjautuminen', array('message' => 'Olet kirjautunut ulos!'));
    }
    
}

