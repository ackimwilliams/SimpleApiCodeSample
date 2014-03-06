<?php


include_once 'AbstractWebservice.php';


/**
 * Api version 1
 * 
 * @author AckimWilliams
 * @see ./problem_description
 */
class Api_Version1 extends AbstractWebservice
{
	
	protected $_api_version = "v1";
	
	/**
	 * (non-PHPdoc)
	 * @see \Webservice\AbstractWebservice::authenticate()
	 */
	public function authenticate( $username, $password )
	{
		throw new \Exception( "Method not supported." );	
	}
	
	/**
	 * Delete records
	 *
	 * @param string $key
	 * @throws \Exception
	 * @return Response
	 */
	public function delete( $key )
	{
		$requestUri = $this->_webserviceRoot . "/" . $this->_api_version . "/key?" .
		"key=" . $key;
			
		return $this->execute( $requestUri, AbstractWebservice::API_METHOD_DELETE );
	}
	
	/**
	 * List records
	 * 
	 * GET /v1/list
	 * HTTP/1.1 200 OK
	 * {"status": "ok", "keys": ["mykey"]}
	 *
	 * @throws \Exception
	 * @return Response
	 */
	public function getList()
	{
		$requestUri = $this->_webserviceRoot . "/" . $this->_api_version . "/list";
	
		return $this->execute( $requestUri, AbstractWebservice::API_METHOD_GET );
	}
	
	/**
	 * Get record
	 *
	 * @param string $key
	 * @throws \Exception
	 * @return Response
	 */
	public function get( $key )
	{
		$requestUri = $this->_webserviceRoot . "/" . $this->_api_version . "/key?" .
		"key=" . $key;
			
		return $this->execute( $requestUri, AbstractWebservice::API_METHOD_GET );
	}
	
	/**
	 * Update record
	 *
	 * This method only allows you to set a single value of
	 * a single key
	 *
	 * @param string $key
	 * @param string $value
	 * @throws \Exception
	 * @return Response
	 */
	public function put( $key, $value )
	{
		$requestUri = $this->_webserviceRoot . "/" . $this->_api_version . "/key?" .
		"key=" . $key .
		"&value=" . $value;
	
		return $this->execute( $requestUri, AbstractWebservice::API_METHOD_PUT );
			
	}
	
}
