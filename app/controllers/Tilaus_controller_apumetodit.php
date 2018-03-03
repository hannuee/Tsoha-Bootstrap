<?php

class TilausControllerApumetodit {
    
    public static function tarkistaOluteranId(){
        
    }
    
    /**
     * Apumetodi joka erottelee lähetetystä tilauslomakkeesta pakkaustyyppien id:t ja niiden lukumäärät,
     * validoi nämä tiedot ja sitten palauttaa nämä tiedot TilausPakkaustyyppi-liitostaulumalleina.
     * @param type $params Lähetetyn lomakkeen sisältö POST-muodossa.
     * @return \TilausPakkaustyyppi Lista TilausPakkaustyyppi-liitostaulumalleja.
     */
    public static function tilausPakkaustyyppiMallienTiedotLomakkeesta($params){
        $pakkaustyypitJaMaarat = array(); 
        $allErrors = array();
        
        // Otetaan talteen lomakkeen tiedot: pakkaustyyppien id:t ja kyseisten pakkaustyyppien määrät.
        foreach($params as $key => $value){
            if(strstr($key, "quantity") && $value != 0){
                $pakkaustyyppiJaMaara = new TilausPakkaustyyppi(array(
                    'pakkaustyyppi_id' => str_replace("quantity", "", $key),
                    'lukumaara' => $value
                ));
                $errors = $pakkaustyyppiJaMaara->errors();
                $pakkaustyyppiJaMaara->oliomuuttujatLomakemuodostaTietokantamuotoon();
                
                $pakkaustyypitJaMaarat[] = $pakkaustyyppiJaMaara;
                $allErrors = array_merge($allErrors, $errors);
            }
        }
        
        // Jos erroreita osatilauksissa niin redirect errormessagein takas tilauslomakkeeseen.
        if(count($allErrors) != 0){
            Redirect::to('/hallinnointi/tilaukset/uusi/' . $params['olutera_id'], array('errors' => $allErrors));
        }
        // ^TODO: ATTRIBUUTIT!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        
        return $pakkaustyypitJaMaarat;
    }
    
    /**
     * Apumetodi joka erottelee lähetetystä tilauslomakkeesta toimitusohjeet, oluterän id:n ja yritysasiakkaan id:n,
     * validoi nämä tiedot ja sitten palauttaa nämä tiedot Tilaus-malleina.
     * @param type $params Lähetetyn lomakkeen sisältö POST-muodossa.
     * @return \Tilaus Tilaus-mallin.
     */
    public static function tilausMallinTiedotLomakkeesta($params){
        $tilausajankohta = new DateTime();
        $tilausajankohta->format('Y-m-d');   // TOISTASEKS HOIDETTU SQL NOW():LLA.!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        // Tilausolion teko ja samalla Oluterä id:n syntax check
        $tilaus = new Tilaus(array(
            'tilausajankohta' => $tilausajankohta,
            'toimitettu' => 0,
            'toimitusohjeet' => $params['toimitusohjeet'],
            'olutera_id' => $params['olutera_id'],
            'yritysasiakas_id' => $params['yritysasiakas_id']
        ));
        $tilausErrors = $tilaus->errors();   // instanceVariablesToDatabaseForm() seuraavaks ???????????????????????????
        
        // Jos erroreita oluterän id:ssä niin redirect etusivulle, koska ei tietoa oluterän id:stä.
        if(count($tilausErrors) != 0){
            Redirect::to('/', array('errors' => $tilausErrors));
        }
        
        return $tilaus;
    }
    
    public static function senttilitrojenLaskeminenJaPakkaustyyppienTarkistus($tilausPakkaustyypit, $params){
        $senttilitroja = 0;
        // Tarkastetaan että kaikki pakkaustyypit ovat saatavilla(jos virheellisen lomakkeen "väärennys" TAI pakkauksen saatavuus juuri vaihtunut).
        // Lasketaan samalla paljonko olutta on senttilitroissa tilattu.
        $pakkausErrors = array();
        foreach($tilausPakkaustyypit as $tilausPakkaustyyppi){
            $pakkaustyyppi = Pakkaustyyppi::one($tilausPakkaustyyppi->pakkaustyyppi_id);
            $pakkaustyyppi->oliomuuttujatTietokantamuodostaEsitysmuotoon();
            if(is_null($pakkaustyyppi)){
                $pakkausErrors = array_merge($pakkausErrors, array("Lomake sisälsi virheellisen pakkaustyypin ID:n: " . $tilausPakkaustyyppi->pakkaustyyppi_id));
            } elseif($pakkaustyyppi->saatavilla == 0){
                $pakkausErrors = array_merge($pakkausErrors, array("Pakkaustyyppi " . $pakkaustyyppi->pakkaustyypin_nimi . " ei valitettavasti enää ole saatavilla."));
            } else {
                $senttilitroja += $tilausPakkaustyyppi->lukumaara * $pakkaustyyppi->vetoisuus * 100;  // MUUNTOJA!!!!?!?!?!?!?????????????!?!?!?!?!?
            }
        }
        
        // Jos erroreita pakkaustyypeissä niin redirect errormessagein takas tilauslomakkeeseen.
        if(count($pakkausErrors) != 0){
            Redirect::to('/tilaukset/uusi/' . $params['olutera_id'], array('errors' => $pakkausErrors));
        }
        // ^TODO: ATTRIBUUTIT!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        
        return $senttilitroja;
    }
    
    public static function tarkistaOluteranIdJaVapaanOluenMaara($senttilitroja, $tilaus, $params){
        // Tarkistetaan että oluterän ID ok ja että oluterässä riittävästi vapaana olutta.
        $oluteraErrors = array();
        $olutera = Olutera::one($tilaus->olutera_id);
        $olutera->oliomuuttujatTietokantamuodostaEsitysmuotoon();
        $olutera->vapaana = $olutera->vapaana * 100;  // OLUTERÄ NYT SENTTILITROISSA!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        if(is_null($olutera)){
            $oluteraErrors[] = "Virheellinen oluterän ID!";  // REDIRECT EI-LOMAKKEESEEN!!!??
        } elseif($olutera->vapaana < $senttilitroja){
            $oluteraErrors[] = "Oluterässä ei tarpeeksi litroja vapaana!";
        }
        
        // Jos erroreita oluterässä niin redirect errormessagein takas tilauslomakkeeseen.
        if(count($oluteraErrors) != 0){
            Redirect::to('/tilaukset/uusi/' . $params['olutera_id'], array('errors' => $oluteraErrors));
        }
        // ^TODO: ATTRIBUUTIT!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        
        return $olutera;
    }
    
    public static function lisaaUusiTilaus($senttilitroja, $tilaus, $tilausPakkaustyypit, $olutera){
        // Tallennetaan Tilaus-olio, TilausPakkaustyyppi-oliot(ja tallennetaan niihin tilaus_id) sekä vähennetään kyseisen oluterän vapaana olevan oluen määrää.
        $olutera->vapaana -= $senttilitroja;
        $olutera->updateAmountAvailable();
        
        $tilaus->save();
        
        foreach($tilausPakkaustyypit as $tilausPakkaustyyppi){
            $tilausPakkaustyyppi->tilaus_id = $tilaus->id;
            $tilausPakkaustyyppi->save();
        }
        
        Redirect::to('/hallinnointi/oluterat', array('message' => 'Tilaus lähetetty onnistuneesti!'));
    }
    
}
