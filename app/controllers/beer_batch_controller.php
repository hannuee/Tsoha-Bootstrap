<?php

class BeerBatchController extends BaseController{
    
    public static function index(){
        self::check_user_logged_in();
        
        $oluterat = Olutera::allAvailableWithMargin(400);
        View::make('beer_batch_list.html', array('oluterat' => $oluterat));
    }
    
    public static function indexAdmin(){
        self::check_admin_logged_in();
        
        $oluterat = Olutera::all();
        View::make('beer_batch_list_admin.html', array('oluterat' => $oluterat));
    }

    public static function showAdmin($id){
        self::check_admin_logged_in();
        
        $olutera = Olutera::one($id);  // Entä jos ei löydy, esim. virheellinen id? sitten ei renderöidä batchpagea!
        View::make('beer_batch_show_admin.html', array('olutera' => $olutera));
    }
    
    public static function saveNewAdmin(){
        self::check_admin_logged_in();     
        
        $params = $_POST;
        
        $olutera = new Olutera(array(
            'oluen_nimi' => $params['oluen_nimi'],
            'valmistuminen' => $params['valmistuminen'],
            'eran_koko' => $params['eran_koko'],
            'vapaana' => $params['eran_koko'],  // vapaana = eran_koko, koska koko erä on tietenkin vapaana kun erä luodaan. 
            'hinta' => $params['hinta']
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
            'hinta' => $params['hinta'])));
        }
    }
    
    public static function updateDateAdmin(){
        self::check_admin_logged_in();
        
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
    
    public static function deleteAdmin(){
        self::check_admin_logged_in();
        
        $params = $_POST;
        
        $olutera = Olutera::one($params['id']);
        $olutera->delete();
 
        Redirect::to('/hallinnointi/oluterat', array('message' => 'Oluterä ja sen tilaukset on poistettu onnistuneesti!'));
    }
    
}