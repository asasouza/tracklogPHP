<?php
//sempre tem que ter o nome 'test' na frente do teste;
//comanda php phpunit\phpunit.phar --bootstrap autoloader.php --testdox phpunit\csvTest.php
//comanda php phpunit\phpunit.phar --bootstrap autoloader.php phpunit

//inverter a ordem de esperado e resultado

// assertEquals(esperado, atual);


use PHPUnit\Framework\TestCase;

final class CSVTeste extends TestCase{

	public function testCreateFile(){
		$this->assertInstanceOf(
			CSV::class, 
			new CSV('test_files/csv/test_correct.csv')
			);
	}

	public function testInvalidFile(){
		$this->expectException(TracklogPhpException::class);
		new CSV('test_files/csv/test_invalid.csv');
	}

	public function testNoDataFile(){
		$this->expectException(TracklogPhpException::class);
		new CSV('test_files/csv/test_no_data.csv');	
	}

	public function testNoExistingFile(){
		$this->expectException(Exception::class);
		new CSV('test_files/csv/test_no_existing.csv');
	}

	public function testGetTrackName(){
		$csv = new CSV('test_files/csv/test_correct.csv');
		$this->assertEquals('Topo_rdp_topo', $csv->getTrackName());
	}

	public function testOutputCSV(){
		$csv = new CSV('test_files/csv/test_correct.csv');
		$csv->out('csv', 'test_files/csv/test_converted.csv');
		$this->assertEquals(file_get_contents('test_files/csv/test_correct.csv'), file_get_contents('test_files/csv/test_converted.csv'));
	}

	public function testOutputFromOtherFiles(){
		$gpx = new GPX('test_files/csv/test_convert_from_other_file.gpx');
		$gpx->out('csv', 'test_files/csv/test_converted.csv');
		$this->assertEquals(file_get_contents('test_files/csv/test_correct.csv'), file_get_contents('test_files/csv/test_converted.csv'));
	}

	public function testOutputFromOtherFilesWithoutTime(){
		$kml = new KML('test_files/csv/test_convert_from_other_file_w_time.kml');
		$kml->out('csv', 'test_files/csv/test_converted.csv');
		$this->assertEquals(file_get_contents('test_files/csv/test_correct.csv'), file_get_contents('test_files/csv/test_converted.csv'));
	}
}

?>