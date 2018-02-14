<?php

class PackageTypeController extends BaseController{
    
    public static function admin(){
        self::check_admin_logged_in();
        
        $pakkaustyypit = Pakkaustyyppi::all();
        View::make('package_type_admin.html', array('pakkaustyypit' => $pakkaustyypit));
    }
    
    public static function newPackageType(){
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
        
        if(count($errors) == 0){  // Syötteet valideja.
            $pakkaustyyppi->save();
            Redirect::to('/hallinnointi/pakkaustyypit', array('message' => 'Uusi pakkaustyyppi lisätty onnistuneesti!'));
        } else {                  // Syötteet ei-valideja.
            Redirect::to('/hallinnointi/pakkaustyypit', array('errors' => $errors, 'attributes' => 
                array(
            'pakkaustyypin_nimi' => $params['pakkaustyypin_nimi'],
            'vetoisuus' => $params['vetoisuus'],
            'hinta' => $params['hinta'],
            'pantti' => $params['pantti'])));
        }
    }
    
    public static function switchAvailability(){
        self::check_admin_logged_in();
        
        $params = $_POST;
        
        $pakkaustyyppi = Pakkaustyyppi::one($params['id']);
        if($pakkaustyyppi->saatavilla == 1){
            $pakkaustyyppi->saatavilla = 0;
        } else {
            $pakkaustyyppi->saatavilla = 1;
        }
        
        $pakkaustyyppi->updateAvailability();
        Redirect::to('/hallinnointi/pakkaustyypit', array('message' => 'Saatavuusstatus muutettu onnistuneesti!'));
    }
    
}