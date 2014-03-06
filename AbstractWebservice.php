<?php

include_once 'Response.php';

/**
 * Abstract webservice 
 * 
 * --------------------------- ASSUMPTIONS
 * did not use separate objects for handling the different webservice calls
 * as the methods will be executed in succession.  It is therefore more 
 * efficient to reuse the object.
 * 
 * I am assuming that the user credentials can change at any time according 
 * to the input file
 * 
 * Within the 5 minute window, it is faster to reused the request token for 
 * multiple calls instead of requesting a new one each time.
 * 
 * We are not using a http request library such as \Zend\Http\Client of a curl 
 * library
 * 
 * API V2 returns message for GET method in the format: {"status": "ok", "mykey": "myvalue"}
 * 
 * Processing a single large file may result in a token expiring before the file
 * processing is complete. I disabled this feature as not sure this is important 
 * for this exercise :)
 * 
 * @author AckimWilliams
 * @see ./problem_description
 */
abstract class AbstractWebservice
{
	
	const API_METHOD_GET = "GET";
	const API_METHOD_DELETE = "DELETE";
	const API_METHOD_PUT = "PUT";
	const API_METHOD_LIST = "GET";
	
	const RESPONSE_OK = "ok";
	const RESPONSE_FAIL = "fail";
	
	
	protected $_webserviceRoot = "http://website.connect.net";
// 	protected $_credentials = array( "username" => "", "password" => "" );
	protected $_requestToken = "";
	
// 	private $_maxNumberRetryAttempts = 3;
	
	private $_headers = array
		(
			'Accept: application/json',
			'Content-Type: application/json',
		);
	
// 	private $_attemptToCorrectStatuses = true;
	private $_curlHandle = null;
	
	
	public function __construct()
	{
		//setup curl handle
		$this->_curlHandle = curl_init();
		
		if ( !$this->_curlHandle )
			throw new \Exception( "Could not initialize a curl handle." );
		
		curl_setopt( $this->_curlHandle, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $this->_curlHandle, CURLOPT_RETURNTRANSFER, true );
	}
	
	
	public function __destruct()
	{
		curl_close ( $this->_curlHandle );
		//save credentials or perform cleanup
	}
	
	/**
	 * Set the webservice root uri
	 * 
	 * @param string $serviceRoot
	 */
	public function setWebserviceRoot( $serviceRoot )
	{
		$this->_webserviceRoot = $serviceRoot;
	}
	
	
	/**
	 * Execute http request
	 * 
	 * @param string $url
	 * @param string $requestMethod
	 * @param array $data
	 * @throws \Exception
	 * @return Response http response object
	 */
	protected function execute( $url, $requestMethod, $data = array(), $headers = array() )
	{
		
		//prepare headers
		$curlHeaders = $this->_headers;
		if ( !empty( $curlHeaders ) )
			$curlHeaders = $headers;
		
		//setup curl options
		curl_setopt( $this->_curlHandle, CURLOPT_URL, $url );
		curl_setopt( $this->_curlHandle, CURLOPT_CUSTOMREQUEST, $requestMethod );
		curl_setopt( $this->_curlHandle, CURLOPT_HTTPHEADER, $curlHeaders);
		
		$curlPostData = $data;
		curl_setopt( $this->_curlHandle, CURLOPT_POSTFIELDS, $curlPostData );
		
		
		// proceed with executing curl
		$curlResponse = null;
		$content = "";
		
		$curlResponse = curl_exec( $this->_curlHandle );	//execute curl request
		$content = curl_exec( $this->_curlHandle );
		$curlResponse = curl_getinfo( $this->_curlHandle );
		
		$content = json_decode( $content );
		$returnResponse = new Response( $content, $curlResponse );
		
		return $returnResponse;
	}
	
	
// 	/**
// 	 * Attempt to handle various common http request issues
// 	 *
// 	 * @param array $response Curl response
// 	 * @return boolean
// 	 */
// 	private function retryRequest( $response )
// 	{
// 		if ( $response == null || empty( $response['http_code'] ) )
// 		{
// 			//if nothing is returned of call attempt fails, retry
// 			return true;
// 		}
	
// 		switch( intval( $response['http_code'] ) )
// 		{
// 			//reauthenticate if token has expired,
// 			//not expected for api version 1
// 			case 401:
// 				//assuming that the credentials were valid before,
// 				//there is no reason to assume that it will become
// 				//invalid within an instance of this object.
// 				try
// 				{
// 					$this->authenticate();
// 				}
// 				catch( \Exception $ex )
// 				{
// 					return false;	//unable to reauthenticate
// 				}
// 				return true;
// 				break;
	
// 				//@todo: can add additional callbacks, fixes for
// 				//other status code, but not important for this exercise
// 				//or throw an exception
				
// 			case 200:
// 			case 404:
// 				return false;
// 				break;
	
// 		}
	
// 		//catch 503
// 		return true;
// 	}
	
	

	/**
	 * Authenticate user
	 * 
	 * @param unknown $username
	 * @param unknown $password
	 * 
	 * @throws \Exception
	 */
	abstract public function authenticate( $username, $password ); 
	
}

