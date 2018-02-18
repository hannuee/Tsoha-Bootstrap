<?php

class Yritysasiakas extends BaseModel{
    
    public $id, $yrityksen_nimi, $y_tunnus, $osoite, $toimitusosoite, $laskutusosoite, 
           $puhelinnumero, $sahkoposti, $salasana, $aktiivinen, $tyontekija;
   
    public function __construct($attributes){   
        parent::__construct($attributes);
        $this->validators = array('validate_yrityksen_nimi', 'validate_y_tunnus', 'validate_osoite', 'validate_toimitusosoite', 
                                  'validate_laskukutusosoite', 'validate_puhelinnumero', 'validate_sahkoposti', 'validate_salasana');
    }
    
    public static function all(){
        $query = DB::connection()->prepare('SELECT * FROM Yritysasiakas');
        $query->execute();
        $rows = $query->fetchAll();
        
        $yritysasiakas = array();
        foreach($rows as $row){
            $yritysasiakas = new Yritysasiakas($row);
            //$yritysasiakas->instanceVariablesToViewForm();
            $yritysasiakas[] = $yritysasiakas;
        }
        return $yritysasiakas;
    }
    
    public static function one($id){
        $query = DB::connection()->prepare('SELECT * FROM Yritysasiakas WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        
        if($row){
            $yritysasiakas = new Yritysasiakas($row);
            //$yritysasiakas->instanceVariablesToViewForm();
            return $yritysasiakas;
        }
        return null;
    }

    public function save(){
        $query = DB::connection()->prepare(
                'INSERT INTO Yritysasiakas (yrityksen_nimi, y_tunnus, osoite, toimitusosoite, laskutusosoite, 
                                            puhelinnumero, sahkoposti, salasana, aktiivinen, tyontekija)
                 VALUES (:yrityksen_nimi, :y_tunnus, :osoite, :toimitusosoite, :laskutusosoite
                         :puhelinnumero, :sahkoposti, :salasana, :aktiivinen, :tyontekija)
                 RETURNING id');
        $query->execute(array(
                'yrityksen_nimi' => $this->yrityksen_nimi,
                'y_tunnus' => $this->y_tunnus,
                'osoite' => $this->osoite,
                'toimitusosoite' => $this->toimitusosoite,
                'laskutusosoite' => $this->laskutusosoite,
                'puhelinnumero' => $this->puhelinnumero,
                'sahkoposti' => $this->sahkoposti,
                'salasana' => $this->salasana,
                'aktiivinen' => $this->aktiivinen,
                'tyontekija' => $this->tyontekija));
        $row = $query->fetch();
        $this->id = $row['id'];
    }
    
    public function update(){
        $query = DB::connection()->prepare(
                'UPDATE Yritysasiakas SET 
                    yrityksen_nimi=:yrityksen_nimi, y_tunnus=:y_tunnus, osoite=:osoite, toimitusosoite=:toimitusosoite, laskutusosoite=:laskutusosoite, 
                    puhelinnumero=:puhelinnumero, sahkoposti=:sahkoposti, salasana=:salasana, aktiivinen=:aktiivinen, tyontekija=:tyontekija WHERE id=:id');
        $query->execute(array(
                'id' => $this->id,
                'yrityksen_nimi' => $this->yrityksen_nimi,
                'y_tunnus' => $this->y_tunnus,
                'osoite' => $this->osoite,
                'toimitusosoite' => $this->toimitusosoite,
                'laskutusosoite' => $this->laskutusosoite,
                'puhelinnumero' => $this->puhelinnumero,
                'sahkoposti' => $this->sahkoposti,
                'salasana' => $this->salasana,
                'aktiivinen' => $this->aktiivinen,
                'tyontekija' => $this->tyontekija));
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
        $query = DB::connection()->prepare('SELECT * FROM Yritysasiakas WHERE sahkoposti = :email AND salasana = :password LIMIT 1');
        $query->execute(array('email' => $email, 'password' => $password));
        $row = $query->fetch();
        
        if($row){
          $yritysasiakas = new Yritysasiakas($row);
            
          return $yritysasiakas;
        }
        return null;
    }
    
    public function validate_yrityksen_nimi(){
        $errors = array();
        
        if(!BaseModel::validate_string_length($this->yrityksen_nimi, 1, 100)){
          $errors[] = 'Yrityksen nimen on oltava 1 - 100 merkkiä!';
        }

        return $errors;
    }
    
    public function validate_y_tunnus(){
        $errors = array();
        
        if(!BaseModel::validate_string_length($this->y_tunnus, 1, 20)){
          $errors[] = 'Y-tunnuksen on oltava 1 - 20 merkkiä!';
        }

        return $errors;
    }
    
    public function validate_validate_osoite(){
        $errors = array();
        
        if(!BaseModel::validate_string_length($this->validate_osoite, 1, 250)){
          $errors[] = 'Osoitteen on oltava 1 - 250 merkkiä!';
        }

        return $errors;
    }
    
    public function validate_toimitusosoite(){
        $errors = array();
        
        if(!BaseModel::validate_string_length($this->toimitusosoite, 1, 250)){
          $errors[] = 'Toimitusosoitteen on oltava 1 - 250 merkkiä!';
        }

        return $errors;
    }
    
    public function validate_laskukutusosoite(){
        $errors = array();
        
        if(!BaseModel::validate_string_length($this->laskukutusosoite, 1, 250)){
          $errors[] = 'Laskutusosoitteen on oltava 1 - 250 merkkiä!';
        }

        return $errors;
    }
    
    public function validate_puhelinnumero(){
        $errors = array();
        
        if(!BaseModel::validate_string_length($this->puhelinnumero, 1, 20)){
          $errors[] = 'Puhelinnumeron on oltava 1 - 20 merkkiä!';
        }

        return $errors;
    }
    
    public function validate_sahkoposti(){
        $errors = array();
        
        if(!BaseModel::validate_string_length($this->sahkoposti, 1, 100)){
          $errors[] = 'Sähköpostin on oltava 1 - 100 merkkiä!';
        }

        return $errors;
    }
    
    public function validate_salasana(){
        $errors = array();
        
        if(!BaseModel::validate_string_length($this->salasana, 1, 100)){
          $errors[] = 'Salasanan on oltava 1 - 100 merkkiä!';
        }

        return $errors;
    }
    
}