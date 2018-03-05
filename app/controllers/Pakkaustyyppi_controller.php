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
        
        $pakkaustyyppi = new Pakkaustyyppi(array(
            'pakkaustyypin_nimi' => $params['pakkaustyypin_nimi'],
            'vetoisuus' => $params['vetoisuus'],
            'hinta' => $params['hinta'],
            'pantti' => $params['pantti'], 
            'saatavilla' => 1  // Oletetaan että pakkaustyypit ovat saatavilla kun ne lisätään ensimmäisen kerran.  
        ));
        
        
        $errors = $pakkaustyyppi->errors();
        if(count($errors) != 0){
            Redirect::to('/hallinnointi/pakkaustyypit', array('errors' => $errors, 'attributes' => 
                array(
            'pakkaustyypin_nimi' => $params['pakkaustyypin_nimi'],
            'vetoisuus' => $params['vetoisuus'],
            'hinta' => $params['hinta'],
            'pantti' => $params['pantti'])));
        }
        
        
        $pakkaustyyppi->oliomuuttujatLomakemuodostaTietokantamuotoon();
        
        
        $onnistuiko = $pakkaustyyppi->save();
        if(!$onnistuiko){
            Redirect::to('/hallinnointi/pakkaustyypit', array('errors' => array('Tapahtui virhe tallennettaessa pakkaustyyppiä!')));
        }
        
        
        Redirect::to('/hallinnointi/pakkaustyypit', array('message' => 'Uusi pakkaustyyppi lisätty onnistuneesti!'));
    }
    
    public static function muokkaaSaatavuusstatusta(){
        self::check_admin_logged_in();
        
        $params = $_POST;
        
        
        $idSyntaxError = BaseModel::validate_id_directly($params['id']);
        if(count($idSyntaxError) != 0){
            Redirect::to('/hallinnointi/pakkaustyypit', array('errors' => $idSyntaxError));
        }
        
        
        $onnistuiko = Pakkaustyyppi::updateAvailability($params['id']);
        if(!$onnistuiko){
            Redirect::to('/hallinnointi/pakkaustyypit', array('errors' => array('Tapahtui virhe muuttaessa pakkaustyypin saatavuutta!')));
        }
        
        Redirect::to('/hallinnointi/pakkaustyypit', array('message' => 'Saatavuusstatus muutettu onnistuneesti!'));
    }
    
}