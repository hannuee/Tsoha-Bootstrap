<?php

class YritysasiakasController extends BaseController{
    
    // Näkymien kontrollointi:
    
    /**
     * Sisäänkirjautumisnäkymä.
     */
    public static function login(){
        View::make('login.html');
    }
    
    /**
     * Asiakkaan omien tietojen katsominen.
     */
    public static function esittely(){  
        self::check_user_logged_in();
        
        $yritysasiakas = self::get_user_logged_in();
        
        View::make('Yritysasiakas_esittely.html', array('yritysasiakas' => $yritysasiakas));
    }
    
    /**
     * Asiakkaan omien tietojen muokkaaminen.
     */
    public static function muokkaus(){
        self::check_user_logged_in();
        
        $yritysasiakas = self::get_user_logged_in();
        
        View::make('Yritysasiakas_muokkaus.html', array('yritysasiakas' => $yritysasiakas));
    }
    
    /**
     * Listaa KAIKKI käyttäjät.
     */
    public static function listaus(){
        self::check_admin_logged_in();
        
        $yritysasiakkaat = Yritysasiakas::all();
        
        View::make('Yritysasiakas_listaus_tyontekijalle.html', array('yritysasiakkaat' => $yritysasiakkaat));
    }
    
    /**
     * Käyttäjän tietojen katsominen kaikilla tiedoilla.
     * @param type $id Käyttäjän ID joka halutaan nähdä.
     */
    public static function esittelyLisatiedoin($id){
        self::check_admin_logged_in();
        
        self::tarkasta_id_ulkoasu($id, '/hallinnointi/yritysasiakkaat');
        
        $yritysasiakas = self::tarkasta_onnistuminen(
                Yritysasiakas::one($id), '/hallinnointi/yritysasiakkaat', 'Tapahtui virhe haettaessa tietoja!', NULL);

        View::make('Yritysasiakas_esittely.html', array('yritysasiakas' => $yritysasiakas));
    }
    
    /**
     * Käyttäjän tietojen muokkaaminen kaikilla vaihtoehdoilla.
     * @param type $id Käyttäjän ID joka halutaan ehkä muokata.
     */
    public static function muokkausLisavaihtoehdoin($id){
        self::check_admin_logged_in();
        
        self::tarkasta_id_ulkoasu($id, '/hallinnointi/yritysasiakkaat');
        
        $yritysasiakas = self::tarkasta_onnistuminen(
                Yritysasiakas::one($id), '/hallinnointi/yritysasiakkaat', 'Tapahtui virhe haettaessa tietoja!', NULL);
        
        View::make('Yritysasiakas_muokkaus.html', array('yritysasiakas' => $yritysasiakas));
    }
    
    /**
     * Lomake jolla voi luoda uuden käyttäjän.
     */
    public static function lisays(){
        self::check_admin_logged_in();
        
        View::make('Yritysasiakas_lisays_tyontekijalle.html');
    }
    
    
    // Lomakkeiden käsittely:
    
    /**
     * Asiakkaan omien tietojen päivitys.
     */
    public static function muokkaa(){
        self::check_user_logged_in();
        
        $params = $_POST;
        
        $yritysasiakas = new Yritysasiakas($params);
        $yritysasiakas->id = $_SESSION['user'];
        

        $errors = $yritysasiakas->customErrors(array('validate_osoite', 'validate_toimitusosoite', 'validate_laskutusosoite', 
                                                     'validate_puhelinnumero', 'validate_sahkoposti', 'validate_salasana'));
        if(count($errors) != 0){
            Redirect::to('/omattiedot/muokkaa', array('errors' => $errors, 'attributes' => $params));
        }
        
        self::tarkasta_onnistuminen(
                $yritysasiakas->updateForCustomer(), '/omattiedot/muokkaa', 'Tapahtui virhe päivittäessä tietoja!', $params);
        
        Redirect::to('/omattiedot', array('message' => 'Tiedot päivitetty onnistuneesti'));
    }
    
    /**
     * Uuden käyttäjän luominen.
     */
    public static function lisaaUusi(){
        self::check_admin_logged_in();
        
        $params = $_POST;
        
        
        if(isset($params['aktiivinen'])){
            $params['aktiivinen'] = 1;
        } else {
            $params['aktiivinen'] = 0;
        }
        if(isset($params['tyontekija'])){
            $params['tyontekija'] = 1;
        } else {
            $params['tyontekija'] = 0;
        }
        $yritysasiakas =  new Yritysasiakas($params);

        
        $errors = $yritysasiakas->errors();
        if(count($errors) != 0){
            Redirect::to('/hallinnointi/yritysasiakkaat/teeuusi', array('errors' => $errors, 'attributes' => $params));
        }
        
        self::tarkasta_onnistuminen(
                $yritysasiakas->save(), '/hallinnointi/yritysasiakkaat/teeuusi', 'Tapahtui virhe tallennettaessa uutta käyttäjää!', $params);
        
        Redirect::to('/hallinnointi/yritysasiakkaat/' . $yritysasiakas->id, array('message' => 'Tiedot päivitetty onnistuneesti'));
    }
    
    /**
     * Käyttäjän tietojen päivitys.
     */
    public static function muokkaaLisavaihtoehdoin(){
        self::check_admin_logged_in();
        
        $params = $_POST;
        
        if(isset($params['aktiivinen'])){
            $params['aktiivinen'] = 1;
        } else {
            $params['aktiivinen'] = 0;
        }
        if(isset($params['tyontekija'])){
            $params['tyontekija'] = 1;
        } else {
            $params['tyontekija'] = 0;
        }
        $yritysasiakas =  new Yritysasiakas($params);
        
        
        self::tarkasta_id_ulkoasu($params['id'], '/hallinnointi/yritysasiakkaat');
        
        $errors = $yritysasiakas->errors();
        if(count($errors) != 0){            
            Redirect::to('/hallinnointi/yritysasiakkaat/muokkaa/' . $params['id'], array('errors' => $errors, 'attributes' => $params));
        }
        
        self::tarkasta_onnistuminen(
                $yritysasiakas->update(), '/hallinnointi/yritysasiakkaat/muokkaa/' . $params['id'], 'Tapahtui virhe tallennettaessa muutoksia!', $params);
        
        Redirect::to('/hallinnointi/yritysasiakkaat/' . $yritysasiakas->id, array('message' => 'Tiedot päivitetty onnistuneesti'));
    }
    
    /**
     * Sisäänkirjautumisyrityksen käsittely.
     */
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
    
    /**
     * Uloskirjautumispyynnön toteuttaminen.
     */
    public static function logout(){
        $_SESSION['user'] = null;
        $_SESSION['admin'] = null;
        $_SESSION['name'] = null;
        Redirect::to('/kirjautuminen', array('message' => 'Olet kirjautunut ulos!'));
    }
    
}

