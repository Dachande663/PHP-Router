Router
====================

A drop dead simple URI routing library for PHP, with
support for optional parameters and regular expressions.

[![Build Status](https://travis-ci.org/Dachande663/PHP-Router.png)](https://travis-ci.org/Dachande663/PHP-Router)


0.0 Table of Contents
---------------------

* Introduction
* Examples
* Format
* Running Tests
* Troubleshooting
* Changelog


1.0 Introduction
----------------

Router is a very simple URI request routing library,
designed for providing a simple front controller for
applications. A request is run against a dictionary of
routes, and the first one to match is executed.

It supports expansions for common expressions (numeric,
slugs etc), along with full regexes and optional parameter
support.

Can be used in front of a full MVC stack, or with simple
in-place closures.


2.0 Examples
------------

```php
$router = new \HybridLogic\Router;

$router->get('about', function(){
	echo 'About Us';
});

$router->run();
```


3.0 Format
----------

Each route you wish to match should have a rule. The rule
shouldn't include a beginning or ending slash, except for
the homepage, which is just /.

When defining a route, you can specify which HTTP methods
it should respond to (GET, POST etc). For example, to only
allow GET requests, use ->get(...). A utility method for
quickly adding GET and POST is provided via ->any(...).

A route pattern matches a request exactly, that means sub-
directories are not included, for example "about" would
match /about, but not /about-us or /about/john-smith.

There are three default regex expressions provided:

  * **:num** Matches any numeric value ([0-9]+)
  * **:any** Matches alphanumeric values, including - and _ ([a-z0-9-_]+)
  * **:all** Matches anything, including slashes, will override anything after it

You can also provide your own regex, e.g.

    /about/:[a-z][a-z0-9]+

To make a section optional, simply put a question mark (?)
at the end, e.g.

    /about/:any?


Example patterns:

Pattern            | Matches           | Description
-------------------|-------------------|--------------------------------
/                  | /                 | Homepage
about              | /about            | Matches optional trailing slash
about/:any?        | /about/john       | Match optional directories
archive/:num/:num? | /archive/2012/06  | Match archive pattern
:all               | /matches/anything | Catch-all handler (404s)


4.0 Running Tests
-----------------

phpunit tests


5.0 Troubleshooting
-------------------

Nothing here yet.


6.0 Changelog
-------------

* **[2012-12-17]** Initial Version
* **[2012-12-18]** Removed 404 handler
* **[2012-12-19]** Add any() and all() methods.
