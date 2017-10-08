<html>
<head>
	<title>Tracklog PHP</title>
	<meta charset="UTF-8">
	<meta lang="PT-BR">
	<meta name="author" content="asasouza">
	<meta name="description" content="Tracklog converter">
	<meta name="keywords" content="PHP,GPS,Tracklog,Converter">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<script src="https://use.fontawesome.com/ead9cb7aca.js"></script>
	<script defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBv4z_wInBt7RYjZGtDyyro_7Rpz7km8uU&callback=initMap"></script>
	<link href="https://fonts.googleapis.com/css?family=Roboto+Mono" rel="stylesheet">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>

</head>

<body>
	<style type="text/css">
	html{font-family: 'Roboto Mono', 'Roboto', monospace;}
	i{margin: 5px;}
	.col-5{float:left;margin:5px;width:19%;}
	.col-4{float:left;box-sizing:border-box;padding:5px;width:25%;}
	.col-3{float:left;box-sizing:border-box;padding:5px;width:33%;}
	.col-2{float:left;box-sizing:border-box;padding:5px;width:50%;}
	.col-1{float:left;box-sizing:border-box;padding:5px;width:100%;}
	.info{border-radius: 5px; background-color: #f5f5f5; text-align: center;}
	.data{font-weight: bold;}
	</style>


	<div id="content" style="margin:auto; width:80%;">
		<div id="map" style="height:calc(100vw / 4); width:100%;"></div>
		<div id="file-chooser" style="margin: 10px auto 10px auto; width:100%;">
			<div style="border: dashed 5px #cecece; color:#ccc; cursor:pointer; padding:5px; text-align:center">
				<a href="javascript:" style="color:#bbb">Click to choose a tracklog file</a>
			</div>
		</div>
		<form enctype="multipart/form-data">
			<input accept=".kml, .gpx, .tcx, .csv, .js" name="tracklogFile" style="display:none" type="file">
		</form>

		<div id="info-board" style="margin:10px; width:100%;">
			<div class="info col-5">
				<div class="title"><i class="fa fa-globe"></i>Distance</div>
				<div class="data" id="data-distance">0.0 KM</div>
			</div>
			<div class="info col-5">
				<div class="title"><i class="fa fa-clock-o"></i>Total Time</div>
				<div class="data" id="data-total-time"><b>00:00:00</b></div>
			</div>
			<div class="info col-5">
				<div class="title"><i class="fa fa-tachometer"></i>Pace</div>
				<div class="data" id="data-pace"><b>0:00</b></div>
			</div>
			<div class="info col-5">
				<div class="title"><i class="fa fa-arrow-up"></i>Elevation Gain</div>
				<div class="data" id="data-elevation-gain"><b>000 M</b></div>
			</div>
			<div class="info col-5">
				<div class="title"><i class="fa fa-arrow-down"></i>Elevation Loss</div>
				<div class="data" id="data-elevation-loss"><b>000 M</b></div>
			</div>
		</div>


		<canvas id="charts" style="width:100%;"></canvas>
	</div>


	<script type="text/javascript">
	initCharts();

	function initMap(){
		var map = new google.maps.Map(document.getElementById('map'), {
			center: {lat: 0, lng: 0},
			zoom: 1
		});
	}
	function initCharts(){
		var ctx = document.getElementById('charts').getContext('2d');
		var chart = new Chart(ctx, {
			type: 'line',
			data: {
				labels: ["0.0", "100.0", "220.0", "300.0", "430.0", "500.0", "590.0"],
				datasets: [{
					label: "Elevation",
					borderColor: '#F67C7C',
					data: [0, 10, 15, 20, 15, 10, 0],
				}, 
				{
					label: "Pace",
					borderColor: '#95F079',
					data: [{t: new Date(), y: "04.55"}, 10, 5, 2, 20, 30, 45],
				}]
			},
			options: {
				responsive: true,
			}
		});
	}
	$(document).ready(function() {
		$("#file-chooser").click(function(){
			$("input[type=file]").click();
		})

		$("input[type=file]").change(function() {
			var form = new FormData($("form")[0]);
			$.ajax({
				url: 'tracklogPhpAjax.php',
				type: 'POST',
				data: form,
				processData: false,
				contentType: false,
				success: function(response){
					response = $.parseJSON(response);
					updateInfoBoard(response.info_board);
				}
			})			
		});
	});

	function updateInfoBoard(data){
		$("#data-pace").html(data.data_pace);
		$("#data-distance").html(data.data_total_distance + " KM");
		$("#data-elevation-loss").html(data.data_elevation_loss + " M");
		$("#data-elevation-gain").html(data.data_elevation_gain + " M");
		$("#data-total-time").html(data.data_total_time);
	}

	</script>
</body>
</html>