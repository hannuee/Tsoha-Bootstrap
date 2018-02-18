<?php

class CorporateCustomerController extends BaseController{
    
    // ASIAKASTIETOSIVUT:
    
    public static function index(){
        self::check_user_logged_in();
        
        View::make('customerpage.html');
    }
    
    public static function admin(){
        self::check_admin_logged_in();
        
        $yritysasiakkaat = Yritysasiakas::all();
        View::make('corporate_customer_admin.html', array('yritysasiakkaat' => $yritysasiakkaat));
    }
    
    public static function show($id){
        self::check_admin_logged_in();
        
        $yritysasiakas = Yritysasiakas::one($id);
        View::make('corporate_customer_show.html', array('yritysasiakas' => $yritysasiakas));
    }
    
    public static function edit($id){
        self::check_admin_logged_in();
        
        $yritysasiakas = Yritysasiakas::one($id);
        View::make('corporate_customer_edit.html', array('yritysasiakas' => $yritysasiakas));
    }
    
    public static function update(){
        self::check_admin_logged_in();
        
        $params = $_POST;
        
        $yritysasiakas = Yritysasiakas::one($params['id']);
        
        $yritysasiakas->yrityksen_nimi = $params['yrityksen_nimi'];
        $yritysasiakas->y_tunnus = $params['y_tunnus'];
        $yritysasiakas->osoite = $params['osoite'];
        $yritysasiakas->toimitusosoite = $params['toimitusosoite'];
        $yritysasiakas->laskutusosoite = $params['laskutusosoite'];
        $yritysasiakas->puhelinnumero = $params['puhelinnumero'];
        $yritysasiakas->sahkoposti = $params['sahkoposti'];
        $yritysasiakas->salasana = $params['salasana'];
        if(isset($params['aktiivinen'])){
            $yritysasiakas->aktiivinen = 1;
        } else {
            $yritysasiakas->aktiivinen = 0;
        }
        if(isset($params['tyontekija'])){
            $yritysasiakas->tyontekija = 1;
        } else {
            $yritysasiakas->tyontekija = 0;
        }

        $errors = $yritysasiakas->errors();
 
        if(count($errors) == 0){  // Syötteet valideja.
            $yritysasiakas->update();
            Redirect::to('/hallinnointi/yritysasiakkaat/' . $yritysasiakas->id, array('message' => 'Tiedot päivitetty onnistuneesti'));
        } else {                  // Syötteet ei-valideja.
            Redirect::to('/hallinnointi/yritysasiakkaat/muokkaa/' . $yritysasiakas->id, array('errors' => $errors, 'attributes' => 
                array(
            'id' => $params['id'],
            'yrityksen_nimi' => $params['yrityksen_nimi'],
            'y_tunnus' => $params['y_tunnus'],
            'osoite' => $params['osoite'],
            'toimitusosoite' => $params['toimitusosoite'],
            'laskutusosoite' => $params['laskutusosoite'],
            'puhelinnumero' => $params['puhelinnumero'],
            'sahkoposti' => $params['sahkoposti'],
            'salasana' => $params['salasana'],
            'aktiivinen' => $params['aktiivinen'],
            'tyontekija' => $params['tyontekija'])));
        }
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

