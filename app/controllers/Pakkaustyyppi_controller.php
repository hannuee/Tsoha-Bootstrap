<?php

class PakkaustyyppiController extends BaseController{
    
    // Näkymien kontrollointi:
    
    public static function listaus(){
        self::check_admin_logged_in();
        
        $pakkaustyypit = Pakkaustyyppi::all();
        BaseModel::olioidenMuuttujatTietokantamuodostaEsitysmuotoon($pakkaustyypit);
        
        View::make('Pakkaustyyppi_listaus_tyontekijalle.html', array('pakkaustyypit' => $pakkaustyypit));
    }
    
    
    // Lomakkeiden käsittely:
    
    public static function lisaaUusi(){
        self::check_admin_logged_in();     
        
        $params = $_POST;
        
        $pakkaustyyppi = new Pakkaustyyppi($params);
        $pakkaustyyppi->saatavilla = 1; // Oletetaan että pakkaustyypit ovat saatavilla kun ne lisätään ensimmäisen kerran.
        
        
        $errors = $pakkaustyyppi->errors();
        if(count($errors) != 0){
            Redirect::to('/hallinnointi/pakkaustyypit', array('errors' => $errors, 'attributes' => $params));
        }
        
        
        $pakkaustyyppi->oliomuuttujatLomakemuodostaTietokantamuotoon();
        
        self::tarkasta_onnistuminen(
                $pakkaustyyppi->save(), '/hallinnointi/pakkaustyypit', 'Tapahtui virhe tallennettaessa pakkaustyyppiä!', $params);
        
        Redirect::to('/hallinnointi/pakkaustyypit', array('message' => 'Uusi pakkaustyyppi lisätty onnistuneesti!'));
    }
    
    public static function muokkaaSaatavuusstatusta(){
        self::check_admin_logged_in();
        
        $params = $_POST;
        
        self::tarkasta_id_ulkoasu($params['id'], '/hallinnointi/pakkaustyypit');
        
        self::tarkasta_onnistuminen(
                Pakkaustyyppi::updateAvailability($params['id']), '/hallinnointi/pakkaustyypit', 'Tapahtui virhe muuttaessa pakkaustyypin saatavuutta!', NULL);
        
        Redirect::to('/hallinnointi/pakkaustyypit', array('message' => 'Saatavuusstatus muutettu onnistuneesti!'));
    }
    
}