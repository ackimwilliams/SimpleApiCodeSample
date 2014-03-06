<?php

include_once 'Api_Version1.php';
include_once 'Api_Version2.php';
include_once 'Response.php';


/**
 * Author: Ackim Williams
 * 
 * Call this script using php BeginExecution.php inputfile version#
 * 
 * Requirements: register_argc_argv is enabled
 * 
 */
class HiringApiClient
{
	
	//webservice root
	const webserviceRoot = "http://website.connect.net";
	
	//supported commands
	const COMMAND_GET = "get";
	const COMMAND_SET = "set";
	const COMMAND_DELETE = "delete";
	const COMMAND_LIST = "list";
	const COMMAND_AUTH = "auth";
	
	private $_inputFile = "";
	private $_apiVersion = 2;	//set the default api version
	
	/**
	 * Class constructor
	 * 
	 * @param string $inputFile
	 */
	public function __construct( $inputFile, $apiVersion )
	{
		$this->_inputFile = $inputFile;
		$this->_apiVersion = $apiVersion;
	}


	/**
	 * Process file
	 */
	public function processFile()
	{
		$file = @fopen( $this->_inputFile, "r" );
		
		$hndApi = null;	//initialize http client
		if ( $this->_apiVersion == 1 )
			$hndApi = new Api_Version1();
		else if ( $this->_apiVersion == 2 )
			$hndApi = new Api_Version2();

		//read entire file to completion
		while ( !feof( $file ) )
		{
			//get a line from file
			$currentLine = trim( strtolower( fgets( $file ) ) );
			
			//split across consecutive whitespace characters
			$lineParts = preg_split( "/\s+/", $currentLine );
			
			$response = null;	//initialize webservice response
			
			try 
			{
				switch( $lineParts[0] )
				{
					case self::COMMAND_GET:
							$response = $hndApi->get( $lineParts[1] );
							$this->displayOutput( self::COMMAND_GET, $response, $lineParts[1] );
						break;
					
					case self::COMMAND_SET:
							$response = $hndApi->put( $lineParts[1], $lineParts[2] );
							$this->displayOutput( self::COMMAND_SET, $response );
						break;
							
					case self::COMMAND_DELETE:
							$response = $hndApi->delete( $lineParts[1] );
							$this->displayOutput( self::COMMAND_DELETE, $response );
						break;
						
					case self::COMMAND_LIST:
							$response = $hndApi->getList();
							$this->displayOutput( self::COMMAND_LIST, $response );
						break;
						
					case self::COMMAND_AUTH:
							$response = $hndApi->authenticate( $lineParts[1], $lineParts[2] );
							$this->displayOutput( self::COMMAND_AUTH, $response );
						break;
					
					default:
						//something bad occurred, flag the record, send to record correction
				}
				
			}
			catch( Exception $ex )
			{
				//something bad occurred, flag the record, send to record correction
			}
		}
		
		fclose( $file );
		echo "\n\n\n----- complete -----\n\n";
	}

	
	/**
	 * Display output to stdout 
	 * 
	 * @param string $command
	 * @param Response $response
	 * @param string $key
	 */
	private function displayOutput( $command, Response $response, $key = "" )
	{
		if ( is_null( $response ) )	//should throw an error here
			return;
		
		//catch empty content
		//@todo: consider handling this as an error
		if ( is_null( $response->getContent() ) )
			return;
		
		if ( $response->getHttpStatusCode() == 200 )
		{
			switch( $command )
			{
				case self::COMMAND_LIST:
						foreach ( $response->getContent()->keys as $singleKey )
							echo "\n" . $singleKey;
					break;
					
				case self::COMMAND_GET:
						//GET call can return an empty key with API V1
						if ( isset( $response->getContent()->$key ) )
							echo "\n" . $response->getContent()->$key;
						else
							echo "\n";
					break;
				
				case self::COMMAND_DELETE:
				case self::COMMAND_SET:
				case self::COMMAND_AUTH:
				default:
					echo "\n" . $response->getStatus();
			}
		}
		else
		{
			if ( isset( $response->getContent()->msg ) )
			{
				echo "\nerror " . $response->getHttpStatusCode() . " " . $response->getContent()->msg;
			}
			else
			{
				//@todo: consider throwing an error
			}
		}
				
	}

}

$hndBeginExecution = new BeginExecution( $argv[1], $argv[2] );
$hndBeginExecution->processFile();	//begin program