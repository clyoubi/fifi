<?php

require 'autoloader.php';

//API routes

$router = new Router(new Request);

// start web routes

  $router->get('/', function () {
    return view('welcome');
  });


  $router->post('/artist', function () {

    $model = new Artist();
    $model->fromJson();
    $model->save();

  });


  $router->get('/artists', function () {
    return (new Response(Artist::all() ))->sendJson();
  });

  $router->get("/artist/1", function () {
    $artist = Artist::find(2);
    $artist->id = 2;
      foreach($artist->getAlbums() as $album){
        $album->getTracks();
      }
      return (new Response($artist))->sendJson();
  });

  $router->get('/album/1', function () {
    $album = Album::find(1);
      $album->getTracks();
      return (new Response($album))->sendJson();
  });


//end web routes


///////////////////////////////////////////  


$router->get('/api/user/me', function ($params) {
  UserController::read($params);
}, ["AuthMiddleware", "index"]);

$router->post('/api/user', function ($params) {
  UserController::create($params);
});

/*
  $router->post('/api/user/profiles', function ($params) {
    ProfileController::create($params);
  }, ["AuthMiddleware", "index"]);
*/


$router->get('/api/test', function ($params) {
  
});

