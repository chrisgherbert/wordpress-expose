<?php

namespace App\Data;

class Gravatar {

	public $url;
	protected $email;
	protected $hash;

	public function __construct($url){
		$this->url = $url;
	}

	public function get_email(){

		if (isset($this->email)){
			return $this->email;
		}

		$hash = new EmailHash($this->get_hash());

		$email = $hash->reverse_hash();

		$this->email = $email;

		return $this->email;

	}

	public function get_hash(){

		if (!$this->url){
			return false;
		}

		$parts = parse_url($this->url);

		$path = trim($parts['path'], '/');

		$path_parts = explode('/', $path);

		if (isset($path_parts[1])){
			return $path_parts[1];
		}

	}

}