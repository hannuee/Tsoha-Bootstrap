<?php

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
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
  
  $routes->get('/hallinnointi/oluterat/:id', function($id) {
      BeerBatchController::show($id);
  });
  
  $routes->get('/hallinnointi/tilaukset/uusi/:id', function($id) {
      OrderController::admin($id);
  });
  
  $routes->get('/hallinnointi/pakkaustyypit', function() {
      PackageTypeController::admin();
  });
  
  $routes->get('/hallinnointi/yritysasiakkaat', function() {
      CorporateCustomerController::admin();
  });
  