<?php

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });
  

  
  $routes->get('/', function() {
    FrontController::index();
  });
  
  $routes->get('/tilauslomake/:id', function($id) {
    OrderController::index($id);
  });
  
  
  
  $routes->get('/hallinnointi', function() {
    HelloWorldController::frontpageAdmin();
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
