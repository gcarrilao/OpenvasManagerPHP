<?php

/**
 	* @author   Guillermo Federico Carrilao Avila (https://github.com/gcarrilao)
	* @author   Juan Facundo Gregorini (https://github.com/gregojff)
 	* @license     GPL v3.0
 	*
 */

class OpenvasManager {

	private $host;
	private $port;
	private $username;
	private $password;

	/*
		Configure parameters to connect with OMP (openvas manager protocol)

	*/
	function __construct ($host,$port,$username,$password){

		$this->host=$host;
		$this->port=$port;
		$this->username=$username;
		$this->password=$password;

	}

	/*

		Get connection from OMP

	*/
	private function get_connection(){

		$context = stream_context_create(array(
				'ssl' => array(
						'verify_peer_name' => false,
						'verify_peer' => false,
						'allow_self_signed' => true
				)
		));

		// Response and Errors
		$errno = null;
		$errstr = null;

		/*
		 * Connect to OpenVAS with SSL/TLS
		 */
		$fp = stream_socket_client('ssl://' . $this->host . ':' . $this->port, $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
		if ($errno) {
				throw new Exception("sendToOpenvas: The connection to openVAS failed, because of Error: (" . $errno . ") " . $errstr);
		}

		return $fp;

	}

	private function read_stream_to_buffer($fp, $length = 8192) {
	    $response = "";
	    do {
	        $response.=$buf = fread($fp, $length);
	    } while (strlen($buf) == $length);
	    return $response;
	}

  /*
		Get autentication with OMP
	*/
	private function autenticate($cx){
		fwrite($cx,"<authenticate><credentials><username>$this->username</username><password>$this->password</password></credentials></authenticate>");
		$response = $this->read_stream_to_buffer($cx);
	  // Verificar autenticacion, informar si no se logro
		return $response;
	}

	/*
		Send command and take response
	*/

	private function getCmd($cmd){
		$cx = $this->get_connection();
		$this->autenticate($cx);
		fwrite($cx,$cmd);
		$response = $this->read_stream_to_buffer($cx);
		$response = simplexml_load_string($response);
		return $response;
	}

	/*
		get version
	*/

	function getVersion(){
		return $this->getCmd("<get_version/>");
	}

}

$ov = new OpenvasManager("localhost","9390","admin","admin");
print_r($ov->getVersion());
