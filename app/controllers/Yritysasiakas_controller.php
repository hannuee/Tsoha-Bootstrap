<?php

class YritysasiakasController extends BaseController{
    
    // Näkymien kontrollointi:
    
    public static function login(){
        View::make('login.html');
    }
    
    public static function esittely(){  
        self::check_user_logged_in();
        
        $yritysasiakas = self::get_user_logged_in();
        if(!$yritysasiakas){
            View::make('Yritysasiakas_esittely.html', array('errors' => array('Tapahtui virhe haettaessa tietoja!')));
        }
        
        View::make('Yritysasiakas_esittely.html', array('yritysasiakas' => $yritysasiakas));
    }
    
    public static function muokkaus(){
        self::check_user_logged_in();
        
        $yritysasiakas = self::get_user_logged_in();
        if(!$yritysasiakas){
            View::make('Yritysasiakas_muokkaus.html', array('errors' => array('Tapahtui virhe haettaessa tietoja!')));
        }
        
        View::make('Yritysasiakas_muokkaus.html', array('yritysasiakas' => $yritysasiakas));
    }
    
    public static function listaus(){
        self::check_admin_logged_in();
        
        $yritysasiakkaat = Yritysasiakas::all();
        View::make('Yritysasiakas_listaus_tyontekijalle.html', array('yritysasiakkaat' => $yritysasiakkaat));
    }
    
    public static function esittelyLisatiedoin($id){
        self::check_admin_logged_in();
        
        
        $idSyntaxError = BaseModel::validate_id_directly($id);
        if(count($idSyntaxError) != 0){
            Redirect::to('/hallinnointi/yritysasiakkaat', array('errors' => $idSyntaxError));
        }
        
        
        $yritysasiakas = Yritysasiakas::one($id);
        if(!$yritysasiakas){
            View::make('Yritysasiakas_esittely.html', array('errors' => array('Tapahtui virhe haettaessa tietoja!')));
        }
        
        
        View::make('Yritysasiakas_esittely.html', array('yritysasiakas' => $yritysasiakas));
    }
    
    public static function muokkausLisavaihtoehdoin($id){
        self::check_admin_logged_in();
        
        
        $idSyntaxError = BaseModel::validate_id_directly($id);
        if(count($idSyntaxError) != 0){
            Redirect::to('/hallinnointi/yritysasiakkaat', array('errors' => $idSyntaxError));
        }
        
        
        $yritysasiakas = Yritysasiakas::one($id);
        if(!$yritysasiakas){
            View::make('Yritysasiakas_muokkaus.html', array('errors' => array('Tapahtui virhe haettaessa tietoja!')));
        }
        
        View::make('Yritysasiakas_muokkaus.html', array('yritysasiakas' => $yritysasiakas));
    }
    
    public static function lisays(){
        self::check_admin_logged_in();
        
        View::make('Yritysasiakas_lisays_tyontekijalle.html');
    }
    
    
    // Lomakkeiden käsittely:
    
    public static function muokkaa(){
        self::check_user_logged_in();
        
        $params = $_POST;
        
        $yritysasiakas = new Yritysasiakas(array(
            'id' => $_SESSION['user'],
            'osoite' => $params['osoite'],
            'toimitusosoite' => $params['toimitusosoite'],
            'laskutusosoite' => $params['laskutusosoite'],
            'puhelinnumero' => $params['puhelinnumero'],
            'sahkoposti' => $params['sahkoposti'],
            'salasana' => $params['salasana']));
        

        $errors = $yritysasiakas->customErrors(array('validate_osoite', 'validate_toimitusosoite', 'validate_laskutusosoite', 
                                                     'validate_puhelinnumero', 'validate_sahkoposti', 'validate_salasana'));
        if(count($errors) != 0){
            Redirect::to('/omattiedot/muokkaa', array('errors' => $errors, 'attributes' => 
                array(
            'osoite' => $params['osoite'],
            'toimitusosoite' => $params['toimitusosoite'],
            'laskutusosoite' => $params['laskutusosoite'],
            'puhelinnumero' => $params['puhelinnumero'],
            'sahkoposti' => $params['sahkoposti'],
            'salasana' => $params['salasana'])));
        }
        
        $onnistuiko = $yritysasiakas->updateForCustomer();
        if(!$onnistuiko){
            Redirect::to('/omattiedot/muokkaa', array('errors' => array('Tapahtui virhe päivittäessä tietoja!'), 'attributes' => 
                array(
            'osoite' => $params['osoite'],
            'toimitusosoite' => $params['toimitusosoite'],
            'laskutusosoite' => $params['laskutusosoite'],
            'puhelinnumero' => $params['puhelinnumero'],
            'sahkoposti' => $params['sahkoposti'],
            'salasana' => $params['salasana'])));
        }
        
        Redirect::to('/omattiedot', array('message' => 'Tiedot päivitetty onnistuneesti'));
    }
    
    public static function lisaaUusi(){
        self::check_admin_logged_in();
        
        $params = $_POST;
        
        $aktiivinen;
        if(isset($params['aktiivinen'])){
            $aktiivinen = 1;
        } else {
            $aktiivinen = 0;
        }
        $tyontekija;
        if(isset($params['tyontekija'])){
            $tyontekija = 1;
        } else {
            $tyontekija = 0;
        }
        $yritysasiakas =  new Yritysasiakas(array(
            'yrityksen_nimi' => $params['yrityksen_nimi'],
            'y_tunnus' => $params['y_tunnus'],
            'osoite' => $params['osoite'],
            'toimitusosoite' => $params['toimitusosoite'],
            'laskutusosoite' => $params['laskutusosoite'],
            'puhelinnumero' => $params['puhelinnumero'],
            'sahkoposti' => $params['sahkoposti'],
            'salasana' => $params['salasana'],
            'aktiivinen' => $aktiivinen,
            'tyontekija' => $tyontekija));

        
        $errors = $yritysasiakas->errors();
        if(count($errors) != 0){
            Redirect::to('/hallinnointi/yritysasiakkaat/uusi', array('errors' => $errors, 'attributes' => 
                array(
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
        
        $onnistuiko = $yritysasiakas->save();
        if(!$onnistuiko){
            Redirect::to('/hallinnointi/yritysasiakkaat', array('errors' => array('Tapahtui virhe tallennettaessa uutta käyttäjää!')));
        }
        
        Redirect::to('/hallinnointi/yritysasiakkaat/' . $yritysasiakas->id, array('message' => 'Tiedot päivitetty onnistuneesti'));
    }
    
    public static function muokkaaLisavaihtoehdoin(){
        self::check_admin_logged_in();
        
        $params = $_POST;
        
        $yritysasiakas = new Yritysasiakas(array(
            'id' => $params['id'],
            'yrityksen_nimi' => $params['yrityksen_nimi'],
            'y_tunnus' => $params['y_tunnus'],
            'osoite' => $params['osoite'],
            'toimitusosoite' => $params['toimitusosoite'],
            'laskutusosoite' => $params['laskutusosoite'],
            'puhelinnumero' => $params['puhelinnumero'],
            'sahkoposti' => $params['sahkoposti'],
            'salasana' => $params['salasana']));
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
        
        
        $idSyntaxError = $yritysasiakas->customErrors(array('validate_id'));   //????????????????
        if(count($idSyntaxError) != 0){
            Redirect::to('/hallinnointi/oluterat', array('errors' => $idSyntaxError));
        }
        
        $errors = $yritysasiakas->errors();
        if(count($errors) != 0){            
            Redirect::to('/hallinnointi/yritysasiakkaat/muokkaa/' . $yritysasiakas->id, array('errors' => $errors, 'attributes' => 
                array(
            'id' => $params['id'],   // TURHA??????????????????????????????
            'yrityksen_nimi' => $params['yrityksen_nimi'],
            'y_tunnus' => $params['y_tunnus'],
            'osoite' => $params['osoite'],
            'toimitusosoite' => $params['toimitusosoite'],
            'laskutusosoite' => $params['laskutusosoite'],
            'puhelinnumero' => $params['puhelinnumero'],
            'sahkoposti' => $params['sahkoposti'],
            'salasana' => $params['salasana'],
            'aktiivinen' => $yritysasiakas->aktiivinen,
            'tyontekija' => $yritysasiakas->tyontekija)));
        }
        
        $onnistuiko = $yritysasiakas->update();
        if(!$onnistuiko){
            Redirect::to('/hallinnointi/yritysasiakkaat/muokkaa/' . $yritysasiakas->id, array('errors' => array('Tapahtui virhe tallennettaessa muutoksia!'), 'attributes' => 
                array(
            'id' => $params['id'],   // TURHA??????????????????????????????
            'yrityksen_nimi' => $params['yrityksen_nimi'],
            'y_tunnus' => $params['y_tunnus'],
            'osoite' => $params['osoite'],
            'toimitusosoite' => $params['toimitusosoite'],
            'laskutusosoite' => $params['laskutusosoite'],
            'puhelinnumero' => $params['puhelinnumero'],
            'sahkoposti' => $params['sahkoposti'],
            'salasana' => $params['salasana'],
            'aktiivinen' => $yritysasiakas->aktiivinen,
            'tyontekija' => $yritysasiakas->tyontekija)));
        }
        
        Redirect::to('/hallinnointi/yritysasiakkaat/' . $yritysasiakas->id, array('message' => 'Tiedot päivitetty onnistuneesti'));
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
            $_SESSION['name'] = $corporate_customer->yrityksen_nimi;
            Redirect::to('/hallinnointi/oluterat', array('message' => 'Tervetuloa takaisin ' . $corporate_customer->yrityksen_nimi . '!'));
        } elseif ($corporate_customer->tyontekija == 0) {  // Jos kyseessä normaali yritysasiakas.
            $_SESSION['user'] = $corporate_customer->id;
            $_SESSION['name'] = $corporate_customer->yrityksen_nimi;
            Redirect::to('/', array('message' => 'Tervetuloa takaisin ' . $corporate_customer->yrityksen_nimi . '!'));
        }
    }
    
    public static function logout(){
        $_SESSION['user'] = null;
        $_SESSION['admin'] = null;
        $_SESSION['name'] = null;
        Redirect::to('/kirjautuminen', array('message' => 'Olet kirjautunut ulos!'));
    }
    
}

