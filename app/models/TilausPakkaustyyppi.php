<?php

class TilausPakkaustyyppi extends BaseModel{
    
    public $tilaus_id, $pakkaustyyppi_id, $lukumaara;
    
    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validate_pakkaustyyppi_id', 'validate_lukumaara');
    }
    
    public function saveTRANS($connection){
        $query = $connection->prepare(
                'INSERT INTO TilausPakkaustyyppi (tilaus_id, pakkaustyyppi_id, lukumaara)
                 VALUES (:tilaus_id, :pakkaustyyppi_id, :lukumaara) 
                 RETURNING tilaus_id');
        $query->execute(array(
                'tilaus_id' => $this->tilaus_id,
                'pakkaustyyppi_id' => $this->pakkaustyyppi_id, 
                'lukumaara' => $this->lukumaara));
        $row = $query->fetch();
        
        if(!$row){
            $connection->rollBack();
            return FALSE;
        }
        
        return TRUE;
    }
    
    
    public function oliomuuttujatLomakemuodostaTietokantamuotoon(){
        $this->pakkaustyyppi_id = intval($this->pakkaustyyppi_id);
        $this->lukumaara = intval($this->lukumaara);
    }
    
    public function validate_pakkaustyyppi_id(){
        $errors = array();
        
        if(BaseModel::validate_non_negative_string_integer($this->pakkaustyyppi_id)){
          if(!BaseModel::validate_bounds_of_string_integer($this->pakkaustyyppi_id, 1, 2147483647)){
              $errors[] = 'Tapahtui tekninen virhe!';
          }
        } else {
            $errors[] = 'Tapahtui tekninen virhe!';
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

