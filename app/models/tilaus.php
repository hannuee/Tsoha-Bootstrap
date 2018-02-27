<?php

class Tilaus extends BaseModel{
    
    public $id, $tilausajankohta, $toimitettu, $toimitusohjeet, $olutera_id, $yritysasiakas_id;
    
    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_olutera_id'); // MUISTA VALIDAATTORIT!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
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
    
    public function save(){
        $query = DB::connection()->prepare(
                'INSERT INTO Tilaus (tilausajankohta, toimitettu, toimitusohjeet, olutera_id, yritysasiakas_id)
                 VALUES (current_date, :toimitettu, :toimitusohjeet, :olutera_id, :yritysasiakas_id)
                 RETURNING id');
        $query->execute(array(
                'toimitettu' => $this->toimitettu, 
                'toimitusohjeet' => $this->toimitusohjeet,
                'olutera_id' => $this->olutera_id,
                'yritysasiakas_id' => $this->yritysasiakas_id));
        $row = $query->fetch();
        $this->id = $row['id'];
    }
    
    public static function updateDeliveryStatus($id){
        $query = DB::connection()->prepare(
                'UPDATE Tilaus SET toimitettu=1 WHERE id=:id');
        $query->execute(array('id' => $id));
    }
    
    public static function delete($id){
        $query = DB::connection()->prepare(
                'DELETE FROM Tilaus WHERE id=:id LIMIT 1');
        $query->execute(array('id' => $id));
    }
    
    public function validate_olutera_id(){
        $errors = array();
        
        if(!BaseModel::validate_non_negative_string_integer($this->olutera_id)){
          // Tämä virheilmoitus annetaan vain jos lähetetyn HTML-lomakkeen olutera_id id:tä on muokattu TAI
          // jos tilauslomakkeen URL:iin on muutettu manuaalisesti numeron paikalle joku muu kuin numero. 
          $errors[] = 'Tekninen ongelma tilauksen vastaanottamisessa!';
        }

        return $errors;
    }
    
}

