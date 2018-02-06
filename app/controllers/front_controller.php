<?php

class FrontController extends BaseController{
    
    public static function index(){
        $oluterat = Olutera::allAvailableWithMargin(400);
        View::make('frontpage.html', array('oluterat' => $oluterat));
    }
    
    public static function admin(){
        $oluterat = Olutera::all();
        View::make('frontpageAdmin.html', array('oluterat' => $oluterat));
    }
    
    public static function newBeerBatch(){
        $params = $_POST;
        
        $olutera = new Olutera(array(
            'oluen_nimi' => $params['oluen_nimi'],
            'valmistuminen' => $params['valmistuminen'],
            'eran_koko' => $params['eran_koko'] * 100, // Muunto senttilitroiksi.
            'vapaana' => $params['eran_koko'] * 100,  // Muunto senttilitroiksi, koko erä on tietenkin vapaana kun erä luodaan. 
            'hinta' => $params['hinta_euroa'] * 100 + $params['hinta_senttia']  // Eurojen muunto senteiksi ja yhdistäminen sentteihin.
        ));
        $errors = $olutera->errors();
        
        if(count($errors) == 0){  // Syötteet valideja.
            $olutera->save();
            Redirect::to('/hallinnointi', array('message' => 'Uusi oluterä lisätty onnistuneesti!'));
        } else {                  // Syötteet ei-valideja.
            Redirect::to('/hallinnointi', array('errors' => $errors, 'attributes' => 
                array(
            'oluen_nimi' => $params['oluen_nimi'],
            'valmistuminen' => $params['valmistuminen'],
            'eran_koko' => $params['eran_koko'],
            'hinta_euroa' => $params['hinta_euroa'],
            'hinta_senttia' => $params['hinta_senttia'])
                ));
        }
    }
    
}
