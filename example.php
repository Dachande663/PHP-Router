<?php

include './autoload.php';

$router = new \HybridLogic\Router;


$router->get('/', function(){
	echo 'homepage';
});

$router->get('subpage/us?', function(){
	echo 'Optional uri segment';
});

$router->get('view/:num', function($id){
	echo "View #$id";
});

$router->get('users/:any', function($username){
	echo "User: $username";
});

$router->get('blog/:num/:num/:any', function($year, $month, $slug){
	echo "View blogpost from $year-$month with slug $slug";
});

$router->get('regex/:[a-f][a-f][0-9]/:(foo|bar)?', function($hex, $opt){
	echo "HEX $hex and Opt $opt";
});

$router->post('submit', function(){
	echo 'On POST to /submit';
});

$router->any('contact', function(){
	echo 'On GET or POST to /contact';
});

$router->get(':all', function($uri){
	echo "Catch all: $uri";
});


$router->run();
