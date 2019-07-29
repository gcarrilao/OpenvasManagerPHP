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
		$response = json_decode(json_encode(simplexml_load_string($response)));
		return $response;
	}

	 /*
	 * Magic method to allow calls to be constructed via
	 * method chaining. ie: $call->get_version
	 * result in a endpoint location of <get_version/>.
	 * @param   string   $location The api endpoint to call.
	 * @param   string[] $slug     Any arguments to parse as part of the location
	 */
		public function __call($command, $options)
		{
				//Verificar status
				$parameters = " ";
				if(count($options) > 0){
					foreach($options[0] as $clave => $valor){
							if(is_string($valor)){
								$parameters .= "$clave=\"$valor\" ";
						  }
							else{
								$parameters .= "$clave=$valor ";

							}
					}
				}
				$cmd ="<$command $parameters/>";
				return $this->getCmd($cmd);

		}

		public function create_target($name,$hosts){
			$name = "<name>$name</name>";
			$hosts ="<hosts>$hosts</hosts>";
			$cmd ="<create_target> " .
			 		"$name".
					"$hosts".
					"</create_target>";
			print($cmd);
			return $this->getCmd($cmd);
		}

		public function create_task($name,$comment,$options){
			$name = "<name>$name</name>";
			$comment ="<comment>$comment</comment>";
			$cmd ="<create_task> " .
			 		"$name".
					"$hosts";
			foreach ($options as $clave=>$valor){
					$cmd.=" <$clave id=\"$valor\"/> ";
			}
			$cmd.="</create_task>";
			print($cmd);
			return $this->getCmd($cmd);
		}
}

$ov = new OpenvasManager("localhost","9390","admin","admin");

print_r($ov->get_version());


$options= array(
	"target_id" => "852544d1-9323-447e-b449-bc19f293019b",
);
print_r($ov->get_targets($options));
#print_r($ov->create_target("Maquina facu","10.3.8.199"));

$options= array(
		"target" => "0aeba03c-86cb-477b-9656-d4fe9cff6c60",
		"config" => "74db13d6-7489-11df-91b9-002264764cea",
);
#print_r($ov->create_task("Task-user:admin","Tarea portal",$options));


$options= array(
		"task_id" => "885f09e1-5a42-4aa5-a932-bae3d8adb8db"
);
print_r($ov->start_task($options));
