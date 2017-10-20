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
	<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
	<script src="https://code.highcharts.com/highcharts.js"></script>
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
	.tooltip {cursor:default; border-bottom: 1px dotted black; display: inline-block; position: relative;}
	.tooltip .tooltiptext {background-color: #E1DCDC; border-radius: 6px; bottom:100%; left: 50%; margin-left:-60px; padding: 5px 0; position: absolute; text-align: center; visibility: hidden; width: 130px; z-index: 1;}
	.tooltip:hover .tooltiptext {visibility: visible;}
	</style>

	<div id="content" style="margin:auto; width:80%;">
		<div id="map" style="height:calc(100vw / 4); width:100%;"></div>
		<div style="margin: 10px auto 10px auto; width:100%;">
			<div id="file-chooser" style="border: dashed 5px #cecece; color:#ccc; cursor:pointer; float:left; padding:5px; text-align:center; width:100%;">
				<span id="file-chooser-text" style="text-decoration:underline; color:#bbb;">Click to choose a tracklog file</span>
			</div>		
			<div id="download" style="float:right; display:none; margin:5px auto 6px auto; text-align:center; width:37%;">
				<form action="javascript:" id="download-file" style="margin:0px;" data-file-path="">
					<select style="border-radius:6px; font-family: 'Roboto Mono', 'Roboto', monospace; font-size: 15px; height: 30px; width: 70%;">
						<option value="0">Convert to another extension</option>
						<option>KML</option>
						<option>TCX</option>
						<option>GPX</option>
						<option>GeoJson</option>
						<option>CSV</option>
					</select>
					<button id="download-file-trigger" style="border-radius:6px; cursor:pointer; font-family:'Roboto Mono','Roboto',monospace; font-size:15px; height:30px; width: 27%;">Download</button>
				</form>
			</div>
			<form id="submit-file" enctype="multipart/form-data" style="display:none">
				<input accept=".kml, .gpx, .tcx, .csv, .js" name="tracklogFile" type="file">
			</form>
		</div>
		<div id="info-board" style="margin:10px; width:100%;">
			<div class="info col-5">
				<div class="title data-distance"><i class="fa fa-globe"></i>Distance</div>
				<div class="data data-distance">0.0 KM</div>
			</div>
			<div class="info col-5">
				<div class="title data-total-time"><i class="fa fa-clock-o"></i>Total Time</div>
				<div class="data data-total-time"><b>00:00:00</b></div>
			</div>
			<div class="info col-5">
				<div class="title data-pace"><i class="fa fa-tachometer"></i>Pace</div>
				<div class="data data-pace"><b>0:00</b></div>
			</div>
			<div class="info col-5">
				<div class="title data-elevation-gain"><i class="fa fa-arrow-up"></i>Elevation Gain</div>
				<div class="data data-elevation-gain"><b>000 M</b></div>
			</div>
			<div class="info col-5">
				<div class="title data-elevation-loss"><i class="fa fa-arrow-down"></i>Elevation Loss</div>
				<div class="data data-elevation-loss"><b>000 M</b></div>
			</div>
		</div>
		<div id="charts" style="width:100%;"></div>
	</div>

	<script type="text/javascript">	
	var chart = new Highcharts.chart("charts", {
		chart:{
			type:"line",
			zoomType:"x",
		},		
		title:{
			text:"",
		},
		xAxis: {
			categories: ["0.0", "0.0","0.0","0.0","0.0","0.0"],
			tickInterval: 200,
		},
		yAxis:[{
			reversed: true,
			grideLineWidth: 0,
			labels:{
				format:"{value}m/km",
				formatter: function(){
					this.value = new Date(this.value*1000).toISOString().substr(12, 7) + "/km";
					return this.value;
				}
			},
			title:{
				text: 'Pace',
			},
			opposite: true,
		},{
			labels:{
				format:"{value}m",
			},
			title:{
				text: 'Elevation',
			},
		}],
		tooltip:{
			shared: true,
			formatter: function(){
				var tooltip = "Distance : <b>"+this.x+"</b> m";
				$.each(this.points, function(){
					if (this.series.name == "Pace") {
						tooltip += "<br>"+this.series.name+" : "+ new Date(this.y*1000).toISOString().substr(12, 7) + "/km";
					}else{
						tooltip += "<br>"+this.series.name+" : "+ this.y + " m";	
					}						
				});
				return tooltip;
			}
		},
		series: [{	
			name:"Pace",
			type:"line",
			data: [0, 0, 0, 0, 0, 0],
			tooltip:{
				valueSuffix:" min/km",
			},
			color: "#3FF862",
			zIndex: 1,
		}, 
		{
			name:"Elevation",
			type:"area",
			yAxis: 1,
			data: [0, 0, 0, 0, 0, 0],
			tooltip:{
				valueSuffix:" m",
			},
			color: "#C6C6C6",
			zIndex: 0,
		}],
		plotOptions: {
			area: {
				fillColor: {
					linearGradient: {
						x1: 0,
						y1: 0,
						x2: 0,
						y2: 1
					},
					stops: [
					[0, "#C6C6C6"],
					[1, Highcharts.Color("#C6C6C6").setOpacity(0).get('rgba')]
					]
				},
				threshold: null,
				turboThreshold: 0,
			},
			line: {
				turboThreshold: 0,
			},
		},
	});

function initMap(){
	var map = new google.maps.Map(document.getElementById('map'), {
		center: {lat: 0, lng: 0},
		zoom: 1
	});
};
$(document).ready(function() {
	$("#file-chooser").click(function(){
		$("input[type=file]").click();
	});
	$("input[type=file]").change(function() {
		if ($("input[type=file]").val() != "") {
			revertFileInfos();
			revertChart();
			var form = new FormData($("#submit-file")[0]);
			$.ajax({
				url: 'tracklogPhpAjax.php',
				type: 'POST',
				data: form,
				processData: false,
				contentType: false,
				beforeSend: function(){
					$(".data.data-pace").html("<i class='fa fa-circle-o-notch fa-spin'></i>");
					$(".data.data-elevation-gain").html("<i class='fa fa-circle-o-notch fa-spin'></i>");
					$(".data.data-elevation-loss").html("<i class='fa fa-circle-o-notch fa-spin'></i>");
					$(".data.data-total-time").html("<i class='fa fa-circle-o-notch fa-spin'></i>");
					$(".data.data-distance").html("<i class='fa fa-circle-o-notch fa-spin'></i>");
					chart.showLoading("<i class='fa fa-circle-o-notch fa-spin'></i>");
				},
				success: function(response){
					response = $.parseJSON(response);
					if (response.error) {
						revertFileInfos();
						revertChart();
						chart.hideLoading();
						$("#file-chooser-text").html("<span style='color:#F76868'>"+response.error+"</span><br><span> Please, click to choose another file.</span>")
					}else{
						chart.hideLoading();
						updateInfoBoard(response.info_board);						
						updateCharts(response.data_distances, response.data_elevations, response.data_paces);
						updateFileChooser(response.data_kml);
						updateMapWithKml(response.data_kml);
					}
				}
			});
}
});
$("#download-file-trigger").click(function(){
	if ($("select").val() != 0) {
		var form = new FormData($("#submit-file")[0]);
		form.append('extension_to_download', $("select").val());
		$.ajax({
			url: 'tracklogPhpAjax.php',
			type: 'POST',
			processData: false,
			contentType: false,
			data: form,
			success: function(response){
				response = $.parseJSON(response);
				window.open(response.download_file_path, "_blank");
				$("select").val(0);
			}
		})
	}else{
		console.log("choose a file extension to download!");
		$("#file-chooser-text").html("<span style='color:#F76868'>Choose a file extension to download!</span><br><span> Click to change file.</span>")
	}
})
});
function updateInfoBoard(data){
	if (data.data_pace[0] == "success") {
		$(".data.data-pace").html(data.data_pace[1]);
	}else{
		$(".title.data-pace").append(" <div class='tooltip'><b>?</b><span class='tooltiptext'>"+ data.data_pace[1] +"</span></div>");
		$(".data.data-pace").html("--:--");
	}
	if (data.data_elevation_gain[0] == "success") {
		$(".data.data-elevation-gain").html(data.data_elevation_gain[1] + " M");	
	}else{
		$(".title.data-elevation-gain").append(" <div class='tooltip'><b>?</b><span class='tooltiptext'>"+ data.data_elevation_gain[1] +"</span></div>");
		$(".data.data-elevation-gain").html("--- M");
	}
	if (data.data_elevation_loss[0] == "success") {
		$(".data.data-elevation-loss").html(data.data_elevation_loss[1] + " M");	
	}else{
		$(".title.data-elevation-loss").append(" <div class='tooltip'><b>?</b><span class='tooltiptext'>"+ data.data_elevation_loss[1] +"</span></div>");
		$(".data.data-elevation-loss").html("--- M");
	}
	if (data.data_total_time[0] == "success") {
		$(".data.data-total-time").html(data.data_total_time[1]);	
	}else{
		$(".title.data-total-time").append(" <div class='tooltip'><b>?</b><span class='tooltiptext'>"+ data.data_total_time[1] +"</span></div>");
		$(".data.data-total-time").html("--:--:--");
	}
	$(".data.data-distance").html(data.data_total_distance + " KM");		
}
function updateMapWithKml(kml){
	var kmlLayer = new google.maps.KmlLayer({
		url: kml,
		suppressInfoWindows: false,
		preserveViewport: false,
		map: new google.maps.Map(document.getElementById('map'))
	});
}
function updateCharts(distances, elevations, paces){
	$.each(chart.series, function(index, el) {
		// update the series of the X axis
		this.xAxis.setCategories(distances, false);
		// update the series of the ELEVATIONS Y axis, if response is sucess
		if (elevations[0] == "success" && this.name == "Elevation") {
			this.update({data: elevations[1]}, false);
		}else{
			if (elevations[0] == "error") {
				this.update({data: [0]}, false);
			};
		}
		// update the series of the PACE Y axis, if response is sucess
		if(paces[0] == "success" && this.name == "Pace"){
			this.update({data: paces[1]}, false);
		}else{
			if (paces[0] == "error") {
				this.update({data: [0]}, false);
			};
		}
	});
	chart.redraw();
}
function updateFileChooser(file_path){
	$("#download-file").attr('data-file-path', file_path);
	$("#file-chooser").css('width', '60%');
	$("#file-chooser-text").html("Click to change file");
	$("#download").css('display', 'inline');
}
function revertFileInfos(){
	$("#download-file").attr('data-file-path', "");
	$("#file-chooser").css('width', '100%');
	$("#file-chooser-text").html("Click to choose a tracklog file");
	$("#download").css('display', 'none');
	$(".tooltip").remove();
	$(".data.data-pace").html("0:00");
	$(".data.data-elevation-gain").html("000 M");
	$(".data.data-elevation-loss").html("000 M");
	$(".data.data-total-time").html("00:00:00");
	$(".data.data-distance").html("0.0 M");
}
function revertChart(){
	$.each(chart.series, function(index, el) {
		this.xAxis.setCategories(["0.0", "0.0", "0.0", "0.0", "0.0", "0.0"], false);
		this.update({data: [0]}, false);
	});
	chart.redraw();
}

</script>
</body>
</html>