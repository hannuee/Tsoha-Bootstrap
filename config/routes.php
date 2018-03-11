<?php

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });

  
  
  $routes->get('/kirjautuminen', function() {
    YritysasiakasController::login();
  });
  
  $routes->post('/kirjautuminen', function() {
    YritysasiakasController::handle_login();
  });
  
  $routes->post('/ulos', function() {
    YritysasiakasController::logout();
  });  
  
  

  $routes->get('/', function() {
      OluteraController::listausMarginaalilla();
  });
  
  $routes->post('/tilaukset/uusi', function() {
      TilausController::lisaaUusiAsiakkaalta();
  });
  
  $routes->get('/tilaukset/uusi/:id', function($id) {
      TilausController::lisays($id);
  });
  
  $routes->get('/omattiedot', function() {
      YritysasiakasController::esittely();
  });
  
  $routes->get('/omattiedot/muokkaa', function() {
      YritysasiakasController::muokkaus();
  }); 
  
  $routes->post('/omattiedot/tallenna', function() {
      YritysasiakasController::muokkaa();
  }); 
  
  
  
  $routes->get('/hallinnointi/oluterat', function() {
      OluteraController::listaus();
  });
  
  $routes->post('/hallinnointi/oluterat/uusi', function() {
      OluteraController::lisaaUusi();
  });
  
  $routes->post('/hallinnointi/oluterat/uusipvm', function() {
      OluteraController::muokkaaValmistumispaivamaaraa();
  });
  
  $routes->post('/hallinnointi/oluterat/poisto', function() {
      OluteraController::poista();
  });
  
  $routes->get('/hallinnointi/oluterat/:id', function($id) {
      OluteraController::esittely($id);
  });
  
  $routes->post('/hallinnointi/tilaukset/uusi', function() {
      TilausController::lisaaUusi();
  });
  
  $routes->get('/hallinnointi/tilaukset/uusi/:id', function($id) {
      TilausController::lisaysLisavaihtoehdoin($id);
  });
  
  $routes->post('/hallinnointi/tilaukset/toimitettu', function() {
      TilausController::muokkaaToimitetuksi();
  });
  
  $routes->post('/hallinnointi/tilaukset/poista', function() {
      TilausController::poista();
  });
  
  $routes->get('/hallinnointi/pakkaustyypit', function() {
      PakkaustyyppiController::listaus();
  });
  
  $routes->post('/hallinnointi/pakkaustyypit/uusi', function() {
      PakkaustyyppiController::lisaaUusi();
  });
  
  $routes->post('/hallinnointi/pakkaustyypit/muutasaatavilla', function() {
      PakkaustyyppiController::muokkaaSaatavuusstatusta();
  });
  
  $routes->get('/hallinnointi/yritysasiakkaat', function() {
      YritysasiakasController::listaus();
  });
  
  $routes->get('/hallinnointi/yritysasiakkaat/teeuusi', function() {
      YritysasiakasController::lisays();
  });
  
  $routes->post('/hallinnointi/yritysasiakkaat/uusi', function() {
      YritysasiakasController::lisaaUusi();
  });
  
  $routes->post('/hallinnointi/yritysasiakkaat/muokkaa', function() {
      YritysasiakasController::muokkaaLisavaihtoehdoin();
  });
  
  $routes->get('/hallinnointi/yritysasiakkaat/muokkaa/:id', function($id) {
      YritysasiakasController::muokkausLisavaihtoehdoin($id);
  });
  
  $routes->get('/hallinnointi/yritysasiakkaat/:id', function($id) {
      YritysasiakasController::esittelyLisatiedoin($id);
  });
  