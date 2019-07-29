# Openvas PHP Communicator

Es un middleware entre una aplicación PHP y el OMP de openvas

## Complexity

#### false, query example
```xml
<get_reports report_id="f0fdf522-276d-4893-9274-fb8699dc2270"/>
```
```xml
<get_version/>
```
#### true, query example
```xml
<create_task>
  <name>Scan Webserver</name>
  <comment>Hourly scan of the webserver</comment>
  <config id="daba56c8-73ec-11df-a475-002264764cea"/>
  <target id="b493b7a8-7489-11df-a3ec-002264764cea" />
  <scanner id="15348381-3180-213f-4eec-123591912388"/>
</create_task>
```
```xml
<create_target>
  <name>All GNU/Linux machines</name>
  <hosts>192.168.1.0/24</hosts>
</create_target>
```
## Use mode

#### Get version

$ov = new OpenvasManager("localhost","9390","admin","admin");

print_r($ov->get_version());


#### Get target

$options= array(
	"complexity" => false,
	"target_id" => "2e0b354e-c410-4dd6-90ba-b71156887838",
);

print_r($ov->get_targets($options));

#### Create target

$options= array(
	"complexity" => true,
	"name" => "Maquina 2",
	"hosts" => "10.3.8.196",
);


print_r($ov->create_target($options));

#### Create task

$options= array(
		"complexity" => true,
		"name" => "Tarea nueva",
		"comment" => "Tarea portal",
		"target" => array( "id" =>"0aeba03c-86cb-477b-9656-d4fe9cff6c60"),
		"config" => array( "id" =>"74db13d6-7489-11df-91b9-002264764cea"),
);

print_r($ov->create_task($options));

#### Start task

$options= array(
		"task_id" => "225eee06-b029-4bed-8b83-ab4cf7943a63",
);

print_r($ov->start_task($options));

#### Get report

$options= array(
	"complexity" => False,
	"report_id" => "92a80a0a-bf25-4927-ae99-f8a9d5e3ed9d",
);

print_r($ov->get_reports($options));
