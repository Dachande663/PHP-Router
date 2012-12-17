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

		$this->router->on_404(function(){
			return '404';
		});

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

	} // end func: setUp



	/**
	 * Test Router
	 *
	 * @return void
	 * @author Luke Lanchester
	 * @dataProvider providerRequests
	 **/
	public function testRouter($uri, $method, $result) {
		$this->assertEquals($this->router->run($uri, $method), $result);
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
			array('not-real', 'GET', '404'),
			array('single', 'GET', 'single'),

			array('dir/sub', 'GET', 'subdir'),
			array('dir/sub2', 'GET', '404'),

			array('dir2', 'GET', 'dir2'),
			array('dir2/optional', 'GET', 'dir2'),
			array('dir2/not-real', 'GET', '404'),
			array('dir2/optional/not-real', 'GET', '404'),

			array('num', 'GET', '404'),
			array('num/123', 'GET', 'num-123'),
			array('num/abc', 'GET', '404'),
			array('num/123/test', 'GET', '404'),

			array('any', 'GET', '404'),
			array('any/test', 'GET', 'any-test'),
			array('any/example-slug-2', 'GET', 'any-example-slug-2'),
			array('any/example/not-real', 'GET', '404'),

			array('any2', 'GET', 'any2-'),
			array('any2/hello-world', 'GET', 'any2-hello-world'),

			array('multiple/123/456', 'GET', 'multiple-123-456'),
			array('multiple2/123/456/789', 'GET', 'multiple2-123-456-789'),
			array('multiple2/123/456', 'GET', 'multiple2-123-456-'),
			array('multiple2/123', 'GET', 'multiple2-123--'),
			array('multiple2', 'GET', '404'),

			array('reg/ab3/that', 'GET', 'regex-ab3-that'),
			array('reg/ab3/fail', 'GET', '404'),
			array('reg/ab3', 'GET', 'regex-ab3-'),
			array('reg/12c/that', 'GET', '404'),

		);

	} // end func: providerRequests



} // end class: RouterTest