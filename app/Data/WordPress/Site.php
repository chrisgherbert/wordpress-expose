<?php

namespace App\Data\WordPress;

use Illuminate\Support\Facades\Cache;

class Site {

	public $url;
	public $site;
	protected $users_collection;

	public function __construct($url){
		$this->url = $url;
		$this->site = $this->get_site_obj();
	}

	public function get_comments_endpoint_url(){

		if ($this->get_index_url()){
			$endpoint = $this->get_index_url() . 'wp/v2/comments';
		}
		else {
			$parts = parse_url($this->url);
			$endpoint = $parts['scheme'] . '://' . $parts['host'] . '/wp-json/wp/v2/comments';
		}

		return $endpoint;

	}

	public function get_users_endpoint_url(){

		if ($this->get_index_url()){
			$endpoint = $this->get_index_url() . 'wp/v2/users';
		}
		else {
			$parts = parse_url($this->url);
			$endpoint = $parts['scheme'] . '://' . $parts['host'] . '/wp-json/wp/v2/users';
		}

		return $endpoint;

	}

	public function name(){

		if ($this->site){
			return $this->site->getName();
		}

	}

	public function description(){

		if ($this->site){
			return $this->site->getDescription();
		}

	}

	public function url(){

		if ($this->site){
			return $this->site->getURL();
		}

	}

	public function get_index_url(){

		if ($this->site){
			return $this->site->getIndexURL() ?? false;
		}

	}

	public function get_site_obj(){

		if (isset($this->site)){
			return $this->site;
		}

		// Silencing errors because the WordPress discovery tool is pretty sloppy. Tsk.
		$site = @\WordPress\Discovery\discover($this->url);

		$this->site = $site;

		return $this->site;

	}

	////////////
	// Static //
	////////////

	/**
	 * Will use cached instance of object if available
	 * @param  string $url Site URL
	 * @return Site        Site object
	 */
	public static function get_cached($url){

		$cache_key = self::cache_key($url);

		if (Cache::has($cache_key)){
			return Cache::get($cache_key);
		}

		$obj = new self($url);

		Cache::put($cache_key, $obj, (60 * 24));

		return $obj;

	}

	public static function cache_key($url){
		return 'site_obj_' . $url;
	}

}