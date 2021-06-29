<?php

namespace Core\Classes;

/**
 * Error and exception handler
 *
 * PHP version 7.0
 */
class DataResponse
{

	public function __construct($data, $total, $filtered){

		$this->recordsTotal = $total;
		$this->recordsFiltered = $filtered;			
		$this->data = $data;
		

	}

	public function asJson(){

		return json_encode($this);

	}


}
