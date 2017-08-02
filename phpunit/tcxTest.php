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
		$this->expectException(Exception::class);
		new TCX('test_files/tcx/test_invalid.tcx');
	}

	public function testNoDataFile(){
		$this->expectException(Exception::class);
		new TCX('test_files/tcx/test_no_data.tcx');	
	}

	public function testNoExistingFile(){
		$this->expectException(Exception::class);
		new TCX('test_files/tcx/test_no_existing.tcx');	
	}

	public function testGetAllPoints(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertFalse(empty($tcx->getPoints()));
		$this->assertEquals(count($tcx->getPoints()), 1293);
		$this->assertArraySubset(['0' => [ 'lat' => -23.8097560, 
											'lon' => -45.4189910, 
											'ele' => 36.900000, 
											'dstc' => 0.000, 
											'time' => '2017-06-08T21:58:50Z']], $tcx->getPoints());
	}

	public function testGetAllLatitudes(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertFalse(empty($tcx->getLatitudes()));
		$this->assertEquals(count($tcx->getLatitudes()), 1293);
		$this->assertArraySubset( [-23.8097560, -23.8097460, -23.8097540, -23.8097660] ,$tcx->getLatitudes());
	}

	public function testGetAllLongitudes(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertFalse(empty($tcx->getLongitudes()));
		$this->assertEquals(count($tcx->getLongitudes()), 1293);
		$this->assertArraySubset( [-45.4189910, -45.4188970, -45.4188470, -45.4188140] ,$tcx->getLongitudes());
	}

	public function testGetAllElevations(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertFalse(empty($tcx->getElevations()));
		$this->assertEquals(count($tcx->getElevations()), 1293);
		$this->assertArraySubset( [36.900000, 35.400000, 34.000000, 33.100000] ,$tcx->getElevations());
	}

	public function testGetAllTimes(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertFalse(empty($tcx->getTimes()));
		$this->assertEquals(count($tcx->getTimes()), 1293);
		$this->assertArraySubset( ['2017-06-08T21:58:50Z', '2017-06-08T21:59:00Z', '2017-06-08T21:59:04Z', '2017-06-08T21:59:06Z'] ,$tcx->getTimes());
	}

	public function testGetAllDistances(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertFalse(empty($tcx->getDistances()));
		$this->assertEquals(count($tcx->getDistances()), 1293);
		$this->assertArraySubset( [0.000, 9.627, 14.792, 18.404] ,$tcx->getDistances());
	}

	public function testGetTotalDistance(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertEquals($tcx->getTotalDistance('meters') , 6010.70, "meters");
		$this->assertEquals($tcx->getTotalDistance('miles') , 3.73, "miles");
		$this->assertEquals($tcx->getTotalDistance('kilometers') , 6.01, "kilometers");
	}

	public function testGetMaxElevation(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertEquals($tcx->getMaxElevation() , 38.90, "meters");
	}

	public function testGetPace(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertEquals($tcx->getPace() , '6:53');
	}

	public function testGetTotalTime(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$this->assertEquals('2.4850', $tcx->getTotalTime('seconds'));
		$this->assertEquals('00:41:25', $tcx->getTotalTime('minutes'));
		$this->assertEquals('0.7', $tcx->getTotalTime('hours'));
	}

	public function testOutputTCX(){
		$tcx = new TCX('test_files/tcx/test_correct.tcx');
		$tcx->out('tcx', 'test_files/tcx/test_converted.tcx');
		$this->assertXmlFileEqualsXmlFile('test_files/tcx/test_correct.tcx', 'test_files/tcx/test_converted.tcx');
	}

	public function testOutputFromOtherFiles(){}
}

?>