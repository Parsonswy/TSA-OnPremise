<?php 
	/*
	 * Custom Authentication Failure Exception
	 * Thrown when system us unable to verify the credentials of user in question
	 */
	class AuthenticationFailureException extends Exception{
		public function __construct($message, $code=0, Exception $previus = null, $user, $ip){
			$this->message = "[" . $user . "@" . $ip . "]" . $message;
		}
	}
?>