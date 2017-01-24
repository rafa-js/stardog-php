<?php

namespace StardogPhp\Request;

class Response
{

    private $statusCode;
    private $content;
    private $headers;
    private $errorMessage;

    /**
     * @param $statusCode
     * @param $headers
     * @param $errorMessage
     * @param $content
     */
    public function __construct($statusCode = 200, $content = '', $headers = array(), $errorMessage = '')
    {
        $this->statusCode = $statusCode;
        $this->content = $content;
        $this->headers = $headers;
        $this->errorMessage = $errorMessage;
    }


    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
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

    /**
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param mixed $errorMessage
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    public function isSuccess()
    {
        return $this->statusCode / 10 === 20;
    }


    function __toString()
    {
        return 'Response[' .
        'statusCode=' . $this->statusCode . ', ' .
        'content=' . $this->content . ', ' .
        'headers=' . json_encode( $this->headers ) . ', ' .
        'errorMessage=' . $this->errorMessage .
        ']';
    }


}