<?php

class PakkaustyyppiController extends BaseController{
    
    // Näkymien kontrollointi:
    
    /**
     * Listaa KAIKKI pakkaustyypit.
     */
    public static function listaus(){
        self::check_admin_logged_in();
        
        $pakkaustyypit = Pakkaustyyppi::all();
        BaseModel::olioidenMuuttujatTietokantamuodostaEsitysmuotoon($pakkaustyypit);
        
        View::make('Pakkaustyyppi_listaus_tyontekijalle.html', array('pakkaustyypit' => $pakkaustyypit));
    }
    
    
    // Lomakkeiden käsittely:
    
    /**
     * Lisää uuden pakkaustyypin.
     */
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
    
    /**
     * Vaihtaa pakkaustyypin saatavuutta.
     * @param type $id Pakkaustyypin ID jonka saatavuutta halutaan vaihtaa.
     */
    public static function muokkaaSaatavuusstatusta($id){
        self::check_admin_logged_in();
        
        self::tarkasta_id_ulkoasu($id, '/hallinnointi/pakkaustyypit');
        
        self::tarkasta_onnistuminen(
                Pakkaustyyppi::updateAvailability($id), '/hallinnointi/pakkaustyypit', 'Tapahtui virhe muuttaessa pakkaustyypin saatavuutta!', NULL);
        
        Redirect::to('/hallinnointi/pakkaustyypit', array('message' => 'Saatavuusstatus muutettu onnistuneesti!'));
    }
    
}