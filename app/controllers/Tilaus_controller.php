<?php

class TilausController extends BaseController{
    
    // Näkymien kontrollointi:
    
    public static function lisays($id){
        self::check_user_logged_in();
        
        $olutera = Olutera::oneAvailableWithMargin($id, 400);
        $olutera->oliomuuttujatTietokantamuodostaEsitysmuotoon();
        $pakkaustyypit = Pakkaustyyppi::allAvailable();
        BaseModel::olioidenMuuttujatTietokantamuodostaEsitysmuotoon($pakkaustyypit);
        
        View::make('Tilaus_lisays.html', array('olutera' => $olutera, 'pakkaustyypit' => $pakkaustyypit));
    }
 
    public static function lisaysLisavaihtoehdoin($id){
        self::check_admin_logged_in();
        
        $olutera = Olutera::one($id);
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
        
        $tilausPakkaustyypit = TilausControllerApumetodit::tilausPakkaustyyppiMallienTiedotLomakkeesta($params);
        $tilaus = TilausControllerApumetodit::tilausMallinTiedotLomakkeesta($params);
        $senttilitroja = TilausControllerApumetodit::senttilitrojenLaskeminenJaPakkaustyyppienTarkistus($tilausPakkaustyypit, $params);
        $olutera = TilausControllerApumetodit::tarkistaOluteranIdJaVapaanOluenMaara($senttilitroja, $tilaus, $params);
        
        TilausControllerApumetodit::lisaaUusiTilaus($senttilitroja, $tilaus, $tilausPakkaustyypit, $olutera);
    }
    
    public static function muokkaaToimitetuksi(){
        self::check_admin_logged_in();
        
        $params = $_POST;
        
        $tilaus = new Tilaus(array(
            'id' => $params['tilaus_id']
        ));
        
        
        $idSyntaxError = $tilaus->customErrors(array('validate_id'));
        if(count($idSyntaxError) != 0){
            Redirect::to('/hallinnointi/oluterat', array('errors' => $idSyntaxError));
        }
        
        // TARKASTA VIÄLÄ LUULTAVASTI OLUTERÄ ID!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        
        
        $tilaus->updateDeliveryStatus();        
        Redirect::to('/hallinnointi/oluterat/' . $params['olutera_id'], array('message' => 'Tilaus merkitty toimitetuksi!'));
    }
    
    public static function poista(){
        self::check_admin_logged_in();
        
        $params = $_POST;
        
        $tilaus = new Tilaus(array(
            'id' => $params['tilaus_id']
        ));
        
        
        $idSyntaxError = $tilaus->customErrors(array('validate_id'));
        if(count($idSyntaxError) != 0){
            Redirect::to('/hallinnointi/oluterat', array('errors' => $idSyntaxError));
        }
        
        
        // Lasketaan montako litraa pitää vapauttaa oluterästä.
        $senttilitraa = 0;
        $pakkaustyypitJaLukumaarat = Pakkaustyyppi::allForOrder($tilaus->id);
        foreach($pakkaustyypitJaLukumaarat as $pakkaustyyppiJalukumaara){
            $senttilitraa += $pakkaustyyppiJalukumaara[0]->vetoisuus * 100 * $pakkaustyyppiJalukumaara[1];
        }
        
        
        $olutera_id = $tilaus->delete();
        if(is_null($olutera_id)){
            Redirect::to('/hallinnointi/oluterat', array('errors' => array('Tapahtui tekninen virhe!')));  // Koita testata tää virheviesti jotenkin.
        }
       
        Olutera::updateAmountAvailableReduce($olutera_id, $senttilitraa);
        Redirect::to('/hallinnointi/oluterat/' . $olutera_id, array('message' => 'Tilaus poistettu! ' . $senttilitraa));    
    }
    
}