<?php 
	/*
	 * Custom Authentication Failure Exception
	 * Thrown when system us unable to verify the credentials of user in question
	 */
	class TransactionException extends Exception{
		private $_charged;
		public function __construct($message, $code=0, Exception $previus = null, $op, $charged){
			$this->message = "[" . $op . "]" . $message;
			$this->_charged = $charged;
		}
		public function wasCharged(){
			return $this->_charged;
		}
	}
?>