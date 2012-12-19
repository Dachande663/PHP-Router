<?php

include './autoload.php';

/**
 * Router Tests
 *
 * @package default
 * @author Luke Lanchester
 **/
class RouterTest extends PHPUnit_Framework_TestCase {


	/**
	 * @var Router Router instance
	 **/
	public $router;


	/**
	 * Setup
	 *
	 * @return void
	 * @author Luke Lanchester
	 **/
	public function setUp() {

		$this->router = new \HybridLogic\Router;

		$this->router->get('/', function(){
			return 'root';
		});

		$this->router->get('single', function(){
			return 'single';
		});

		$this->router->get('dir/sub', function(){
			return 'subdir';
		});

		$this->router->get('dir2/optional?', function(){
			return 'dir2';
		});

		$this->router->get('num/:num', function($num){
			return "num-$num";
		});

		$this->router->get('any/:any', function($any){
			return "any-$any";
		});

		$this->router->get('any2/:any?', function($any){
			return "any2-$any";
		});

		$this->router->get('multiple/:num/:num', function($one, $two){
			return "multiple-$one-$two";
		});

		$this->router->get('multiple2/:num/:num?/:num', function($one, $two, $three){
			return "multiple2-$one-$two-$three";
		});

		$this->router->get('reg/:[a-f][a-f][0-9]/:(this|that)?', function($one, $two){
			return "regex-$one-$two";
		});

		$this->router->get('catch/:all/:num', function($uri){
			return "catch-$uri";
		});

		$this->router->post('submit/:any', function($one){
			return "post-$one";
		});

		$this->router->any('contact/:any', function($one){
			return "contact-$one";
		});

		$this->router->put('add/:any', function($one){
			return "add-$one";
		});

		$this->router->delete('remove/:any', function($one){
			return "remove-$one";
		});

		$this->router->all('all/:any', function($one){
			return "all-$one";
		});

	} // end func: setUp



	/**
	 * Test Router
	 *
	 * @return void
	 * @author Luke Lanchester
	 * @dataProvider providerRequests
	 **/
	public function testRouter($uri, $method, $expected) {
		$this->assertEquals($expected, $this->router->run($uri, $method));
	} // end func: testRouter



	/**
	 * Provider
	 *
	 * @return array Requests
	 * @author Luke Lanchester
	 **/
	public function providerRequests() {

		return array(

			array('/', 'GET', 'root'),
			array('not-real', 'GET', false),
			array('single', 'GET', 'single'),

			array('dir/sub', 'GET', 'subdir'),
			array('dir/sub2', 'GET', false),

			array('dir2', 'GET', 'dir2'),
			array('dir2/optional', 'GET', 'dir2'),
			array('dir2/not-real', 'GET', false),
			array('dir2/optional/not-real', 'GET', false),

			array('num', 'GET', false),
			array('num/123', 'GET', 'num-123'),
			array('num/abc', 'GET', false),
			array('num/123/test', 'GET', false),

			array('any', 'GET', false),
			array('any/test', 'GET', 'any-test'),
			array('any/example-slug-2', 'GET', 'any-example-slug-2'),
			array('any/example/not-real', 'GET', false),

			array('any2', 'GET', 'any2-'),
			array('any2/hello-world', 'GET', 'any2-hello-world'),

			array('multiple/123/456', 'GET', 'multiple-123-456'),
			array('multiple2/123/456/789', 'GET', 'multiple2-123-456-789'),
			array('multiple2/123/456', 'GET', 'multiple2-123-456-'),
			array('multiple2/123', 'GET', 'multiple2-123--'),
			array('multiple2', 'GET', false),

			array('reg/ab3/that', 'GET', 'regex-ab3-that'),
			array('reg/ab3/fail', 'GET', false),
			array('reg/ab3', 'GET', 'regex-ab3-'),
			array('reg/12c/that', 'GET', false),

			array('catch/one/two/three', 'GET', 'catch-one/two/three'),

			array('/', 'POST', false),
			array('submit/one', 'POST', 'post-one'),

			array('contact/one', 'GET', 'contact-one'),
			array('contact/one', 'POST', 'contact-one'),
			array('contact/one', 'PUT', false),

			array('add/one', 'PUT', 'add-one'),
			array('add/one', 'GET', false),
			array('remove/one', 'DELETE', 'remove-one'),
			array('remove/one', 'GET', false),

			array('all/one', 'GET', 'all-one'),
			array('all/one', 'POST', 'all-one'),
			array('all/one', 'PUT', 'all-one'),
			array('all/one', 'DELETE', 'all-one'),

		);

	} // end func: providerRequests



} // end class: RouterTest