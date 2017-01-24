<?php

namespace StardogPhp\Request;

class Request
{

    private $method;
    private $url;
    private $headers;
    private $body;

    /**
     * @param $method
     * @param $url
     * @param $headers
     * @param $body
     */
    public function __construct($method = 'GET', $url = '', $headers = array(), $body = '')
    {
        $this->method = $method;
        $this->url = $url;
        $this->headers = $headers;
        $this->body = $body;
    }


    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param mixed $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    public function addHeader($header)
    {
        $this->headers[] = $header;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    function __toString()
    {
        return 'Request[' .
        'method=' . $this->method . ', ' .
        'url=' . $this->url . ', ' .
        'headers=' . json_encode( $this->headers ) . ', ' .
        'body=' . $this->body . ']';
    }


}