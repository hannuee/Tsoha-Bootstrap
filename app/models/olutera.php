<?php

class Olutera extends BaseModel{
    
    // Mallin tietokantatauluista poikkeavat oliomuuttujien tiedon säilytysmuodot:
    // eran_koko (litraa, EI senttilitraa)
    // vapaana (litraa, EI senttilitraa)
    // hinta_euroa JA hinta_senttia (EI pelkkiä senttejä)
    public $id, $oluen_nimi, $valmistuminen, $eran_koko, $vapaana, $hinta_euroa, $hinta_senttia;
    
    public function __construct($attributes){   
        parent::__construct($attributes);
        $this->validators = array('validate_oluen_nimi', 'validate_valmistuminen', 'validate_eran_koko', 'validate_hinta_euroa', 'validate_hinta_senttia');
    }
    
    /**
     * 
     * @param type $margin Vaadittu oluterän vapaana olevan oluen määrä senttilitroissa
     * jotta funktio palauttaa oluterän. (Älä salli käyttäjän syötettä tähän parametriin!)
     * @return \Olutera
     */
    public static function allAvailableWithMargin($margin){  
        $query = DB::connection()->prepare('SELECT * FROM Olutera WHERE vapaana >= ' . $margin);
        $query->execute();
        $rows = $query->fetchAll();
        
        $oluterat = array();
        foreach($rows as $row){
            $row['eran_koko'] = $row['eran_koko'] / 100; // Muutetaan erän koko cl --> l.
            $row['vapaana'] = $row['vapaana'] / 100;     // Muutetaan vapaana cl --> l.
            $row['hinta_euroa'] = intval(intval($row['hinta']) / 100);
            $row['hinta_senttia'] = intval($row['hinta']) - intval(intval($row['hinta']) / 100) * 100;  // esim. 875 - 800
            
            $oluterat[] = new Olutera($row);
        }
        return $oluterat;
    }
    
    /**
     * 
     * @return \Olutera
     */
    public static function all(){
        $query = DB::connection()->prepare('SELECT * FROM Olutera');
        $query->execute();
        $rows = $query->fetchAll();
        
        $oluterat = array();
        foreach($rows as $row){
            $row['eran_koko'] = $row['eran_koko'] / 100; // Muutetaan erän koko cl --> l.
            $row['vapaana'] = $row['vapaana'] / 100;     // Muutetaan vapaana cl --> l.
            $row['hinta_euroa'] = intval(intval($row['hinta']) / 100);
            $row['hinta_senttia'] = intval($row['hinta']) - intval(intval($row['hinta']) / 100) * 100;  // esim. 875 - 800
            
            $oluterat[] = new Olutera($row);
        }
        return $oluterat;
    }
    
    /**
     * 
     * @param type $id
     * @param type $margin Vaadittu oluterän vapaana olevan oluen määrä senttilitroissa
     * jotta funktio palauttaa oluterän. (Älä salli käyttäjän syötettä tähän parametriin!)
     * @return \Olutera
     */
    public static function oneAvailableWithMargin($id, $margin){
        $query = DB::connection()->prepare('SELECT * FROM Olutera WHERE id = :id AND vapaana > ' . $margin . ' LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        
        if($row){
            $row['eran_koko'] = $row['eran_koko'] / 100; // Muutetaan erän koko cl --> l.
            $row['vapaana'] = $row['vapaana'] / 100;     // Muutetaan vapaana cl --> l.
            $row['hinta_euroa'] = intval(intval($row['hinta']) / 100);
            $row['hinta_senttia'] = intval($row['hinta']) - intval(intval($row['hinta']) / 100) * 100;  // esim. 875 - 800
            
            $olutera = new Olutera($row);
            
            return $olutera;
        }
        return null;
    }
    
    /**
     * 
     * @param type $id
     * @return \Olutera
     */
    public static function one($id){
        $query = DB::connection()->prepare('SELECT * FROM Olutera WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        
        if($row){
            $row['eran_koko'] = $row['eran_koko'] / 100; // Muutetaan erän koko cl --> l.
            $row['vapaana'] = $row['vapaana'] / 100;     // Muutetaan vapaana cl --> l.
            $row['hinta_euroa'] = intval(intval($row['hinta']) / 100);
            $row['hinta_senttia'] = intval($row['hinta']) - intval(intval($row['hinta']) / 100) * 100;  // esim. 875 - 800
            
            $olutera = new Olutera($row);
            
            return $olutera;
        }
        return null;
    }
    
    public function save(){
        $hinta = $this->hinta_euroa * 100 + $this->hinta_senttia;
        
        $query = DB::connection()->prepare(
                'INSERT INTO Olutera (oluen_nimi, valmistuminen, eran_koko, vapaana, hinta)
                 VALUES (:oluen_nimi, :valmistuminen, :eran_koko, :vapaana, :hinta)
                 RETURNING id');
        $query->execute(array(
                'oluen_nimi' => $this->oluen_nimi, 'valmistuminen' => $this->valmistuminen, 
                'eran_koko' => intval($this->eran_koko) * 100, 'vapaana' => intval($this->vapaana) * 100, 'hinta' => $hinta));
        $row = $query->fetch();
        $this->id = $row['id'];
    }
    
    public function updateDate(){
        $query = DB::connection()->prepare(
                'UPDATE Olutera SET valmistuminen=:valmistuminen WHERE id=:id');
        $query->execute(array('valmistuminen' => $this->valmistuminen, 'id' => $this->id));
    }
    
    public function delete(){
        $query = DB::connection()->prepare(
                'DELETE FROM Olutera WHERE id=:id');
        $query->execute(array('id' => $this->id));
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
        
        if(!BaseModel::validate_non_negative_string_integer($this->eran_koko) ||
           !BaseModel::validate_lower_bound_of_string_numeric($this->eran_koko, 4)){
          $errors[] = 'Erän koon on oltava kokonaisluku ja vähintään 4!';
        }

        return $errors;
    }
    
    public function validate_hinta_euroa(){
        $errors = array();
        
        if(!BaseModel::validate_non_negative_string_integer($this->hinta_euroa)){
          $errors[] = 'Oluen euroa/litra hinnan on oltava ei-negatiivinen kokonaisluku!';
        }

        return $errors;
    }
    
    public function validate_hinta_senttia(){
        $errors = array();
        
        if(!BaseModel::validate_non_negative_string_integer($this->hinta_senttia) ||
           !BaseModel::validate_upper_bound_of_string_numeric($this->hinta_senttia, 99)     ){
          $errors[] = 'Oluen senttiä/litra hinnan on oltava kokonaisluku välillä 0-99!';
        }

        return $errors;
    }
    
}