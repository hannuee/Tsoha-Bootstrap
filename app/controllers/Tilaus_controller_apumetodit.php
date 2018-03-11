<?php

class TilausControllerApumetodit {
    
    public static function tarkistaLomakkeestaOluteraIdJaPalautaOlutera($params){
       $idSyntaxError = BaseModel::validate_id_directly($params['olutera_id']);
       if(count($idSyntaxError) != 0){
           Redirect::to('/hallinnointi/oluterat', array('errors' => $idSyntaxError));
       }

       $olutera = Olutera::one($params['olutera_id']); 
       if(!$olutera){
           Redirect::to('/hallinnointi/oluterat', array('errors' => array('Tekninen virhe!')));
       } 

       return $olutera;
    }
    
    public static function tarkistaLomakkeestaYritysasiakasId($params){
        $idSyntaxError = BaseModel::validate_id_directly($params['yritysasiakas_id']);
        if(count($idSyntaxError) != 0){
            Redirect::to('/hallinnointi/oluterat', array('errors' => $idSyntaxError));
        }
         
        $yritysasiakas = Yritysasiakas::one($params['yritysasiakas_id']); 
        if(!$yritysasiakas){
            Redirect::to('/hallinnointi/oluterat', array('errors' => array('Tekninen virhe!')));
        }
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
            Redirect::to('/hallinnointi/tilaukset/uusi/' . $params['olutera_id'], array('errors' => $allErrors, 'attributes' => $params));
        }
        
        return $pakkaustyypitJaMaarat;
    }
    
    public static function toimitusohjeetLomakkeestaJaPalautaTilaus($params){
        $tilaus = new Tilaus(array(
            'toimitettu' => 0,
            'toimitusohjeet' => $params['toimitusohjeet'],
            'olutera_id' => $params['olutera_id'],
            'yritysasiakas_id' => $params['yritysasiakas_id']
        ));
        
        return $tilaus;
    }
    
    public static function senttilitrojenLaskeminenJaPakkaustyyppienTarkistus($tilausPakkaustyypit, $params){
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
            Redirect::to('/hallinnointi/tilaukset/uusi/' . $params['olutera_id'], array('errors' => $pakkausErrors, 'attributes' => $params));
        }
        
        return $senttilitroja;
    }
    
    public static function tarkistaVapaanOluenMaara($senttilitroja, $olutera, $params){
        // Jos erroreita oluterässä niin redirect errormessagein takas tilauslomakkeeseen.
        if($olutera->vapaana < $senttilitroja){
            Redirect::to('/hallinnointi/tilaukset/uusi/' . $params['olutera_id'], array('errors' => array('Oluterässä ei ole tarpeeksi olutta!'), 'attributes' => $params));
        }
    }
    
    
    // Tallennetaan Tilaus-olio, TilausPakkaustyyppi-oliot(ja tallennetaan niihin tilaus_id) sekä vähennetään kyseisen oluterän vapaana olevan oluen määrää.
    public static function lisaaUusiTilaus($senttilitroja, $tilaus, $tilausPakkaustyypit, $olutera, $params){
        
        // Aloitetaan transaktio.
        $connection = BaseModel::beginTransaction();
        if(!$connection){
            Redirect::to('/hallinnointi/tilaukset/uusi/' . $params['olutera_id'], array('errors' => array('Tekninen virhe!'), 'attributes' => $params));
        }
        
        //Olutera::updateAmountAvailableReduce($olutera->id, $senttilitroja);
        $onnistuiko = Olutera::updateAmountAvailableReduceTRANS($olutera->id, $senttilitroja, $connection);
        if(!$onnistuiko){
            Redirect::to('/hallinnointi/tilaukset/uusi/' . $params['olutera_id'], array('errors' => array('Tekninen virhe!'), 'attributes' => $params));
        }
        
        $onnistuiko = $tilaus->saveTRANS($connection);
        if(!$onnistuiko){
            Redirect::to('/hallinnointi/tilaukset/uusi/' . $params['olutera_id'], array('errors' => array('Tekninen virhe!'), 'attributes' => $params));
        }
        
        foreach($tilausPakkaustyypit as $tilausPakkaustyyppi){
            $tilausPakkaustyyppi->tilaus_id = $tilaus->id;
            $tilausPakkaustyyppi->saveTRANS($connection);
            
            if(!$onnistuiko){
                Redirect::to('/hallinnointi/tilaukset/uusi/' . $params['olutera_id'], array('errors' => array('Tekninen virhe!'), 'attributes' => $params));
            }
        }
        
        // Lopetetaan transaktio.
        $onnistuiko = BaseModel::commit($connection);
        if(!$onnistuiko){
            Redirect::to('/hallinnointi/tilaukset/uusi/' . $params['olutera_id'], array('errors' => array('Tekninen virhe!'), 'attributes' => $params));
        }
        
        Redirect::to('/hallinnointi/oluterat', array('message' => 'Tilaus lähetetty onnistuneesti!'));
    }
    
}
