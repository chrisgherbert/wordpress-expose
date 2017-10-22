<?php

namespace App\Data;
use Illuminate\Support\Facades\DB;

class EmailHash {

	public $hash;

	public function __construct($hash){
		$this->hash = $hash;
	}

	public function reverse_hash(){

		$email = DB::connection('hashes')->table('emails')->where('hash', $this->hash)->value('emails');

		return $email;

	}

	////////////
	// Static //
	////////////

	public static function reverse($hash){

		$reverser = new self($hash);

		return $reverser->reverse_hash();

	}

}