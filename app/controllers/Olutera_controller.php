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
        
        
        $idSyntaxError = BaseModel::validate_id_directly($id);
        if(count($idSyntaxError) != 0){
            Redirect::to('/hallinnointi/oluterat', array('errors' => $idSyntaxError));
        }
        
        
        $olutera = Olutera::one($id);
        
        
        if(is_null($olutera)){
            Redirect::to('/hallinnointi/oluterat', array('errors' => array('Etsimääsi oluterää ei löytynyt!')));
        }
        
        
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
        
        $olutera = new Olutera(array(
            'oluen_nimi' => $params['oluen_nimi'],
            'valmistuminen' => $params['valmistuminen'],
            'eran_koko' => $params['eran_koko'],
            'vapaana' => $params['eran_koko'],  // vapaana = eran_koko, koska koko erä on tietenkin vapaana kun erä luodaan. 
            'hinta' => $params['hinta']
        ));
        
        
        $errors = $olutera->errors();
        if(count($errors) != 0){
            Redirect::to('/hallinnointi/oluterat', array('errors' => $errors, 'attributes' => 
                array(
            'oluen_nimi' => $params['oluen_nimi'],
            'valmistuminen' => $params['valmistuminen'],
            'eran_koko' => $params['eran_koko'],
            'hinta' => $params['hinta'])));
        }
        
        
        $olutera->oliomuuttujatLomakemuodostaTietokantamuotoon();
        
        $onnistuiko = $olutera->save();
        if(!$onnistuiko){
            Redirect::to('/hallinnointi/oluterat', array('errors' => array('Tapahtui virhe tallennettaessa oluterää!')));
        }
        
        Redirect::to('/hallinnointi/oluterat', array('message' => 'Uusi oluterä lisätty onnistuneesti!'));
    }
    
    public static function muokkaaValmistumispaivamaaraa(){
        self::check_admin_logged_in();
        
        $params = $_POST;
        
        
        $idSyntaxError = BaseModel::validate_id_directly($params['id']);
        if(count($idSyntaxError) != 0){
            Redirect::to('/hallinnointi/oluterat', array('errors' => $idSyntaxError));
        }
        
        $valmistuminenError = Olutera::validate_valmistuminen_staattinen($params['valmistuminen']);
        if(count($valmistuminenError) != 0){
            Redirect::to('/hallinnointi/oluterat/' . $params['id'], array('errors' => $valmistuminenError));  // ATTRIBUUTIT??????
        }
        
        
        $onnistuiko = Olutera::updateDate($params['id'], $params['valmistuminen']);
        if(!$onnistuiko){
            Redirect::to('/hallinnointi/oluterat/' . $params['id'], array('errors' => array('Tapahtui virhe muuttaessa päivämäärää!')));
        }
        
        Redirect::to('/hallinnointi/oluterat/' . $params['id'], array('message' => 'Valmistumispäivämäärä muutettu onnistuneesti!'));
    }
    
    public static function poista(){
        self::check_admin_logged_in();
        
        $params = $_POST;
            
        
        $idSyntaxError = BaseModel::validate_id_directly($params['id']);
        if(count($idSyntaxError) != 0){
            Redirect::to('/hallinnointi/oluterat', array('errors' => $idSyntaxError));
        }
        
        
        $onnistuiko = Olutera::delete($params['id']);
        if(!$onnistuiko){
            Redirect::to('/hallinnointi/oluterat', array('errors' => array('Tapahtui virhe poistaessa oluterää!')));
        }
        
        Redirect::to('/hallinnointi/oluterat', array('message' => 'Oluterä ja sen tilaukset on poistettu onnistuneesti!'));
    }
    
}