<?php


namespace StardogPhp\Request;


class CurlRequestPerformer implements IRequestPerformer
{

    static $DEBUG = true;

    private $accessToken;

    /**
     * @param $accessToken
     */
    public function __construct($accessToken = '')
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function performRequest(Request $request)
    {
        $headers = $request->getHeaders();
        if ( !empty($this->accessToken) and !array_key_exists( 'Authorization', $request->getHeaders() ) ) {
            $headers[] = 'Authorization: Basic ' . $this->accessToken;
        }
        if ( static::$DEBUG ) {
            $this->logRequest( $request );
        }
        $ch = curl_init( $request->getUrl() );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $request->getMethod() );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $request->getBody() );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_HEADER, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

        $result = curl_exec( $ch );
        $statusCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        $header_size = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );
        $header = substr( $result, 0, $header_size );
        $headers = $this->http_parse_headers( $header );
        $body = substr( $result, $header_size );
        $response = new Response( $statusCode, $body, $headers );
        if ( static::$DEBUG ) {
            $this->logResponse( $response );
        }
        if ( curl_errno( $ch ) ) {
            $response->setErrorMessage( curl_error( $ch ) );
        }
        curl_close( $ch );
        return $response;
    }

    private function logRequest(Request $request)
    {
        echo '-> Sending request ' . $request . PHP_EOL;
    }

    private function logResponse(Response $response)
    {
        echo '<- Receive response ' . $response . PHP_EOL;
    }

    private function http_parse_headers($header)
    {
        $headers = preg_replace( '/^\r\n/m', '', $header );
        $headers = preg_replace( '/\r\n\s+/m', ' ', $headers );
        preg_match_all( '/^([^: ]+):\s(.+?(?:\r\n\s(?:.+?))*)?\r\n/m', $headers . "\r\n", $matches );

        $result = array();
        foreach ( $matches[ 1 ] as $key => $value )
            $result[ $value ] = (array_key_exists( $value, $result ) ? $result[ $value ] . "\n" : '') . $matches[ 2 ][ $key ];

        return $result;
    }

}