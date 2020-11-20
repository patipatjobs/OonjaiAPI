<?php

/*
|
| return $router->app->version();
|
*/

Route::get('/privacy', function () 
{

  return view('privacy');

});

$router->group(['prefix' => 'api'], function () use ($router) 
{

  $router->group(['prefix' => '/v3'], function () use ($router) 
  {

    $router->group(['prefix' => '/read'],  function () use ($router)
    {

      $router->get('',  ['uses' => 'ReadController@read']);
      $router->get('/home',  ['uses' => 'ReadController@home']);
      $router->get('/cars',  ['uses' => 'ReadController@cars']);
      $router->get('/student',  ['uses' => 'ReadController@student']);
      $router->get('/history',  ['uses' => 'ReadController@history']);
      $router->get('/details',  ['uses' => 'ReadController@details']);

    });
    
  });

  $router->group(['prefix' => 'v2'], function () use ($router) 
  { 

    $router->group(['prefix' => '/create'],  function () use ($router)
    {

      $router->post('',  ['uses' => 'CreateController@create']);
      $router->post('/student',  ['uses' => 'CreateController@student']);
      $router->post('/scanIn',  ['uses' => 'CreateController@scanIn']);

    });

    $router->group(['prefix' => '/read'],  function () use ($router)
    {

      $router->get('',  ['uses' => 'ReadController@read']);
      $router->get('/home',  ['uses' => 'ReadController@home']);
      $router->get('/cars',  ['uses' => 'ReadController@cars']);
      $router->get('/student',  ['uses' => 'ReadController@student']);
      $router->get('/history',  ['uses' => 'ReadController@history']);
      $router->get('/details',  ['uses' => 'ReadController@details']);

    });

    $router->group(['prefix' => '/update'],  function () use ($router)
    {

      $router->put('',  ['uses' => 'UpdateController@update']);
      $router->put('/scanOut',  ['uses' => 'UpdateController@scanOut']);
      $router->put('/isReset',  ['uses' => 'UpdateController@isReset']);

    });

    $router->delete('/delete',  ['uses' => 'DeleteController@delete']);     
    
  });

});