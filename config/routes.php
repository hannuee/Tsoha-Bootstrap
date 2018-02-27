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
      OluteraController::index();
  });
  
  $routes->get('/tilaukset/uusi/:id', function($id) {
      TilausController::index($id);
  });
  
  $routes->post('/tilaukset/uusi', function() {
      TilausController::saveNew();
  });
  
  $routes->get('/omattiedot', function() {
      YritysasiakasController::show();
  });
  
  $routes->get('/omattiedot/muokkaa', function() {
      YritysasiakasController::edit();
  }); 
  
  $routes->post('/omattiedot/tallenna', function() {
      YritysasiakasController::update();
  }); 
  
  
  
  $routes->get('/hallinnointi/oluterat', function() {
      OluteraController::indexAdmin();
  });
  
  $routes->post('/hallinnointi/oluterat/uusi', function() {
      OluteraController::saveNewAdmin();
  });
  
  $routes->post('/hallinnointi/oluterat/uusipvm', function() {
      OluteraController::updateDateAdmin();
  });
  
  $routes->post('/hallinnointi/oluterat/poisto', function() {
      OluteraController::deleteAdmin();
  });
  
  $routes->get('/hallinnointi/oluterat/:id', function($id) {
      OluteraController::showAdmin($id);
  });
  
  $routes->get('/hallinnointi/tilaukset/uusi/:id', function($id) {
      TilausController::admin($id);
  });
  
  $routes->post('/hallinnointi/tilaukset/toimitettu', function() {
      TilausController::updateAsDelivered();
  });
  
  $routes->post('/hallinnointi/tilaukset/poista', function() {
      TilausController::delete();
  });
  
  $routes->get('/hallinnointi/pakkaustyypit', function() {
      PakkaustyyppiController::indexAdmin();
  });
  
  $routes->post('/hallinnointi/pakkaustyypit/uusi', function() {
      PakkaustyyppiController::saveNewAdmin();
  });
  
  $routes->post('/hallinnointi/pakkaustyypit/muutasaatavilla', function() {
      PakkaustyyppiController::updateAvailabilityAdmin();
  });
  
  $routes->get('/hallinnointi/yritysasiakkaat', function() {
      YritysasiakasController::indexAdmin();
  });
  
  $routes->get('/hallinnointi/yritysasiakkaat/teeuusi', function() {
      YritysasiakasController::newAdmin();
  });
  
  $routes->post('/hallinnointi/yritysasiakkaat/uusi', function() {
      YritysasiakasController::saveNewAdmin();
  });
  
  $routes->post('/hallinnointi/yritysasiakkaat/muokkaa', function() {
      YritysasiakasController::updateAdmin();
  });
  
  $routes->get('/hallinnointi/yritysasiakkaat/muokkaa/:id', function($id) {
      YritysasiakasController::editAdmin($id);
  });
  
  $routes->get('/hallinnointi/yritysasiakkaat/:id', function($id) {
      YritysasiakasController::showAdmin($id);
  });
  