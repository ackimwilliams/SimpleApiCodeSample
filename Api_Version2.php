<?php

/**
 * Api version 2
 * 
 * @author AckimWilliams
 * @see ./problem_description
 */
class Api_Version2 extends AbstractWebservice
{
	
	protected $_api_version = "v2";
	protected $_attemptToCorrectStatuses = false;
	
	
	/**
	 * (non-PHPdoc)
	 * @see \Webservice\AbstractWebservice::authenticate()
	 */
	public function authenticate( $username, $password )
	{
		$this->_attemptToCorrectStatuses = false;	//disable correcting statuses
			
		$requestUri = $this->_webserviceRoot . "/" . $this->_api_version . "/auth?user=" .
				$username . "&pass=" . $password;
	
		$response = $this->execute( $requestUri, AbstractWebservice::API_METHOD_GET );
		$content = $response->getContent();
			
		$this->_attemptToCorrectStatuses = true;	//restore correcting statuses
			
		if ( $content->status == self::RESPONSE_OK )
			$this->_requestToken = $content->token;
		
		return $response;
	}
	
	
	/**
	 * Has this object authenticated with remote server?
	 *
	 * @return boolean
	 */
	public function isAuthenticated()
	{
		if ( $this->_requestToken == "" )
			return false;
		else
			return true;
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
		$requestUri = $this->_webserviceRoot . "/" . $this->_api_version .
			"/key?token=" . $this->_requestToken .
			"&key=" . $key;
			
		return $this->execute( $requestUri, AbstractWebservice::API_METHOD_DELETE );
	}
	
	/**
	 * List records
	 *
	 * @throws \Exception
	 * @return Response
	 */
	public function getList()
	{
		$requestUri = $this->_webserviceRoot . "/" . $this->_api_version .
			"/list?token=" . $this->_requestToken;
	
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
		$requestUri = $this->_webserviceRoot . "/" . $this->_api_version .
			"/key?token=" . $this->_requestToken .
			"&key=" . $key;
			
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
		$requestUri = $this->_webserviceRoot . "/" . $this->_api_version .
			"/key?token=" . $this->_requestToken .
			"&key=" . $key .
			"&value=" . $value;
	
		return $this->execute( $requestUri, AbstractWebservice::API_METHOD_PUT );
			
	}
	
}
