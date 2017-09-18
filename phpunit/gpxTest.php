<?php
//sempre tem que ter o nome 'test' na frente do teste;
//comanda php phpunit\phpunit.phar --bootstrap autoloader.php --testdox phpunit\gpxTest.php
//comanda php phpunit\phpunit.phar --bootstrap autoloader.php phpunit

// assertEquals(esperado, atual);

use PHPUnit\Framework\TestCase;

final class GPXTest extends TestCase{

	public function testCreateFile(){
		$this->assertInstanceOf(
			GPX::class, 
			new GPX('test_files/gpx/test_correct.gpx')
			);
	}

	public function testCreateNoElevationFile(){
		$this->assertInstanceOf(
			GPX::class, 
			new GPX('test_files/gpx/test_correct_no_elevation.gpx')
			);
	}

	public function testInvalidFileException(){
		$this->expectException(TracklogPhpException::class);
		new GPX('test_files/gpx/test_invalid.gpx');
	}

	public function testNoDataFileException(){
		$this->expectException(TracklogPhpException::class);
		new GPX('test_files/gpx/test_no_data.gpx');	
	}

	public function testNoExistingFileException(){
		$this->expectException(Exception::class);
		new GPX('test_files/gpx/test_no_existing.gpx');
	}

	public function testOutputGPX(){
		$gpx = new GPX('test_files/gpx/test_correct.gpx');
		$gpx->out('gpx', 'test_files/gpx/test_converted.gpx');
		$this->assertInstanceOf(
			GPX::class, 
			new GPX('test_files/gpx/test_converted.gpx')
			);
	}

	public function testOutputFromOtherFiles(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$tcx->out('gpx', 'test_files/gpx/test_converted.tcx');
		$this->assertInstanceOf(
			GPX::class, 
			new GPX('test_files/gpx/test_converted.gpx')
			);	
	}

	public function testOutputFromOtherFilesWithoutTime(){
		$kml = new KML('test_files/kml/test_correct_no_time.kml');
		$kml->out('gpx', 'test_files/gpx/test_converted_no_time.gpx');
		$this->assertInstanceOf(
			GPX::class, 
			new GPX('test_files/gpx/test_converted_no_time.gpx')
			);
	}

	public function testOutputFromOtherFilesWithoutElevation(){
		$json = new GeoJson('test_files/geoJson/test_correct_no_elevation.js');
		$json->out('gpx', 'test_files/gpx/test_converted_no_elevation.gpx');
		$this->assertInstanceOf(
			GPX::class, 
			new GPX('test_files/gpx/test_converted_no_elevation.gpx')
			);
	}
}

?>