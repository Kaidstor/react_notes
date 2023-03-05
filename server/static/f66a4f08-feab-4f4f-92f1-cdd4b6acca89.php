<?php   
if (!is_user_logged_in() && is_single()) {
	auth_redirect();
} 
function tupleAdd($key, &$tuple, $addVal){ 
	if (!array_key_exists($key, $tuple))
		$tuple[$key] = $addVal;
	else  
		$tuple[$key] += $addVal; 
} 

function tupleAddArray($key, &$tuple, $addVal){ 
	if (!array_key_exists($key, $tuple))
		$tuple[$key] = $addVal;
	else    
		foreach($addVal as $k => $v){ 
			$keyAdd = $k;
			$valueAdd = $v; 

			if (!array_key_exists($keyAdd, $tuple[$key]))
				$tuple[$key][$keyAdd] = $valueAdd;
			else 
				$tuple[$key][$keyAdd] += $valueAdd;
		}  
} 

function newMap($name, $values, $param)
{
	echo $param . $name . " = new Map([";
	foreach ($values as $key => $value) {
		echo "['$key',$value],";
	}
	echo "]);\n";
}
function newMapJsonValue($name, $values, $param)
{
	echo $param . $name . " = new Map([";
	foreach ($values as $key => $value) {
		$value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		if ($value != "[]")
			echo "['$key', " . $value . "],";
	}
	echo "]);\n";
}

function mapFill($mapLaunched, $mapPassed, $mapMinutes, $devices, $devices_CPU, $FPS,  $mapSimulationNames, $mapPassedFrom, $mapFIO, $url = null)
{
	$param = $url ? "urls['$url']." : "";

	newMap('countDailyLaunched', $mapLaunched, $param);			  //Daily launched
	newMap('countDailyExams',    $mapPassed, $param);      		  //Passed count
	newMap('durationDailyExams', $mapMinutes, $param);  		  //Durations of passed  

	newMapJsonValue('mapProducts', $mapSimulationNames, $param);  //ids
	newMapJsonValue('mapCities', $mapPassedFrom, $param);   	  //города 
	newMapJsonValue('mapDevices', $devices, $param);   	  //видеокарта 
	newMapJsonValue('mapDevicesCPU', $devices_CPU, $param);     //процессор 
	newMapJsonValue('mapFPS', $FPS, $param);   	  	  //ФПС 
	newMapJsonValue('mapUsers', $mapFIO, $param);     			  //ФИО 
} 

$datenow = new DateTime('NOW'); 
$query_data = [];
foreach (get_field("lms") as $link) {  
	$site = explode('//', $link["lms_link"])[1];
	$query_data[] = ["site_url" => $site];
}
$query_data = json_encode($query_data); 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://lcontent.ru/lk/get_date.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, 'sites='.$query_data);

$response = curl_exec($ch);  
curl_close($ch);
 
$json_response = json_decode("[$response]", true); 


// echo '<pre style="overflow: unset">';
// var_dump($json_response);
// echo '</pre>';  
  
$url_data = [];

foreach ($json_response as $record) {
	$time_in_seconds = $record['datetime']['$date'];
	$stmtdate = gmdate('Y-m-d', $time_in_seconds);
	$url = $record['site_url'];

	$cities = $record['passed_from'];
	if (!empty($cities))
		foreach ($cities as $city => $value)
			if ($value != 0) {
				tupleAddArray($stmtdate, $url_data['*']['passed_from'], [$city => $value]);
				tupleAdd($stmtdate, $url_data['*']['passed'], $value);

				tupleAddArray($stmtdate, $url_data[$url]['passed_from'], [$city => $value]);
				tupleAdd($stmtdate, $url_data[$url]['passed'], $value);

				$url_data[$url]['runs_passed'] += $value;
			}

	foreach ($record['users'] as $user_name => $user) {
		foreach ($user as $product => $value) {
			tupleAddArray($stmtdate, $url_data['*']['myFIO'], [$user_name => $value]);
			tupleAddArray($stmtdate, $url_data['*']['mySimulationName'], [$product => $value]); // какие продукты были запущены
			tupleAdd($stmtdate, $url_data['*']['launched'], $value);

			tupleAddArray($stmtdate, $url_data[$url]['myFIO'], [$user_name => $value]);
			tupleAddArray($stmtdate, $url_data[$url]['mySimulationName'], [$product => $value]); // какие продукты были запущены
			tupleAdd($stmtdate, $url_data[$url]['launched'], $value);

			$url_data[$url]['runs_launched'] += $value;
		}
	}

	$prepareFPS = [];
	foreach($record['FPS'] as $FPS){ 
		$FPS = round($FPS / 10) * 10;
		$prepareFPS[] = intval($FPS < 10 ? 10 : $FPS);
	}
		
	$FPSes = [];
	foreach($prepareFPS as $FPS){
		tupleAdd($FPS, $FPSes, 1);
	}  

	tupleAdd($stmtdate, $url_data['*']['myMinutes'], $record['total_time']);
	tupleAddArray($stmtdate, $url_data['*']['devices'], $record['device']);
	tupleAddArray($stmtdate, $url_data['*']['devices_CPU'], $record['device_CPU']);
	tupleAddArray($stmtdate, $url_data['*']['FPS'], $FPSes);

	tupleAdd($stmtdate, $url_data[$url]['myMinutes'], $record['total_time']);
	tupleAddArray($stmtdate, $url_data[$url]['devices'], $record['device']);
	tupleAddArray($stmtdate, $url_data[$url]['devices_CPU'], $record['device_CPU']);
	tupleAddArray($stmtdate, $url_data[$url]['FPS'], $FPSes);

	$url_data[$url]['all_seconds'] += $record['total_time'];
} ?>
<? get_header(); ?>

<script src="https://lk.ranik.org/tincanreport/js/chart.min.js"></script>
<script src="https://lk.ranik.org/tincanreport/js/chartjs-plugin-regression-0.2.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">

<div class="container">
	<div class="row">
		<h1>Статистика</h1>
		<div class="col-12">
			<!-- <select id="list_ids" size="1">
				<option value="*">Все продукты</option>
			</select> -->
			<!-- <select id="list_geo" size="0">
				<option value="*">Все города</option>
			</select>
			<select id="list_fio" size="1">
				<option value="*">Все ФИО</option> 
			</select>-->
			<select id="filterUrls" size="1">
				<option value="*">Все urls</option>
			</select>
			<input type="date" id="filterDateFrom">
			<input type="date" id="filterDateTo">
		</div>
		<div class="row" style="margin: 20px 0 0 0;">
			<div class="col-md-3 col-xs-12 vertical-align">
				<div>
					<h3>Запускаемые продукты</h3>
					<div class="chart-left-container"> 
						<canvas id="chartProducts"></canvas>
					</div>
				</div>
				<div>
					<h3>В каких городах были запуски</h3>
					<div class="chart-left-container"> 
						<canvas id="chartCities"></canvas>
					</div> 
				</div>
			</div>
			<div class="col-md-9 col-xs-12">
				<h3>Динамика запусков и сколько из них экзаменов</h3>
				<div><canvas id="mainChart"></canvas></div>
				<div id="mainChartDescription">
					<p style="padding-top:10px;">Всего запусков за период - <b id="launched_data"></b>, из них успешно доведены до финиша - <b id="finished_data"></b>.</p>
					<p>Уникальных пользователей - <b id="users_data"></b>, длительность использования тренажеров общая - <b id="minutes_data"></b> мин.</p>
					<p>Средняя длительность на один запуск - <b id="averageDurationPerOneLaunch_data"></b> мин., средняя длительность на одного пользователя - <b id="averageDuration_data"></b> мин.</p>
				</div>
			</div>
		</div>
		<div class="charts_device"> 
			<div class="user_chart col-5"><canvas id="chartDevicesCPU"></canvas></div>
			<div class="user_chart col-4"><canvas id="chartDevices"></canvas></div>
			<div class="user_chart col-3"><canvas id="chartFPS"></canvas></div>
		</div>
		<div class="col-12">
			<h3>Сколько кто запускал</h3>
			<p>Показаны 30 наиболее активных пользователей</p>
			<div class="user_chart"><canvas id="chartUsers"></canvas></div>
		</div>
	</div>
	<style>
		@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap');

		* {
			font-family: 'Montserrat';
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		.charts_device{
			display: flex;
			margin: 40px 0;
		}
		h3 {
			font-size: 1.4rem;
		}

		a {
			text-decoration: none;
		}

		.wrapper {
			width: 1320px;
			margin: 0 auto
		}

		.vertical-align {
			display: flex;
			flex-direction: column;
		}

		.vertical-align .chart-left-container{
			height: 230px;
		}
		.user_chart{
			height: 500px;
		}
		.vertical-align > div {
			height: 50%;
		}
	</style>
	<script>
		<?php
		$urls = [];
		foreach ($url_data as $url => $url_props) {
			$urls[] = $url;
		}
		$url_obj = '{
		countDailyLaunched: new Map(),		
		countDailyExams: new Map(),		
		durationDailyExams: new Map(),		
		mapProducts: new Map(),			
		mapCities: new Map(),	 		
		mapUsers: new Map(),		
		mapDevices: new Map(),			
		mapDevicesCPU: new Map(),	 		
		mapFPS: new Map() 				
	}';

		//Количество запусков 
		//Количество экзаменов
		//Длительности экзаменов
		//Запускаемые продукты
		//Города
		//Пользователи 
		//Пользователи 


		$join_urls = join("':$url_obj,'", $urls);
		echo "let urls = {'$join_urls': $url_obj}; var mapUrls = new Map();";

		foreach ($urls as $key => $value) // urls
			echo "mapUrls.set('" . $value . "'," . $key . ");\n";

		foreach ($url_data as $url => $url_props) {
			ksort($url_props['launched']);
			ksort($url_props['passed']);
			ksort($url_props['myMinutes']);

			mapFill(
				$url_props['launched'],
				$url_props['passed'],
				$url_props['myMinutes'],
				$url_props['devices'],
				$url_props['devices_CPU'],
				$url_props['FPS'],
				$url_props['mySimulationName'],
				$url_props['passed_from'],
				$url_props['myFIO'],
				$url
			);
		}
		?>
	</script>

</div> <!-- container -->

<script>
	function updateChartData(chart, map) {
		const arr = [...map].map(el => el[1]);
		const temp = {}

		for (let data of arr) {
			for (let key in data) {
				if (temp[key])
					temp[key] += data[key]
				else
					temp[key] = data[key]
			}
		}

		const objToArr = []

		for (let key in temp)
			objToArr.push([key, temp[key]])

		chart.data.uniqueData = objToArr.length;

		const filteredMap = [...objToArr].sort((a, b) => b[1] - a[1]);
		const result = filteredMap.length > 30 ? filteredMap.slice(0, 30) : filteredMap;

		for (let key of result) {
			chart.data.labels.push(key[0]);
			chart.data.datasets[0].data.push(key[1]);
		}

		chart.update();
	}

	function updateChartDataXY(data, map) {
		for (let key of map)
			data.data.push({
				x: key[0],
				y: key[1]
			});

		return data.data.map(days => days.x)
	}

	function dataGenerator(label, color, yAxisID) {
		return {
			label,
			borderColor: color,
			backgroundColor: color,
			data: [],
			yAxisID
		};
	}

	let filterUrl = '*',
		filterDateFrom = 0,
		filterDateTo = 4102444800000;

	const filterDate = (arr) => [...arr].filter(el => Date.parse(el[0]) >= filterDateFrom && Date.parse(el[0]) <= filterDateTo);


	const axisCountDailyLaunched = dataGenerator("Кол-во запусков", "rgba(255, 0, 0, 1)", "y");
	const axisCountDailyExams = dataGenerator("Кол-во эказаменов", "green", "y");
	const axisDurationDailyExams = dataGenerator("Обшая длительность экзаменов, мин.", "blue", "y2");

	const myChart = new Chart(document.getElementById('mainChart'), {
		type: "line",
		data: {
			labels: [axisCountDailyLaunched.data],
			datasets: [
				axisCountDailyLaunched,
				axisDurationDailyExams,
				axisCountDailyExams,
			]
		},
	});

	function createChart(elem_id) {
		return new Chart(
			document.getElementById(elem_id), {
				type: 'bar',
				data: {
					uniqueData: null,
					labels: [],
					datasets: [{
						label: 'Запусков',
						data: [],
						backgroundColor: "green",
						orderColor: "green",
						borderWidth: 1
					}]
				},
				options: {
  maintainAspectRatio: false,
					indexAxis: 'y',
					scales: {
						y: {
							position: 'left'
						},
						x: {
							position: 'top'
						}
					},
					plugins: {
						legend: {
							display: false
						}
					}
				},
			}
		);
	}

	const chartProducts = createChart('chartProducts');
	const chartDevices = createChart('chartDevices');
	const chartDevicesCPU = createChart('chartDevicesCPU');
	const chartFPS = createChart('chartFPS');
	const chartCities = createChart('chartCities');
	const chartUsers = createChart('chartUsers');

	function optionset(options, element_id) {
		for (let key of options) {
			if (key[0] == '*') continue;

			const optionItem = document.createElement("option");
			optionItem.text = key[0];
			optionItem.value = key[0];

			document.getElementById(element_id).options.add(optionItem);
		}
	}

	// optionset(mapProducts,"list_ids");
	// optionset(mapCities,"list_geo");
	// optionset(mapUsers,"list_fio");
	optionset(mapUrls, "filterUrls");


	// document.getElementById("list_ids").addEventListener('change',  () => {

	// });
	// document.getElementById("list_geo").addEventListener('change',  () => {

	// });
	// document.getElementById("list_fio").addEventListener('change',  () => {

	// });

	function filterChart(chart, data) {
		chart.data.labels = [];
		chart.data.datasets[0].data = [];

		updateChartData(chart, filterDate(data));
	}

	function chartsFilter() {
		axisCountDailyLaunched.data = [];
		axisDurationDailyExams.data = [];
		axisCountDailyExams.data = [];

		const filteredLaunched = urls[filterUrl]?.countDailyLaunched ? urls[filterUrl].countDailyLaunched : [];
		const filteredDuration = urls[filterUrl]?.durationDailyExams ? urls[filterUrl].durationDailyExams : [];
		const filteredExams = urls[filterUrl]?.countDailyExams ? urls[filterUrl].countDailyExams : [];

		const filteredProducts = urls[filterUrl]?.mapProducts ? urls[filterUrl].mapProducts : [];
		const filteredCities = urls[filterUrl]?.mapCities ? urls[filterUrl].mapCities : [];
		const filteredUsers = urls[filterUrl]?.mapUsers ? urls[filterUrl].mapUsers : [];
		const filteredDevices = urls[filterUrl]?.mapDevices ? urls[filterUrl].mapDevices : [];
		const filteredDevicesCPU = urls[filterUrl]?.mapDevicesCPU ? urls[filterUrl].mapDevicesCPU : [];
		const filteredFPS = urls[filterUrl]?.mapFPS ? urls[filterUrl].mapFPS : [];

		myChart.data.labels = updateChartDataXY(axisCountDailyLaunched, filterDate(filteredLaunched));
		updateChartDataXY(axisDurationDailyExams, filterDate(filteredDuration));
		updateChartDataXY(axisCountDailyExams, filterDate(filteredExams));

		filterChart(chartProducts, filteredProducts)
		filterChart(chartCities, filteredCities)
		filterChart(chartUsers, filteredUsers)
 
		filterChart(chartDevices, filteredDevices)
		filterChart(chartDevicesCPU, filteredDevicesCPU)
		filterChart(chartFPS, filteredFPS)

		const datasets = myChart.data.datasets;

		const countMinutes = Math.round(datasets[1].data.reduce((acc, item) => acc + item.y, 0));
		const countFinished = datasets[2].data.reduce((acc, item) => acc + item.y, 0);
		const countLaunched = datasets[0].data.reduce((acc, item) => acc + item.y, 0);
		const averageDuration = Math.round(countMinutes / chartUsers.data.uniqueData);
		const averageDurationPerOneLaunch = Math.round(countMinutes / countLaunched);

		document.getElementById('users_data').innerText = chartUsers.data.uniqueData;
		document.getElementById('launched_data').innerText = countLaunched;
		document.getElementById('launched_data').innerText = countLaunched;
		document.getElementById('minutes_data').innerText = countMinutes;
		document.getElementById('finished_data').innerText = countFinished;
		document.getElementById('averageDuration_data').innerText = averageDuration;
		document.getElementById('averageDurationPerOneLaunch_data').innerText = averageDurationPerOneLaunch;

		myChart.update();
	}

	document.getElementById("filterUrls").addEventListener('change', (e) => {
		filterUrl = e.target.value;
		chartsFilter();
	});
	document.getElementById("filterDateFrom").addEventListener('change', (e) => {
		const date = e.target.value ? e.target.value : 0;
		filterDateFrom = Date.parse(date);
		chartsFilter();
	});
	document.getElementById("filterDateTo").addEventListener('change', (e) => {
		const date = e.target.value ? e.target.value : 4102444800000;
		filterDateTo = Date.parse(e.target.value);
		chartsFilter();
	});

	chartsFilter();
	let date = new Date();
	let dateStr = date.toISOString().slice(0, 10);
	let datePrevMonthStr = new Date(date.setMonth(date.getMonth() - 1)).toISOString().slice(0, 10);

	console.log(dateStr)
	console.log(datePrevMonthStr)

	document.getElementById('filterDateFrom').value = datePrevMonthStr;
	document.getElementById('filterDateTo').value = dateStr;

	document.getElementById('filterDateFrom').dispatchEvent(new Event('change', {
		bubbles: true
	}));
	document.getElementById('filterDateTo').dispatchEvent(new Event('change', {
		bubbles: true
	}));
</script>

<?php get_footer(); ?>

