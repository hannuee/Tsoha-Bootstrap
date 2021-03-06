<?php

class Pakkaustyyppi extends BaseModel{
    
    public $id, $pakkaustyypin_nimi, $vetoisuus, $hinta, $pantti, $saatavilla;
    
    public function __construct($attributes){
        parent::__construct($attributes);
        $this->validators = array('validate_pakkaustyypin_nimi', 'validate_vetoisuus', 'validate_hinta', 'validate_pantti');
    }
    
    public static function allAvailable(){
        $query = DB::connection()->prepare('SELECT * FROM Pakkaustyyppi WHERE saatavilla = 1');
        $query->execute();
        $rows = $query->fetchAll();
        
        $pakkaustyypit = array();
        foreach($rows as $row){
            $pakkaustyyppi = new Pakkaustyyppi($row);
            $pakkaustyypit[] = $pakkaustyyppi;
        }
        return $pakkaustyypit;
    }
    
    /**
     * 
     * @param type $id Tilauksen id johon liittyvät pakkaustyypit ja niiden lukumäärät halutaan.
     * @return 2-ulotteinen taulukko jossa sisemmän taulukon ensimmäinen alkio on
     * pakkaustyyppi-olio ja toinen alkio kertoo näiden pakkaustyypin pakkausten lukumäärän.
     */
    public static function allForOrder($id){
        $query = DB::connection()->prepare('SELECT * FROM TilausPakkaustyyppi WHERE tilaus_id = ' . $id);  // TÄÄ TilausPakkaustyyppiin??
        $query->execute();
        $rows = $query->fetchAll();
        
        $pakkaustyypitMaarilla = array();
        foreach($rows as $row){
            $pakkaustyyppi = self::one($row['pakkaustyyppi_id']);
            $pakkaustyyppi->oliomuuttujatTietokantamuodostaEsitysmuotoon();  // FUNTI LOKAATIO!!!!!!!!!!!!
            
            $pakkaustyyppiMaaralla = array();
            $pakkaustyyppiMaaralla[] = $pakkaustyyppi;
            $pakkaustyyppiMaaralla[] = $row['lukumaara'];
                    
            $pakkaustyypitMaarilla[] = $pakkaustyyppiMaaralla;
        }
        return $pakkaustyypitMaarilla;
    }

    public static function all(){
        $query = DB::connection()->prepare('SELECT * FROM Pakkaustyyppi');
        $query->execute();
        $rows = $query->fetchAll();
        
        $pakkaustyypit = array();
        foreach($rows as $row){
            $pakkaustyyppi = new Pakkaustyyppi($row);
            $pakkaustyypit[] = $pakkaustyyppi;
        }
        return $pakkaustyypit;
    }
    
    public static function one($id){
        $query = DB::connection()->prepare('SELECT * FROM Pakkaustyyppi WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        
        if(!$row){
            return FALSE;
        }
        
        return new Pakkaustyyppi($row);
    }
    
    public function save(){
        $query = DB::connection()->prepare(
                'INSERT INTO Pakkaustyyppi (pakkaustyypin_nimi, vetoisuus, hinta, pantti, saatavilla)
                 VALUES (:pakkaustyypin_nimi, :vetoisuus, :hinta, :pantti, :saatavilla)
                 RETURNING id');
        $query->execute(array(
                'pakkaustyypin_nimi' => $this->pakkaustyypin_nimi,
                'vetoisuus' => $this->vetoisuus, 
                'hinta' => $this->hinta,
                'pantti' => $this->pantti,
                'saatavilla' => $this->saatavilla));
        $row = $query->fetch();
        
        if(!$row){
            return FALSE;
        }
        
        $this->id = $row['id'];
        return TRUE;
    }
    
    
    public static function updateAvailability($id){
        $query = DB::connection()->prepare(
                'UPDATE Pakkaustyyppi SET saatavilla=CASE saatavilla WHEN 1 THEN 0 ELSE 1 END WHERE id=:id RETURNING id');
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
        // Vetoisuus:
        $this->vetoisuus = str_replace(' ', '', $this->vetoisuus);
        $this->vetoisuus = str_replace(',', '.', $this->vetoisuus);  // Muutetaan , -> . jotta käyttäjä voi käyttää kumpaa tahansa.
        $this->vetoisuus = floatval($this->vetoisuus);
        $this->vetoisuus = intval(round($this->vetoisuus*100, 0, PHP_ROUND_HALF_UP));  // Muunto senttilitroiksi ja katkaistaan mahdolliset senttilitrojen murto-osat pois muuttamalla integeriksi.
        // Hinta:
        $this->hinta = str_replace(' ', '', $this->hinta);
        $this->hinta = str_replace(',', '.', $this->hinta);  // Muutetaan , -> . jotta käyttäjä voi käyttää kumpaa tahansa.
        $this->hinta = floatval($this->hinta);
        $this->hinta = intval(round($this->hinta*100, 0, PHP_ROUND_HALF_UP));  // Muutetaan hinta senteiksi ja katkaistaan mahdolliset sentin murto-osat pois muuttamalla integeriksi.
        // Pantti:
        $this->pantti = str_replace(' ', '', $this->pantti);
        $this->pantti = str_replace(',', '.', $this->pantti);  // Muutetaan , -> . jotta käyttäjä voi käyttää kumpaa tahansa.
        $this->pantti = floatval($this->pantti);
        $this->pantti = intval(round($this->pantti*100, 0, PHP_ROUND_HALF_UP));  // Muutetaan hinta senteiksi ja katkaistaan mahdolliset sentin murto-osat pois muuttamalla integeriksi.
    }
    
    public function oliomuuttujatTietokantamuodostaEsitysmuotoon(){
        $this->vetoisuus = $this->vetoisuus / 100;  // Muutetaan erän koko cl --> l.
        $this->hinta = $this->hinta / 100;          // Muutetaan senttihinta "eurot,sentit"-muotoiseksi desimaalihinnaksi.
        $this->pantti = $this->pantti / 100;        // Muutetaan senttihinta "eurot,sentit"-muotoiseksi desimaalihinnaksi.
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
    
    public function validate_pakkaustyypin_nimi(){
        $errors = array();
        
        if(!BaseModel::validate_string_length($this->pakkaustyypin_nimi, 1, 100)){
          $errors[] = 'Pakkaustyypin nimen on oltava 1 - 100 merkkiä!';
        }

        return $errors;
    }
    
    public function validate_vetoisuus(){
        $errors = array();
        
        if(!BaseModel::validate_non_negative_string_float_and_its_bounds($this->vetoisuus, 0, 1000000)){
          $errors[] = 'Vetoisuuden on oltava kokonais- tai desimaaliluku väliltä 0 ja 1 000 000!';
        }

        return $errors;
    }
    
    public function validate_hinta(){  // Minimi erän koko final oliomuuttujaks?
        $errors = array();
        
        if(!BaseModel::validate_non_negative_string_float_and_its_bounds($this->hinta, 0, 1000000)){
          $errors[] = 'Hinnan on oltava kokonais- tai desimaaliluku väliltä 0 ja 1 000 000!';
        }

        return $errors;
    }
    
    public function validate_pantti(){
        $errors = array();
        
        if(!BaseModel::validate_non_negative_string_float_and_its_bounds($this->pantti, 0, 1000000)){
          $errors[] = 'Pantin on oltava kokonais- tai desimaaliluku väliltä 0 ja 1 000 000!';
        }

        return $errors;
    }
    
}

