<?php
//sempre tem que ter o nome 'test' na frente do teste;
//comanda php phpunit\phpunit.phar --bootstrap autoloader.php --testdox phpunit\tcxTest.php
//comanda php phpunit\phpunit.phar --bootstrap autoloader.php phpunit

// assertEquals(esperado, atual);

use PHPUnit\Framework\TestCase;

final class TCXTeste extends TestCase{

	public function testCreateFile(){
		$this->assertInstanceOf(
			TCX::class, 
			new TCX('test_files/tcx/test_correct.tcx')
			);
	}

	public function testCreateNoDistanceFile(){
		$this->assertInstanceOf(
			TCX::class, 
			new TCX('test_files/tcx/test_correct_no_distance.tcx')
			);
	}

	public function testCreateNoElevationFile(){
		$this->assertInstanceOf(
			TCX::class, 
			new TCX('test_files/tcx/test_correct_no_elevation.tcx')
			);
	}

	public function testInvalidFileException(){
		$this->expectException(TracklogPhpException::class);
		new TCX('test_files/tcx/test_invalid.tcx');
	}

	public function testNoDataFileException(){
		$this->expectException(TracklogPhpException::class);
		new TCX('test_files/tcx/test_no_data.tcx');	
	}

	public function testNoExistingFileException(){
		$this->expectException(Exception::class);
		new TCX('test_files/tcx/test_no_existing.tcx');
	}

	public function testOutputTCX(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$tcx->out('tcx', 'test_files/tcx/test_converted.tcx');
		$this->assertInstanceOf(
			TCX::class, 
			new TCX('test_files/tcx/test_converted.tcx')
			);
	}

	public function testOutputFromOtherFiles(){
		$gpx = new GPX('test_files/gpx/test_correct.gpx');
		$gpx->out('tcx', 'test_files/tcx/test_converted.tcx');
		$this->assertInstanceOf(
			TCX::class, 
			new TCX('test_files/tcx/test_converted.tcx')
			);	
	}

	public function testOutputFromOtherFilesWithoutTime(){
		$kml = new KML('test_files/kml/test_correct_no_time.kml');
		$kml->out('tcx', 'test_files/tcx/test_converted_no_time.tcx');
		$this->assertInstanceOf(
			TCX::class, 
			new TCX('test_files/tcx/test_converted_no_time.tcx')
			);
	}

	public function testOutputFromOtherFilesWithoutElevation(){
		$json = new GeoJson('test_files/geoJson/test_correct_no_elevation.js');
		$json->out('tcx', 'test_files/tcx/test_converted_no_elevation.tcx');
		$this->assertInstanceOf(
			TCX::class, 
			new TCX('test_files/tcx/test_converted_no_elevation.tcx')
			);
	}
}

?>