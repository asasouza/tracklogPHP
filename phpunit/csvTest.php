<?php
//sempre tem que ter o nome 'test' na frente do teste;
//comanda php phpunit\phpunit.phar --bootstrap autoloader.php --testdox phpunit\csvTest.php
//comanda php phpunit\phpunit.phar --bootstrap autoloader.php phpunit

// assertEquals(esperado, atual);


use PHPUnit\Framework\TestCase;

final class CSVTeste extends TestCase{

	public function testCreateFile(){
		$this->assertInstanceOf(
			CSV::class, 
			new CSV('test_files/csv/test_correct.csv')
			);
	}

	public function testCreateNoHeadFile(){
		$this->assertInstanceOf(
			CSV::class, 
			new CSV('test_files/csv/test_correct_no_head.csv')
			);
	}

	public function testCreateNoElevationFile(){
		$this->assertInstanceOf(
			CSV::class, 
			new CSV('test_files/csv/test_correct_no_elevation.csv')
			);
	}

	public function testInvalidFileException(){
		$this->expectException(TracklogPhpException::class);
		new CSV('test_files/csv/test_invalid.csv');
	}

	public function testNoDataFileException(){
		$this->expectException(TracklogPhpException::class);
		new CSV('test_files/csv/test_no_data.csv');	
	}

	public function testNoExistingFileException(){
		$this->expectException(Exception::class);
		new CSV('test_files/csv/test_no_existing.csv');
	}

	public function testOutputCSV(){
		$csv = new CSV('test_files/csv/test_correct.csv');
		$csv->out('csv', 'test_files/csv/test_converted.csv');
		$this->assertInstanceOf(
			CSV::class, 
			new CSV('test_files/csv/test_converted.csv')
			);
	}

	public function testOutputFromOtherFiles(){
		$gpx = new GPX('test_files/gpx/test_correct.gpx');
		$gpx->out('csv', 'test_files/csv/test_converted.csv');
		$this->assertInstanceOf(
			CSV::class, 
			new CSV('test_files/csv/test_converted.csv')
			);	
	}

	public function testOutputFromOtherFilesWithoutTime(){
		$kml = new KML('test_files/kml/test_correct_no_time.kml');
		$kml->out('csv', 'test_files/csv/test_converted_no_time.csv');
		$this->assertInstanceOf(
			CSV::class, 
			new CSV('test_files/csv/test_converted_no_time.csv')
			);
	}

	public function testOutputFromOtherFilesWithoutElevation(){
		$json = new GeoJson('test_files/geoJson/test_correct_no_elevation.js');
		$json->out('csv', 'test_files/csv/test_converted_no_elevation.csv');
		$this->assertInstanceOf(
			CSV::class, 
			new CSV('test_files/csv/test_converted_no_elevation.csv')
			);
	}

	public function testOutputFromOtherFileWithAllData(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$tcx->out('csv', 'test_files/csv/test_converted_all_data.csv');
		$this->assertInstanceOf(
			CSV::class, 
			new CSV('test_files/csv/test_converted_all_data.csv')
			);	
	}
}

?>