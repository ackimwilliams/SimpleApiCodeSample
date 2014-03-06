<?php 


/**
 * Http response object
 * 
 * @author AckimWilliams
 * @see ./problem_description
 */
class Response
{
	
	private $_content = null;
	private $_response = null;
	
	/**
	 * 
	 * @param object $content
	 * @param array $response
	 */
	public function __construct( $content, $response )
	{
		$this->_content = $content;
		$this->_response = $response;
	}
	
	
	/**
	 * Get content
	 * @return object
	 */
	public function getContent()
	{
		return $this->_content;
	}
	
	/**
	 * Get http response
	 * @return array
	 */
	public function getResponse()
	{
		return $this->_response;
	}
	
	/**
	 * Get status message
	 * @return string
	 */
	public function getStatus()
	{
		return $this->_content->status;
	}
	

	/**
	 * Get http status
	 * @return number
	 */
	public function getHttpStatusCode()
	{
		return intval( $this->_response[ 'http_code' ] );
	}
	
}