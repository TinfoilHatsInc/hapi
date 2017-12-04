<?php

namespace core\hapi;

class Server{

	public function __construct(){
		
	}

	public function chubRegistration($chubid){
		if(strlen($chubid)>20 || strlen($chubid)<20){
			new ErrorResponse(ErrorResponse::HTTP_BAD_REQUEST, 'Malformed CHUB ID'))->send();
		}
		else{
			$db = new RegistrationDBController;
		}
	}


}
