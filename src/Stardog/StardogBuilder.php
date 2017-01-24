<?php


namespace StardogPhp\Stardog;


use Exception;

class StardogBuilder
{

    private $server;
    private $port;
    private $user;
    private $password;


    public static function withCredentials($user, $password)
    {
        return new StardogBuilder( '', '', $user, $password );
    }

    public static function withServer($server, $port)
    {
        return new StardogBuilder( $server, $port, '', '' );
    }


    /**
     * @param $server
     * @param $port
     * @param $user
     * @param $password
     */
    public function __construct($server, $port, $user, $password)
    {
        $this->server = $server;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @param mixed $server
     * @param $port
     * @return StardogBuilder
     */
    public function setServer($server, $port)
    {
        $this->server = $server;
        $this->port = $port;
        return $this;
    }

    /**
     * @param mixed $user
     * @param $password
     * @return StardogBuilder
     */
    public function setCredentials($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
        return $this;
    }


    public function build()
    {
        $this->checkParameters();
        $url = $this->server . (empty($this->port) ? '' : ':' . $this->port);
        return new Stardog( $url, $this->user, $this->password );
    }

    private function checkParameters()
    {
        if ( empty($this->server) ) {
            throw new Exception( 'Invalid parameter: server' );
        }
        if ( empty($this->port) ) {
            throw new Exception( 'Invalid parameter: port' );
        }
        if ( empty($this->user) ) {
            throw new Exception( 'Invalid parameter: user' );
        }
        if ( empty($this->password) ) {
            throw new Exception( 'Invalid parameter: password' );
        }
    }

}