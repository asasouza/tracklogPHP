<?php
//sempre tem que ter o nome 'test' na frente do teste;
//comanda php phpunit\phpunit.phar --bootstrap autoloader.php --testdox phpunit\tcxTest.php
//comanda php phpunit\phpunit.phar --bootstrap autoloader.php phpunit
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

	public function testGetAllPoints(){}

	public function testGetAllLatitudes(){}

	public function testGetAllLongitudes(){}

	public function testGetAllElevations(){}

	public function testGetAllTimes(){}

	public function testGetAllDistances(){}

	public function testGetTotalDistance(){}

	public function testGetMaxElevation(){}

	public function testGetPace(){}

	public function testGetTotalTime(){}

	public function testOutputTCX(){}

	public function testOutputOtherFiles(){}
}

?>