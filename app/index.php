<?php

require 'autoloader.php';

$router = new Router(new Request);


$router->get('/', function () {
  return view('home');
});

$router->get('/user/:id', function($params) {
  return "User ID: " . $params['id'];
});
