<?php
use PHPUnit\Framework\TestCase;

final class TCXTeste extends TestCase{

	public function createFile():void{
		$this->assertInstanceOf(
			TCX::class, 
			TCX::__construct('../test_files/test.tcx')
			);
	}
}

?>