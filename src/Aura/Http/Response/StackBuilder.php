<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Http\Response;

use Aura\Http\Message\Factory as MessageFactory;

/**
 * 
 * Builds a response stack from headers and content.
 * 
 * @package Aura.Http
 * 
 */
class StackBuilder
{
    public function __construct(MessageFactory $message_factory)
    {
        $this->message_factory = $message_factory;
    }
    
    /**
     * 
     * Creates and returns a new Stack object with responses in it.
     * 
     * @return \Aura\Http\Response\Stack
     * 
     */
    public function newInstance(array $headers, $content = null)
    {
        // a response stack
        $stack = new Stack;
        
        // have a new response available regardless
        $response = $this->message_factory->newInstance('response');
        
        // add headers
        foreach ($headers as $header) {
            
            // split on the first colon
            $pos = strpos($header, ':');
            $is_http = strtoupper(substr($header, 0, 5)) == 'HTTP/';
            
            // look for an HTTP header to start a new response
            if ($pos === false && $is_http) {
                
                // start a new response and add it to the stack
                $response = $this->message_factory->newInstance('response');
                $stack->push($response);
                
                // set the version, status code, and status text in the response
                preg_match('/HTTP\/(.+?) ([0-9]+)(.*)/i', $header, $matches);
                $response->setVersion($matches[1]);
                $response->setStatusCode($matches[2]);
                $response->setStatusText($matches[3]);
                
                // go to the next header line
                continue;
            }
            
            // the header label is before the colon
            $label = substr($header, 0, $pos);
            
            // the header value is the part after the colon,
            // less any leading spaces.
            $value = ltrim(substr($header, $pos+1));
            
            // is this a set-cookie header?
            if (strtolower($label) == 'set-cookie') {
                // add the cookie
                $cookie = $response->cookies->addFromString($value, $default_uri);
            } else {
                // add the header
                $response->headers->add($label, $value, false);
            }
        }
        
        // set the content on the current (last) response in the stack
        $response->setContent($content);
        
        // done!
        return $stack;
    }
}
