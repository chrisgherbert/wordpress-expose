<?php

namespace App\Data\WordPress;

class CommentsCollection extends EntityCollection {

	public function app_url_slug(){
		return 'comments';
	}

	public function items(){

		if ($this->data){

			return array_map(function($item){
				return new Comment($item);
			}, $this->data);

		}

	}

}