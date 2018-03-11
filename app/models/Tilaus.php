<?php

class Tilaus extends BaseModel{
    
    public $id, $tilausajankohta, $toimitettu, $toimitusohjeet, $olutera_id, $yritysasiakas_id;
    
    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array();
    }
    
    public static function allForBeerBatch($id){
        $query = DB::connection()->prepare('SELECT * FROM Tilaus WHERE olutera_id = ' . $id);
        $query->execute();
        $rows = $query->fetchAll();
        
        $tilaukset = array();
        foreach($rows as $row){
            $tilaus = new Tilaus($row);
            $tilaukset[] = $tilaus;
        }
        return $tilaukset;
    }
    
    public function saveTRANS($connection){
        $query = $connection->prepare(
                'INSERT INTO Tilaus (tilausajankohta, toimitettu, toimitusohjeet, olutera_id, yritysasiakas_id)
                 VALUES (current_date, :toimitettu, :toimitusohjeet, :olutera_id, :yritysasiakas_id)
                 RETURNING id');
        $query->execute(array(
                'toimitettu' => $this->toimitettu, 
                'toimitusohjeet' => $this->toimitusohjeet,
                'olutera_id' => $this->olutera_id,
                'yritysasiakas_id' => $this->yritysasiakas_id));
        $row = $query->fetch();
        
        if(!$row){
            $connection->rollBack();
            return FALSE;
        }
        
        $this->id = $row['id'];
        return TRUE;
    }
    
    public static function updateDeliveryStatus($id){
        $query = DB::connection()->prepare(
                'UPDATE Tilaus SET toimitettu=1 WHERE id=:id RETURNING id');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        
        if(!$row){
            return FALSE;
        }
        
        return TRUE;
    }
    
    public static function deleteTRANS($id, $connection){
        $query = $connection->prepare(
                'DELETE FROM Tilaus WHERE id=:id RETURNING olutera_id');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        
        if(!$row){
            $connection->rollBack();
            return FALSE;
        }
        
        return $row['olutera_id'];
    }
    
    
    // ALLA LUULTAVASTI TURHAA KAMAA:
//    public function validate_id(){  // Tämä validointi ei mene läpi vain jos POST-dataa muokataan tai tapahtuu jotain odottamatonta.
//        $errors = array();
//        
//        if(BaseModel::validate_non_negative_string_integer($this->id)){
//          if(!BaseModel::validate_bounds_of_string_integer($this->id, 1, 2147483647)){
//              $errors[] = 'Tapahtui tekninen virhe!';
//          }
//        } else {
//            $errors[] = 'Tapahtui tekninen virhe!';
//        }
//
//        return $errors;
//    }
//    
//    public function validate_olutera_id(){
//        $errors = array();
//        
//        if(!BaseModel::validate_non_negative_string_integer($this->olutera_id)){
//          // Tämä virheilmoitus annetaan vain jos lähetetyn HTML-lomakkeen olutera_id id:tä on muokattu TAI
//          // jos tilauslomakkeen URL:iin on muutettu manuaalisesti numeron paikalle joku muu kuin numero. 
//          $errors[] = 'Tekninen ongelma tilauksen vastaanottamisessa!';
//        }
//
//        return $errors;
//    }
    
}

