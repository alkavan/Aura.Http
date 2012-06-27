<?php
/**
 * 
 * This file is part of the Aura Project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Http\Cookie;

use Aura\Http\Cookie\Factory as CookieFactory;
use Aura\Http\Cookie;

/**
 * 
 * Collection of Cookie objects.
 * 
 * @package Aura.Http
 * 
 */
class Collection implements \IteratorAggregate, \Countable
{
    /**
     * 
     * The list of all cookies.
     * 
     * @var array
     * 
     */
    protected $list = [];

    /**
     * 
     * @var Aura\Http\Cookie\Factory
     * 
     */
    protected $factory;

    /**
     *
     * @param Aura\Http\Cookie\Factory $factory
     *
     */
    public function __construct(CookieFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * 
     * Get a cookie.
     * 
     * @param string $key 
     * 
     * @return array
     * 
     */
    public function __get($key)
    {
        return $this->list[$key];
    }
    
    /**
     * 
     * Does a cookie exist.
     * 
     * @param string $key 
     * 
     * @return boolean
     * 
     */
    public function __isset($key)
    {
        return isset($this->list[$key]);
    }
    
    /**
     * 
     * Unset a cookie.
     * 
     * @param string $key 
     * 
     * @return void
     * 
     */
    public function __unset($key)
    {
        unset($this->list[$key]);
    }
    
    /**
     * 
     * Returns the cookie collection as a string of `name=value` pairs.
     * 
     * @return string
     * 
     */
    public function __toString()
    {
        $list = array();
        foreach ($this->list as $cookie) {
            $list[] = $cookie->__toString();
        }
        return implode(';', $list);
    }
    
    /**
     * 
     * Count the number of cookies.
     * 
     * @return integer
     * 
     */
    public function count()
    {
        return count($this->list);
    }
    
    /** 
     * 
     * Gets all cookies as an iterator.
     * 
     * @return array
     * 
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->list);
    }
    
    /**
     * 
     * Sets a single cookie by name.
     * 
     * @param string $name The cookie name.
     * 
     * @param array $info The cookie info.
     * 
     */
    public function set($name, array $info = [])
    {
        if ($name instanceof Cookie) {
            $cookie = $name;
        } else {
            $cookie = $this->factory->newInstance($name, $info);
        }

        $this->list[$cookie->getName()] = $cookie;
    }
    
    public function setFromJar(CookieJar $jar, $url)
    {
        $cookies = $jar->listAll($url);
        foreach ($cookies as $cookie) {
            $this->set($cookie);
        }
    }
    
    /**
     * 
     * Parses the value of the "Set-Cookie" header and sets it.
     * 
     * @param string $text The Set-Cookie text string value.
     * 
     * @param string $uri The URL to use when setting the secure,
     * host and path property defaults.
     * 
     * @return void
     * 
     */
    public function setFromString($str, $uri = null)
    {
        $cookie = $this->factory->newInstance();
        $cookie->setFromString($str, $uri);
        $this->list[$cookie->getName()] = $cookie;
    }
    
    /** 
     * 
     * Gets all cookies.
     * 
     * @return array
     * 
     */
    public function getAll()
    {
        return $this->list;
    }
    
    /**
     * 
     * Sets all cookies at once removing all previous cookies.
     * 
     * @param array $cookies The array of all cookies where the key is the
     * name and the value is the array of cookie info.
     * 
     * @return void
     * 
     */
    public function setAll(array $cookies = [])
    {
        $this->list = [];
        foreach ($cookies as $name => $info) {
            $this->set($name, $info);
        }
    }
}
