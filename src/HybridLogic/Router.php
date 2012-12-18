<?php

namespace HybridLogic;

/**
 * Request Router
 *
 * @package default
 * @author Luke Lanchester
 **/
class Router {


	/**
	 * @var array Allowed HTTP request methods
	 **/
	private $methods = array('GET', 'POST', 'PUT', 'DELETE');


	/**
	 * @var array Router options
	 **/
	private $options = array(
		'support_post_method' => true,
	);


	/**
	 * @var array Route patterns
	 **/
	private $routes = array(
		'GET'    => array(),
		'POST'   => array(),
		'PUT'    => array(),
		'DELETE' => array(),
	);


	/**
	 * Constructor
	 *
	 * @param array Options
	 * @return void
	 * @author Luke Lanchester
	 **/
	function __construct(array $options = null) {
		if($options !== null) $this->options = array_merge($this->options, $options);
	} // end func: __construct



	/**
	 * Add a GET route
	 *
	 * @param string Route pattern
	 * @param array Options
	 * @param callable Callback
	 * @return void
	 * @author Luke Lanchester
	 **/
	public function get($pattern, $options, $callback = null) {
		return static::on(array('GET'), $pattern, $options, $callback);
	} // end func: get



	/**
	 * Add a POST route
	 *
	 * @param string Route pattern
	 * @param array Options
	 * @param callable Callback
	 * @return void
	 * @author Luke Lanchester
	 **/
	public function post($pattern, $options, $callback = null) {
		return static::on(array('POST'), $pattern, $options, $callback);
	} // end func: post



	/**
	 * Add a PUT route
	 *
	 * @param string Route pattern
	 * @param array Options
	 * @param callable Callback
	 * @return void
	 * @author Luke Lanchester
	 **/
	public function put($pattern, $options, $callback = null) {
		return static::on(array('PUT'), $pattern, $options, $callback);
	} // end func: put



	/**
	 * Add a DELETE route
	 *
	 * @param string Route pattern
	 * @param array Options
	 * @param callable Callback
	 * @return void
	 * @author Luke Lanchester
	 **/
	public function delete($pattern, $options, $callback = null) {
		return static::on(array('DELETE'), $pattern, $options, $callback);
	} // end func: delete



	/**
	 * Add a route
	 *
	 * @param array HTTP Methods
	 * @param string Route pattern
	 * @param array Options
	 * @param callable Callback
	 * @return void
	 * @author Luke Lanchester
	 **/
	public function on(array $methods, $pattern, $options, $callback = null) {

		if($callback === null and !is_array($options)) {
			$callback = $options;
			$options = null;
		}

		$route = array(
			'pattern'  => $pattern,
			'callback' => $callback,
		);

		$route = is_array($options) ? array_merge($route, $options) : $route;

		foreach($methods as $method) {
			if(!in_array($method, $this->methods)) continue;
			$this->routes[$method][] = $route;
		}

		return true;

	} // end func: on



	/**
	 * Execute route that matches current request
	 *
	 * @param string Current request URI
	 * @return void
	 * @author Luke Lanchester
	 **/
	public function run($uri = null, $method = null) {

		$method = $this->get_request_method($method);
		$uri = $this->get_request_uri($uri);

		$routes = $this->routes[$method];
		if(empty($routes)) return false;

		foreach($routes as $route) {

			$match_args = $this->matches($route, $uri);
			if($match_args === false) continue;

			if(!is_callable($route['callback'])) throw new \RuntimeException('Uncallable callback provided for route: ' . $route['pattern']);
			return call_user_func_array($route['callback'], $match_args);

		}

		return false;

	} // end func: run



	/**
	 * Returns true if provided URI matches Format
	 *
	 * @param array Route
	 * @param string URI
	 * @return bool True if matches
	 * @author Luke Lanchester
	 **/
	public function matches($route, $uri) {

		if($route['pattern'] === '/'){
			return ($uri === '/') ? array() : false;
		}

		list($pattern, $capture) = $this->expand_pattern($route);

		$count = preg_match($pattern, $uri, $matches);
		if($count === 0) return false;

		$args = array();
		foreach($capture as $offset) {
			$args[] = isset($matches[$offset]) ? $matches[$offset] : null;
		}
		return $args;

	} // end func: matches



	/**
	 * Convert pattern into preg format
	 *
	 * @param array Route
	 * @return string Format
	 * @author Luke Lanchester
	 **/
	private function expand_pattern($route) {

		$input = $route['pattern'];

		$pattern = '';
		$parts = explode('/', $input);
		$optional = false;
		$bracket_count = 0;
		$capture_ints = array();

		foreach($parts as $part) {

			$catch_all = (substr($part, 0, 4) === ':all');
			$capture   = (substr($part, 0, 1) === ':');
			$optional  = ($optional or substr($part, -1) === '?');
			$capture_i = null;

			$part = strtr($part, array(
				':num' => ':[0-9]+',
				':any' => ':[a-z0-9-_]+',
				':all' => ':.*',
			));

			if($capture) {
				$capture_i = $bracket_count + 1;
				$part = '(' . substr($part, 1) . ')';
				$bracket_count++;
			}

			if($optional) {
				if($capture) $capture_i++;
				$part = "(/$part)?";
				$bracket_count++;
			} else {
				$part = "/$part";
			}

			$pattern .= $part;
			if($capture_i) $capture_ints[] = $capture_i;

			if($catch_all) break;

		}

		$pattern = ";^$pattern/?$;i";
		return array($pattern, $capture_ints);

	} // end func: expand_pattern



	/**
	 * Return current request URI
	 *
	 * @param string URI
	 * @return string URI
	 * @author Luke Lanchester
	 **/
	private function get_request_uri($uri = null) {

		if($uri === null) {
			$uri = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
		}

		$uri = trim($uri, '/');
		return "/$uri";

	} // end func: get_request_uri



	/**
	 * Return current request method
	 *
	 * @param string Method
	 * @return string Method
	 * @author Luke Lanchester
	 **/
	private function get_request_method($method = null) {

		if($method === null) {
			$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;
		}

		$method = strtoupper($method);
		if(!in_array($method, $this->methods)) $method = 'GET';

		if(
			$this->options['support_post_method']
			and $method === 'POST'
			and isset($_POST['method'])
		) {
			$method = strtoupper($_POST['method']);
			if(!in_array($method, $this->methods)) $method = 'POST';
		}

		return $method;

	} // end func: get_request_method



} // end class: Router