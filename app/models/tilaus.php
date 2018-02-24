<?php

class Tilaus extends BaseModel{
    
    public $id, $tilausajankohta, $toimitettu, $toimitusohjeet, $olutera_id, $yritysasiakas_id;
    
    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array(); // MUISTA VALIDAATTORIT!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
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
    
}

