<?php
//sempre tem que ter o nome 'test' na frente do teste;
//comanda php phpunit\phpunit.phar --bootstrap autoloader.php --testdox phpunit\tracklogTest.php
//comanda php phpunit\phpunit.phar --bootstrap autoloader.php phpunit

//inverter a ordem de esperado e resultado

// assertEquals(esperado, atual);


use PHPUnit\Framework\TestCase;

final class TracklogTeste extends TestCase{

	public function testGetAllPoints(){
		$gpx = new GPX('test_files/gpx/test_correct.gpx');
		$this->assertFalse(empty($gpx->getPoints()));
		$this->assertEquals(1293, count($gpx->getPoints()));
		$this->assertArraySubset(['0' => [ 'latitude' => -23.8097560, 
											'longitude' => -45.4189910, 
											'elevation' => 36.900000, 
											'distance' => 0.000, 
											'time' => '2017-06-08T21:58:50Z']], $gpx->getPoints());
	}

	public function testGetAllLatitudes(){
		$csv = new CSV('test_files/csv/test_correct_no_head.csv');
		$this->assertFalse(empty($csv->getLatitudes()));
		$this->assertEquals(1293, count($csv->getLatitudes()));
		$this->assertArraySubset( [-23.8097560, -23.8097460, -23.8097540, -23.8097660] ,$csv->getLatitudes());
	}

	public function testGetAllLongitudes(){
		$json = new GeoJson('test_files/geoJson/test_correct.js');
		$this->assertFalse(empty($json->getLongitudes()));
		$this->assertEquals(1293, count($json->getLongitudes()));
		$this->assertArraySubset( [-45.4189910, -45.4188970, -45.4188470, -45.4188140] ,$json->getLongitudes());
	}

	public function testGetAllElevations(){
		$kml = new KML('test_files/kml/test_correct_no_time.kml');
		$this->assertFalse(empty($kml->getElevations()));
		$this->assertEquals(1293, count($kml->getElevations()));
		$this->assertArraySubset( [36.900000, 35.400000, 34.000000, 33.100000] ,$kml->getElevations());
	}

	public function testGetAllTimes(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertFalse(empty($tcx->getTimes()));
		$this->assertEquals(1293, count($tcx->getTimes()));
		$this->assertArraySubset( ['2017-06-08T21:58:50Z', '2017-06-08T21:59:00Z', '2017-06-08T21:59:04Z', '2017-06-08T21:59:06Z'] ,$tcx->getTimes());
	}

	public function testGetAllDistances(){
		$gpx = new GPX('test_files/gpx/test_correct_no_time.gpx');
		$this->assertFalse(empty($gpx->getDistances()));
		$this->assertEquals(1293, count($gpx->getDistances()));
		$this->assertArraySubset( [0.000, 9.627, 14.791, 18.404] ,$gpx->getDistances());
	}

	public function testGetTotalDistance(){
		$kml = new KML('test_files/kml/test_correct_no_time.kml');
		$this->assertEquals(6010.62, $kml->getTotalDistance('meters'), "meters");
		$this->assertEquals(3.73, $kml->getTotalDistance('miles'), "miles");
		$this->assertEquals(6.01, $kml->getTotalDistance('kilometers'), "kilometers");
	}

	public function testGetMaxElevation(){
		$csv = new CSV('test_files/csv/test_correct.csv');
		$this->assertEquals(38.90, $csv->getMaxElevation(), "meters");
	}

	public function testGetPace(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertEquals('6:53', $tcx->getPace(), 'minutes per kilometer');
	}

	public function testGetTotalTime(){
		$gpx = new GPX('test_files/gpx/test_correct.gpx');
		$this->assertEquals('2.4850', $gpx->getTotalTime('seconds'));
		$this->assertEquals('41.4', $gpx->getTotalTime('minutes'));
		$this->assertEquals('0.7', $gpx->getTotalTime('hours'));
		$this->assertEquals('00:41:25', $gpx->getTotalTime(), 'date format');
	}

	public function testGetElevationGain(){
		$kml = new KML('test_files/kml/test_correct_time.kml');
		$this->assertEquals('234.9', $kml->getElevationGain());
	}

	public function testGetElevationLoss(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertEquals('95.5', $tcx->getElevationLoss());
	}

	public function testGetTimesException(){
		$this->expectException(TracklogPhpException::class);
		$kml = new KML('test_files/kml/test_correct_no_time.kml');
		$kml->getTimes();
	}

	public function testGetElevationsException(){
		$this->expectException(TracklogPhpException::class);
		$json = new GeoJson('test_files/geoJson/test_correct_no_elevation.js');
		$json->getElevations();
	}	

	public function testGetMaxElevationException(){
		$this->expectException(TracklogPhpException::class);
		$gpx = new GPX('test_files/gpx/test_correct_no_elevation.gpx');
		$gpx->getMaxElevation();
	}

	public function testGetPaceException(){
		$this->expectException(TracklogPhpException::class);
		$csv = new CSV('test_files/csv/test_correct.csv');
		$csv->getPace();
	}

	public function testGetElevationGainException(){
		$this->expectException(TracklogPhpException::class);
		$kml = new KML('test_files/kml/test_correct_time_no_elevation.kml');
		$kml->getElevationGain();
	}

	public function testGetElevationLossException(){
		$this->expectException(TracklogPhpException::class);
		$kml = new KML('test_files/kml/test_correct_time_no_elevation.kml');
		$kml->getElevationLoss();
	}
}

?>