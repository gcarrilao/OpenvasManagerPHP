## Openvas PHP Communicator

Es un middleware entre una aplicación PHP y el OMP de openvas


## Modo de uso


##### Get version

$ov = new OpenvasManager("localhost","9390","admin","admin");

print_r($ov->get_version());


##### Get target

$options= array(
	"complexity" => false,
	"target_id" => "2e0b354e-c410-4dd6-90ba-b71156887838",
);

print_r($ov->get_targets($options));

##### Create target

$options= array(
	"complexity" => true,
	"name" => "Maquina 2",
	"hosts" => "10.3.8.196",
);


print_r($ov->create_target($options));

##### Create task

$options= array(
		"complexity" => true,
		"name" => "Tarea nueva",
		"comment" => "Tarea portal",
		"target" => array( "id" =>"0aeba03c-86cb-477b-9656-d4fe9cff6c60"),
		"config" => array( "id" =>"74db13d6-7489-11df-91b9-002264764cea"),
);

print_r($ov->create_task($options));

##### Start task

$options= array(
		"task_id" => "225eee06-b029-4bed-8b83-ab4cf7943a63",
);

print_r($ov->start_task($options));

##### Get report

$options= array(
	"complexity" => False,
	"report_id" => "92a80a0a-bf25-4927-ae99-f8a9d5e3ed9d",
);

print_r($ov->get_reports($options));
