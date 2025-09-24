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
        $routes->view('example-page', 'example-page');
    });

   // Authenticated Admin
    $routes->group('',[], static function($routes) {
        $routes->view('example-auth', 'example-auth');
    });

});