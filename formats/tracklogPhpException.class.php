<?php
class TracklogPhpException extends Exception{
	private $message;

	public function __construct($message){
		$this->message = $message;
	}

	public function getMessage(){
		return $this->message();
	}
}	

?>