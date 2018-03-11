<?php

class TilausControllerApumetodit extends BaseController{
    
    public static function tarkistaLomakkeestaOluteraIdJaPalautaOlutera($params, $uudelleenohjaus){
       self::tarkasta_id_ulkoasu($params['olutera_id'], $uudelleenohjaus);

       $olutera = self::tarkasta_onnistuminen(
                Olutera::one($params['olutera_id']), $uudelleenohjaus, 'Tekninen virhe!', NULL);

       return $olutera;
    }
    
    public static function tarkistaLomakkeestaYritysasiakasId($params, $uudelleenohjaus){
        self::tarkasta_id_ulkoasu($params['yritysasiakas_id'], $uudelleenohjaus);
        
        $yritysasiakas = self::tarkasta_onnistuminen(
                Yritysasiakas::one($params['yritysasiakas_id']), $uudelleenohjaus, 'Tekninen virhe!', NULL);
     }
    
    /**
     * Apumetodi joka erottelee lähetetystä tilauslomakkeesta pakkaustyyppien id:t ja niiden lukumäärät,
     * validoi nämä tiedot ja sitten palauttaa nämä tiedot TilausPakkaustyyppi-liitostaulumalleina.
     * @param type $params Lähetetyn lomakkeen sisältö POST-muodossa.
     * @return \TilausPakkaustyyppi Lista TilausPakkaustyyppi-liitostaulumalleja.
     */
    public static function tilausPakkaustyyppiMallienTiedotLomakkeesta($params, $uudelleenohjaus){
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
            Redirect::to($uudelleenohjaus . $params['olutera_id'], array('errors' => $allErrors, 'attributes' => $params));
        }
        
        return $pakkaustyypitJaMaarat;
    }
    
    public static function toimitusohjeetLomakkeestaJaPalautaTilaus($params, $yritysasiakas_id){
        $tilaus = new Tilaus(array(
            'toimitettu' => 0,
            'toimitusohjeet' => $params['toimitusohjeet'],
            'olutera_id' => $params['olutera_id'],
            'yritysasiakas_id' => $yritysasiakas_id
        ));
        
        return $tilaus;
    }
    
    public static function senttilitrojenLaskeminenJaPakkaustyyppienTarkistus($tilausPakkaustyypit, $params, $uudelleenohjaus){
        $senttilitroja = 0;
        // Tarkastetaan että kaikki pakkaustyypit ovat saatavilla(jos virheellisen lomakkeen "väärennys" TAI pakkauksen saatavuus juuri vaihtunut).
        // Lasketaan samalla paljonko olutta on senttilitroissa tilattu.
        $pakkausErrors = array();
        foreach($tilausPakkaustyypit as $tilausPakkaustyyppi){
            
            $pakkaustyyppi = Pakkaustyyppi::one($tilausPakkaustyyppi->pakkaustyyppi_id);
            if(!$pakkaustyyppi){
                $pakkausErrors = array_merge($pakkausErrors, array('Tekninen virhe!'));
            } elseif($pakkaustyyppi->saatavilla == 0){
                $pakkausErrors = array_merge($pakkausErrors, array("Pakkaustyyppi " . $pakkaustyyppi->pakkaustyypin_nimi . " ei valitettavasti enää ole saatavilla."));
            } else {
                $senttilitroja += $tilausPakkaustyyppi->lukumaara * $pakkaustyyppi->vetoisuus;
            }
        }
        
        // Jos erroreita pakkaustyypeissä niin redirect errormessagein takas tilauslomakkeeseen.
        if(count($pakkausErrors) != 0){
            Redirect::to($uudelleenohjaus . $params['olutera_id'], array('errors' => $pakkausErrors, 'attributes' => $params));
        }
        
        return $senttilitroja;
    }
    
    public static function tarkistaVapaanOluenMaara($senttilitroja, $olutera, $params, $uudelleenohjaus){
        // Jos erroreita oluterässä niin redirect errormessagein takas tilauslomakkeeseen.
        if($olutera->vapaana < $senttilitroja){
            Redirect::to($uudelleenohjaus . $params['olutera_id'], array('errors' => array('Oluterässä ei ole tarpeeksi olutta!'), 'attributes' => $params));
        }
    }
    
    
    // Tallennetaan Tilaus-olio, TilausPakkaustyyppi-oliot(ja tallennetaan niihin tilaus_id) sekä vähennetään kyseisen oluterän vapaana olevan oluen määrää.
    public static function lisaaUusiTilaus($senttilitroja, $tilaus, $tilausPakkaustyypit, $olutera, $params, $uudelleenohjaus, $uudelleenohjausOnnistuessa){
        $connection = self::tarkasta_onnistuminen(
                BaseModel::beginTransaction(), $uudelleenohjaus . $params['olutera_id'], 'Tekninen virhe!', $params);
        
        self::tarkasta_onnistuminen(
                Olutera::updateAmountAvailableReduceTRANS($olutera->id, $senttilitroja, $connection), $uudelleenohjaus . $params['olutera_id'], 'Tekninen virhe!', $params);

        self::tarkasta_onnistuminen(
                $tilaus->saveTRANS($connection), $uudelleenohjaus . $params['olutera_id'], 'Tekninen virhe!', $params);

        foreach($tilausPakkaustyypit as $tilausPakkaustyyppi){
            $tilausPakkaustyyppi->tilaus_id = $tilaus->id;
            
            self::tarkasta_onnistuminen(
                $tilausPakkaustyyppi->saveTRANS($connection), $uudelleenohjaus . $params['olutera_id'], 'Tekninen virhe!', $params);
        }
        
        self::tarkasta_onnistuminen(
                BaseModel::commit($connection), $uudelleenohjaus . $params['olutera_id'], 'Tekninen virhe!', $params);
        
        Redirect::to($uudelleenohjausOnnistuessa, array('message' => 'Tilaus lähetetty onnistuneesti!'));
    }
    
}
