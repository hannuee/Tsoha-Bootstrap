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
        if(ctype_digit($string)){
            return true;
        } else {
            return false;
        }
    }
    
    public static function validate_upper_bound_of_string_numeric($string, $high){
        $integer = intval($string);
        if($high >= $integer){
            return true;
        } else {
            return false;
        }
    }
    
    public static function validate_lower_bound_of_string_numeric($string, $low){
        $integer = intval($string);
        if($low <= $integer){
            return true;
        } else {
            return false;
        }
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

  }
