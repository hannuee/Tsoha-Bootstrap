<?php

class TilausController extends BaseController{
    
    // Näkymien kontrollointi:
    
    public static function lisays($id){
        self::check_user_logged_in();
        
        self::tarkasta_id_ulkoasu($id, '/');
        
        $olutera = self::tarkasta_onnistuminen(
                Olutera::oneAvailableWithMargin($id, 400), '/', 'Etsimääsi oluterää ei löytynyt!', NULL);
        
        $olutera->oliomuuttujatTietokantamuodostaEsitysmuotoon();
        
        $pakkaustyypit = Pakkaustyyppi::allAvailable();
        BaseModel::olioidenMuuttujatTietokantamuodostaEsitysmuotoon($pakkaustyypit);
        
        View::make('Tilaus_lisays.html', array('olutera' => $olutera, 'pakkaustyypit' => $pakkaustyypit));
    }
 
    public static function lisaysLisavaihtoehdoin($id){
        self::check_admin_logged_in();
        
        self::tarkasta_id_ulkoasu($id, '/hallinnointi/oluterat');
        
        $olutera = self::tarkasta_onnistuminen(
                Olutera::one($id), '/hallinnointi/oluterat', 'Etsimääsi oluterää ei löytynyt!', NULL);
        
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
        
        self::tarkasta_id_ulkoasu($params['tilaus_id'], '/hallinnointi/oluterat');
    
        self::tarkasta_onnistuminen(
                Tilaus::updateDeliveryStatus($params['tilaus_id']), '/hallinnointi/oluterat/' . $params['olutera_id'], 'Tapahtui virhe merkittäessä tilausta toimitetuksi!', NULL);
        
        Redirect::to('/hallinnointi/oluterat/' . $params['olutera_id'], array('message' => 'Tilaus merkitty toimitetuksi!'));
    }
    
    public static function poista(){
        self::check_admin_logged_in();
        
        $params = $_POST;
        
        self::tarkasta_id_ulkoasu($params['tilaus_id'], '/hallinnointi/oluterat');
        
        // Lasketaan montako litraa pitää vapauttaa oluterästä.
        $senttilitraa = 0;
        $pakkaustyypitJaLukumaarat = Pakkaustyyppi::allForOrder($params['tilaus_id']);
        foreach($pakkaustyypitJaLukumaarat as $pakkaustyyppiJalukumaara){
            $senttilitraa += $pakkaustyyppiJalukumaara[0]->vetoisuus * 100 * $pakkaustyyppiJalukumaara[1];
        }
        
       
        $connection = self::tarkasta_onnistuminen(
                BaseModel::beginTransaction(), '/hallinnointi/oluterat/' . $params['olutera_id'], 'Virhe poistettaessa tilausta, tilausta ei poistettu!', NULL);
        
        $olutera_id = self::tarkasta_onnistuminen(  // Tilausta poistettaessa saadaan tietää luotettavasti mistä oluterästä tilausta poistetaan.
                Tilaus::deleteTRANS($params['tilaus_id'], $connection), '/hallinnointi/oluterat/' . $params['olutera_id'], 'Virhe poistettaessa tilausta, tilausta ei poistettu!', NULL);

        self::tarkasta_onnistuminen(
                Olutera::updateAmountAvailableAddTRANS($olutera_id, $senttilitraa, $connection), '/hallinnointi/oluterat/' . $params['olutera_id'], 'Virhe poistettaessa tilausta, tilausta ei poistettu!', NULL);
        
        self::tarkasta_onnistuminen(
                BaseModel::commit($connection), '/hallinnointi/oluterat/' . $params['olutera_id'], 'Virhe poistettaessa tilausta, tilausta ei poistettu!', NULL);
        
        
        Redirect::to('/hallinnointi/oluterat/' . $olutera_id, array('message' => 'Tilaus poistettu! ' . $senttilitraa));    
    }
    
}