<?php
//sempre tem que ter o nome 'test' na frente do teste;
//comanda php phpunit\phpunit.phar --bootstrap autoloader.php --testdox phpunit\geoJsonTest.php
//comanda php phpunit\phpunit.phar --bootstrap autoloader.php phpunit

// assertEquals(esperado, atual);


use PHPUnit\Framework\TestCase;

final class GeoJsonTest extends TestCase{

	public function testCreateFile(){
		$this->assertInstanceOf(
			GeoJson::class, 
			new GeoJson('test_files/geoJson/test_correct.js')
			);
	}

	public function testCreateNoElevationFile(){
		$this->assertInstanceOf(
			GeoJson::class, 
			new GeoJson('test_files/geoJson/test_correct_no_elevation.js')
			);
	}

	public function testInvalidFileException(){
		$this->expectException(TracklogPhpException::class);
		new GeoJson('test_files/geoJson/test_invalid.js');
	}

	public function testNoDataFileException(){
		$this->expectException(TracklogPhpException::class);
		new GeoJson('test_files/geoJson/test_no_data.js');	
	}

	public function testNoExistingFileException(){
		$this->expectException(Exception::class);
		new GeoJson('test_files/geoJson/test_no_existing.js');
	}

	public function testOutputGeoJson(){
		$json = new GeoJson('test_files/geoJson/test_correct.js');
		$json->out('geoJson', 'test_files/geoJson/test_converted.js');
		$this->assertInstanceOf(
			GeoJson::class, 
			new GeoJson('test_files/geoJson/test_converted.js')
			);
	}

	public function testOutputFromOtherFiles(){
		$gpx = new GPX('test_files/gpx/test_correct.gpx');
		$gpx->out('geoJson', 'test_files/geoJson/test_converted.js');
		$this->assertInstanceOf(
			GeoJson::class, 
			new GeoJson('test_files/geoJson/test_converted.js')
			);	
	}

	public function testOutputFromOtherFilesWithoutTime(){
		$kml = new KML('test_files/kml/test_correct_no_time.kml');
		$kml->out('geoJson', 'test_files/geoJson/test_converted_no_time.js');
		$this->assertInstanceOf(
			GeoJson::class, 
			new GeoJson('test_files/geoJson/test_converted_no_time.js')
			);
	}

	public function testOutputFromOtherFilesWithoutElevation(){
		$json = new GeoJson('test_files/geoJson/test_correct_no_elevation.js');
		$json->out('geoJson', 'test_files/geoJson/test_converted_no_elevation.js');
		$this->assertInstanceOf(
			GeoJson::class, 
			new GeoJson('test_files/geoJson/test_converted_no_elevation.js')
			);
	}

	public function testOutputFromOtherFileWithAllData(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$tcx->out('geoJson', 'test_files/geoJson/test_converted_all_data.js');
		$this->assertInstanceOf(
			GeoJson::class, 
			new GeoJson('test_files/geoJson/test_converted_all_data.js')
			);	
	}
}

?>