<?php

class OluteraController extends BaseController{
    
    // Näkymien kontrollointi:
    
    public static function listausMarginaalilla(){
        self::check_user_logged_in();
        
        $oluterat = Olutera::allAvailableWithMargin(400);
        BaseModel::olioidenMuuttujatTietokantamuodostaEsitysmuotoon($oluterat);
        
        View::make('Olutera_listaus.html', array('oluterat' => $oluterat));
    }
    
    public static function listaus(){
        self::check_admin_logged_in();
        
        $oluterat = Olutera::all();
        BaseModel::olioidenMuuttujatTietokantamuodostaEsitysmuotoon($oluterat);
        
        View::make('Olutera_listaus.html', array('oluterat' => $oluterat));
    }

    // Esittelee Oluterän sekä oluterään liittyvät tilaukset.
    // Myös oluterän ja tilausten muokkaus ja poisto.
    public static function esittely($id){  
        self::check_admin_logged_in();
        
        self::tarkasta_id_ulkoasu($id, '/hallinnointi/oluterat');
        
        $olutera = self::tarkasta_onnistuminen(
                Olutera::one($id), '/hallinnointi/oluterat', 'Etsimääsi oluterää ei löytynyt!', NULL);
        
        $olutera->oliomuuttujatTietokantamuodostaEsitysmuotoon();
        
        $tilausrivit = array();
        
        // Haetaan ensin Oluterään liittyvät tilaukset.
        $tilaukset = Tilaus::allForBeerBatch($id);
        
        // Sitten liitetään jokaiseen tilaukseen tieto tilaajasta ja tilauksen sisällöstä.
        foreach($tilaukset as $tilaus){
            $tilausrivi = array();
            
            $tilausrivi[] = Yritysasiakas::one($tilaus->yritysasiakas_id);
            $tilausrivi[] = $tilaus;
            $tilausrivi[] = Pakkaustyyppi::allForOrder($tilaus->id);
            
            $tilausrivit[] = $tilausrivi;
        }
        
        View::make('Olutera_esittely_tyontekijalle.html', array('olutera' => $olutera, 'tilausrivit' => $tilausrivit));
    }
    
    
    // Lomakkeiden käsittely:
    
    public static function lisaaUusi(){
        self::check_admin_logged_in();     
        
        $params = $_POST;
        
        $olutera = new Olutera($params);
        $olutera->vapaana = $olutera->eran_koko; // vapaana = eran_koko, koska koko erä on tietenkin vapaana kun erä luodaan.
        
        
        $errors = $olutera->errors();
        if(count($errors) != 0){
            Redirect::to('/hallinnointi/oluterat', array('errors' => $errors, 'attributes' => $params));
        }
        
        $olutera->oliomuuttujatLomakemuodostaTietokantamuotoon();
        
        self::tarkasta_onnistuminen(
                $olutera->save(), '/hallinnointi/oluterat', 'Tapahtui virhe tallennettaessa oluterää!', $params);
        
        Redirect::to('/hallinnointi/oluterat', array('message' => 'Uusi oluterä lisätty onnistuneesti!'));
    }
    
    public static function muokkaaValmistumispaivamaaraa(){
        self::check_admin_logged_in();
        
        $params = $_POST;
        
        
        self::tarkasta_id_ulkoasu($params['id'], '/hallinnointi/oluterat');
        
        $valmistuminenError = Olutera::validate_valmistuminen_staattinen($params['valmistuminen']);
        if(count($valmistuminenError) != 0){
            Redirect::to('/hallinnointi/oluterat/' . $params['id'], array('errors' => $valmistuminenError, 'attributes' => $params));
        }
        
        
        self::tarkasta_onnistuminen(
                Olutera::updateDate($params['id'], $params['valmistuminen']), '/hallinnointi/oluterat/' . $params['id'], 'Tapahtui virhe muuttaessa päivämäärää!', $params);
        
        Redirect::to('/hallinnointi/oluterat/' . $params['id'], array('message' => 'Valmistumispäivämäärä muutettu onnistuneesti!'));
    }
    
    public static function poista(){
        self::check_admin_logged_in();
        
        $params = $_POST;
            
        self::tarkasta_id_ulkoasu($params['id'], '/hallinnointi/oluterat');
        
        self::tarkasta_onnistuminen(
                Olutera::delete($params['id']), '/hallinnointi/oluterat/' . $params['id'], 'Tapahtui virhe poistaessa oluterää!', NULL);
        
        Redirect::to('/hallinnointi/oluterat', array('message' => 'Oluterä ja sen tilaukset on poistettu onnistuneesti!'));
    }
    
}