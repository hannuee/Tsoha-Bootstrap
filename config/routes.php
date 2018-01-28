<?php

  $routes->get('/', function() {
    HelloWorldController::index();
  });

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });
  
  
  
  $routes->get('/etusivu', function() {
    HelloWorldController::frontpage();
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
  
  $routes->get('/tilauslomake', function() {
    HelloWorldController::orderpage();
  });
  
  $routes->get('/tilauslomakkeet', function() {
    HelloWorldController::orderpageAdmin();
  });
  
  $routes->get('/tilaukset', function() {
    HelloWorldController::patchpageAdmin();
  });
