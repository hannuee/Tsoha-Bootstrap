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
        
        if(!$row){
            return FALSE;
        }
        
        return new Olutera($row);
    }
    
    public static function one($id){
        $query = DB::connection()->prepare('SELECT * FROM Olutera WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        
        if(!$row){
            return FALSE;
        }
        
        return new Olutera($row);
    }
    
    public function save(){
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
        
        if(!$row){
            return FALSE;
        }
        
        $this->id = $row['id'];
        return TRUE;
    }
    
    public static function updateDate($id, $valmistuminen){
        $query = DB::connection()->prepare(
                'UPDATE Olutera SET valmistuminen=:valmistuminen WHERE id=:id RETURNING id');
        $query->execute(array('valmistuminen' => $valmistuminen, 'id' => $id));
        $row = $query->fetch();
        
        if(!$row){
            return FALSE;
        }
        
        return TRUE;
    }
    
    public static function updateAmountAvailableReduce($id, $senttilitraa){
        $query = DB::connection()->prepare(
                'UPDATE Olutera SET vapaana=vapaana-:senttilitraa WHERE id=:id');
        $query->execute(array('senttilitraa' => $senttilitraa, 'id' => $id));
    }
    
    public static function updateAmountAvailableReduceTRANS($id, $senttilitraa, $connection){
        $query = $connection->prepare(
                'UPDATE Olutera SET vapaana=vapaana-:senttilitraa WHERE id=:id');
        $onnistuiko = $query->execute(array('senttilitraa' => $senttilitraa, 'id' => $id));
        
        if(!$onnistuiko){
            $connection->rollBack();
            return FALSE;
        }
        return TRUE;
    }
    
    public static function updateAmountAvailableAddTRANS($id, $senttilitraa, $connection){
        $query = $connection->prepare(
                'UPDATE Olutera SET vapaana=vapaana+:senttilitraa WHERE id=:id');
        $onnistuiko = $query->execute(array('senttilitraa' => $senttilitraa, 'id' => $id));
        
        if(!$onnistuiko){
            $connection->rollBack();
            return FALSE;
        }
        return TRUE;
    }
    

    public static function delete($id){
        $query = DB::connection()->prepare(
                'DELETE FROM Olutera WHERE id=:id RETURNING id');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        
        if(!$row){
            return FALSE;
        }
        
        return TRUE;
    }
    

    // Oliomuuttujien muuntajat:
    
    /**
     * Muuntaa oliomuuttujien arvot oikeaan muotoon tietokannan näkökulmasta.
     * Olettaa että oliomuuttujat ovat siinä muodossa missä ne on HTML lomakkeesta saatu
     * ja että oliomuuttujien arvot on validoitu.
     */
    public function oliomuuttujatLomakemuodostaTietokantamuotoon(){
        // Erän koko:
        $this->eran_koko = str_replace(' ', '', $this->eran_koko);
        $this->eran_koko = str_replace(',', '.', $this->eran_koko);  // Muutetaan , -> . jotta käyttäjä voi käyttää kumpaa tahansa.
        $this->eran_koko = floatval($this->eran_koko);
        $this->eran_koko = intval(round($this->eran_koko*100, 0, PHP_ROUND_HALF_UP));  // Muunto senttilitroiksi ja katkaistaan mahdolliset senttilitrojen murto-osat pois muuttamalla integeriksi.
        // Vapaana:
        $this->vapaana = str_replace(' ', '', $this->vapaana);
        $this->vapaana = str_replace(',', '.', $this->vapaana);  // Muutetaan , -> . jotta käyttäjä voi käyttää kumpaa tahansa.
        $this->vapaana = floatval($this->vapaana);
        $this->vapaana = intval(round($this->vapaana*100, 0, PHP_ROUND_HALF_UP));  // Muunto senttilitroiksi ja katkaistaan mahdolliset senttilitrojen murto-osat pois muuttamalla integeriksi.
        // Hinnan muutos:
        $this->hinta = str_replace(' ', '', $this->hinta);
        $this->hinta = str_replace(',', '.', $this->hinta);  // Muutetaan , -> . jotta käyttäjä voi käyttää kumpaa tahansa.
        $this->hinta = floatval($this->hinta);
        $this->hinta = intval(round($this->hinta*100, 0, PHP_ROUND_HALF_UP));  // Muutetaan hinta senteiksi ja katkaistaan mahdolliset sentin murto-osat pois muuttamalla integeriksi.
    }
    
    public function oliomuuttujatTietokantamuodostaEsitysmuotoon(){
        $this->eran_koko = $this->eran_koko / 100;  // Muutetaan erän koko cl --> l.
        $this->vapaana = $this->vapaana / 100;      // Muutetaan vapaana cl --> l.
        $this->hinta = $this->hinta / 100;          // Muutetaan senttihinta "eurot,sentit"-muotoiseksi desimaalihinnaksi.
    }
    
    
    // Validaattorit:
    
    public function validate_id(){  // Tämä validointi ei mene läpi vain jos POST-dataa muokataan tai tapahtuu jotain odottamatonta.
        $errors = array();
        
        if(BaseModel::validate_non_negative_string_integer($this->id)){
          if(!BaseModel::validate_bounds_of_string_integer($this->id, 1, 2147483647)){
              $errors[] = 'Tapahtui tekninen virhe!';
          }
        } else {
            $errors[] = 'Tapahtui tekninen virhe!';
        }

        return $errors;
    }
    
    public static function validate_valmistuminen_staattinen($valmistuminen){
        $errors = array();
        
        if(!BaseModel::validate_date($valmistuminen)){
          $errors[] = 'Päivämäärä on virheellisessä muodossa!';
        }

        return $errors;
    }
    
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