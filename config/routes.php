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
  
  $routes->get('/omattiedot', function() {
      CorporateCustomerController::index();
  }); 
  
  
  
  $routes->get('/hallinnointi/oluterat', function() {
      BeerBatchController::admin();
  });
  
  $routes->post('/hallinnointi/oluterat/uusi', function() {
      BeerBatchController::newBeerBatch();
  });
  
  $routes->post('/hallinnointi/oluterat/uusipvm', function() {
      BeerBatchController::updateDate();
  });
  
  $routes->post('/hallinnointi/oluterat/poisto', function() {
      BeerBatchController::delete();
  });
  
  $routes->get('/hallinnointi/oluterat/:id', function($id) {
      BeerBatchController::show($id);
  });
  
  $routes->get('/hallinnointi/tilaukset/uusi/:id', function($id) {
      OrderController::admin($id);
  });
  
  $routes->get('/hallinnointi/pakkaustyypit', function() {
      PackageTypeController::admin();
  });
  
  $routes->post('/hallinnointi/pakkaustyypit/uusi', function() {
      PackageTypeController::newPackageType();
  });
  
  $routes->post('/hallinnointi/pakkaustyypit/muutasaatavilla', function() {
      PackageTypeController::switchAvailability();
  });
  
  $routes->get('/hallinnointi/yritysasiakkaat', function() {
      CorporateCustomerController::admin();
  });
  
  $routes->get('/hallinnointi/yritysasiakkaat/teeuusi', function() {
      CorporateCustomerController::makeNew();
  });
  
  $routes->post('/hallinnointi/yritysasiakkaat/uusi', function() {
      CorporateCustomerController::saveNew();
  });
  
  $routes->post('/hallinnointi/yritysasiakkaat/muokkaa', function() {
      CorporateCustomerController::update();
  });
  
  $routes->get('/hallinnointi/yritysasiakkaat/muokkaa/:id', function($id) {
      CorporateCustomerController::edit($id);
  });
  
  $routes->get('/hallinnointi/yritysasiakkaat/:id', function($id) {
      CorporateCustomerController::show($id);
  });
  