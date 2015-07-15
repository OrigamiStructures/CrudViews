<?php
use Cake\Routing\Router;

Router::plugin('CrudViews', function ($routes) {
    $routes->fallbacks('InflectedRoute');
});
