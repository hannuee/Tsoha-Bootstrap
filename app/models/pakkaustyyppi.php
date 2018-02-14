<?php

class Pakkaustyyppi extends BaseModel{
    
    public $id, $pakkaustyypin_nimi, $vetoisuus, $hinta, $pantti, $saatavilla;
    
    public function __construct($attributes){
        parent::__construct($attributes);
        $this->validators = array('validate_pakkaustyypin_nimi', 'validate_vetoisuus', 'validate_hinta', 'validate_pantti');
    }
    
    public static function allAvailable(){  
        $query = DB::connection()->prepare('SELECT * FROM Pakkaustyyppi WHERE saatavilla = 1');
        $query->execute();
        $rows = $query->fetchAll();
        
        $pakkaustyypit = array();
        foreach($rows as $row){
            $pakkaustyyppi = new Pakkaustyyppi($row);
            $pakkaustyyppi->instanceVariablesToViewForm();
            $pakkaustyypit[] = $pakkaustyyppi;
        }
        return $pakkaustyypit;
    }

    public static function all(){
        $query = DB::connection()->prepare('SELECT * FROM Pakkaustyyppi');
        $query->execute();
        $rows = $query->fetchAll();
        
        $pakkaustyypit = array();
        foreach($rows as $row){
            $pakkaustyyppi = new Pakkaustyyppi($row);
            $pakkaustyyppi->instanceVariablesToViewForm();
            $pakkaustyypit[] = $pakkaustyyppi;
        }
        return $pakkaustyypit;
    }
    
    public static function one($id){
        $query = DB::connection()->prepare('SELECT * FROM Pakkaustyyppi WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        
        if($row){
            $pakkaustyyppi = new Pakkaustyyppi($row);
            $pakkaustyyppi->instanceVariablesToViewForm();
            return $pakkaustyyppi;
        }
        return null;
    }
    
    public function save(){
        $this->instanceVariablesToDatabaseForm();
        
        $query = DB::connection()->prepare(
                'INSERT INTO Pakkaustyyppi (pakkaustyypin_nimi, vetoisuus, hinta, pantti, saatavilla)
                 VALUES (:pakkaustyypin_nimi, :vetoisuus, :hinta, :pantti, :saatavilla)
                 RETURNING id');
        $query->execute(array(
                'pakkaustyypin_nimi' => $this->pakkaustyypin_nimi,
                'vetoisuus' => $this->vetoisuus, 
                'hinta' => $this->hinta,
                'pantti' => $this->pantti,
                'saatavilla' => $this->saatavilla));
        $row = $query->fetch();
        $this->id = $row['id'];
    }
    
    public function updateAvailability(){
        $query = DB::connection()->prepare(
                'UPDATE Pakkaustyyppi SET saatavilla=:saatavilla WHERE id=:id');
        $query->execute(array('valmistuminen' => $this->saatavilla, 'id' => $this->id));
    }
    
}

