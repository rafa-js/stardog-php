<?php


namespace StardogPhp\Stardog;


class StardogEndpointFactory
{

    private $baseUrl;

    public static function withServerUrl($baseUrl = 'localhost:5820')
    {
        return new StardogEndpointFactory( $baseUrl );
    }

    /**
     * @param $baseUrl
     */
    private function __construct($baseUrl)
    {
        if ( substr( $baseUrl, -1 ) == '/' ) {
            $baseUrl = substr( $baseUrl, 0, -1 );
        }
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param $db string
     * @return string
     */
    public function getBeginTransactionEndpoint($db)
    {
        return "$this->baseUrl/$db/transaction/begin";
    }

    /**
     * @param $db string
     * @param $transactionId
     * @return string
     */
    public function getCommitTransaction($db, $transactionId)
    {
        return "$this->baseUrl/$db/transaction/commit/$transactionId";
    }

    /**
     * @param $db string
     * @param $transactionId
     * @return string
     */
    public function getAddEndpoint($db, $transactionId)
    {
        return "$this->baseUrl/$db/$transactionId/add";
    }

}