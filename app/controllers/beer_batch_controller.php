<?php

class BeerBatchController extends BaseController{
    
    public static function index(){
        $oluterat = Olutera::allAvailableWithMargin(400);
        View::make('beer_batch.html', array('oluterat' => $oluterat));
    }
    
    public static function admin(){
        $oluterat = Olutera::all();
        View::make('beer_batch_admin.html', array('oluterat' => $oluterat));
    }

    public static function show($id){
        $olutera = Olutera::one($id);  // Entä jos ei löydy, esim. virheellinen id? sitten ei renderöidä batchpagea!
        View::make('batchpageAdmin.html', array('olutera' => $olutera));
    }
    
    public static function updateDate(){
        $params = $_POST;
        
        $olutera = Olutera::one($params['id']);
        $olutera->valmistuminen = $params['valmistuminen'];
        $errors = $olutera->errors();
 
        if(count($errors) == 0){  // Syötteet valideja.
            $olutera->updateDate();
            Redirect::to('/hallinnointi/oluterat/' . $olutera->id, array('message' => 'Valmistumispäivämäärä muutettu onnistuneesti!'));
        } else {                  // Syötteet ei-valideja.
            Redirect::to('/hallinnointi/oluterat/' . $olutera->id, array('errors' => $errors));
        }
    }
    
    public static function newBeerBatch(){
        $params = $_POST;
        
        $olutera = new Olutera(array(
            'oluen_nimi' => $params['oluen_nimi'],
            'valmistuminen' => $params['valmistuminen'],
            'eran_koko' => $params['eran_koko'],
            'vapaana' => $params['eran_koko'],  // vapaana = eran_koko, koska koko erä on tietenkin vapaana kun erä luodaan. 
            'hinta_euroa' => $params['hinta_euroa'],
            'hinta_senttia' => $params['hinta_senttia']
        ));
        $errors = $olutera->errors();
        
        if(count($errors) == 0){  // Syötteet valideja.
            $olutera->save();
            Redirect::to('/hallinnointi/oluterat', array('message' => 'Uusi oluterä lisätty onnistuneesti!'));
        } else {                  // Syötteet ei-valideja.
            Redirect::to('/hallinnointi/oluterat', array('errors' => $errors, 'attributes' => 
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