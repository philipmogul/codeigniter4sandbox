<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->setAutoRoute(true);


// Add routes for admins 
$routes->group('admin', static function($routes) {
       
   // Non-Authenticated Admin 
    $routes->group('',[], static function($routes) {
        //$routes->view('example-page', 'example-page');
        $routes->get('home','AdminController::index', ['as'=> 'admin.home']);  
        $routes->get('logout','AdminController::logoutHandler', ['as'=> 'admin.logout']);

    });

   // Authenticated Admin
    $routes->group('',[], static function($routes) {
        //$routes->view('example-auth', 'example-auth');
        $routes->get('login','AuthController::loginForm', ['as'=> 'admin.login.form']);
        // Add posts handler for authentication purposes 
        $routes->post('login','AuthController::loginHandler', ['as'=> 'admin.login.handler']);

    });

});