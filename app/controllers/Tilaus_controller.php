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
        
        Tilaus::updateDeliveryStatus($params['tilaus_id']); // PITÄSKÖ LAITTAA RETURNING ID?? JA TARKISTUS TÄTEN ETTÄ KAIKKI SUJU OK?
 
        Redirect::to('/hallinnointi/oluterat/' . $params['olutera_id'], array('message' => 'Tilaus merkitty toimitetuksi!'));
    }
    
    public static function poista(){
        self::check_admin_logged_in();
        
        $params = $_POST;
        
        // Lasketaan montako litraa pitää vapauttaa oluterästä.
        $senttilitraa = 0;
        $pakkaustyypitJaLukumaarat = Pakkaustyyppi::allForOrder($params['tilaus_id']);
        foreach($pakkaustyypitJaLukumaarat as $pakkaustyyppiJalukumaara){
            $senttilitraa += $pakkaustyyppiJalukumaara[0]->vetoisuus * 100 * $pakkaustyyppiJalukumaara[1];
        }
        
        // Vapautetaan kyseinen litramäärä oluterästä.
        $olutera = Olutera::one($params['olutera_id']);
        $olutera->oliomuuttujatTietokantamuodostaEsitysmuotoon();
        $olutera->vapaana = $olutera->vapaana * 100 + $senttilitraa;
        $olutera->updateAmountAvailable();
        
        Tilaus::delete($params['tilaus_id']); // PITÄSKÖ LAITTAA RETURNING ID?? JA TARKISTUS TÄTEN ETTÄ KAIKKI SUJU OK?
 
        Redirect::to('/hallinnointi/oluterat/' . $params['olutera_id'], array('message' => 'Tilaus poistettu! ' . $senttilitraa));
    }
    
}