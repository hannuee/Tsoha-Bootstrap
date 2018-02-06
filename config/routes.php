<?php

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });
  

  
  $routes->get('/', function() {
    FrontController::index();
  });
  
  $routes->get('/hallinnointi', function() {
    FrontController::admin();
  });
  
  $routes->post('/hallinnointi/uusiera', function() {
    FrontController::newBeerBatch();
  });
  
  $routes->get('/tilauslomake/:id', function($id) {
    OrderController::index($id);
  });

  $routes->post('/tilaukset/uusipvm', function() {
       BatchController::newDate();
  });
  
  $routes->get('/tilaukset/:id', function($id) {
       BatchController::admin($id);
  });
  
  
  
  $routes->get('/omattiedot', function() {
    HelloWorldController::customerpage();
  });
  
  $routes->get('/kayttajientiedot', function() {
    HelloWorldController::customerpageAdmin();
  });
  
  $routes->get('/tilauslomakkeet', function() {
    HelloWorldController::orderpageAdmin();
  });
  
