<?php

//получаем статистику по клиенту начиная с конкретной даты

define("CODEBASE", "/home/c/cm90303/lk/public_html/tincanreport");

//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

$idclienta=get_field("client_name");

echo $idclienta;

$LMS_LINK="lms.lcontent.ru";
$LMS=get_field("lms",$idclienta);
if ($LMS) {
	$LMS_LINK=$LMS[0][lms_link];
}

$pos1 = stripos($LMS_LINK, "://");
$tmp = '';
if ($pos1 !== false) {
	$tmp = explode('://',$LMS_LINK);
	$LMS_LINK = $tmp[1];
}

$terms = get_terms([
	'taxonomy' => 'products',
	'hide_empty' => false,
	'meta_key' => 'flag_new',
	'meta_value' => true
]);

foreach( $terms as $term ){
	$newtovar_link = get_term_field('link_code',$term);
	echo $term->name." (".$newtovar_link.")" ;
}

$jsonfile = CODEBASE."/stmts";

$stmts = [];

function getstmts(){

	global $jsonfile;
	global $stmts;

	$stmts = unserialize(file_get_contents($jsonfile));

}

function ISO8601ToSeconds($ISO8601){
	$interval = new \DateInterval($ISO8601);

	return ($interval->d * 24 * 60 * 60) +
		($interval->h * 60 * 60) +
		($interval->i * 60) +
		$interval->s;
}

getstmts();

$myMapVerbs = array();
$mySimulationName = array();
$mySimulationLicensed = array();
$myMapLaunchURL = array();
$myGEO = array();
$myFIO = array();
$myMinutes = array();
$launched = array();
$passed = array();

$datenow = new DateTime('NOW');
$monthago = new DateTime('NOW');
$monthago->modify('-1 month');
$step = new DateInterval('P1D');
$period = new DatePeriod($monthago, $step, $datenow);

$myDates = array(); 
foreach($period as $datetime) {
  $myDates[] = $datetime->format("Y-m-d");
  $launched[$datetime->format("Y-m-d")] = 0;
  $passed[$datetime->format("Y-m-d")] = 0;
  $myMinutes[$datetime->format("Y-m-d")] = 0;
}

$site = array($LMS_LINK);

$numstmts = 0;
$runs_launched = 0;
$runs_passed = 0;
$runs_minutes = 0;
$all_seconds = 0;

function normalizeLP() {
	global $launched;
	global $passed;
	foreach ($launched as $key => $value) {
		if ($launched[$key]<$passed[$key]) {
			$passed[$key]=$launched[$key];
		}
	}
}

foreach ($stmts as $key => $value) {

  global $numstmts;
  global $runs_launched;
  global $runs_passed;
  global $runs_minutes;
  global $all_seconds;

  $stmtdate = $value->stored;
  $stmtdate = explode('T',$stmtdate)[0];

  if ($stmtdate>$monthago->format("Y-m-d"))
  if (array_key_exists("context", $value))
  if (array_key_exists("contextActivities", $value->context))
  if (array_key_exists("parent", $value->context->contextActivities)) {

    $url = $value->context->contextActivities->parent;
    $url = $url[0]->id;
    $key = parse_url($url,PHP_URL_HOST);

    if (gettype(array_search($key, $site)) == 'integer') {

	$numstmts++;

	//verbs
	$key_0 = $value->verb->id;
	if (!array_key_exists($key_0, $myMapVerbs)) {
		$myMapVerbs[$key_0] = 1;
	} else {
		$myMapVerbs[$key_0]++;
	}

	//launched and passed by days
	if ($key_0 == "http://adlnet.gov/expapi/verbs/launched"){
		$runs_launched++;
		if (!array_key_exists($stmtdate, $launched)){
			$launched[$stmtdate] = 1;
		} else {
			$launched[$stmtdate]++;
		}
	}
	if ($key_0 == "http://adlnet.gov/expapi/verbs/passed"){
		$runs_passed++;
		if (!array_key_exists($stmtdate, $passed)){
			$passed[$stmtdate] = 1;
		} else {
			$passed[$stmtdate]++;
		}
		if (!array_key_exists($stmtdate, $myMinutes)){
			$myMinutes[$stmtdate] = ISO8601ToSeconds($value->result->duration);
		} else {
			$myMinutes[$stmtdate] += ISO8601ToSeconds($value->result->duration);
		}
		$all_seconds += ISO8601ToSeconds($value->result->duration);
	}

	//launched ids
	if ($key_0 == "http://adlnet.gov/expapi/verbs/launched"){
		$key_1 = $value->object->id;
//		$key_1 = (array)$value->object->definition->name;
//		$key_1 = $key_1['en-US'];
		if (!array_key_exists($key_1, $mySimulationName)) {
			$mySimulationName[$key_1] = 1;
		} else {
			$mySimulationName[$key_1]++;
		}
	}

	//licensed ids
	if ($key_0 == "https://lcontent.ru/xapi/verbs/licensed"){
		$key_1 = $value->object->id;
		if (!array_key_exists($key_1, $mySimulationLicensed)) {
			$mySimulationLicensed[$key_1] = 1;
		} else {
			$mySimulationLicensed[$key_1]++;
		}
	}

	//launched URLs
	if ($key_0 == "http://adlnet.gov/expapi/verbs/launched"){
		if (array_key_exists("contextActivities", $value->context))
		if (array_key_exists("parent", $value->context->contextActivities)) {

			$url1 = $value->context->contextActivities->parent;
			$url1 = $url1[0]->id;

			$key_1 = parse_url($url1,PHP_URL_HOST);
			if (!array_key_exists($key_1, $myMapLaunchURL)) {
				$myMapLaunchURL[$key_1] = 1;
			} else {
				$myMapLaunchURL[$key_1]++;
			}
		}
	}

	//GEO
	if ($key_0 == "http://adlnet.gov/expapi/verbs/passed"){
		if (array_key_exists("extensions", $value->context))
		if (array_key_exists("https://w3id.org/xapi/acme/extensions/training-location", $value->context->extensions)) {

			$temp=(array)$value->context->extensions;
			$key_1 = $temp["https://w3id.org/xapi/acme/extensions/training-location"];
			if ($key_1 !== "tyumen") {
				if (!array_key_exists($key_1, $myGEO)) {
					$myGEO[$key_1] = 1;
				} else {
					$myGEO[$key_1]++;
				}
			}
		}
	}

	//launched fio
	if ($key_0 == "http://adlnet.gov/expapi/verbs/launched"){
		$key_1 = $value->actor->name;
		if (!array_key_exists($key_1, $myFIO)) {
			$myFIO[$key_1] = 1;
		} else {
			$myFIO[$key_1]++;
		}
	}
    }//учет сайта клиента
  }//учет даты

}

?>

<html lang="en">
<head>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Отчет для клиента</title>

<script src="https://lk.ranik.org/tincanreport/js/chart.min.js"></script>
<script src="https://lk.ranik.org/tincanreport/js/chartjs-plugin-regression-0.2.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">

</head>
<body>

<div class="container">

        <div class="row">
                <div class="col-12">
                        <h1>Статистика за последний месяц</h1>
                </div>
        </div>

<style>
	select{display:none;}
	#butrenew{display:none;}
</style>

        <div class="row">
                <div class="col-12">
			<!--select id="list_verbs" size="1">
				<option value="*">Все глаголы</option>
			</select>
			<select id="list_clients" size="1">
				<option value="*">Все клиенты</option>
			</select-->
			<select id="list_ids" size="1">
				<option value="*">Все продукты</option>
			</select>
			<!--select id="list_urls" size="1">
				<option value="*">Все urls</option>
			</select-->
			<select id="list_geo" size="0">
				<option value="*">Все города</option>
			</select>
			<select id="list_fio" size="1">
				<option value="*">Все ФИО</option>
			</select>
			<input type="button" value="Обновить" id="butrenew" onclick="getnewdata()">
			<p style="padding-top:15px;"><? echo implode(",",$site); ?> (всего записей об обучении <? echo $numstmts; ?>)</p>
                </div>
        </div>

	<div class="row">
                <div class="col-md-3 col-xs-12">
			<h3>Запускаемые продукты</h3>
			<canvas id="myChart7"></canvas>
			<h3>В каких городах были запуски</h3>
			<canvas id="myChart8"></canvas>
		</div>
		<div class="col-md-9 col-xs-12">
			<h3>Динамика запусков и сколько из них экзаменов</h3>
			<div><canvas id="myChart9"></canvas></div>
			<p style="padding-top:10px;">Всего запусков за период - <b><? echo $myMapVerbs['http://adlnet.gov/expapi/verbs/launched']; ?></b>, из них успешно доведены до финиша - <b><? echo $myMapVerbs['http://adlnet.gov/expapi/verbs/passed']; ?></b>.</p>
			<p>Уникальных пользователей - <b><? echo count($myFIO); ?></b>, длительность использования тренажеров общая - <b><? echo ceil($all_seconds/60);  ?></b> мин.</p>
			<p>Средняя длительность на один запуск - <b><? echo ceil($all_seconds/60/$myMapVerbs['http://adlnet.gov/expapi/verbs/launched']); ?></b> мин., средняя длительность на одного пользователя - <b><? echo ceil($all_seconds/60/count($myFIO)); ?></b> мин.</p>
                </div>
	</div>

	<div class="row">
                <div class="col-12">
                        <h3>Сколько кто запускал</h3>
			<p>Показаны 30 наиболее активных пользователей</p>
			<div><canvas id="myChart10"></canvas></div>
                </div>
        </div>

<script>
	var myValues = new Map();//Количество запусков
	var myValues2 = new Map();//Количество экзаменов
	var myValues3 = new Map();//Длительности экзаменов
	var mapSort = new Map();//Запускаемые продукты
	var mapSort1 = new Map();//Города
	var mapSort2 = new Map();//Пользователи
<?
	ksort($launched); 
	ksort($passed);
	ksort($myMinutes);
	arsort($mySimulationName); 
	arsort($myGEO); 
	arsort($myFIO);
	$myFIO = array_slice($myFIO,0,30); 
	normalizeLP();
	foreach ($launched as $key => $value) {
		echo "myValues.set('".$key."',".$value.");\n";
	} 
	foreach ($passed as $key => $value) {
		echo "myValues2.set('".$key."',".$value.");\n";
	}
	//Durations of passed
	foreach ($myMinutes as $key => $value) {
		echo "myValues3.set('".$key."',".ceil($value/60).");\n";
	}
	//ids
	foreach ($mySimulationName as $key => $value) {
		echo "mapSort.set('".$key."',".$value.");\n";
	}
	//города
	foreach ($myGEO as $key => $value) {
		echo "mapSort1.set('".$key."',".$value.");\n";
	}
	//ФИО
	foreach ($myFIO as $key => $value) {
		echo "mapSort2.set('".$key."',".$value.");\n";
	}
?>
</script>

</div> <!-- container -->
<script src="https://lk.ranik.org/tincanreport/js/app.js"></script>
</body>
