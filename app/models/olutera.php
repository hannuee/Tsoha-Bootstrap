<?php

class Olutera extends BaseModel{
    
    public $id, $oluen_nimi, $valmistuminen, $eran_koko, $vapaana, $hinta;
    
    public function __construct($attributes){
        parent::__construct($attributes);
    }
    
    /**
     * 
     * @param type $margin Vaadittu oluterän vapaana olevan oluen määrä senttilitroissa
     * jotta funktio palauttaa oluterän. (Älä salli käyttäjän syötettä tähän parametriin!)
     * @return \Olutera
     */
    public static function allAvailableWithMargin($margin){  
        $query = DB::connection()->prepare('SELECT * FROM Olutera WHERE vapaana > ' . $margin);
        $query->execute();
        $rows = $query->fetchAll();
        
        $oluterat = array();
        foreach($rows as $row){
            $row['eran_koko'] = $row['eran_koko'] / 100; // Muutetaan erän koko cl --> l.
            $row['vapaana'] = $row['vapaana'] / 100;     // Muutetaan vapaana cl --> l.
            $row['hinta'] = $row['hinta'] / 100;         // Muutetaan hinta snt/l --> €/l.
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
            $row['hinta'] = $row['hinta'] / 100;         // Muutetaan hinta snt/l --> €/l.
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
            $row['hinta'] = $row['hinta'] / 100;         // Muutetaan hinta snt/l --> €/l.
            $olutera = new Olutera($row);
            return $olutera;
        }
        return null;
    }
    
}