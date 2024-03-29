<?php

/**
 	* @author   Guillermo Federico Carrilao Avila (https://github.com/gcarrilao)
	* @author   Juan Facundo Gregorini (https://github.com/FacundoGregorini)
 	* @license     GPL v3.0
 	*
 */

namespace Openvas;
use \Exception;

class OpenvasManager {
	private $host;
	private $port;
	private $username;
	private $password;

	/*
		Configure parameters to connect with OMP (openvas manager protocol)

	*/
	function __construct ($host=null,$port=null,$username=null,$password=null){


		if ((!isset($host)) or (!isset($port)) or (!isset($username) or !(isset($password)))){
				throw new Exception("Please verify that you have entered the following fields: host, port, username and password. They can't be NULL");
		}
		else {
			$this->host=$host;
			$this->port=$port;
			$this->username=$username;
			$this->password=$password;
		}
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
		if ($errno){
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
		fclose($cx);
		$response = json_decode(json_encode(simplexml_load_string($response)));
		return $response;
	}

 /*
 * Magic method to allow calls to be constructed via
 * method chaining. ie: $call->get_version
 * result in a endpoint location of <get_version/>.
 * @param   string   $location The api endpoint to call.
 * @param   string[] $options     Any arguments to parse as part of the location
 */
	public function __call($command, $options)
	{
			//Verificar status
			if(count($options) > 0){
				if(!($options[0]["complexity"])){
					$cmd = $this->simple_query($command,$options[0]);
				}
				else{
					$cmd = $this->complex_query($command,$options[0]);
				}
			}
			else {
				$cmd ="<$command/>";
			}
			print($cmd);
			return $this->getCmd($cmd);
	}
	/*
		Resolve complex xml query

		*/
	public function complex_query($command,$options){
		$cmd="<$command>";
		unset($options["complexity"]);
		foreach ($options as $clave=>$valor){
			if(is_array($valor)){
					$cmd.= $this->simple_query($clave,$valor);
			}
			else{
					$cmd.=" <$clave>$valor</$clave> ";
			}
		}
		$cmd.="</$command>";
		return $cmd;
	}


	/*
		Resolve simple xml query

		*/
	public function simple_query($command,$options){
		$cmd = "<$command ";
		unset($options["complexity"]);
		foreach($options as $clave=>$valor){
			$cmd.="$clave=\"$valor\"";
		}
		$cmd.=" />";
		return $cmd;
	}

	/*
		Get substring between two string
	*/

	private function get_string_between($string, $start, $end){
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}
	/*
		Get report csv
	*/

	public function get_report_csv($report_id){
		$options= array(
			"complexity" => False,
			"report_id" => $report_id,
			"format_id" => "c1645568-627a-11e3-a660-406186ea4fc5"
		);
		$cmd=$this->simple_query("get_reports",$options);
		$cx = $this->get_connection();
		$this->autenticate($cx);
		fwrite($cx,$cmd);
		$response = $this->read_stream_to_buffer($cx);
		$response = $this->get_string_between($response,"</report_format>","</report>");
		$response = base64_decode($response);
		return ($response);
	}
}
