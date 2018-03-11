<?php

class TilausController extends BaseController{
    
    // Näkymien kontrollointi:
    
    public static function lisays($id){
        self::check_user_logged_in();
        
        
        $idSyntaxError = BaseModel::validate_id_directly($id);
        if(count($idSyntaxError) != 0){
            Redirect::to('/', array('errors' => $idSyntaxError));
        }
        
        $olutera = Olutera::oneAvailableWithMargin($id, 400);
        if(!$olutera){
            Redirect::to('/', array('errors' => array('Etsimääsi oluterää ei löytynyt!')));
        }
        
        
        $olutera->oliomuuttujatTietokantamuodostaEsitysmuotoon();
        
        
        $pakkaustyypit = Pakkaustyyppi::allAvailable();
        BaseModel::olioidenMuuttujatTietokantamuodostaEsitysmuotoon($pakkaustyypit);
        
        View::make('Tilaus_lisays.html', array('olutera' => $olutera, 'pakkaustyypit' => $pakkaustyypit));
    }
 
    public static function lisaysLisavaihtoehdoin($id){
        self::check_admin_logged_in();
        
        
        $idSyntaxError = BaseModel::validate_id_directly($id);
        if(count($idSyntaxError) != 0){
            Redirect::to('/hallinnointi/oluterat', array('errors' => $idSyntaxError));
        }
        
        
        $olutera = Olutera::one($id);
        if(!$olutera){
            Redirect::to('/hallinnointi/oluterat', array('errors' => array('Etsimääsi oluterää ei löytynyt!')));
        }
        
        $olutera->oliomuuttujatTietokantamuodostaEsitysmuotoon();
        
        
        $pakkaustyypit = Pakkaustyyppi::allAvailable();
        BaseModel::olioidenMuuttujatTietokantamuodostaEsitysmuotoon($pakkaustyypit);
        
        $yritysasiakkaatKaikki = Yritysasiakas::all();
        // Poistetaan kaikki työntekijät, näin sisäänkirjautunut työntekijä voi varata
        // olutta pienpanimon omaan käyttöön vain omiin tunnuksiinsa
        // JA ennen kaikkea lista yritysasiakkaista on siistimpi.
        $yritysasiakkaat = array();
        foreach($yritysasiakkaatKaikki as $yritysasiakas){
            if($yritysasiakas->tyontekija == 0){
                $yritysasiakkaat[] = $yritysasiakas;
            }
        }
        
        View::make('Tilaus_lisays.html', array('olutera' => $olutera, 'pakkaustyypit' => $pakkaustyypit, 'yritysasiakkaat' => $yritysasiakkaat));
    }
    
    
    // Lomakkeiden käsittely:
    
    public static function lisaaUusi(){
        self::check_admin_logged_in();
        
        $params = $_POST;
        
        // Tietojen ottaminen lähetetystä lomakkeesta & Tarkistuksia.
        $olutera = TilausControllerApumetodit::tarkistaLomakkeestaOluteraIdJaPalautaOlutera($params);
        TilausControllerApumetodit::tarkistaLomakkeestaYritysasiakasId($params);
        $tilausPakkaustyypit = TilausControllerApumetodit::tilausPakkaustyyppiMallienTiedotLomakkeesta($params);  // Eli montako kappaletta kutakin pakkausta.
        $tilaus = TilausControllerApumetodit::toimitusohjeetLomakkeestaJaPalautaTilaus($params);
        
        // Tarkistuksia. (Apumetodit saavat lomakkeen tiedot ainoastaan virheestä johtuvaa uudelleenohjausta varten.)
        $senttilitroja = TilausControllerApumetodit::senttilitrojenLaskeminenJaPakkaustyyppienTarkistus($tilausPakkaustyypit, $params);
        TilausControllerApumetodit::tarkistaVapaanOluenMaara($senttilitroja, $olutera, $params);
        
        TilausControllerApumetodit::lisaaUusiTilaus($senttilitroja, $tilaus, $tilausPakkaustyypit, $olutera, $params);
    }
    
    public static function muokkaaToimitetuksi(){
        self::check_admin_logged_in();
        
        $params = $_POST;   
        
        
        $idSyntaxError = BaseModel::validate_id_directly($params['tilaus_id']);
        if(count($idSyntaxError) != 0){
            Redirect::to('/hallinnointi/oluterat', array('errors' => $idSyntaxError));
        }
    
        
        // Oluterä_id:tä turha tarkastaa, se laitetaan lomakkeessa mukana vain redirectiä varten, ja sivut joihin redirectetään
        // osaavat händlätä virheelliset oluterä_id:t.
        $onnistuiko = Tilaus::updateDeliveryStatus($params['tilaus_id']);       
        if(!$onnistuiko){
            Redirect::to('/hallinnointi/oluterat/' . $params['olutera_id'], array('errors' => array('Tapahtui virhe merkittäessä tilausta toimitetuksi!')));
        }
        
        Redirect::to('/hallinnointi/oluterat/' . $params['olutera_id'], array('message' => 'Tilaus merkitty toimitetuksi!'));
    }
    
    public static function poista(){
        self::check_admin_logged_in();
        
        $params = $_POST;
        
        
        $idSyntaxError = BaseModel::validate_id_directly($params['tilaus_id']);
        if(count($idSyntaxError) != 0){
            Redirect::to('/hallinnointi/oluterat', array('errors' => $idSyntaxError));
        }
        
        
        // Lasketaan montako litraa pitää vapauttaa oluterästä.
        $senttilitraa = 0;
        $pakkaustyypitJaLukumaarat = Pakkaustyyppi::allForOrder($params['tilaus_id']);
        foreach($pakkaustyypitJaLukumaarat as $pakkaustyyppiJalukumaara){
            $senttilitraa += $pakkaustyyppiJalukumaara[0]->vetoisuus * 100 * $pakkaustyyppiJalukumaara[1];
        }
        
        
        
        $connection = BaseModel::beginTransaction();
        if(!$connection){
            Redirect::to('/hallinnointi/oluterat/' . $params['olutera_id'], array('errors' => array('Virhe poistettaessa tilausta, tilausta ei poistettu!')));
        }
        
        $olutera_id = Tilaus::deleteTRANS($params['tilaus_id'], $connection);
        if(!$olutera_id){
            Redirect::to('/hallinnointi/oluterat/' . $params['olutera_id'], array('errors' => array('Virhe poistettaessa tilausta, tilausta ei poistettu!')));
        }
       
        $onnistuiko = Olutera::updateAmountAvailableAddTRANS($olutera_id, $senttilitraa, $connection);
        if(!$onnistuiko){
            Redirect::to('/hallinnointi/oluterat/' . $params['olutera_id'], array('errors' => array('Virhe poistettaessa tilausta, tilausta ei poistettu!')));
        }
        
        $onnistuiko = BaseModel::commit($connection);
        if(!$onnistuiko){
            Redirect::to('/hallinnointi/oluterat/' . $params['olutera_id'], array('errors' => array('Virhe poistettaessa tilausta, tilausta ei poistettu!')));
        }
        
        
        Redirect::to('/hallinnointi/oluterat/' . $olutera_id, array('message' => 'Tilaus poistettu! ' . $senttilitraa));    
    }
    
}