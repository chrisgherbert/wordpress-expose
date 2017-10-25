<?php

namespace App\Data\WordPress;

use Curl\Curl;
use Illuminate\Support\Facades\Cache;

abstract class EntityCollection {

	public $site_url;
	public $endpoint_url;
	public $options;
	public $per_page;
	public $page;

	public $data;
	public $headers;

	public function __construct($site_url, $endpoint_url, $page = 1, $per_page = 25){
		$this->site_url = $site_url;
		$this->endpoint_url = $endpoint_url;
		$this->page = $page;
		$this->per_page = $per_page;
		$this->make_request();
	}

	abstract public function items();

	abstract public function app_url_slug();

	public function next_prev(){

		if ($this->total_pages() > 1){

			$items = array();

			$app_url_slug = $this->app_url_slug();

			if ($this->page != 1){

				$items[] = array(
					'name' => '&larr; Previous',
					'url' => "/wordpress/$app_url_slug?" . http_build_query([
						'url' => $this->site_url,
						'page' => $this->page - 1
					])
				);

			}

			if ($this->total_pages() != $this->page){

				$items[] = array(
					'name' => 'Next &rarr;',
					'url' => "/wordpress/$app_url_slug?" . http_build_query([
						'url' => $this->site_url,
						'page' => $this->page + 1
					])
				);

			}

			return $items;

		}

	}

	public function pagination(){

		if ($this->total_pages() > 1){

			$pages = array();

			$app_url_slug = $this->app_url_slug();

			foreach (range(1, $this->total_pages()) as $page_num){

				$pages[] = [
					'num' => $page_num,
					'url' => "/wordpress/$app_url_slug?" . http_build_query([
						'url' => $this->site_url,
						'page' => $page_num
					]),
					'current' => $this->current_page() == $page_num
				];

			}

			return $pages;

		}

	}

	public function current_page(){
		return $this->page ?? 1;
	}

	public function total_pages(){
		return $this->headers['X-WP-TotalPages'] ?? 1;
	}

	public function total_items(){
		return $this->headers['X-WP-Total'] ?? 0;
	}

	public function get_data_item($key){
		return $this->data[$key] ?? false;
	}

	public function get_header_item($key){
		return $this->headers[$key] ?? false;
	}

	///////////////
	// Protected //
	///////////////

	protected function make_request(){

		$curl = static::get_curl();

		$params = array(
			'per_page' => $this->per_page,
			'page' => $this->page
		);

		$request_url = $this->endpoint_url . '?' . http_build_query($params);

		$curl->get($request_url);

		if ($curl->error){
			throw new \Exception('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage);
		}

		$this->data = $curl->response;
		$this->headers = $curl->responseHeaders;

		return true;

	}

	////////////
	// Static //
	////////////

	protected static function get_curl(){

		$curl = new Curl;
		$curl->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Safari/604.1.38');
		$curl->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
		$curl->setOpt(CURLOPT_SSL_VERIFYPEER, 0);
		$curl->setOpt(CURLOPT_FOLLOWLOCATION, 1);

		return $curl;

	}

	/**
	 * Will use cached instance of object if available
	 * @param  string $url Site URL
	 * @return Site        Site object
	 */
	public static function get_cached($site_url, $endpoint_url, $page = 1, $per_page = 25){

		$cache_key = self::cache_key($site_url, $endpoint_url, $page, $per_page);

		if (Cache::has($cache_key)){
			return Cache::get($cache_key);
		}

		$obj = new static($site_url, $endpoint_url, $page, $per_page);

		Cache::put($cache_key, $obj, (15));

		return $obj;

	}

	public static function cache_key($site_url, $endpoint_url, $page, $per_page){

		$string = implode('-', array($site_url, $endpoint_url, $page, $per_page));

		return get_called_class() . '_obj_' . $string;

	}

}