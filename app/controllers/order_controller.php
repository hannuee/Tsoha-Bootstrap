<?php

class OrderController extends BaseController{
    
    public static function index($id){
        self::check_user_logged_in();
        
        $olutera = Olutera::oneAvailableWithMargin($id, 400);
        $pakkaustyypit = Pakkaustyyppi::allAvailable();
        
        View::make('order_new.html', array('olutera' => $olutera, 'pakkaustyypit' => $pakkaustyypit));
    }
 
    public static function admin($id){
        self::check_admin_logged_in();
        
        View::make('order_new_admin.html');
    }
    
    public static function saveNew(){
        self::check_user_logged_in();
        
        $params = $_POST;
        
        $osatilaukset = array();
        $allErrors = array();
        
        // Otetaan talteen lomakkeen tiedot: pakkaustyyppien id:t ja kyseisten pakkaustyyppien määrät.
        foreach($params as $key => $value){
            if(strpos($key, "quantity")){
                $pakkaustyyppi_id = str_replace("quantity", "", $key);
                $lukumaara = $value;
                
                $osatilaus = new TilausPakkaustyyppi(array(
                    'pakkaustyyppi_id' => $pakkaustyyppi_id,
                    'lukumaara' => $lukumaara
                ));
                $errors = $osatilaus->errors();        // instanceVariablesToDatabaseForm() seuraavaks ???????????????????????????
                
                $osatilaukset = array_merge($osatilaukset, $osatilaus);
                $allErrors = array_merge($allErrors, $errors);
            }
        }
        
        // Jos erroreita osatilauksissa niin redirect errormessagein takas tilauslomakkeeseen.
        if(count($allErrors) != 0){
            Redirect::to('/tilaukset/uusi/' . $params['olutera_id'], array('errors' => $allErrors));
        }
        // ^TODO: ATTRIBUUTIT!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        
        
        $tilausajankohta = new DateTime();
        $tilausajankohta->format('Y-m-d');   // TOISTASEKS HOIDETTU SQL NOW():LLA.!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        // Tilausolion teko ja samalla Oluterä id:n syntax check
        $tilaus = new Tilaus(array(
            'tilausajankohta' => $tilausajankohta,
            'toimitettu' => 0,
            'toimitusohjeet' => $params['toimitusohjeet'],
            'olutera_id' => $params['olutera_id'],
            'yritysasiakas_id' => $_SESSION['user']   // JOSTAIN SIISTISTI METODIN KAUTTA????????????????????????????
        ));
        $tilausErrors = $tilaus->errors();   // instanceVariablesToDatabaseForm() seuraavaks ???????????????????????????
        
        // Jos erroreita oluterän id:ssä niin redirect etusivulle, koska ei tietoa oluterän id:stä.
        if(count($tilausErrors) != 0){
            Redirect::to('/', array('errors' => $tilausErrors));
        }
        
        
        $senttilitroja = 0;
        // Tarkastetaan että kaikki pakkaustyypit ovat saatavilla(jos virheellisen lomakkeen "väärennys" TAI pakkauksen saatavuus juuri vaihtunut).
        // Lasketaan samalla paljonko olutta on senttilitroissa tilattu.
        $pakkausErrors = array();
        foreach($osatilaukset as $osatilaus){
            $pakkaustyyppi = Pakkaustyyppi::one($osatilaus->pakkaustyyppi_id);
            if(is_null($pakkaustyyppi)){
                $pakkausErrors = array_merge($pakkausErrors, array("Lomake sisälsi virheellisen pakkaustyypin ID:n: " . $osatilaus->pakkaustyyppi_id));
            } elseif($pakkaustyyppi->saatavilla == 0){
                $pakkausErrors = array_merge($pakkausErrors, array("Pakkaustyyppi " . $pakkaustyyppi->pakkaustyypin_nimi . " ei valitettavasti enää ole saatavilla."));
            } else {
                $senttilitroja += $osatilaus->lukumaara * $pakkaustyyppi->vetoisuus;
            }
        }
        
        // Jos erroreita pakkaustyypeissä niin redirect errormessagein takas tilauslomakkeeseen.
        if(count($pakkausErrors) != 0){
            Redirect::to('/tilaukset/uusi/' . $params['olutera_id'], array('errors' => $pakkausErrors));
        }
        // ^TODO: ATTRIBUUTIT!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        
        // Tarkistetaan että oluterän ID ok ja että oluterässä riittävästi vapaana olutta.
        $oluteraErrors = array();
        $olutera = Olutera::one($tilaus->olutera_id);
        if(is_null($olutera)){
            $oluteraErrors = array_merge($oluteraErrors, array("Virheellinen oluterän ID!"));  // REDIRECT EI-LOMAKKEESEEN!!!??
        } elseif($olutera->vapaana < $senttilitroja){
            $oluteraErrors = array_merge($oluteraErrors, array("Oluterässä ei tarpeeksi litroja vapaana!"));
        }
        
        // Jos erroreita oluterässä niin redirect errormessagein takas tilauslomakkeeseen.
        if(count($oluteraErrors) != 0){
            Redirect::to('/tilaukset/uusi/' . $params['olutera_id'], array('errors' => $oluteraErrors));
        }
        // ^TODO: ATTRIBUUTIT!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        
        
        // Tallennetaan Tilaus-olio, TilausPakkaustyyppi-oliot(ja tallennetaan niihin tilaus_id) sekä vähennetään kyseisen oluterän vapaana olevan oluen määrää.
        $olutera->vapaana = $olutera->vapaana * 100 - $senttilitroja;
        $olutera->updateAmountAvailable();
        
        $tilaus->save();
        
        foreach($osatilaukset as $osatilaus){
            $osatilaus->tilaus_id = $tilaus->id;
            $osatilaus->save();
        }
        
        Redirect::to('/', array('message' => 'Tilaus lähetetty onnistuneesti!'));
    }
}