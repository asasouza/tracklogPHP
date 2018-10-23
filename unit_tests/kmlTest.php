<?php
//sempre tem que ter o nome 'test' na frente do teste;
//comanda php unit_tests\phpunit.phar --bootstrap lib\tracklogPhp.main.php --testdox unit_tests\kmlTest.php
//comanda php unit_tests\phpunit.phar --bootstrap lib\tracklogPhp.main.php unit_tests

// assertEquals(esperado, atual);

use PHPUnit\Framework\TestCase;

final class KMLTest extends TestCase{

	public function testCreateTimeFile(){
		$this->assertInstanceOf(
			KML::class, 
			new KML('test_files/kml/test_correct_time.kml')
			);
	}

	public function testCreateNoTimeFile(){
		$this->assertInstanceOf(
			KML::class, 
			new KML('test_files/kml/test_correct_no_time.kml')
			);
	}

	public function testCreateNoTimeAndElevationFile(){
		$this->assertInstanceOf(
			KML::class, 
			new KML('test_files/kml/test_correct_no_time_no_elevation.kml')
			);
	}

	public function testCreateTimeNoElevationFile(){
		$this->assertInstanceOf(
			KML::class, 
			new KML('test_files/kml/test_correct_time_no_elevation.kml')
			);
	}

	public function testInvalidFileException(){
		$this->expectException(TracklogPhpException::class);
		new KML('test_files/kml/test_invalid.kml');
	}

	public function testNoTimeAndNoDataFileException(){
		$this->expectException(TracklogPhpException::class);
		new KML('test_files/kml/test_no_time_no_data.kml');	
	}

	public function testTimeAndNoDataFileException(){
		$this->expectException(TracklogPhpException::class);
		new KML('test_files/kml/test_time_no_data.kml');	
	}

	public function testNoExistingFileException(){
		$this->expectException(Exception::class);
		new KML('test_files/kml/test_no_existing.kml');
	}

	public function testOutputTimeKML(){
		$kml = new KML('test_files/kml/test_correct_time.kml');
		$kml->out('kml', 'test_files/kml/test_converted_time.kml');
		$this->assertInstanceOf(
			KML::class, 
			new KML('test_files/kml/test_converted.kml')
			);
	}

	public function testOutputNoTimeKML(){
		$kml = new KML('test_files/kml/test_correct_no_time.kml');
		$kml->out('kml', 'test_files/kml/test_converted_no_time.kml');
		$this->assertInstanceOf(
			KML::class, 
			new KML('test_files/kml/test_converted_no_time.kml')
			);
	}

	public function testOutputFromOtherFiles(){
		$gpx = new GPX('test_files/gpx/test_correct.gpx');
		$gpx->out('kml', 'test_files/kml/test_converted.kml');
		$this->assertInstanceOf(
			KML::class, 
			new KML('test_files/kml/test_converted.kml')
			);	
	}

	public function testOutputFromOtherFilesWithoutTime(){
		$json = new GeoJson('test_files/geoJson/test_correct.js');
		$json->out('kml', 'test_files/kml/test_converted_no_time.kml');
		$this->assertInstanceOf(
			KML::class, 
			new KML('test_files/kml/test_converted_no_time.kml')
			);
	}

	public function testOutputFromOtherFilesWithoutElevation(){
		$csv = new CSV('test_files/csv/test_correct_no_elevation.csv');
		$csv->out('kml', 'test_files/kml/test_converted_no_elevation.kml');
		$this->assertInstanceOf(
			KML::class, 
			new KML('test_files/kml/test_converted_no_elevation.kml')
			);	
	}

	public function testOutputFromOtherFilesAllData(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$tcx->out('kml', 'test_files/kml/test_converted_all_data.kml');
		$this->assertInstanceOf(
			KML::class, 
			new KML('test_files/kml/test_converted_all_data.kml')
			);	
	}
}

?>