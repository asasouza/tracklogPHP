<?php
//sempre tem que ter o nome 'test' na frente do teste;
//comanda php phpunit\phpunit.phar --bootstrap autoloader.php --testdox phpunit\tcxTest.php
//comanda php phpunit\phpunit.phar --bootstrap autoloader.php phpunit

//inverter a ordem de esperado e resultado

// assertEquals(esperado, atual);


use PHPUnit\Framework\TestCase;

final class TCXTeste extends TestCase{

	public function testCreateFile(){
		$this->assertInstanceOf(
			TCX::class, 
			new TCX('test_files/tcx/test_correct.tcx')
			);
	}

	public function testInvalidFile(){
		$this->expectException(TracklogPhpException::class);
		new TCX('test_files/tcx/test_invalid.tcx');
	}

	public function testNoDataFile(){
		$this->expectException(TracklogPhpException::class);
		new TCX('test_files/tcx/test_no_data.tcx');	
	}

	public function testNoExistingFile(){
		$this->expectException(TracklogPhpException::class);
		new TCX('test_files/tcx/test_no_existing.tcx');
	}

	public function testGetTrackName(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertEquals('Topo_rdp_topo', $tcx->getTrackName());
	}

	public function testGetAllPoints(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertFalse(empty($tcx->getPoints()));
		$this->assertEquals(1293, count($tcx->getPoints()));
		$this->assertArraySubset(['0' => [ 'lat' => -23.8097560, 
											'lon' => -45.4189910, 
											'ele' => 36.900000, 
											'dstc' => 0.000, 
											'time' => '2017-06-08T21:58:50Z']], $tcx->getPoints());
	}

	public function testGetAllLatitudes(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertFalse(empty($tcx->getLatitudes()));
		$this->assertEquals(1293, count($tcx->getLatitudes()));
		$this->assertArraySubset( [-23.8097560, -23.8097460, -23.8097540, -23.8097660] ,$tcx->getLatitudes());
	}

	public function testGetAllLongitudes(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertFalse(empty($tcx->getLongitudes()));
		$this->assertEquals(1293, count($tcx->getLongitudes()));
		$this->assertArraySubset( [-45.4189910, -45.4188970, -45.4188470, -45.4188140] ,$tcx->getLongitudes());
	}

	public function testGetAllElevations(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertFalse(empty($tcx->getElevations()));
		$this->assertEquals(1293, count($tcx->getElevations()));
		$this->assertArraySubset( [36.900000, 35.400000, 34.000000, 33.100000] ,$tcx->getElevations());
	}

	public function testGetAllTimes(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertFalse(empty($tcx->getTimes()));
		$this->assertEquals(1293, count($tcx->getTimes()));
		$this->assertArraySubset( ['2017-06-08T21:58:50Z', '2017-06-08T21:59:00Z', '2017-06-08T21:59:04Z', '2017-06-08T21:59:06Z'] ,$tcx->getTimes());
	}

	public function testGetAllDistances(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertFalse(empty($tcx->getDistances()));
		$this->assertEquals(1293, count($tcx->getDistances()));
		$this->assertArraySubset( [0.000, 9.627, 14.792, 18.404] ,$tcx->getDistances());
	}

	public function testGetTotalDistance(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertEquals(6010.70, $tcx->getTotalDistance('meters'), "meters");
		$this->assertEquals(3.73, $tcx->getTotalDistance('miles'), "miles");
		$this->assertEquals(6.01, $tcx->getTotalDistance('kilometers'), "kilometers");
	}

	public function testGetMaxElevation(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertEquals(38.90, $tcx->getMaxElevation(), "meters");
	}

	public function testGetPace(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertEquals('6:53', $tcx->getPace(), 'minutes per kilometer');
	}

	public function testGetTotalTime(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertEquals('2.4850', $tcx->getTotalTime('seconds'));
		$this->assertEquals('41.4', $tcx->getTotalTime('minutes'));
		$this->assertEquals('0.7', $tcx->getTotalTime('hours'));
		$this->assertEquals('00:41:25', $tcx->getTotalTime(), 'date format');
	}

	public function testOutputTCX(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$tcx->out('tcx', 'test_files/tcx/test_converted.tcx');
		$this->assertXmlFileEqualsXmlFile('test_files/tcx/test_correct.tcx', 'test_files/tcx/test_converted.tcx');
	}

	public function testOutputFromOtherFiles(){
		$gpx = new GPX('test_files/tcx/test_convert_from_other_file.gpx');
		$gpx->out('tcx', 'test_files/tcx/test_converted.tcx');
		$this->assertXmlFileEqualsXmlFile('test_files/tcx/test_correct.tcx', 'test_files/tcx/test_converted.tcx');
	}

	public function testOutputFromOtherFilesWithoutTime(){
		$kml = new KML('test_files/tcx/test_convert_from_other_file_w_time.kml');
		$kml->out('tcx', 'test_files/tcx/test_converted.tcx');
		$this->assertXmlFileEqualsXmlFile('test_files/tcx/test_correct.tcx', 'test_files/tcx/test_converted.tcx');
	}
}

?>