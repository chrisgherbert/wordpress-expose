<?php

namespace App\Data\WordPress;

class Entity {

	public $data;

	public function __construct($data){
		$this->data = $data;
	}

	public function get_data_item($key){
		return $this->data->$key ?? false;
	}

	public function id(){
		return $this->get_data_item('id');
	}

}