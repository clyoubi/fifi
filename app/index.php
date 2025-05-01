<?php

require 'autoloader.php';

$request = new Request;
$router = new Router($request);


$router->get('/', function () {
  return view('home');
});

$router->get('/users', function($params) {
  try{
    $users = User::findAll();
    return Response::send($users);
  } catch(Exception $e) {
    return Response::send([], 500, $e->getMessage());
  }
});

$router->get('/users/:id', function($params) {
  try{
    $id = $params['id'];
    $user = User::find($id);
    return Response::send($user);
  } catch(Exception $e) {
    return Response::send([], 500, $e->getMessage());
  }
}, ['AuthMiddleware', 'index']);

