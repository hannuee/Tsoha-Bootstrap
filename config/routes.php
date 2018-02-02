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
  
  $routes->get('/tilauslomake/:id', function($id) {
    OrderController::index($id);
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
  
  $routes->get('/tilaukset', function() {
    HelloWorldController::patchpageAdmin();
  });
