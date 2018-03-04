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
    
    public static function updateDeliveryStatus(){
        $query = DB::connection()->prepare(
                'UPDATE Tilaus SET toimitettu=1 WHERE id=:id');
        $query->execute(array('id' => $this->id));
    }
    
    public function delete(){
        $query = DB::connection()->prepare(
                'DELETE FROM Tilaus WHERE id=:id RETURNING olutera_id');
        $query->execute(array('id' => $this->id));
        $row = $query->fetch();
        
        if($row){
            return $row['olutera_id'];
        }
        
        return null;
    }
    
    
    public function oliomuuttujatLomakemuodostaTietokantamuotoon(){
//        // Erän koko:
//        $this->eran_koko = str_replace(' ', '', $this->eran_koko);
//        $this->eran_koko = str_replace(',', '.', $this->eran_koko);  // Muutetaan , -> . jotta käyttäjä voi käyttää kumpaa tahansa.
//        $this->eran_koko = floatval($this->eran_koko);
//        $this->eran_koko = intval($this->eran_koko*100);  // Muunto senttilitroiksi ja katkaistaan mahdolliset senttilitrojen murto-osat pois muuttamalla integeriksi.
//        // Vapaana:
//        $this->vapaana = str_replace(' ', '', $this->vapaana);
//        $this->vapaana = str_replace(',', '.', $this->vapaana);  // Muutetaan , -> . jotta käyttäjä voi käyttää kumpaa tahansa.
//        $this->vapaana = floatval($this->vapaana);
//        $this->vapaana = intval($this->vapaana*100);  // Muunto senttilitroiksi ja katkaistaan mahdolliset senttilitrojen murto-osat pois muuttamalla integeriksi.
//        // Hinnan muutos:
//        $this->hinta = str_replace(' ', '', $this->hinta);
//        $this->hinta = str_replace(',', '.', $this->hinta);  // Muutetaan , -> . jotta käyttäjä voi käyttää kumpaa tahansa.
//        $this->hinta = floatval($this->hinta);
//        $this->hinta = intval($this->hinta*100);  // Muutetaan hinta senteiksi ja katkaistaan mahdolliset sentin murto-osat pois muuttamalla integeriksi.
    }
    
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

