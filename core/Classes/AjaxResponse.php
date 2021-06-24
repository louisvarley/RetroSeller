<?php

namespace Core\Classes;

/**
 * Error and exception handler
 *
 * PHP version 7.0
 */
class AjaxResponse
{

	public function __construct($responseCode, $code, $response){

		http_response_code($responseCode);
		$this->code = $code;
		$this->response = $response;

	}

	public function asJson(){

		return json_encode($this);

	}


}
