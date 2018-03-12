<?php

class TilausController extends BaseController{
    
    // Näkymien kontrollointi:
    
    /**
     * Näyttää oluterän tilauslomakkeen asiakkaalle.
     * @param type $id Oluterän ID jota asiakas haluaa tilata.
     */
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
 
    /**
     * Näyttää oluterän tilauslomakkeen työntekijälle.
     * @param type $id Oluterän ID jota työntekijä haluaa tilata pienpanimon omaan käyttöön tai asiakkaalle.
     */
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
    
    /**
     * Lisää uuden tilauksen työntekijältä.
     */
    public static function lisaaUusi(){
        self::check_admin_logged_in();
        
        $params = $_POST;
        
        // Tietojen ottaminen lähetetystä lomakkeesta & Tarkistuksia.
        $olutera = TilausControllerApumetodit::tarkistaLomakkeestaOluteraIdJaPalautaOlutera($params, '/hallinnointi/oluterat');
        TilausControllerApumetodit::tarkistaLomakkeestaYritysasiakasId($params, '/hallinnointi/oluterat');
        $tilausPakkaustyypit = TilausControllerApumetodit::tilausPakkaustyyppiMallienTiedotLomakkeesta($params, '/hallinnointi/tilaukset/uusi/');  // Eli montako kappaletta kutakin pakkausta.
        $tilaus = TilausControllerApumetodit::toimitusohjeetLomakkeestaJaPalautaTilaus($params, $params['yritysasiakas_id']);
        
        // Tarkistuksia. (Apumetodit saavat lomakkeen tiedot ainoastaan virheestä johtuvaa uudelleenohjausta varten.)
        $senttilitroja = TilausControllerApumetodit::senttilitrojenLaskeminenJaPakkaustyyppienTarkistus($tilausPakkaustyypit, $params, '/hallinnointi/tilaukset/uusi/');
        TilausControllerApumetodit::tarkistaVapaanOluenMaara($senttilitroja, $olutera, $params, '/hallinnointi/tilaukset/uusi/');
        
        TilausControllerApumetodit::lisaaUusiTilaus($senttilitroja, $tilaus, $tilausPakkaustyypit, $olutera, $params, '/hallinnointi/tilaukset/uusi/', '/hallinnointi/oluterat');
    }
    
    /**
     * Lisää uuden tilauksen asiakkaalta.
     */
    public static function lisaaUusiAsiakkaalta(){
        self::check_user_logged_in();
        
        $params = $_POST;
        
        // Tietojen ottaminen lähetetystä lomakkeesta & Tarkistuksia.
        $olutera = TilausControllerApumetodit::tarkistaLomakkeestaOluteraIdJaPalautaOlutera($params, '/');
        $tilausPakkaustyypit = TilausControllerApumetodit::tilausPakkaustyyppiMallienTiedotLomakkeesta($params, '/tilaukset/uusi/');  // Eli montako kappaletta kutakin pakkausta.
        $tilaus = TilausControllerApumetodit::toimitusohjeetLomakkeestaJaPalautaTilaus($params, $_SESSION['user']);
        
        // Tarkistuksia. (Apumetodit saavat lomakkeen tiedot ainoastaan virheestä johtuvaa uudelleenohjausta varten.)
        $senttilitroja = TilausControllerApumetodit::senttilitrojenLaskeminenJaPakkaustyyppienTarkistus($tilausPakkaustyypit, $params, '/tilaukset/uusi/');
        TilausControllerApumetodit::tarkistaVapaanOluenMaara($senttilitroja, $olutera, $params, '/tilaukset/uusi/');
        
        TilausControllerApumetodit::lisaaUusiTilaus($senttilitroja, $tilaus, $tilausPakkaustyypit, $olutera, $params, '/tilaukset/uusi/', '/');
    }
    
    /**
     * Merkitsee tilauksen toimitetuksi.
     */
    public static function muokkaaToimitetuksi(){
        self::check_admin_logged_in();
        
        $params = $_POST;   
        
        self::tarkasta_id_ulkoasu($params['tilaus_id'], '/hallinnointi/oluterat');
    
        self::tarkasta_onnistuminen(
                Tilaus::updateDeliveryStatus($params['tilaus_id']), '/hallinnointi/oluterat/' . $params['olutera_id'], 'Tapahtui virhe merkittäessä tilausta toimitetuksi!', NULL);
        
        Redirect::to('/hallinnointi/oluterat/' . $params['olutera_id'], array('message' => 'Tilaus merkitty toimitetuksi!'));
    }
    
    /**
     * Poistaa tilauksen.
     */
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