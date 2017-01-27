<?php


namespace StardogPhp\Stardog;


use StardogPhp\Request\CurlRequestPerformer;
use StardogPhp\Request\Request;
use StardogPhp\Sparql\QueryBuilder;

class Stardog
{

    private $token;

    /**
     * @var CurlRequestPerformer
     */
    private $requestPerformer;

    /**
     * @var StardogEndpointFactory
     */
    private $endpointFactory;

    /**
     * @param $url
     * @param $user
     * @param $password
     */
    public function __construct($url, $user, $password)
    {
        $this->token = base64_encode( $user . ':' . $password );
        $this->requestPerformer = new CurlRequestPerformer( $this->token );
        $this->endpointFactory = StardogEndpointFactory::withServerUrl( $url );
    }

    /**
     * @param $db
     * @return TransactionFluent
     * @throws \Exception
     */
    public function beginTransaction($db)
    {
        $url = $this->endpointFactory->getBeginTransactionEndpoint( $db );
        $request = new Request( 'POST', $url );
        $response = $this->requestPerformer->performRequest( $request );
        if ( !$response->isSuccess() ) {
            throw new \Exception( 'Exception in begin transaction: ' . $response );
        }
        $transactionId = $response->getContent();
        return new TransactionFluent( $transactionId, $db, $this->requestPerformer, $this->endpointFactory );
    }

    /**
     * @param $db
     * @param $update QueryBuilder
     * @return TransactionFluent
     * @throws \Exception
     */
    public function update($db, $update)
    {
        $url = $this->endpointFactory->getUpdateEndpoint( $db );
        $request = new Request( 'POST', $url );
        $request->addHeader( 'Content-Type: application/sparql-update' );
        $request->setBody( $update->buildSparqlUpdate() );
        $response = $this->requestPerformer->performRequest( $request );
        if ( !$response->isSuccess() ) {
            throw new \Exception( 'Exception in update transaction: ' . $response );
        }
    }


}