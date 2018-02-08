<?php

class Yritysasiakas extends BaseModel{
    
    public $id, $yrityksen_nimi, $y_tunnus, $osoite, $toimitusosoite, $laskutusosoite, 
           $puhelinnumero, $sahkoposti, $salasana, $aktiivinen, $tyontekija;
   
    public function __construct($attributes){   
        parent::__construct($attributes);
        $this->validators = array();
    }
    
    public static function find($id){
        $query = DB::connection()->prepare('SELECT * FROM Yritysasiakas WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        
        if($row){
            $yritysasiakas = new Yritysasiakas($row);
            
            return $yritysasiakas;
        }
        return null;
    }
    
    public static function authenticate($email, $password){
        $query = DB::connection()->prepare('SELECT * FROM Yritysasiakas WHERE sahkoposti = :sahkoposti AND salasana = :password LIMIT 1');
        $query->execute(array('sahkoposti' => $email, 'password' => $password));
        $row = $query->fetch();
        
        if($row){
          $yritysasiakas = new Yritysasiakas($row);
            
          return $yritysasiakas;
        }
        return null;
    }
    
}