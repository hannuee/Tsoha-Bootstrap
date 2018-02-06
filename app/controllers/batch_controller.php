<?php

class BatchController extends BaseController{
    
    public static function admin($id){
        $olutera = Olutera::one($id);  // Entä jos ei löydy, esim. virheellinen id? sitten ei renderöidä batchpagea!
        View::make('batchpageAdmin.html', array('olutera' => $olutera));
    }
    
    public static function newDate(){
        $params = $_POST;
        
        $olutera = Olutera::one($params['oluen_nimi']);
        
        // SELVENNÄ VALIDAATTORIT JA KÄYTTÖLIITTYMÄSSÄ MUUNTO ENNEN KUIN JATKAT!!!!!!
        // ÄLÄ MUUNNA KÄYTTÖLIITTYMÄSSÄ, VAAN MUUNNA OLIOIDEN JULKISET OLIOMUUTTUJAT ENNEN KUIN
        // ANNAT NE TWIGILLE??????
        
        
        
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
