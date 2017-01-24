<?php


namespace StardogPhp\Stardog;


use StardogPhp\Request\CurlRequestPerformer;
use StardogPhp\Request\IRequestPerformer;
use StardogPhp\Request\Request;

class TransactionFluent
{

    /**
     * @var IRequestPerformer
     */
    private $requestPerformer;

    /**
     * @var StardogEndpointFactory
     */
    private $endpointFactory;


    private $transactionId;
    private $database;


    /**
     * @param $transactionId string
     * @param $database
     * @param $requestPerformer IRequestPerformer
     * @param $endpointFactory StardogEndpointFactory
     */
    public function __construct($transactionId, $database, $requestPerformer, $endpointFactory)
    {
        $this->transactionId = $transactionId;
        $this->database = $database;
        $this->endpointFactory = $endpointFactory;
        $this->requestPerformer = $requestPerformer;
    }


    public function add($content)
    {
        $url = $this->endpointFactory->getAddEndpoint( $this->database, $this->transactionId );
        $request = new Request( 'POST', $url, array('Content-Type: text/turtle'), $content );
        $response = $this->requestPerformer->performRequest( $request );
        if ( !$response->isSuccess() ) {
            throw new \Exception( 'Exception adding in transaction: ' . $response );
        }
        return $this;
    }

    public function query()
    {
        return $this;
    }

    public function delete()
    {
        return $this;
    }

    public function clearDatabase()
    {
        return $this;
    }

    public function commitTransaction()
    {
        $url = $this->endpointFactory->getCommitTransaction( $this->database, $this->transactionId );
        $request = new Request( 'POST', $url );
        $response = $this->requestPerformer->performRequest( $request );
        if ( !$response->isSuccess() ) {
            throw new \Exception( 'Exception in commit transaction: ' . $response );
        }
        return $this;
    }

    public function rollbackTransaction()
    {
        return $this;
    }

}