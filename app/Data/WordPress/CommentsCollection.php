<?php

namespace App\Data\WordPress;

class CommentsCollection extends EntityCollection {

	public function app_url_slug(){
		return 'comments';
	}

	public function items(){

		if ($this->data && is_array($this->data)){

			return array_map(function($item){
				return new Comment($item);
			}, $this->data);

		}
		else if ($this->data){
			error_log(print_r($this->data, true));
		}

	}

}