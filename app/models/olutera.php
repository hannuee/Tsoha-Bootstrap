<?php

class Olutera extends BaseModel{
    
    public $id, $oluen_nimi, $valmistuminen, $eran_koko, $vapaana, $hinta;
    
    public function __construct($attributes){
        parent::__construct($attributes);
        $this->validators = array('validate_oluen_nimi', 'validate_valmistuminen', 'validate_eran_koko', 'validate_hinta');
    }
    
    /**
     * @param type $margin Vaadittu oluterän vapaana olevan oluen määrä senttilitroissa
     * jotta funktio palauttaa oluterän. (Älä salli käyttäjän syötettä tähän parametriin!)
     */
    public static function allAvailableWithMargin($margin){  
        $query = DB::connection()->prepare('SELECT * FROM Olutera WHERE vapaana >= ' . $margin);
        $query->execute();
        $rows = $query->fetchAll();
        
        $oluterat = array();
        foreach($rows as $row){
            $olutera = new Olutera($row);
            $olutera->instanceVariablesToViewForm();
            $oluterat[] = $olutera;
        }
        return $oluterat;
    }

    public static function all(){
        $query = DB::connection()->prepare('SELECT * FROM Olutera');
        $query->execute();
        $rows = $query->fetchAll();
        
        $oluterat = array();
        foreach($rows as $row){
            $olutera = new Olutera($row);
            $olutera->instanceVariablesToViewForm();
            $oluterat[] = $olutera;
        }
        return $oluterat;
    }
    
    /**
     * @param type $margin Vaadittu oluterän vapaana olevan oluen määrä senttilitroissa
     * jotta funktio palauttaa oluterän. (Älä salli käyttäjän syötettä tähän parametriin!)
     */
    public static function oneAvailableWithMargin($id, $margin){
        $query = DB::connection()->prepare('SELECT * FROM Olutera WHERE id = :id AND vapaana > ' . $margin . ' LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        
        if($row){
            $olutera = new Olutera($row);
            $olutera->instanceVariablesToViewForm();
            return $olutera;
        }
        return null;
    }
    
    public static function one($id){
        $query = DB::connection()->prepare('SELECT * FROM Olutera WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        
        if($row){
            $olutera = new Olutera($row);
            $olutera->instanceVariablesToViewForm();
            return $olutera;
        }
        return null;
    }
    
    public function save(){
        $this->instanceVariablesToDatabaseForm();
        
        $query = DB::connection()->prepare(
                'INSERT INTO Olutera (oluen_nimi, valmistuminen, eran_koko, vapaana, hinta)
                 VALUES (:oluen_nimi, :valmistuminen, :eran_koko, :vapaana, :hinta)
                 RETURNING id');
        $query->execute(array(
                'oluen_nimi' => $this->oluen_nimi,
                'valmistuminen' => $this->valmistuminen, 
                'eran_koko' => $this->eran_koko,
                'vapaana' => $this->vapaana,
                'hinta' => $this->hinta));
        $row = $query->fetch();
        $this->id = $row['id'];
        
        $this->instanceVariablesToViewForm();  // Selkeyden vuoksi pidetään oliomuuttujat aina esitysmuodossa vaikka niitä ei käytettäisikään enää.
    }
    
    public function updateDate(){
        $this->instanceVariablesToDatabaseForm();  // Turha, mutta selkeyden vuoksi.
        
        $query = DB::connection()->prepare(
                'UPDATE Olutera SET valmistuminen=:valmistuminen WHERE id=:id');
        $query->execute(array('valmistuminen' => $this->valmistuminen, 'id' => $this->id));
        
        $this->instanceVariablesToViewForm();  // Selkeyden vuoksi pidetään oliomuuttujat aina esitysmuodossa vaikka niitä ei käytettäisikään enää.
    }
    
    public function delete(){
        $query = DB::connection()->prepare(
                'DELETE FROM Olutera WHERE id=:id');
        $query->execute(array('id' => $this->id));
    }
    

    // Oliomuuttujien muuntajat:
    
    /**
     * Muuntaa oliomuuttujien arvot oikeaan muotoon tietokannan näkökulmasta.
     * Olettaa että oliomuuttujat ovat siinä muodossa missä ne on HTML lomakkeesta saatu
     * ja että oliomuuttujien arvot on validoitu.
     */
    public function instanceVariablesToDatabaseForm(){
        // Erän koko:
        $this->eran_koko = str_replace(' ', '', $this->eran_koko);
        $this->eran_koko = str_replace(',', '.', $this->eran_koko);  // Muutetaan , -> . jotta käyttäjä voi käyttää kumpaa tahansa.
        $this->eran_koko = floatval($this->eran_koko);
        $this->eran_koko = intval($this->eran_koko*100);  // Muunto senttilitroiksi ja katkaistaan mahdolliset senttilitrojen murto-osat pois muuttamalla integeriksi.
        // Vapaana:
        $this->vapaana = str_replace(' ', '', $this->vapaana);
        $this->vapaana = str_replace(',', '.', $this->vapaana);  // Muutetaan , -> . jotta käyttäjä voi käyttää kumpaa tahansa.
        $this->vapaana = floatval($this->vapaana);
        $this->vapaana = intval($this->vapaana*100);  // Muunto senttilitroiksi ja katkaistaan mahdolliset senttilitrojen murto-osat pois muuttamalla integeriksi.
        // Hinnan muutos:
        $this->hinta = str_replace(' ', '', $this->hinta);
        $this->hinta = str_replace(',', '.', $this->hinta);  // Muutetaan , -> . jotta käyttäjä voi käyttää kumpaa tahansa.
        $this->hinta = floatval($this->hinta);
        $this->hinta = intval($this->hinta*100);  // Muutetaan hinta senteiksi ja katkaistaan mahdolliset sentin murto-osat pois muuttamalla integeriksi.
    }
    
    public function instanceVariablesToViewForm(){
            $this->eran_koko = $this->eran_koko / 100;  // Muutetaan erän koko cl --> l.
            $this->vapaana = $this->vapaana / 100;      // Muutetaan vapaana cl --> l.
            $this->hinta = $this->hinta / 100;          // Muutetaan senttihinta "eurot,sentit"-muotoiseksi desimaalihinnaksi.
    }
    
    
    // Validaattorit:
    
    public function validate_oluen_nimi(){
        $errors = array();
        
        if(!BaseModel::validate_string_length($this->oluen_nimi, 1, 100)){
          $errors[] = 'Oluen nimen on oltava 1 - 100 merkkiä!';
        }

        return $errors;
    }
    
    public function validate_valmistuminen(){
        $errors = array();
        
        if(!BaseModel::validate_date($this->valmistuminen)){
          $errors[] = 'Päivämäärä on virheellisessä muodossa!';
        }

        return $errors;
    }
    
    public function validate_eran_koko(){  // Minimi erän koko final oliomuuttujaks?
        $errors = array();
        
        if(!BaseModel::validate_non_negative_string_float_and_its_bounds($this->eran_koko, 4, 1000000)){
          $errors[] = 'Erän koon on oltava kokonais- tai desimaaliluku väliltä 4 ja 1 000 000!';
        }

        return $errors;
    }
    
    public function validate_hinta(){
        $errors = array();
        
        if(!BaseModel::validate_non_negative_string_float_and_its_bounds($this->hinta, 0, 1000)){
          $errors[] = 'Oluen €/litra hinnan on oltava kokonais- tai desimaaliluku väliltä 0 ja 1000!';
        }

        return $errors;
    }
    
}