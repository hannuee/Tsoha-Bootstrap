<?php

class Olutera extends BaseModel{
    
    public $id, $oluen_nimi, $valmistuminen, $eran_koko, $vapaana, $hinta;
    
    public function __construct($attributes){
        parent::__construct($attributes);
    }
    
    public static function all(){
        $rows = DB::connection()->prepare('SELECT * FROM Olutera')->execute()->fetchAll();
        $oluterat = array();
        foreach($rows as $row){
            $oluterat[] = new Olutera($row);
        }
        return $oluterat;
    }
    
    public static function find($id){
         $row = DB::connection()
                 ->prepare('SELECT * FROM Olutera WHERE id = :id LIMIT 1')
                 ->execute(array('id' => $id))
                 ->fetch();
         if($row){
             $olutera = new Olutera($row);
             return $olutera;
         }
         return null;
    }
    
}