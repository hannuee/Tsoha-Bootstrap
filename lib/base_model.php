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
    
    public static function validate_non_negative_string_float($string){
        $string = str_replace(' ', '', $string);
        $string = str_replace(',', '.', $string);
        $string = str_replace('.', '0', $string, 1);
        if(ctype_digit($string)){
            return true;
        } else {
            return false;
        }
    }
    
    public static function validate_upper_bound_of_non_negative_string_float($string, $high){
        $string = str_replace(' ', '', $string);
        $string = str_replace(',', '.', $string);
        if(floatval($string) <= $high){
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
