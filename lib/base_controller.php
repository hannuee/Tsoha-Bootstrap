<?php

  class BaseController{
      
    public static function get_user_logged_in(){
        if(isset($_SESSION['user'])){
          $id = $_SESSION['user'];
          $corporate_customer = Yritysasiakas::find($id);

          return $corporate_customer;
        }

        return null;
    }
    
    public static function get_admin_logged_in(){
        if(isset($_SESSION['admin'])){
          $id = $_SESSION['admin'];
          $corporate_customer = Yritysasiakas::find($id);

          return $corporate_customer;
        }

        return null;
    }
    
    public static function get_user_name_if_logged_in(){
        if(isset($_SESSION['user'])){
          return $_SESSION['name'];
        }
        return null;
    }
    
    public static function get_admin_name_if_logged_in(){
        if(isset($_SESSION['admin'])){
          return $_SESSION['name'];
        }
        return null;
    }

    public static function check_user_logged_in(){
        if(!isset($_SESSION['user'])){
            View::make('login.html', array('error' => 'Yrittämällesi sivulle pääsee vain sisäänkirjautuneet yritysasiakkaat!'));
        }
    }
    
    public static function check_admin_logged_in(){
        if(!isset($_SESSION['admin'])){
            View::make('login.html', array('error' => 'Yrittämällesi sivulle pääsee vain sisäänkirjautuneet työntekijät!'));
        }
    }
    
    /**
     * Tarkastaa että annettu id on numero ja että tämä numero on välillä 1-(tietokannan ja PHPn int max value).
     * Jos annettu id ei täytä näitä vaatimuksia niin uudelleenohjataan käyttäjä annettuun osoitteeseen
     * virheviestillä "Tapahtui tekninen virhe!".
     * @param type $id Id joka tarkastetaan.
     * @param type $uudelleenohjaus Osoite johon uudelleenohjataan jos id ei ole kunnossa.
     */
    public static function tarkasta_id_ulkoasu($id, $uudelleenohjaus){
        $idSyntaxError = BaseModel::validate_id_directly($id);
        if(count($idSyntaxError) != 0){
            Redirect::to($uudelleenohjaus, array('errors' => $idSyntaxError));
        }
    }
    
    /**
     * Tarkastaa että operaatio ei anna tuotoksena falsea merkiksi epäonnistumisesta.
     * Jos operaatio epäonnistuu niin uudelleenohjataan käyttäjä annettuun osoitteeseen,
     * annetun virheviestin kanssa ja mahdollisesti annettujen parametrien kanssa.
     * @param type $tuotos Operaation antama tuotos.
     * @param type $uudelleenohjaus Osoite johon uudelleenohjataan jos operaatio epäonnistui.
     * @param type $virheviesti Virheviesti joka annetaan käyttäjälle.
     * @param type $params Käyttäjän antama syöte.
     * @return type
     */
    public static function tarkasta_onnistuminen($tuotos, $uudelleenohjaus, $virheviesti, $params){
        if(!$tuotos){
            if(is_null($params)){
                Redirect::to($uudelleenohjaus, array('errors' => array($virheviesti)));
            } else {
                Redirect::to($uudelleenohjaus, array('errors' => array($virheviesti), 'attributes' => $params));
            }
        }
        return $tuotos;
    }

  }
