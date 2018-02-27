<?php

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });

  
  
  $routes->get('/kirjautuminen', function() {
    CorporateCustomerController::login();
  });
  
  $routes->post('/kirjautuminen', function() {
    CorporateCustomerController::handle_login();
  });
  
  $routes->post('/ulos', function() {
    CorporateCustomerController::logout();
  });  
  
  

  $routes->get('/', function() {
      BeerBatchController::index();
  });
  
  $routes->get('/tilaukset/uusi/:id', function($id) {
      OrderController::index($id);
  });
  
  $routes->post('/tilaukset/uusi', function() {
      OrderController::saveNew();
  });
  
  $routes->get('/omattiedot', function() {
      CorporateCustomerController::show();
  });
  
  $routes->get('/omattiedot/muokkaa', function() {
      CorporateCustomerController::edit();
  }); 
  
  $routes->post('/omattiedot/tallenna', function() {
      CorporateCustomerController::update();
  }); 
  
  
  
  $routes->get('/hallinnointi/oluterat', function() {
      BeerBatchController::indexAdmin();
  });
  
  $routes->post('/hallinnointi/oluterat/uusi', function() {
      BeerBatchController::saveNewAdmin();
  });
  
  $routes->post('/hallinnointi/oluterat/uusipvm', function() {
      BeerBatchController::updateDateAdmin();
  });
  
  $routes->post('/hallinnointi/oluterat/poisto', function() {
      BeerBatchController::deleteAdmin();
  });
  
  $routes->get('/hallinnointi/oluterat/:id', function($id) {
      BeerBatchController::showAdmin($id);
  });
  
  $routes->get('/hallinnointi/tilaukset/uusi/:id', function($id) {
      OrderController::admin($id);
  });
  
  $routes->post('/hallinnointi/tilaukset/toimitettu', function($id) {
      OrderController::updateAsDelivered();
  });
  
  $routes->post('/hallinnointi/tilaukset/poista', function($id) {
      OrderController::delete();
  });
  
  $routes->get('/hallinnointi/pakkaustyypit', function() {
      PackageTypeController::indexAdmin();
  });
  
  $routes->post('/hallinnointi/pakkaustyypit/uusi', function() {
      PackageTypeController::saveNewAdmin();
  });
  
  $routes->post('/hallinnointi/pakkaustyypit/muutasaatavilla', function() {
      PackageTypeController::updateAvailabilityAdmin();
  });
  
  $routes->get('/hallinnointi/yritysasiakkaat', function() {
      CorporateCustomerController::indexAdmin();
  });
  
  $routes->get('/hallinnointi/yritysasiakkaat/teeuusi', function() {
      CorporateCustomerController::newAdmin();
  });
  
  $routes->post('/hallinnointi/yritysasiakkaat/uusi', function() {
      CorporateCustomerController::saveNewAdmin();
  });
  
  $routes->post('/hallinnointi/yritysasiakkaat/muokkaa', function() {
      CorporateCustomerController::updateAdmin();
  });
  
  $routes->get('/hallinnointi/yritysasiakkaat/muokkaa/:id', function($id) {
      CorporateCustomerController::editAdmin($id);
  });
  
  $routes->get('/hallinnointi/yritysasiakkaat/:id', function($id) {
      CorporateCustomerController::showAdmin($id);
  });
  