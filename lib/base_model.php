<?php

  class BaseModel{
    // "protected"-attribuutti on käytössä vain luokan ja sen perivien luokkien sisällä
    protected $validators;

    public function __construct($attributes = null){
      // Käydään assosiaatiolistan avaimet läpi
      foreach($attributes as $attribute => $value){
        // Jos avaimen niminen attribuutti on olemassa...
        if(property_exists($this, $attribute)){
          // ... lisätään avaimen nimiseen attribuuttin siihen liittyvä arvo
          $this->{$attribute} = $value;
        }
      }
    }
    
    public static function beginTransaction(){
        $connection = DB::connection();
        $onnistuiko = $connection->beginTransaction();
        if(!$onnistuiko){
            return false;
        }
        return $connection;
    }
    
    public static function commit($connection){
        $connection->commit();
    }
    
    public function errors(){
      // Lisätään $errors muuttujaan kaikki virheilmoitukset taulukkona
      $errors = array();

      foreach($this->validators as $validator){
        // Kutsu validointimetodia tässä ja lisää sen palauttamat virheet errors-taulukkoon
        $errors = array_merge($errors, $this->{$validator}());
      }

      return $errors;
    }
    
    public function customErrors($validators){
      // Lisätään $errors muuttujaan kaikki virheilmoitukset taulukkona
      $errors = array();

      foreach($validators as $validator){
        // Kutsu validointimetodia tässä ja lisää sen palauttamat virheet errors-taulukkoon
        $errors = array_merge($errors, $this->{$validator}());
      }

      return $errors;
    }
    
    
    /**
     * Olioilla oltava metodi oliomuuttujien muuttamiseen tietokantamuotoon.
     * @param type $taulukkoBmOlioita
     */
    public static function olioidenMuuttujatTietokantamuodostaEsitysmuotoon($taulukkoBmOlioita){
        foreach($taulukkoBmOlioita as $bmOlio){
            $bmOlio->oliomuuttujatTietokantamuodostaEsitysmuotoon();
        }
    }
    
    public static function validate_id_directly($id){  // Tämä validointi ei mene läpi vain jos POST-dataa muokataan tai tapahtuu jotain odottamatonta.
        $errors = array();
        
        if(BaseModel::validate_non_negative_string_integer($id)){
          if(!BaseModel::validate_bounds_of_string_integer($id, 1, 2147483647)){
              $errors[] = 'Tapahtui tekninen virhe!';
          }
        } else {
            $errors[] = 'Tapahtui tekninen virhe!';
        }

        return $errors;
    }
    
    
    
    public static function validate_string_length($string, $lengthMin, $lengthMax){
        $len = strlen($string);
        if($lengthMin <= $len && $len <= $lengthMax){
            return true;
        } else {
            return false;
        }
    }
    
    public static function validate_date($date){
        $dateCheck = date_create($date);
        if($dateCheck != false){
           return true; 
        } else {
            return false;
        }
    }
    
    public static function validate_non_negative_string_integer($string){
        $string = str_replace(' ', '', $string);
        if(ctype_digit($string)){
            return true;
        } else {
            return false;
        }
    }
    
    public static function validate_bounds_of_string_integer($string, $low, $high){
        $string = str_replace(' ', '', $string);
        $integer = intval($string);
        if($low <= $integer && $integer <= $high){
            return true;
        } else {
            return false;
        }
    }
    
    public static function validate_non_negative_string_float_and_its_bounds($string, $low, $high){
        $string = str_replace(' ', '', $string);
        $string = str_replace(',', '.', $string);
        
        $count = 1;
        $string2 = str_replace('.', '0', $string, $count);
        if(!ctype_digit($string2)){
            return false;
        }
        
        $asFloat = floatval($string);
        if($low <= $asFloat && $asFloat <= $high){
            return true;
        } else {
            return false;
        }
    }

  }
