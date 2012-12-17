<?php

include './autoload.php';

$router = new \HybridLogic\Router;


$router->on_404(function(){
	echo '404: Page Not Found';
});


$router->get('/', function(){
	echo 'homepage';
});

$router->get('about/us', function(){
	echo 'About Us page';
});

$router->get('subpage/index?', function(){
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

$router->get(':all', function(){ // Overrides 404
	echo "Catch all hit";
	print_r(func_get_args());
});


$router->run();
