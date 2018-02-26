<?php

class TilausPakkaustyyppi extends BaseModel{
    
    public $tilaus_id, $pakkaustyyppi_id, $lukumaara;
    
    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_pakkaustyyppi_id', 'validate_lukumaara');
    }
    
    public function save(){
        $query = DB::connection()->prepare(
                'INSERT INTO TilausPakkaustyyppi (tilaus_id, pakkaustyyppi_id, lukumaara)
                 VALUES (:tilaus_id, :pakkaustyyppi_id, :lukumaara)');
        $query->execute(array(
                'tilaus_id' => $this->tilaus_id,
                'pakkaustyyppi_id' => $this->pakkaustyyppi_id, 
                'lukumaara' => $this->lukumaara));
    }
    
    public function validate_pakkaustyyppi_id(){
        $errors = array();
        
        if(!BaseModel::validate_non_negative_string_integer($this->pakkaustyyppi_id)){
          // Tämä virheilmoitus annetaan vain jos lähetetyn HTML-lomakkeen kenttien id:itä on muokattu.
          $errors[] = 'Tekninen ongelma tilauksen vastaanottamisessa!';
        }

        return $errors;
    }
    
    public function validate_lukumaara(){
        $errors = array();
        
        if(!BaseModel::validate_non_negative_string_integer($this->lukumaara)){
          $errors[] = 'Pakkaustyypin määrien on oltava ei-negatiivisia kokonaislukuja!';
        }

        return $errors;
    }
    
}

